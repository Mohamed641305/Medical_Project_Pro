<?php
session_start();

if (isset($_SESSION['admin_login'])) {

  include('includes/temp/init.php');
  include('includes/temp/navbar.php');

  $page = $_GET['page'] ?? 'All';
  $id   = isset($_GET['id']) ? (int)$_GET['id'] : null;
  $error = '';

  $study = null;
  $studies = [];

  /* ================= CREATE / EDIT ================= */

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $created_by  = trim($_POST['created_by'] ?? '');

    if ($title == '' || $description == '' || $created_by == '') {
      $error = "Please fill all fields.";
    } else {

      if ($page === 'create') {

        $stmt = $connect->prepare("
          INSERT INTO studies (title, description, created_by, created_at)
          VALUES (?, ?, ?, NOW())
        ");

        $stmt->execute([$title, $description, $created_by]);

        $_SESSION['message'] = "Study created successfully.";
        header("Location: studies.php");
        exit();
      }

      if ($page === 'edit' && $id) {

        $stmt = $connect->prepare("
          UPDATE studies
          SET title=?, description=?, created_by=?
          WHERE id=?
        ");

        $stmt->execute([$title, $description, $created_by, $id]);

        $_SESSION['message'] = "Study updated successfully.";
        header("Location: studies.php");
        exit();
      }
    }
  }

  /* ================= DELETE ================= */

  if ($page === 'delete' && $id) {

    $connect->prepare("DELETE FROM studies WHERE id=?")
      ->execute([$id]);

    $_SESSION['message'] = "Study deleted successfully.";
    header("Location: studies.php");
    exit();
  }

  /* ================= GET ONE ================= */

  if (($page === 'edit' || $page === 'show') && $id) {

    $stmt = $connect->prepare("SELECT * FROM studies WHERE id=?");
    $stmt->execute([$id]);
    $study = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$study) {
      $_SESSION['message'] = "Study not found.";
      header("Location: studies.php");
      exit();
    }
  }

  /* ================= GET ALL ================= */

  if ($page === 'All') {
    $studies = $connect->query("
      SELECT * FROM studies ORDER BY id DESC
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

    .table td:nth-child(1)::before{content:"ID";}
    .table td:nth-child(2)::before{content:"Title";}
    .table td:nth-child(3)::before{content:"Description";}
    .table td:nth-child(4)::before{content:"Created By";}
    .table td:nth-child(5)::before{content:"Action";}
  }

</style>

<div class="container py-5">

<div class="main-box">

<!-- MESSAGE -->
<?php if (!empty($_SESSION['message'])): ?>
<div class="alert alert-success text-center auto-hide">
<?= $_SESSION['message']; unset($_SESSION['message']); ?>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div class="alert alert-danger text-center auto-hide">
<?= $error ?>
</div>
<?php endif; ?>

<!-- ALL -->
<?php if ($page === 'All'): ?>

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

    <h2 class="page-title">
      <i class="fa-solid fa-flask"></i>
      Studies Management
    </h2>

    <a href="?page=create" class="btn btn-success px-4 py-2">
      <i class="fa-solid fa-plus me-2"></i>
      Add Study
    </a>

  </div>

  <div class="table-box">

    <table class="table table-dark align-middle text-center">

      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Description</th>
          <th>Created By</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($studies as $s): ?>
          <tr>
            <td><?= $s['id'] ?></td>
            <td><?= $s['title'] ?></td>
            <td><?= $s['description'] ?></td>
            <td><?= $s['created_by'] ?></td>
            <td>
              <a href="?page=show&id=<?= $s['id'] ?>" class="btn btn-success btn-sm"><i class="fa fa-eye"></i></a>
              <a href="?page=edit&id=<?= $s['id'] ?>" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
              <a href="?page=delete&id=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete?')"><i class="fa fa-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>

    </table>
  </div>

<!-- FORM -->
<?php elseif ($page === 'create' || $page === 'edit'): ?>

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

    <h2 class="page-title">
      <i class="fa-solid fa-flask"></i>
      <?= $page === 'create' ? 'Add Study' : 'Edit Study' ?>
    </h2>

  </div>

  <div class="table-box p-4">

    <form method="post">

      <div class="row g-3">

        <div class="col-md-6">
          <label>Title</label>
          <input class="form-control" name="title" value="<?= htmlspecialchars($study['title'] ?? '') ?>" placeholder="Study title">
        </div>

        <div class="col-md-6">
          <label>Created By</label>
          <input class="form-control" name="created_by" value="<?= htmlspecialchars($study['created_by'] ?? '') ?>" placeholder="Researcher or team">
        </div>

        <div class="col-md-12">
          <label>Description</label>
          <textarea class="form-control" name="description" rows="5" placeholder="Study description"><?= htmlspecialchars($study['description'] ?? '') ?></textarea>
        </div>

      </div>

      <div class="mt-4">
        <button class="btn btn-primary px-4 py-2">Save</button>
        <a href="studies.php" class="btn btn-secondary px-4 py-2">Cancel</a>
      </div>

    </form>
  </div>

<!-- SHOW -->
<?php elseif ($page === 'show'): ?>

  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">

    <h2 class="page-title">
      <i class="fa-solid fa-eye"></i>
      Study Details
    </h2>

    <a href="studies.php" class="btn btn-secondary px-4 py-2">
      Back
    </a>

  </div>

  <div class="table-box p-4">

    <table class="table details-table table-borderless">
      <tr>
        <th>ID</th>
        <td><?= $study['id'] ?></td>
      </tr>
      <tr>
        <th>Title</th>
        <td><?= $study['title'] ?></td>
      </tr>
      <tr>
        <th>Description</th>
        <td><?= $study['description'] ?></td>
      </tr>
      <tr>
        <th>Created By</th>
        <td><?= $study['created_by'] ?></td>
      </tr>
      <tr>
        <th>Date</th>
        <td><?= $study['created_at'] ?></td>
      </tr>
    </table>

  </div>

<?php endif; ?>

</div>
</div>

<script>
setTimeout(()=>{
document.querySelectorAll('.auto-hide').forEach(e=>e.style.display='none');
},3000);
</script>

<?php
} else {
  header("Location: ../login.php");
  exit();
}
include('includes/temp/footer.php');
?>