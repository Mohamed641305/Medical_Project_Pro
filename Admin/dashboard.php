<?php
session_start();

if (isset($_SESSION['admin_login'])) {

  include('includes/temp/init.php');
  include('includes/temp/navbar.php');

  /* ========================= COUNTS ========================= */

  $patientCount =
  $connect->query(
    "SELECT COUNT(*) FROM patients"
  )->fetchColumn();

  $researchCount =
  $connect->query(
    "SELECT COUNT(*) FROM researchers"
  )->fetchColumn();

  $studyCount =
  $connect->query(
    "SELECT COUNT(*) FROM studies"
  )->fetchColumn();

  $studyPatientCount =
  $connect->query(
    "SELECT COUNT(*) FROM study_patients"
  )->fetchColumn();

  $uploadCount =
  $connect->query(
    "SELECT COUNT(*) FROM uploads"
  )->fetchColumn();

  /* ========================= TOTAL ========================= */

  $total =
    $patientCount +
    $researchCount +
    $studyCount +
    $studyPatientCount +
    $uploadCount;

  /* ========================= PERCENTAGES ========================= */

  $pPatients =
  $total ?
  round(($patientCount / $total) * 100, 1) : 0;

  $pResearchers =
  $total ?
  round(($researchCount / $total) * 100, 1) : 0;

  $pStudies =
  $total ?
  round(($studyCount / $total) * 100, 1) : 0;

  $pStudyPatients =
  $total ?
  round(($studyPatientCount / $total) * 100, 1) : 0;

  $pUploads =
  $total ?
  round(($uploadCount / $total) * 100, 1) : 0;

?>

<!-- ========================= CHART JS ========================= -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

  body{
    background:
    linear-gradient(135deg,#0f172a,#111827,#020617);
    min-height:100vh;
    color:#fff;
    font-family:'Segoe UI',sans-serif;
    overflow-x:hidden;
    transition:.4s;
  }

  body.dark{
    background:
    linear-gradient(135deg,#020617,#000,#020617);
  }

  /* ========================= TOGGLE ========================= */

  .toggle-btn{
    position:fixed;
    top:20px;
    right:20px;
    width:55px;
    height:55px;
    border:none;
    border-radius:50%;
    background:#14b8a6;
    color:#fff;
    font-size:22px;
    cursor:pointer;
    z-index:999;
    transition:.3s;
    box-shadow:0 10px 25px rgba(20,184,166,.35);
  }

  .toggle-btn:hover{
    transform:scale(1.08);
  }

  /* ========================= HEADER ========================= */

  .dashboard-title{
    font-size:42px;
    font-weight:800;
    color:#fff;
  }

  .dashboard-title span{
    color:#14b8a6;
  }

  .dashboard-sub{
    color:#cbd5e1;
    font-size:17px;
  }

  /* ========================= CARDS ========================= */

  .card-box{
    padding:32px 25px;
    border-radius:28px;
    color:#fff;
    text-align:center;
    overflow:hidden;
    position:relative;
    transition:.4s;
    box-shadow:0 15px 35px rgba(0,0,0,.25);
  }

  .card-box::before{
    content:'';
    position:absolute;
    width:220px;
    height:220px;
    border-radius:50%;
    background:rgba(255,255,255,.10);
    top:-100px;
    right:-100px;
  }

  .card-box:hover{
    transform:translateY(-8px) scale(1.02);
  }

  .card-icon{
    font-size:45px;
    margin-bottom:18px;
    position:relative;
    z-index:2;
  }

  .card-box h3{
    font-size:42px;
    font-weight:800;
    margin-bottom:10px;
    position:relative;
    z-index:2;
  }

  .card-box p{
    font-size:18px;
    margin:0;
    position:relative;
    z-index:2;
  }

  .c1{
    background:
    linear-gradient(135deg,#2563eb,#1d4ed8);
  }

  .c2{
    background:
    linear-gradient(135deg,#10b981,#059669);
  }

  .c3{
    background:
    linear-gradient(135deg,#f59e0b,#d97706);
  }

  .c4{
    background:
    linear-gradient(135deg,#ef4444,#dc2626);
  }

  .c5{
    background:
    linear-gradient(135deg,#06b6d4,#0284c7);
  }

  /* ========================= CHARTS ========================= */

  .chart-box{
    background:rgba(255,255,255,.08);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,.12);
    border-radius:28px;
    padding:30px 25px;
    box-shadow:0 15px 40px rgba(0,0,0,.28);
    transition:.4s;
    position:relative;
    overflow:hidden;
    height:100%;
  }

  .chart-box::before{
    content:'';
    position:absolute;
    width:220px;
    height:220px;
    background:rgba(255,255,255,.05);
    border-radius:50%;
    top:-100px;
    right:-100px;
  }

  .chart-box::after{
    content:'';
    position:absolute;
    width:180px;
    height:180px;
    background:rgba(20,184,166,.08);
    border-radius:50%;
    bottom:-90px;
    left:-90px;
  }

  .chart-box:hover{
    transform:translateY(-8px);
  }

  .chart-title{
    font-size:24px;
    font-weight:700;
    margin-bottom:25px;
    position:relative;
    z-index:2;
  }

  .chart-container{
    position:relative;
    width:100%;
    height:380px;
  }

  .chart-container canvas{
    width:100% !important;
    height:100% !important;
    position:relative;
    z-index:2;
  }

  /* ========================= ANALYTICS ========================= */

  .stats-box{
    margin-top:40px;
    background:rgba(255,255,255,.08);
    border-radius:28px;
    padding:35px;
    backdrop-filter:blur(15px);
    border:1px solid rgba(255,255,255,.12);
    box-shadow:0 12px 30px rgba(0,0,0,.25);
  }

  .stats-title{
    text-align:center;
    margin-bottom:35px;
    font-size:30px;
    font-weight:700;
  }

  .stat-item{
    margin-bottom:25px;
  }

  .stat-item h6{
    display:flex;
    justify-content:space-between;
    margin-bottom:10px;
    font-size:15px;
    color:#fff;
  }

  .progress{
    height:14px;
    border-radius:20px;
    overflow:hidden;
    background:#1e293b;
  }

  .progress-bar{
    border-radius:20px;
  }

  /* ========================= RESPONSIVE ========================= */

  @media(max-width:768px){

    .dashboard-title{
      font-size:32px;
    }

    .chart-container{
      height:300px;
    }

    .card-box h3{
      font-size:34px;
    }

    .chart-title{
      font-size:20px;
    }
  }

</style>

<!-- ========================= DARK MODE ========================= -->

<button class="toggle-btn"
        onclick="toggleDark()">

  🌙

</button>

<div class="container py-5">

  <!-- ========================= HEADER ========================= -->

  <div class="text-center mb-5">

    <h1 class="dashboard-title">
      Medical <span>Dashboard</span>
    </h1>

    <p class="dashboard-sub">
      Smart Hospital Management & Analytics System
    </p>

  </div>

  <!-- ========================= CARDS ========================= -->

  <div class="row g-4">

    <div class="col-12 col-md-6 col-lg-4">

      <div class="card-box c1">

        <div class="card-icon">
          <i class="fa-solid fa-user-injured"></i>
        </div>

        <h3><?= $patientCount ?></h3>

        <p>Patients</p>

      </div>

    </div>

    <div class="col-12 col-md-6 col-lg-4">

      <div class="card-box c2">

        <div class="card-icon">
          <i class="fa-solid fa-user-doctor"></i>
        </div>

        <h3><?= $researchCount ?></h3>

        <p>Researchers</p>

      </div>

    </div>

    <div class="col-12 col-md-6 col-lg-4">

      <div class="card-box c3">

        <div class="card-icon">
          <i class="fa-solid fa-flask"></i>
        </div>

        <h3><?= $studyCount ?></h3>

        <p>Studies</p>

      </div>

    </div>

    <div class="col-12 col-md-6 col-lg-6">

      <div class="card-box c4">

        <div class="card-icon">
          <i class="fa-solid fa-hospital-user"></i>
        </div>

        <h3><?= $studyPatientCount ?></h3>

        <p>Study Patients</p>

      </div>

    </div>

    <div class="col-12 col-md-6 col-lg-6">

      <div class="card-box c5">

        <div class="card-icon">
          <i class="fa-solid fa-file-arrow-up"></i>
        </div>

        <h3><?= $uploadCount ?></h3>

        <p>Uploads</p>

      </div>

    </div>

  </div>

  <!-- ========================= CHARTS ========================= -->

  <div class="row mt-5 g-4 justify-content-center">

    <!-- BAR CHART -->

    <div class="col-12 col-lg-6">

      <div class="chart-box text-center">

        <h3 class="chart-title">
          <i class="fa-solid fa-chart-column me-2 text-info"></i>
          System Statistics
        </h3>

        <div class="chart-container">
          <canvas id="barChart"></canvas>
        </div>

      </div>

    </div>

    <!-- PIE CHART -->

    <div class="col-12 col-lg-6">

      <div class="chart-box text-center">

        <h3 class="chart-title">
          <i class="fa-solid fa-chart-pie me-2 text-warning"></i>
          Data Percentage
        </h3>

        <div class="chart-container">
          <canvas id="pieChart"></canvas>
        </div>

      </div>

    </div>

  </div>

  <!-- ========================= ANALYTICS ========================= -->

  <div class="stats-box">

    <h2 class="stats-title">
      System Analytics
    </h2>

    <!-- PATIENTS -->

    <div class="stat-item">

      <h6>
        <span>Patients</span>
        <span><?= $pPatients ?>%</span>
      </h6>

      <div class="progress">
        <div class="progress-bar bg-primary"
             style="width:<?= $pPatients ?>%">
        </div>
      </div>

    </div>

    <!-- RESEARCHERS -->

    <div class="stat-item">

      <h6>
        <span>Researchers</span>
        <span><?= $pResearchers ?>%</span>
      </h6>

      <div class="progress">
        <div class="progress-bar bg-success"
             style="width:<?= $pResearchers ?>%">
        </div>
      </div>

    </div>

    <!-- STUDIES -->

    <div class="stat-item">

      <h6>
        <span>Studies</span>
        <span><?= $pStudies ?>%</span>
      </h6>

      <div class="progress">
        <div class="progress-bar bg-warning"
             style="width:<?= $pStudies ?>%">
        </div>
      </div>

    </div>

    <!-- STUDY PATIENTS -->

    <div class="stat-item">

      <h6>
        <span>Study Patients</span>
        <span><?= $pStudyPatients ?>%</span>
      </h6>

      <div class="progress">
        <div class="progress-bar bg-danger"
             style="width:<?= $pStudyPatients ?>%">
        </div>
      </div>

    </div>

    <!-- UPLOADS -->

    <div class="stat-item mb-0">

      <h6>
        <span>Uploads</span>
        <span><?= $pUploads ?>%</span>
      </h6>

      <div class="progress">
        <div class="progress-bar bg-info"
             style="width:<?= $pUploads ?>%">
        </div>
      </div>

    </div>

  </div>

</div>

<!-- ========================= JS ========================= -->

<script>

  function toggleDark(){

    document.body.classList.toggle("dark");
  }

  /* ========================= BAR CHART ========================= */

  new Chart(document.getElementById('barChart'), {

    type: 'bar',

    data: {

      labels: [
        'Patients',
        'Researchers',
        'Studies',
        'Study Patients',
        'Uploads'
      ],

      datasets: [{

        label: 'Medical System',

        data: [
          <?= $patientCount ?>,
          <?= $researchCount ?>,
          <?= $studyCount ?>,
          <?= $studyPatientCount ?>,
          <?= $uploadCount ?>
        ],

        backgroundColor: [
          '#3b82f6',
          '#10b981',
          '#f59e0b',
          '#ef4444',
          '#06b6d4'
        ],

        borderRadius: 14,
        borderSkipped: false,

        hoverBackgroundColor: [
          '#60a5fa',
          '#34d399',
          '#fbbf24',
          '#f87171',
          '#22d3ee'
        ]
      }]
    },

    options: {

      responsive: true,
      maintainAspectRatio: false,

      plugins: {

        legend: {
          display: false
        }
      },

      scales: {

        y: {

          beginAtZero: true,

          ticks: {
            color: '#e2e8f0'
          },

          grid: {
            color: 'rgba(255,255,255,.08)'
          }
        },

        x: {

          ticks: {
            color: '#e2e8f0'
          },

          grid: {
            display: false
          }
        }
      }
    }
  });

  /* ========================= DOUGHNUT CHART ========================= */

  new Chart(document.getElementById('pieChart'), {

    type: 'doughnut',

    data: {

      labels: [
        'Patients',
        'Researchers',
        'Studies',
        'Study Patients',
        'Uploads'
      ],

      datasets: [{

        data: [
          <?= $pPatients ?>,
          <?= $pResearchers ?>,
          <?= $pStudies ?>,
          <?= $pStudyPatients ?>,
          <?= $pUploads ?>
        ],

        backgroundColor: [
          '#3b82f6',
          '#10b981',
          '#f59e0b',
          '#ef4444',
          '#06b6d4'
        ],

        hoverOffset: 12,
        borderWidth: 3,
        borderColor: '#0f172a'
      }]
    },

    options: {

      responsive: true,
      maintainAspectRatio: false,

      cutout: '68%',

      plugins: {

        legend: {

          position: 'bottom',

          labels: {

            color: '#fff',
            padding: 18,

            font: {
              size: 13
            }
          }
        }
      }
    }
  });

</script>

<?php

} else {

  $_SESSION['message_login'] = "Login First";

  header("Location: ../login.php");

  exit();
}

include "includes/temp/footer.php";

?>