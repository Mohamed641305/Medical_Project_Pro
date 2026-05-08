<?php
session_start();

if (isset($_SESSION['admin_login'])) {

  include('includes/temp/init.php');
  include('includes/temp/navbar.php');

  $page  = $_GET['page'] ?? 'All';
  $id    = isset($_GET['id']) ? (int)$_GET['id'] : null;
  $error = '';

  $researcher  = null;
  $researchers = [];

  /* ================= CREATE + EDIT ================= */

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = trim($_POST['role'] ?? '');

    if ($name == '' || $email == '' || $password == '' || $role == '') {
      $error = "Please fill all fields.";
    } else {

      /* CREATE */
      if ($page === 'create') {

        $stmt = $connect->prepare("
          INSERT INTO researchers (name, email, password, role, created_at)
          VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->execute([$name, $email, $password, $role]);

        $_SESSION['message'] = "Researcher created successfully.";
        header("Location: researchers.php");
        exit();
      }

      /* EDIT */
      if ($page === 'edit' && $id) {

        $stmt = $connect->prepare("
          UPDATE researchers
          SET name=?, email=?, password=?, role=?
          WHERE id=?
        ");

        $stmt->execute([$name, $email, $password, $role, $id]);

        $_SESSION['message'] = "Researcher updated successfully.";
        header("Location: researchers.php");
        exit();
      }
    }
  }

  /* ================= DELETE ================= */

  if ($page === 'delete' && $id) {

    $connect->prepare("DELETE FROM researchers WHERE id=?")
      ->execute([$id]);

    $_SESSION['message'] = "Researcher deleted successfully.";
    header("Location: researchers.php");
    exit();
  }

  /* ================= GET ONE ================= */

  if (($page === 'edit' || $page === 'show') && $id) {

    $stmt = $connect->prepare("SELECT * FROM researchers WHERE id=?");
    $stmt->execute([$id]);
    $researcher = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$researcher) {
      $_SESSION['message'] = "Researcher not found.";
      header("Location: researchers.php");
      exit();
    }
  }

  /* ================= GET ALL ================= */

  if ($page === 'All') {

    $researchers = $connect->query("
      SELECT * FROM researchers ORDER BY id DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
  }

?>

<style>

  body{
    background:
    linear-gradient(135deg,#0f172a,#111827,#020617);
    min-height:100vh;
    color:#fff;
    font-family:'Segoe UI',sans-serif;
  }

  .main-box{
    background:rgba(255,255,255,.05);
    border:1px solid rgba(255,255,255,.08);
    border-radius:28px;
    padding:30px;
    backdrop-filter:blur(15px);
    box-shadow:0 15px 40px rgba(0,0,0,.25);
  }

  .page-title{
    font-size:32px;
    font-weight:800;
    color:#fff;
    margin:0;
  }

  .page-title i{
    color:#14b8a6;
    margin-right:10px;
  }

  .alert{
    border:none;
    border-radius:14px;
    font-weight:600;
  }

  .table-box{
    background:#0f172a;
    border-radius:24px;
    overflow:hidden;
    border:1px solid rgba(255,255,255,.08);
    box-shadow:0 10px 30px rgba(0,0,0,.25);
  }

  .table{
    width:100%;
    margin:0 !important;
    color:#fff !important;
    background:#1e293b !important;
  }

  .table thead{
    background:
    linear-gradient(135deg,#14b8a6,#0f766e) !important;
  }

  .table thead th{
    color:#fff !important;
    border:none !important;
    padding:18px !important;
    font-size:15px;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.5px;
  }

  .table tbody{
    background:#1e293b !important;
  }

  .table tbody tr{
    background:#1e293b !important;
    transition:.3s;
  }

  .table tbody tr:nth-child(even){
    background:#273449 !important;
  }

  .table tbody tr:hover{
    background:#334155 !important;
  }

  .table tbody td{
    color:#f8fafc !important;
    padding:18px !important;
    border-color:rgba(255,255,255,.05) !important;
    background:transparent !important;
    font-size:15px;
    vertical-align:middle;
  }

  .table tbody td:first-child{
    color:#38bdf8 !important;
    font-weight:700;
  }

  .table tbody td:nth-child(2){
    color:#ffffff !important;
    font-weight:600;
  }

  .btn{
    border:none;
    border-radius:12px;
    transition:.3s;
    font-weight:600;
  }

  .btn:hover{
    transform:translateY(-2px);
  }

  .table .btn{
    width:38px;
    height:38px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    padding:0;
    margin-right:5px;
  }

  .btn-success{
    background:
    linear-gradient(135deg,#10b981,#059669);
  }

  .btn-primary{
    background:
    linear-gradient(135deg,#3b82f6,#2563eb);
  }

  .btn-danger{
    background:
    linear-gradient(135deg,#ef4444,#dc2626);
  }

  .btn-secondary{
    background:
    linear-gradient(135deg,#64748b,#475569);
  }

  .form-control{
    height:55px;
    border:none;
    border-radius:15px;
    background:rgba(255,255,255,.08);
    color:#fff;
    padding-left:15px;
  }

  .form-control:focus{
    background:rgba(255,255,255,.12);
    color:#fff;
    box-shadow:0 0 0 4px rgba(20,184,166,.20);
  }

  .form-control::placeholder{
    color:#cbd5e1;
  }

  label{
    color:#e2e8f0;
    font-weight:600;
    margin-bottom:10px;
  }

  .details-table{
    width:100%;
  }

  .details-table tr{
    border-bottom:1px solid rgba(255,255,255,.08);
  }

  .details-table th{
    width:220px;
    padding:18px;
    color:#14b8a6;
    background:#172554;
  }

  .details-table td{
    padding:18px;
    color:#fff;
    background:#1e293b;
  }

  @media(max-width:768px){

    .page-title{
      font-size:24px;
    }

    .main-box{
      padding:20px;
    }

    .table thead{
      display:none;
    }

    .table,
    .table tbody,
    .table tr,
    .table td{
      display:block;
      width:100%;
    }

    .table tr{
      margin-bottom:15px;
      border-radius:18px;
      overflow:hidden;
      background:#1e293b !important;
    }

    .table td{
      display:flex;
      justify-content:space-between;
      align-items:center;
      padding:16px !important;
      border-bottom:1px solid rgba(255,255,255,.05);
    }

    .table td::before{
      font-weight:700;
      color:#14b8a6;
    }

    .table td:nth-child(1)::before{
      content:"ID";
    }

    .table td:nth-child(2)::before{
      content:"Name";
    }

    .table td:nth-child(3)::before{
      content:"Email";
    }

    .table td:nth-child(4)::before{
      content:"Role";
    }

    .table td:nth-child(5)::before{
      content:"Action";
    }
  }

</style>

<div class="container py-5">

  <div class="main-box">

    <!-- MESSAGE -->
    <?php if (!empty($_SESSION['message'])): ?>
      <div class="alert alert-success text-center auto-hide mb-4">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center auto-hide mb-4">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- ALL -->
    <?php if ($page === 'All'): ?>

      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

        <h2 class="page-title">
          <i class="fa-solid fa-user"></i>
          Researchers Management
        </h2>

        <a href="?page=create" class="btn btn-success px-4 py-2">
          <i class="fa-solid fa-plus me-2"></i>
          Add Researcher
        </a>

      </div>

      <div class="table-box">

        <table class="table table-dark align-middle text-center">

          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>

            <?php foreach ($researchers as $r): ?>
              <tr>

                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= htmlspecialchars($r['role']) ?></td>

                <td>

                  <a href="?page=show&id=<?= $r['id'] ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-eye"></i>
                  </a>

                  <a href="?page=edit&id=<?= $r['id'] ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i>
                  </a>

                  <a href="?page=delete&id=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
                     onclick="return confirm('Delete this researcher?')">
                    <i class="fas fa-trash"></i>
                  </a>

                </td>

              </tr>
            <?php endforeach; ?>

          </tbody>

        </table>

      </div>

    <!-- CREATE / EDIT -->
    <?php elseif ($page === 'create' || $page === 'edit'): ?>

      <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="page-title">
          <i class="fa-solid fa-user"></i>
          <?= $page === 'create' ? 'Add Researcher' : 'Edit Researcher' ?>
        </h2>

        <a href="researchers.php" class="btn btn-secondary px-4 py-2">Back</a>

      </div>

      <div class="table-box p-4">

        <form method="post">

          <div class="row g-4">

            <div class="col-md-6">
              <label>Name</label>
              <input type="text" class="form-control" name="name"
                     value="<?= $researcher['name'] ?? '' ?>">
            </div>

            <div class="col-md-6">
              <label>Email</label>
              <input type="email" class="form-control" name="email"
                     value="<?= $researcher['email'] ?? '' ?>">
            </div>

            <div class="col-md-6">
              <label>Password</label>
              <input type="text" class="form-control" name="password"
                     value="<?= $researcher['password'] ?? '' ?>">
            </div>

            <div class="col-md-6">
              <label>Role</label>
              <input type="text" class="form-control" name="role"
                     value="<?= $researcher['role'] ?? '' ?>">
            </div>

          </div>

          <div class="mt-4">

            <button class="btn btn-primary px-4 py-2">
              <i class="fa-solid fa-floppy-disk me-2"></i>
              Save
            </button>

            <a href="researchers.php" class="btn btn-secondary px-4 py-2">
              Cancel
            </a>

          </div>

        </form>

      </div>

    <!-- SHOW (مهم) -->
    <?php elseif ($page === 'show'): ?>

      <div class="d-flex justify-content-between align-items-center mb-4">

        <h2 class="page-title">
          <i class="fa-solid fa-circle-info"></i>
          Researcher Details
        </h2>

        <a href="researchers.php" class="btn btn-secondary px-4 py-2">
          Back
        </a>

      </div>

      <div class="table-box p-4">

        <table class="table details-table table-borderless">

          <tr>
            <th>ID</th>
            <td><?= $researcher['id'] ?></td>
          </tr>

          <tr>
            <th>Name</th>
            <td><?= $researcher['name'] ?></td>
          </tr>

          <tr>
            <th>Email</th>
            <td><?= $researcher['email'] ?></td>
          </tr>

          <tr>
            <th>Role</th>
            <td><?= $researcher['role'] ?></td>
          </tr>

          <tr>
            <th>Created At</th>
            <td><?= $researcher['created_at'] ?></td>
          </tr>

        </table>

      </div>

    <?php endif; ?>

  </div>

</div>

<script>

  setTimeout(() => {

    document
    .querySelectorAll('.auto-hide')
    .forEach(el => {

      el.style.display = 'none';

    });

  }, 3000);

</script>

<?php
} else {
  $_SESSION['message_login'] = "Login First";
  header("Location: ../login.php");
  exit();
}

include('includes/temp/footer.php');
?>