<?php
session_start();
// include "includes/db/db.php";
include "includes/temp/init.php";
include "includes/temp/navbar.php";

/* ================= SECURITY ================= */
if (!isset($_SESSION['admin_login'])) {
  header("Location: login.php");
  exit();
}

/* ================= INPUTS ================= */
$search = $_GET["search"] ?? "";
$disease = $_GET["disease"] ?? "";

/* ================= SIMPLE AI ================= */
function predict($bp, $sugar, $bmi)
{
  $score = 0;

  if ($bp < 120) $score += 10;
  elseif ($bp < 140) $score += 25;
  elseif ($bp < 160) $score += 40;
  else $score += 60;

  if ($sugar < 100) $score += 10;
  elseif ($sugar < 150) $score += 25;
  elseif ($sugar < 200) $score += 40;
  else $score += 60;

  if ($bmi < 25) $score += 10;
  elseif ($bmi < 30) $score += 25;
  elseif ($bmi < 35) $score += 40;
  else $score += 60;

  if ($score > 120) return "Critical";
  if ($score > 80) return "High";
  if ($score > 40) return "Medium";
  return "Low";
}

/* ================= QUERY ================= */
$sql = "SELECT * FROM patients WHERE 1=1";
$params = [];

if ($search != "") {
  $sql .= " AND name LIKE :search";
  $params["search"] = "%$search%";
}

if ($disease != "") {
  $sql .= " AND disease_type = :disease";
  $params["disease"] = $disease;
}

$stmt = $connect->prepare($sql);
$stmt->execute($params);
$res = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ================= DATA ================= */
$rows = [];
$bpArr = [];
$sugarArr = [];

$low = 0;
$medium = 0;
$high = 0;
$critical = 0;

foreach ($res as $r) {

  $bp = (float)$r["blood_pressure"];
  $sugar = (float)$r["sugar_level"];
  $bmi = (float)$r["bmi"];

  $bpArr[] = $bp;
  $sugarArr[] = $sugar;

  $level = predict($bp, $sugar, $bmi);

  if ($level == "Low") $low++;
  elseif ($level == "Medium") $medium++;
  elseif ($level == "High") $high++;
  else $critical++;

  $rows[] = $r;
}

/* ================= DISEASE ================= */
$diseaseData = [];
$dRes = $connect->query("SELECT disease_type, COUNT(*) c FROM patients GROUP BY disease_type");

while ($d = $dRes->fetch(PDO::FETCH_ASSOC)) {
  $diseaseData[$d["disease_type"]] = $d["c"];
}
?>

<!-- <!DOCTYPE html>
<html>

