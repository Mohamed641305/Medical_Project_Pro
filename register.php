<?php
session_start();
include("includes/db/db.php");
include("includes/temp/header.php");

$message = "";
$name = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $name  = trim($_POST['name']);
  $email = trim($_POST['email']);
  $pass  = trim($_POST['pass']);
  $cpass = trim($_POST['cpass']);

  /* ========================= VALIDATION ========================= */

  if (empty($name) || empty($email) || empty($pass) || empty($cpass)) {

    $message = "Please fill in all fields.";

  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $message = "Enter a valid email address.";

  } elseif (strlen($pass) < 5) {

    $message = "Password must be at least 5 characters long.";

  } elseif ($pass !== $cpass) {

    $message = "Passwords do not match.";
  }

  /* ========================= REGISTER ========================= */

  if (empty($message)) {

    $stmt = $connect->prepare(
      "SELECT * FROM researchers WHERE email = ?"
    );

    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {

      $message = "Email is already registered.";

    } else {

      $stmt = $connect->prepare(
        "INSERT INTO researchers
        (`name`, email, `password`, `role`, created_at)
        VALUES (?, ?, ?, 'researcher', NOW())"
      );

      $stmt->execute([$name, $email, $pass]);

      $_SESSION['success'] =
      "Registration successful! You can login now.";

      header("Location: login.php");
      exit();
    }
  }
}
?>

<style>

  body{
    background:
    linear-gradient(135deg,#0f172a,#1e293b,#0f172a);
    min-height:100vh;
    font-family:'Segoe UI',sans-serif;
    overflow-x:hidden;
  }

  .register-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 15px;
  }

  .register-card{
    width:100%;
    max-width:520px;
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,.12);
    border-radius:28px;
    padding:45px 35px;
    position:relative;
    overflow:hidden;
    box-shadow:0 15px 40px rgba(0,0,0,.35);
    animation:fadeIn 1s ease;
  }

  .register-card::before{
    content:'';
    position:absolute;
    width:220px;
    height:220px;
    border-radius:50%;
    background:#22c55e;
    top:-90px;
    right:-90px;
    opacity:.12;
  }

  .register-card::after{
    content:'';
    position:absolute;
    width:200px;
    height:200px;
    border-radius:50%;
    background:#3b82f6;
    bottom:-90px;
    left:-90px;
    opacity:.12;
  }

  .icon-box{
    width:95px;
    height:95px;
    margin:0 auto 20px;
    border-radius:50%;
    background:
    linear-gradient(135deg,#3b82f6,#2563eb);
    display:flex;
    justify-content:center;
    align-items:center;
    color:#fff;
    font-size:38px;
    box-shadow:0 10px 30px rgba(59,130,246,.4);
  }

  .auth-title{
    text-align:center;
    color:#fff;
    font-size:34px;
    font-weight:700;
    margin-bottom:10px;
  }

  .auth-subtitle{
    text-align:center;
    color:#cbd5e1;
    margin-bottom:35px;
    font-size:15px;
  }

  .form-control{
    height:56px;
    border:none;
    border-radius:15px;
    background:rgba(255,255,255,.10);
    color:#fff;
    margin-bottom:22px;
    padding-left:18px;
    transition:.3s;
  }

  .form-control:focus{
    background:rgba(255,255,255,.16);
    color:#fff;
    box-shadow:0 0 0 4px rgba(59,130,246,.25);
  }

  .form-control::placeholder{
    color:#cbd5e1;
  }

  .register-btn{
    height:56px;
    border:none;
    border-radius:15px;
    background:
    linear-gradient(135deg,#3b82f6,#2563eb);
    color:#fff;
    font-size:18px;
    font-weight:600;
    transition:.3s;
  }

  .register-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 12px 25px rgba(59,130,246,.35);
  }

  .bottom-text{
    text-align:center;
    margin-top:25px;
    color:#e2e8f0;
  }

  .bottom-text a{
    color:#38bdf8;
    text-decoration:none;
    font-weight:600;
  }

  .bottom-text a:hover{
    text-decoration:underline;
  }

  .alert{
    border-radius:12px;
    font-size:15px;
  }

  @keyframes fadeIn{

    from{
      opacity:0;
      transform:translateY(30px);
    }

    to{
      opacity:1;
      transform:translateY(0);
    }
  }

  @media(max-width:576px){

    .register-card{
      padding:35px 22px;
    }

    .auth-title{
      font-size:28px;
    }
  }

</style>

<div class="register-wrapper">

  <div class="register-card">

    <div class="icon-box">
      <i class="fa-solid fa-user-plus"></i>
    </div>

    <h2 class="auth-title">
      Create Account
    </h2>

    <p class="auth-subtitle">
      Join MediCare and start your smart healthcare journey.
    </p>

    <!-- Error Message -->

    <?php if (!empty($message)) { ?>

      <div class="alert alert-danger text-center"
           id="message">

        <?php echo $message; ?>

      </div>

    <?php } ?>

    <!-- Register Form -->

    <form method="post">

      <input
        type="text"
        name="name"
        value="<?php echo htmlspecialchars($name); ?>"
        placeholder="Enter Full Name"
        class="form-control"
      >

      <input
        type="email"
        name="email"
        value="<?php echo htmlspecialchars($email); ?>"
        placeholder="Enter E-mail"
        class="form-control"
      >

      <input
        type="password"
        name="pass"
        placeholder="Enter Password"
        class="form-control"
      >

      <input
        type="password"
        name="cpass"
        placeholder="Confirm Password"
        class="form-control"
      >

      <button
        type="submit"
        class="btn register-btn w-100">

        <i class="fa-solid fa-user-check me-2"></i>
        Register

      </button>

    </form>

    <div class="bottom-text">

      Already have an account?

      <a href="login.php">
        Login here
      </a>

    </div>

  </div>

</div>

<!-- Hide Message -->

<script>

  setTimeout(() => {

    const msg =
    document.getElementById('message');

    if(msg){

      msg.style.display = 'none';
    }

  },3000);

</script>

<?php
include("includes/temp/footer.php");
?>