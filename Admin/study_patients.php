<?php
session_start();
if (isset($_SESSION['admin_login'])) {

  include('includes/temp/init.php');
  include('includes/temp/navbar.php');


  $page = $_GET['page'] ?? 'All';
  $id   = isset($_GET['id']) ? (int)$_GET['id'] : null;
  $error = '';

  $record = null;
  $records = [];

  /* ================= CREATE + EDIT ================= */
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $patient_id = trim($_POST['patient_id'] ?? '');
    $study_id = trim($_POST['study_id'] ?? '');

    if ($patient_id == '' || $study_id == '') {
      $error = "Please fill all fields.";
    } else {

      if ($page === 'create') {
        $connect->prepare("
        INSERT INTO study_patients(patient_id, study_id)
        VALUES (?, ?)
      ")->execute([$patient_id, $study_id]);

        $_SESSION['message'] = "Created successfully.";
        header("Location: study_patients.php");
        exit();
      }

      if ($page === 'edit' && $id) {
        $connect->prepare("
        UPDATE study_patients 
        SET patient_id=?, study_id=?
        WHERE id=?
      ")->execute([$patient_id, $study_id, $id]);

        $_SESSION['message'] = "Updated successfully.";
        header("Location: study_patients.php");
        exit();
      }
    }
  }

  /* ================= DELETE ================= */
  if ($page === 'delete' && $id) {
    $connect->prepare("DELETE FROM study_patients WHERE id=?")->execute([$id]);

    $_SESSION['message'] = "Deleted successfully.";
    header("Location: study_patients.php");
    exit();
  }

  /* ================= GET ONE ================= */
  if (($page === 'edit' || $page === 'show') && $id) {
    $stmt = $connect->prepare("SELECT * FROM study_patients WHERE id=?");
    $stmt->execute([$id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
      $_SESSION['message'] = "Not found.";
      header("Location: study_patients.php");
      exit();
    }
  }

  /* ================= GET ALL ================= */
  if ($page === 'All') {
    $records = $connect->query("SELECT * FROM study_patients")->fetchAll(PDO::FETCH_ASSOC);
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
        content:"Patient ID";
      }

      .table td:nth-child(3)::before{
        content:"Study ID";
      }

      .table td:nth-child(4)::before{
        content:"Action";
      }
    }

  </style>

  <div class="container py-5">
    <div class="main-box">

    <!-- MESSAGE -->
    <?php if (!empty($_SESSION['message'])): ?>
      <div class="alert alert-success text-center py-2 my-3 auto-hide">
        <?= $_SESSION['message'];
        unset($_SESSION['message']); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center py-2 my-3 auto-hide">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- ================= ALL ================= -->
    <?php if ($page === 'All'): ?>

      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="page-title">
          <i class="fa-solid fa-procedures"></i>
          Study Patients
        </h2>

        <a href="?page=create" class="btn btn-success px-4 py-2">
          <i class="fa-solid fa-plus me-2"></i>
          Add Record
        </a>
      </div>

      <div class="table-box">
        <table class="table table-dark align-middle text-center">
          <thead>
            <tr>
              <th>ID</th>
              <th>Patient ID</th>
              <th>Study ID</th>
              <th>Action</th>
            </tr>
          </thead>

          <tbody>
            <?php foreach ($records as $r): ?>
              <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['patient_id']) ?></td>
                <td><?= htmlspecialchars($r['study_id']) ?></td>
                <td>
                  <a href="?page=show&id=<?= $r['id'] ?>" class="btn btn-sm btn-success"><i class="fas fa-eye"></i></a>
                  <a href="?page=edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                  <a href="?page=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-danger"
                    onclick="return confirm('Delete?')"><i class="fas fa-trash"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ================= CREATE / EDIT ================= -->
    <?php elseif ($page === 'create' || $page === 'edit'): ?>

      <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <h2 class="page-title">
          <i class="fa-solid fa-note-sticky"></i>
          <?= $page === 'create' ? 'Add Record' : 'Edit Record' ?>
        </h2>
      </div>

      <div class="table-box p-4">
        <form method="post">
          <div class="row g-3">

            <div class="col-md-6">
              <label>Patient ID</label>
              <input class="form-control" name="patient_id"
                value="<?= $record['patient_id'] ?? '' ?>">
            </div>

            <div class="col-md-6">
              <label>Study ID</label>
              <input class="form-control" name="study_id"
                value="<?= $record['study_id'] ?? '' ?>">
            </div>

          </div>

          <div class="mt-4">
            <button class="btn btn-primary px-4 py-2">Save</button>
            <a href="study_patients.php" class="btn btn-secondary px-4 py-2">Cancel</a>
          </div>

        </form>
      </div>

      <!-- ================= SHOW ================= -->
    <?php elseif ($page === 'show'): ?>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="page-title">
          <i class="fa-solid fa-circle-info"></i>
          Details
        </h2>

        <a href="study_patients.php" class="btn btn-secondary px-4 py-2">
          Back
        </a>
      </div>

      <div class="table-box p-4">
        <table class="table details-table table-borderless">
          <tr>
            <th>ID</th>
            <td><?= $record['id'] ?></td>
          </tr>
          <tr>
            <th>Patient ID</th>
            <td><?= $record['patient_id'] ?></td>
          </tr>
          <tr>
            <th>Study ID</th>
            <td><?= $record['study_id'] ?></td>
          </tr>
        </table>
      </div>

    <?php endif; ?>

  </div>
  </div>

  <script>
    setTimeout(() => {
      document.querySelectorAll('.auto-hide').forEach(el => el.style.display = 'none');
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