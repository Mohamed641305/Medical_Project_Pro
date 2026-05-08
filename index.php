<?php
session_start();

if (!isset($_SESSION['user_login'])) {

    $_SESSION['message_login'] = "Login First";
    header("Location: login.php");
    exit();
}

include("includes/temp/header.php");
include("includes/temp/navbar.php");
?>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: 'Poppins', sans-serif;
    background: #0f172a;
    color: white;
    overflow-x: hidden;
  }

  a {
    text-decoration: none;
  }

  /* HERO */

  .hero {
    min-height: 100vh;

    background:
      linear-gradient(to right,
        rgba(15, 23, 42, .95),
        rgba(15, 23, 42, .75)),
      url('https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=2070');

    background-size: cover;
    background-position: center;

    display: flex;
    align-items: center;

    position: relative;
  }

  .hero::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: #06b6d4;
    border-radius: 50%;
    filter: blur(180px);
    top: -120px;
    right: -120px;
    opacity: .35;
  }

  .hero-content {
    position: relative;
    z-index: 2;
  }

  .hero small {
    color: #22d3ee;
    letter-spacing: 3px;
    font-weight: 600;
  }

  .hero h1 {
    font-size: 72px;
    font-weight: 700;
    margin: 20px 0;
    line-height: 1.1;
  }

  .hero p {
    color: #cbd5e1;
    font-size: 18px;
    max-width: 650px;
    line-height: 1.8;
  }

  .hero-buttons {
    margin-top: 35px;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
  }

  .btn-custom {
    padding: 14px 35px;
    border-radius: 60px;
    font-weight: 600;
    transition: .3s;
  }

  .btn-primary-custom {
    background: linear-gradient(45deg, #06b6d4, #3b82f6);
    border: none;
    color: white;
  }

  .btn-primary-custom:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 30px rgba(59, 130, 246, .3);
  }

  .btn-outline-custom {
    border: 1px solid rgba(255, 255, 255, .2);
    color: white;
    backdrop-filter: blur(10px);
  }

  .btn-outline-custom:hover {
    background: white;
    color: #0f172a;
  }

  /* FEATURES */

  .section {
    padding: 110px 0;
    position: relative;
  }

  .section-title {
    text-align: center;
    margin-bottom: 70px;
  }

  .section-title span {
    color: #22d3ee;
    font-weight: 600;
    letter-spacing: 2px;
  }

  .section-title h2 {
    font-size: 48px;
    margin-top: 15px;
    font-weight: 700;
  }

  .section-title p {
    color: #94a3b8;
    margin-top: 15px;
  }

  .feature-card {
    background: rgba(255, 255, 255, .05);
    border: 1px solid rgba(255, 255, 255, .08);
    backdrop-filter: blur(14px);

    border-radius: 28px;
    padding: 40px 30px;

    transition: .4s;
    height: 100%;
  }

  .feature-card:hover {
    transform: translateY(-10px);
    border-color: #22d3ee;
    box-shadow: 0 20px 40px rgba(0, 0, 0, .3);
  }

  .feature-icon {
    width: 75px;
    height: 75px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 20px;

    font-size: 30px;

    margin-bottom: 25px;

    background: linear-gradient(45deg, #06b6d4, #3b82f6);
  }

  .feature-card h4 {
    margin-bottom: 15px;
    font-weight: 600;
  }

  .feature-card p {
    color: #94a3b8;
    line-height: 1.8;
  }

  /* TEAM */

  .team-card {
    background: #111827;
    border: none;
    overflow: hidden;
    border-radius: 30px;
    transition: .4s;
  }

  .team-card:hover {
    transform: translateY(-12px);
  }

  .team-card img {
    width: 100%;
    height: 380px;
    object-fit: cover;
  }

  .team-card .card-body {
    padding: 30px;
  }

  .team-card h5 {
    font-size: 24px;
    margin-bottom: 10px;
  }

  .team-card p {
    color: #22d3ee;
  }

  /* STATS */

  .stats {
    background:
      linear-gradient(45deg, #06b6d4, #3b82f6);

    border-radius: 40px;
    padding: 60px 40px;
  }

  .stat-box {
    text-align: center;
  }

  .stat-box h2 {
    font-size: 55px;
    font-weight: 700;
  }

  .stat-box p {
    margin-top: 10px;
    font-size: 18px;
  }

  /* CTA */

  .cta {
    text-align: center;
    padding: 120px 20px;
  }

  .cta h2 {
    font-size: 52px;
    font-weight: 700;
    margin-bottom: 20px;
  }

  .cta p {
    color: #94a3b8;
    max-width: 700px;
    margin: auto;
    line-height: 1.8;
  }

  /* RESPONSIVE */

  @media(max-width:992px) {

    .hero {
      text-align: center;
      padding: 120px 0;
    }

    .hero h1 {
      font-size: 50px;
    }

    .section-title h2 {
      font-size: 38px;
    }

    .cta h2 {
      font-size: 40px;
    }
  }

  @media(max-width:768px) {

    .hero h1 {
      font-size: 38px;
    }

    .hero p {
      font-size: 16px;
    }

    .section {
      padding: 80px 0;
    }

    .section-title h2 {
      font-size: 30px;
    }

    .stat-box h2 {
      font-size: 38px;
    }
  }
</style>

<!-- HERO -->

<section class="hero">

  <div class="container">

    <div class="hero-content">

      <small>
        ADVANCED HEALTHCARE PLATFORM
      </small>

      <h1>
        Future Of <br>
        Medical Management
      </h1>

      <p>
        A modern healthcare platform designed for hospitals,
        clinics, doctors, and medical researchers with smart
        patient management and secure medical systems.
      </p>

      <div class="hero-buttons">

        <a href="#" class="btn btn-custom btn-primary-custom">
          Explore Platform
        </a>

        <a href="#" class="btn btn-custom btn-outline-custom">
          Learn More
        </a>

      </div>

    </div>

  </div>

</section>

<!-- FEATURES -->

<section class="section">

  <div class="container">

    <div class="section-title">

      <span>
        CORE FEATURES
      </span>

      <h2>
        Smart Medical Services
      </h2>

      <p>
        Designed to simplify healthcare operations professionally
      </p>

    </div>

    <div class="row g-4">

      <div class="col-lg-3 col-md-6">

        <div class="feature-card">

          <div class="feature-icon">
            <i class="fa fa-user-injured"></i>
          </div>

          <h4>
            Patients
          </h4>

          <p>
            Manage patient records, history, and healthcare
            information securely.
          </p>

        </div>

      </div>

      <div class="col-lg-3 col-md-6">

        <div class="feature-card">

          <div class="feature-icon">
            <i class="fa fa-user-doctor"></i>
          </div>

          <h4>
            Doctors
          </h4>

          <p>
            Connect healthcare professionals and specialists
            inside one system.
          </p>

        </div>

      </div>

      <div class="col-lg-3 col-md-6">

        <div class="feature-card">

          <div class="feature-icon">
            <i class="fa fa-notes-medical"></i>
          </div>

          <h4>
            Research
          </h4>

          <p>
            Clinical studies and advanced medical research
            management tools.
          </p>

        </div>

      </div>

      <div class="col-lg-3 col-md-6">

        <div class="feature-card">

          <div class="feature-icon">
            <i class="fa fa-shield"></i>
          </div>

          <h4>
            Security
          </h4>

          <p>
            Advanced protection for sensitive medical data
            and patient privacy.
          </p>

        </div>

      </div>

    </div>

  </div>

</section>

<!-- TEAM -->

<section class="section">

  <div class="container">

    <div class="section-title">

      <span>
        OUR TEAM
      </span>

      <h2>
        Medical Professionals
      </h2>

    </div>

    <div class="row g-4">

      <div class="col-lg-4 col-md-6">

        <div class="card team-card">

          <img src="https://images.unsplash.com/photo-1537368910025-700350fe46c7?q=80&w=1200">

          <div class="card-body text-center">

            <h5>
              Dr. Ahmed Hassan
            </h5>

            <p>
              Cardiology Specialist
            </p>

          </div>

        </div>

      </div>

      <div class="col-lg-4 col-md-6">

        <div class="card team-card">

          <img src="https://images.unsplash.com/photo-1559839734-2b71ea197ec2?q=80&w=1200">

          <div class="card-body text-center">

            <h5>
              Dr. Sara Ali
            </h5>

            <p>
              Neurology Expert
            </p>

          </div>

        </div>

      </div>

      <div class="col-lg-4 col-md-6">

        <div class="card team-card">

          <img src="https://images.unsplash.com/photo-1612349317150-e413f6a5b16d?q=80&w=1200">

          <div class="card-body text-center">

            <h5>
              Dr. Mohamed Adel
            </h5>

            <p>
              Research Scientist
            </p>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>

<!-- STATS -->

<section class="section">

  <div class="container">

    <div class="stats">

      <div class="row g-4">

        <div class="col-lg-3 col-md-6">

          <div class="stat-box">

            <h2>
              120+
            </h2>

            <p>
              Patients
            </p>

          </div>

        </div>

        <div class="col-lg-3 col-md-6">

          <div class="stat-box">

            <h2>
              25+
            </h2>

            <p>
              Doctors
            </p>

          </div>

        </div>

        <div class="col-lg-3 col-md-6">

          <div class="stat-box">

            <h2>
              18+
            </h2>

            <p>
              Medical Studies
            </p>

          </div>

        </div>

        <div class="col-lg-3 col-md-6">

          <div class="stat-box">

            <h2>
              24/7
            </h2>

            <p>
              Medical Support
            </p>

          </div>

        </div>

      </div>

    </div>

  </div>

</section>

<!-- CTA -->

<section class="cta">

  <div class="container">

    <h2>
      Revolutionizing Healthcare Technology
    </h2>

    <p>
      Build a smarter and more connected medical environment
      with modern healthcare solutions designed for the future.
    </p>

    <a href="#" class="btn btn-custom btn-primary-custom mt-4">
      Start Now
    </a>

  </div>

</section>

<?php
include("includes/temp/footer.php");
?>