<?php
session_start();
include("includes/db/db.php");
include("includes/temp/header.php");

$message = "";
$email   = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

  $email = trim($_POST['email']);
  $pass  = trim($_POST['pass']);

  /* ========================= VALIDATION ========================= */

  $fields = [$email, $pass];
  $empty = 0;

  foreach ($fields as $f) {
    if ($f == "") $empty++;
  }

  if ($empty >= 2) {

    $message = "Please fill in all fields.";

  } else if ($email == "") {

    $message = "Please enter Email.";

  } else if ($pass == "") {

    $message = "Please enter Password.";

  } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $message = "Enter a valid email address.";

  } else if (strlen($pass) < 5) {

    $message = "Password must be at least 5 characters long.";
  }

  /* ========================= LOGIN ========================= */

  if (empty($message)) {

    $statement = $connect->prepare(
      "SELECT * FROM researchers WHERE email=?"
    );

    $statement->execute(array($email));

    if ($statement->rowCount() > 0) {

      $result = $statement->fetch();

      // Check Password
      if ($pass != $result['password']) {

        $_SESSION['message_login'] = "Check Your Password";

      } else {

        // Check Role
        if ($result['role'] == "admin") {

          $_SESSION['admin_login'] = $email;
          header("Location: Admin/dashboard.php");
          exit();

        } else {

          $_SESSION['user_login'] = $email;
          header("Location: index.php");
          exit();
        }
      }

    } else {

      $_SESSION['message_login'] = "Your Account Not in DB";
    }
  }
}
?>

<style>
  body{
    background: linear-gradient(135deg,#0f172a,#1e293b,#0f172a);
    min-height:100vh;
    overflow-x:hidden;
    font-family: 'Segoe UI', sans-serif;
  }

  .login-wrapper{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px 15px;
  }

  .login-card{
    width:100%;
    max-width:500px;
    background:rgba(255,255,255,0.08);
    backdrop-filter: blur(15px);
    border:1px solid rgba(255,255,255,0.15);
    border-radius:25px;
    padding:45px 35px;
    box-shadow:0 15px 40px rgba(0,0,0,.35);
    animation:fadeIn 1s ease;
    position:relative;
    overflow:hidden;
  }

  .login-card::before{
    content:'';
    position:absolute;
    width:200px;
    height:200px;
    background:#22c55e;
    border-radius:50%;
    top:-80px;
    right:-80px;
    opacity:.15;
  }

  .login-card::after{
    content:'';
    position:absolute;
    width:180px;
    height:180px;
    background:#06b6d4;
    border-radius:50%;
    bottom:-80px;
    left:-80px;
    opacity:.15;
  }

  .auth-title{
    color:#fff;
    text-align:center;
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

  .icon-box{
    width:90px;
    height:90px;
    margin:0 auto 20px;
    border-radius:50%;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:38px;
    box-shadow:0 10px 25px rgba(34,197,94,.4);
  }

  .form-control{
    height:55px;
    border-radius:14px;
    border:none;
    background:rgba(255,255,255,.12);
    color:#fff;
    padding-left:18px;
    margin-bottom:22px;
    transition:.3s;
  }

  .form-control:focus{
    background:rgba(255,255,255,.18);
    box-shadow:0 0 0 3px rgba(34,197,94,.25);
    color:#fff;
  }

  .form-control::placeholder{
    color:#cbd5e1;
  }

  .login-btn{
    height:55px;
    border:none;
    border-radius:14px;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    color:#fff;
    font-size:18px;
    font-weight:600;
    transition:.3s;
  }

  .login-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(34,197,94,.35);
  }

  .bottom-text{
    text-align:center;
    margin-top:25px;
    color:#e2e8f0;
  }

  .bottom-text a{
    color:#22c55e;
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

    .login-card{
      padding:35px 22px;
    }

    .auth-title{
      font-size:28px;
    }
  }
</style>

<div class="login-wrapper">

  <div class="login-card">

    <div class="icon-box">
      <i class="fa-solid fa-heart-pulse"></i>
    </div>

    <h2 class="auth-title">MediCare Login</h2>

    <p class="auth-subtitle">
      Welcome back! Login to continue your healthcare journey.
    </p>

    <!-- Validation Message -->
    <?php if (!empty($message)) { ?>

      <div class="alert alert-danger text-center" id="message">
        <?php echo $message; ?>
      </div>

    <?php } ?>

    <!-- Session Message -->
    <?php
      if (isset($_SESSION['message_login'])) {

        echo "
          <div class='alert alert-danger text-center' id='message_login'>
            ".$_SESSION['message_login']."
          </div>
        ";

        unset($_SESSION['message_login']);
      }

      if (isset($_SESSION['success'])) {

        echo "
          <div class='alert alert-success text-center' id='success'>
            ".$_SESSION['success']."
          </div>
        ";

        unset($_SESSION['success']);
      }
    ?>

    <!-- Login Form -->
    <form method="post">

      <input
        type="email"
        name="email"
        value="<?php echo htmlspecialchars($email); ?>"
        placeholder="Enter your E-mail"
        class="form-control"
      >

      <input
        type="password"
        name="pass"
        placeholder="Enter your Password"
        class="form-control"
      >

      <button type="submit" class="btn login-btn w-100">
        <i class="fa-solid fa-right-to-bracket me-2"></i>
        Login
      </button>

    </form>

    <div class="bottom-text">
      Don't have an account?
      <a href="register.php">Register here</a>
    </div>

  </div>

</div>

<!-- Hide Messages -->
<script>
  setTimeout(() => {

    const formMsg = document.getElementById('message');
    if(formMsg){
      formMsg.style.display = 'none';
    }

    const loginMsg = document.getElementById('message_login');
    if(loginMsg){
      loginMsg.style.display = 'none';
    }

    const successMsg = document.getElementById('success');
    if(successMsg){
      successMsg.style.display = 'none';
    }

  }, 3000);
</script>

<?php
include "includes/temp/footer.php";
?>