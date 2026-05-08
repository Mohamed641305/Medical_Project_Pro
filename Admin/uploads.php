<?php
session_start();

if (isset($_SESSION['admin_login'])) {

  include('includes/temp/init.php');
  include('includes/temp/navbar.php');

  $page = $_GET['page'] ?? 'All';
  $id   = isset($_GET['id']) ? (int)$_GET['id'] : null;
  $error = '';

  /* ================= CSV IMPORT ================= */
  if (isset($_POST['upload_csv'])) {

    if (!empty($_FILES['csv_file']['tmp_name'])) {

      $file = fopen($_FILES['csv_file']['tmp_name'], "r");

      $row = 0;

      while (($data = fgetcsv($file, 1000, ",")) !== FALSE) {

        $row++;

        // تجاهل أول سطر (Header)
        if ($row == 1 && preg_match('/name/i', $data[0])) {
          continue;
        }

        // لازم على الأقل 5 أعمدة
        if (count($data) < 5) {
          continue;
        }

        $name           = trim($data[0]);
        $disease_type   = trim($data[1]);
        $blood_pressure = trim($data[2]);
        $sugar_level    = trim($data[3]);
        $bmi            = trim($data[4]);

        // age + gender قيم افتراضية لأنهم مش موجودين في ملفك
        $age    = 0;
        $gender = 'Unknown';

        if ($name != '' && $disease_type != '') {

          $stmt = $connect->prepare("
            INSERT INTO patients 
            (name, age, gender, disease_type, blood_pressure, sugar_level, bmi, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
          ");

          $stmt->execute([
            $name,
            $age,
            $gender,
            $disease_type,
            $blood_pressure,
            $sugar_level,
            $bmi,
            date('Y-m-d H:i:s')
          ]);
        }
      }

      fclose($file);

      // حفظ اسم الملف فقط في uploads
      $uploadedFileName = basename($_FILES['csv_file']['name']);
      $uploadedBy = $_SESSION['admin_login'] ?? 'Admin';

      $stmt = $connect->prepare("
        INSERT INTO uploads (file_name, uploaded_by, uploaded_at)
        VALUES (?, ?, ?)
      ");

      $stmt->execute([
        $uploadedFileName,
        $uploadedBy,
        date('Y-m-d H:i:s')
      ]);

      $_SESSION['message'] = "CSV Imported Successfully";
      header("Location: uploads.php");
      exit;
    }
  }

  /* ================= CREATE + EDIT ================= */
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['upload_csv'])) {

    $name           = trim($_POST['name'] ?? '');
    $age            = trim($_POST['age'] ?? '');
    $gender         = trim($_POST['gender'] ?? '');
    $disease_type   = trim($_POST['disease_type'] ?? '');
    $blood_pressure = trim($_POST['blood_pressure'] ?? '');
    $sugar_level    = trim($_POST['sugar_level'] ?? '');
    $bmi            = trim($_POST['bmi'] ?? '');

    if (empty($name) || empty($age) || empty($gender) || empty($disease_type) || empty($blood_pressure) || empty($sugar_level) || empty($bmi)) {
      $error = 'Please fill all fields.';
    } else {

      if ($page === 'create') {
        $stmt = $connect->prepare(
          "INSERT INTO patients (name, age, gender, disease_type, blood_pressure, sugar_level, bmi, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
          $name,
          $age,
          $gender,
          $disease_type,
          $blood_pressure,
          $sugar_level,
          $bmi,
          date('Y-m-d H:i:s')
        ]);

        $_SESSION['message'] = 'Patient created successfully.';
        header('Location: uploads.php');
        exit;
      }

      if ($page === 'edit' && $id) {
        $stmt = $connect->prepare(
          "UPDATE patients SET name = ?, age = ?, gender = ?, disease_type = ?, blood_pressure = ?, sugar_level = ?, bmi = ? WHERE id = ?"
        );

        $stmt->execute([
          $name,
          $age,
          $gender,
          $disease_type,
          $blood_pressure,
          $sugar_level,
          $bmi,
          $id
        ]);

        $_SESSION['message'] = 'Patient updated successfully.';
        header('Location: uploads.php');
        exit;
      }
    }
  }

  /* ================= DELETE ================= */
  if ($page === 'delete' && $id) {
    $stmt = $connect->prepare("DELETE FROM patients WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = 'Patient deleted successfully.';
    header('Location: uploads.php');
    exit;
  }

  /* ================= DELETE FILE ================= */
  if ($page === 'delete_file' && $id) {
    $stmt = $connect->prepare("DELETE FROM uploads WHERE id = ?");
    $stmt->execute([$id]);

    $_SESSION['message'] = 'File deleted successfully.';
    header('Location: uploads.php');
    exit;
  }

  /* ================= GET SINGLE ================= */
  $upload = null;

  if (($page === 'edit' || $page === 'show') && $id) {
    $stmt = $connect->prepare("SELECT * FROM patients WHERE id = ?");
    $stmt->execute([$id]);
    $upload = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$upload) {
      $_SESSION['message'] = 'Patient not found.';
      header('Location: uploads.php');
      exit;
    }
  }

  /* ================= GET ALL ================= */
  if ($page === 'All') {
    $uploads = $connect->query("SELECT * FROM uploads ORDER BY id DESC")->fetchAll();
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

    .card-box {
      padding: 20px;
      border-radius: 20px;
      color: white;
      text-align: center;
    }

    .c-low { background: #10b981; }
    .c-medium { background: #f59e0b; }
    .c-high { background: #3b82f6; }
    .c-critical { background: #ef4444; }

    .header {
      background: linear-gradient(135deg, #0f766e, #14b8a6);
      color: white;
      padding: 18px;
      border-radius: 18px;
    }

    .chart-box {
      background: white;
      padding: 20px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      height: 380px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    canvas {
      width: 100% !important;
      height: 320px !important;
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
    }

  </style>

  <div class="container my-5">
    <div class="main-box">
    <div class="row justify-content-center">
      <div class="col-md-12">

        <!-- MESSAGE -->
        <?php if (!empty($_SESSION['message'])): ?>
          <div class="alert alert-success text-center py-2 my-3 auto-hide">
            <?= htmlspecialchars($_SESSION['message']) ?>
          </div>
          <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- ERROR -->
        <?php if (!empty($error)): ?>
          <div class="alert alert-danger text-center py-2 my-3 auto-hide">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <!-- ================= ALL ================= -->
        <?php if ($page === 'All'): ?>

          <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="page-title">
              <i class="fa-solid fa-file-upload"></i>
              Patients Import
            </h2>

            <div class="d-flex flex-wrap gap-2">
                      <a href="csv.php" class="btn btn-secondary px-4 py-2">
                  <i class="fas fa-file-csv me-2"></i>
                  CSV
                </a>

                <button class="btn btn-primary px-4 py-2" data-bs-toggle="collapse" data-bs-target="#csvBox">
                  <i class="fas fa-upload me-2"></i>
                  Import Patients
                </button>

                <a href="?page=create" class="btn btn-success px-4 py-2">
                  <i class="fas fa-plus me-2"></i>
                  Add Patient
                </a>
            </div>
          </div>

          <!-- CSV BOX -->
          <div id="csvBox" class="collapse mb-3">
            <div class="table-box p-3">
              <form method="POST" enctype="multipart/form-data">
                <input type="file" name="csv_file" class="form-control mb-2" required>
                <button name="upload_csv" class="btn btn-primary btn-sm">
                  <i class="fas fa-file-upload"></i> Upload CSV
                </button>
              </form>
            </div>
          </div>

          <div class="table-box">
            <table class="table table-dark align-middle text-center">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>File Name</th>
                  <th>Uploaded By</th>
                  <th>Uploaded At</th>
                  <th>Actions</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($uploads as $u): ?>
                  <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['file_name']) ?></td>
                    <td><?= htmlspecialchars($u['uploaded_by']) ?></td>
                    <td><?= $u['uploaded_at'] ?></td>
                    <td>
                      <a href="?page=delete_file&id=<?= $u['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this file?')">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- ================= CREATE (FIXED) ================= -->
        <?php elseif ($page === 'create'): ?>

          <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="page-title">
              <i class="fa-solid fa-plus-circle"></i>
              Add Patient
            </h2>
          </div>

          <div class="table-box p-4">
            <form method="post">

              <div class="row g-3">
                <div class="col-md-6">
                  <label>Name</label>
                  <input type="text" name="name" class="form-control" placeholder="Patient Name" required>
                </div>

                <div class="col-md-6">
                  <label>Age</label>
                  <input type="number" name="age" class="form-control" placeholder="Age" required>
                </div>

                <div class="col-md-6">
                  <label>Gender</label>
                  <select name="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label>Disease Type</label>
                  <input type="text" name="disease_type" class="form-control" placeholder="Disease Type" required>
                </div>

                <div class="col-md-6">
                  <label>Blood Pressure</label>
                  <input type="text" name="blood_pressure" class="form-control" placeholder="Blood Pressure" required>
                </div>

                <div class="col-md-6">
                  <label>Sugar Level</label>
                  <input type="text" name="sugar_level" class="form-control" placeholder="Sugar Level" required>
                </div>

                <div class="col-md-6">
                  <label>BMI</label>
                  <input type="text" name="bmi" class="form-control" placeholder="BMI" required>
                </div>
              </div>

              <div class="mt-4">
                <button class="btn btn-primary px-4 py-2">Create</button>
                <a href="uploads.php" class="btn btn-secondary px-4 py-2">Cancel</a>
              </div>
            </form>
          </div>

          <!-- ================= EDIT ================= -->
        <?php elseif ($page === 'edit'): ?>

          <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="page-title">
              <i class="fa-solid fa-edit"></i>
              Edit Patient
            </h2>
          </div>

          <div class="table-box p-4">
            <form method="post">

              <div class="row g-3">
                <div class="col-md-6">
                  <label>Name</label>
                  <input type="text" name="name" class="form-control"
                    value="<?= htmlspecialchars($upload['name']) ?>" required>
                </div>

                <div class="col-md-6">
                  <label>Age</label>
                  <input type="number" name="age" class="form-control"
                    value="<?= htmlspecialchars($upload['age']) ?>" required>
                </div>

                <div class="col-md-6">
                  <label>Gender</label>
                  <select name="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="Male" <?= $upload['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= $upload['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label>Disease Type</label>
                  <input type="text" name="disease_type" class="form-control"
                    value="<?= htmlspecialchars($upload['disease_type']) ?>" required>
                </div>

                <div class="col-md-6">
                  <label>Blood Pressure</label>
                  <input type="text" name="blood_pressure" class="form-control"
                    value="<?= htmlspecialchars($upload['blood_pressure']) ?>" required>
                </div>

                <div class="col-md-6">
                  <label>Sugar Level</label>
                  <input type="text" name="sugar_level" class="form-control"
                    value="<?= htmlspecialchars($upload['sugar_level']) ?>" required>
                </div>

                <div class="col-md-6">
                  <label>BMI</label>
                  <input type="text" name="bmi" class="form-control"
                    value="<?= htmlspecialchars($upload['bmi']) ?>" required>
                </div>
              </div>

              <div class="mt-4">
                <button class="btn btn-primary px-4 py-2">Update</button>
                <a href="uploads.php" class="btn btn-secondary px-4 py-2">Cancel</a>
              </div>
            </form>
          </div>

          <!-- ================= SHOW ================= -->
        <?php elseif ($page === 'show'): ?>

          <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="page-title">
              <i class="fa-solid fa-eye"></i>
              Patient Details
            </h2>

            <a href="uploads.php" class="btn btn-secondary px-4 py-2">
              Back
            </a>
          </div>

          <div class="table-box p-4">
            <table class="table details-table table-borderless">
              <tr>
                <th>ID</th>
                <td><?= $upload['id'] ?></td>
              </tr>
              <tr>
                <th>Name</th>
                <td><?= htmlspecialchars($upload['name']) ?></td>
              </tr>
              <tr>
                <th>Age</th>
                <td><?= htmlspecialchars($upload['age']) ?></td>
              </tr>
              <tr>
                <th>Gender</th>
                <td><?= htmlspecialchars($upload['gender']) ?></td>
              </tr>
              <tr>
                <th>Disease Type</th>
                <td><?= htmlspecialchars($upload['disease_type']) ?></td>
              </tr>
              <tr>
                <th>Blood Pressure</th>
                <td><?= htmlspecialchars($upload['blood_pressure']) ?></td>
              </tr>
              <tr>
                <th>Sugar Level</th>
                <td><?= htmlspecialchars($upload['sugar_level']) ?></td>
              </tr>
              <tr>
                <th>BMI</th>
                <td><?= htmlspecialchars($upload['bmi']) ?></td>
              </tr>
            </table>
          </div>

        <?php endif; ?>

      </div>
    </div>
  </div>
  </div>

  <script>
    setTimeout(() => {
      document.querySelectorAll('.auto-hide').forEach(el => el.style.display = 'none');
    }, 3000);
  </script>

<?php
  include('includes/temp/footer.php');
} else {
  $_SESSION['message_login'] = "Login First";
  header("Location: ../login.php");
  exit();
}
?>