<head>

  <title>Medical Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 -->

  <style>
    body {
      background:
        linear-gradient(135deg, #0f172a, #111827, #020617);
      min-height: 100vh;
      color: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

    .main-box {
      background: rgba(255, 255, 255, .05);
      border: 1px solid rgba(255, 255, 255, .08);
      border-radius: 28px;
      padding: 30px;
      backdrop-filter: blur(15px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, .25);
    }

    .page-title {
      font-size: 32px;
      font-weight: 800;
      color: #fff;
      margin: 0;
    }

    .page-title i {
      color: #14b8a6;
      margin-right: 10px;
    }

    .alert {
      border: none;
      border-radius: 14px;
      font-weight: 600;
    }

    .table-box {
      background: #0f172a;
      border-radius: 24px;
      overflow: hidden;
      border: 1px solid rgba(255, 255, 255, .08);
      box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
    }

    .table {
      width: 100%;
      margin: 0 !important;
      color: #fff !important;
      background: #1e293b !important;
    }

    .table thead {
      background:
        linear-gradient(135deg, #14b8a6, #0f766e) !important;
    }

    .table thead th {
      color: #fff !important;
      border: none !important;
      padding: 18px !important;
      font-size: 15px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
    }

    .table tbody {
      background: #1e293b !important;
    }

    .table tbody tr {
      background: #1e293b !important;
      transition: .3s;
    }

    .table tbody tr:nth-child(even) {
      background: #273449 !important;
    }

    .table tbody tr:hover {
      background: #334155 !important;
    }

    .table tbody td {
      color: #f8fafc !important;
      padding: 18px !important;
      border-color: rgba(255, 255, 255, .05) !important;
      background: transparent !important;
      font-size: 15px;
      vertical-align: middle;
    }

    .table tbody td:first-child {
      color: #38bdf8 !important;
      font-weight: 700;
    }

    .table tbody td:nth-child(2) {
      color: #ffffff !important;
      font-weight: 600;
    }

    .btn {
      border: none;
      border-radius: 12px;
      transition: .3s;
      font-weight: 600;
    }

    .btn:hover {
      transform: translateY(-2px);
    }

    .table .btn {
      width: 38px;
      height: 38px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      margin-right: 5px;
    }

    .btn-success {
      background:
        linear-gradient(135deg, #10b981, #059669);
    }

    .btn-primary {
      background:
        linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .btn-danger {
      background:
        linear-gradient(135deg, #ef4444, #dc2626);
    }

    .btn-secondary {
      background:
        linear-gradient(135deg, #64748b, #475569);
    }

    .form-control {
      height: 55px;
      border: none;
      border-radius: 15px;
      background: rgba(255, 255, 255, .08);
      color: #fff;
      padding-left: 15px;
    }

    .form-control:focus {
      background: rgba(255, 255, 255, .12);
      color: #fff;
      box-shadow: 0 0 0 4px rgba(20, 184, 166, .20);
    }

    .form-control::placeholder {
      color: #cbd5e1;
    }

    label {
      color: #e2e8f0;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .details-table {
      width: 100%;
    }

    .details-table tr {
      border-bottom: 1px solid rgba(255, 255, 255, .08);
    }

    .details-table th {
      width: 220px;
      padding: 18px;
      color: #14b8a6;
      background: #172554;
    }

    .details-table td {
      padding: 18px;
      color: #fff;
      background: #1e293b;
    }

    .badge-custom {
      padding: 6px 12px;
      border-radius: 20px;
      color: white;
      font-weight: 600;
    }

    .card-box {
      padding: 20px;
      border-radius: 20px;
      color: white;
      text-align: center;
    }

    .c-low {
      background: #10b981;
    }

    .c-medium {
      background: #f59e0b;
    }

    .c-high {
      background: #3b82f6;
    }

    .c-critical {
      background: #ef4444;
    }

    .header {
      background: linear-gradient(135deg, #0f766e, #14b8a6);
      color: white;
      padding: 18px;
      border-radius: 18px;
    }

    .chart-box {
      background: rgba(255, 255, 255, .05);
      border: 1px solid rgba(255, 255, 255, .08);
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, .25);
      height: 380px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    canvas {
      width: 100% !important;
      height: 320px !important;
    }

    @media(max-width:768px) {

      .page-title {
        font-size: 24px;
      }

      .main-box {
        padding: 20px;
      }

      .table thead {
        display: none;
      }

      .table,
      .table tbody,
      .table tr,
      .table td {
        display: block;
        width: 100%;
      }

      .table tr {
        margin-bottom: 15px;
        border-radius: 18px;
        overflow: hidden;
        background: #1e293b !important;
      }

      .table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px !important;
        border-bottom: 1px solid rgba(255, 255, 255, .05);
      }

      .table td::before {
        font-weight: 700;
        color: #14b8a6;
      }
    }
  </style>

<!-- </head>

<body> -->

  <div class="container my-5">
    <div class="main-box">
      <div class="row justify-content-center">
        <div class="col-md-12">

          <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="page-title">
              <i class="fa-solid fa-chart-line"></i>
              Medical Dashboard
            </h2>
          </div>

          <!-- CARDS -->
          <div class="row mt-3 g-3">

            <div class="col-md-3">
              <div class="card-box c-low">
                <i class="fa fa-leaf fa-2x mb-2"></i>
                <div>Low</div>
                <h2><?= $low ?></h2>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card-box c-medium">
                <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                <div>Medium</div>
                <h2><?= $medium ?></h2>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card-box c-high">
                <i class="fa fa-chart-line fa-2x mb-2"></i>
                <div>High</div>
                <h2><?= $high ?></h2>
              </div>
            </div>

            <div class="col-md-3">
              <div class="card-box c-critical">
                <i class="fa fa-skull-crossbones fa-2x mb-2"></i>
                <div>Critical</div>
                <h2><?= $critical ?></h2>
              </div>
            </div>

          </div>

          <!-- ✅ SEARCH ADDED HERE (ONLY CHANGE) -->
          <div class="table-box mt-3">

            <form class="row g-2">

              <div class="col-md-6">
                <input name="search" class="form-control" value="<?= htmlspecialchars($search) ?>" placeholder="Search patient...">
              </div>

              <div class="col-md-4">
                <select name="disease" class="form-control ">
                  <option value="" <?= $disease === '' ? 'selected' : '' ?> class="text-light">All</option>
                  <option value="Diabetes" <?= $disease === 'Diabetes' ? 'selected' : '' ?> class="text-dark">Diabetes</option>
                  <option value="Hypertension" <?= $disease === 'Hypertension' ? 'selected' : '' ?> class="text-dark">Hypertension</option>
                  <option value="Heart Disease" <?= $disease === 'Heart Disease' ? 'selected' : '' ?> class="text-dark">Heart Disease</option>
                  <option value="Asthma" <?= $disease === 'Asthma' ? 'selected' : '' ?> class="text-dark">Asthma</option>
                </select>
              </div>

              <div class="col-md-2">
                <button class="btn btn-success px-4 py-3 w-100">Filter</button>
              </div>

            </form>

          </div>

          <!-- TABLE -->
          <div class="table-box">

            <table class="table table-dark align-middle text-center">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Disease</th>
                  <th>BP</th>
                  <th>Sugar</th>
                  <th>BMI</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>

                <?php foreach ($rows as $r): ?>
                  <tr>
                    <td><?= htmlspecialchars($r["name"]) ?></td>
                    <td><?= $r["disease_type"] ?></td>
                    <td><?= $r["blood_pressure"] ?></td>
                    <td><?= $r["sugar_level"] ?></td>
                    <td><?= $r["bmi"] ?></td>

                    <?php
                    $level = predict($r["blood_pressure"], $r["sugar_level"], $r["bmi"]);
                    $color = match ($level) {
                      "Low" => "#10b981",
                      "Medium" => "#f59e0b",
                      "High" => "#3b82f6",
                      "Critical" => "#ef4444"
                    };
                    ?>

                    <td>
                      <span class="badge-custom" style="background:<?= $color ?>">
                        <?= $level ?>
                      </span>
                    </td>

                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

          </div>

          <!-- CHARTS (UNCHANGED) -->
          <div class="row g-4 mt-3 align-items-stretch">

            <div class="col-md-4 d-flex">
              <div class="chart-box w-100"><canvas id="pie"></canvas></div>
            </div>

            <div class="col-md-4 d-flex">
              <div class="chart-box w-100"><canvas id="bar"></canvas></div>
            </div>

            <div class="col-md-4 d-flex">
              <div class="chart-box w-100"><canvas id="disease"></canvas></div>
            </div>

          </div>

        </div>

      </div>

    </div>

    <script>
      new Chart(document.getElementById("pie"), {
        type: "pie",
        data: {
          labels: ["Low", "Medium", "High", "Critical"],
          datasets: [{
            data: [<?= $low ?>, <?= $medium ?>, <?= $high ?>, <?= $critical ?>],
            backgroundColor: ["#10b981", "#f59e0b", "#3b82f6", "#ef4444"]
          }]
        }
      });

      new Chart(document.getElementById("bar"), {
        type: "bar",
        data: {
          labels: [
            <?php foreach ($rows as $r): ?> "<?= htmlspecialchars($r['name']) ?>",
            <?php endforeach; ?>
          ],
          datasets: [{
              label: "BP",
              data: [
                <?php foreach ($rows as $r): ?>
                  <?= $r["blood_pressure"] ?>,
                <?php endforeach; ?>
              ],
              backgroundColor: "#3b82f6"
            },
            {
              label: "Sugar",
              data: [
                <?php foreach ($rows as $r): ?>
                  <?= $r["sugar_level"] ?>,
                <?php endforeach; ?>
              ],
              backgroundColor: "#ef4444"
            }
          ]
        }
      });

      new Chart(document.getElementById("disease"), {
        type: "pie",
        data: {
          labels: <?= json_encode(array_keys($diseaseData)) ?>,
          datasets: [{
            data: <?= json_encode(array_values($diseaseData)) ?>,
            backgroundColor: ["#3b82f6", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6"]
          }]
        }
      });
    </script>

<!-- </body>

</html> -->

<?php
include ("includes/temp/footer.php")
?>