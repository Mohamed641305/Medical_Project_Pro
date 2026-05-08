<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
?>

<style>
  .custom-navbar {

    background: rgba(15, 23, 42, .75) !important;

    backdrop-filter: blur(14px);

    border-bottom: 1px solid rgba(255, 255, 255, .08);

    padding: 18px 0;

    transition: .3s;

    z-index: 9999;
  }

  .navbar-brand {

    font-size: 28px;
    font-weight: 700;
    color: white !important;

    letter-spacing: 1px;
  }

  .navbar-brand i {
    color: #22d3ee;
  }

  .navbar-nav .nav-link {

    color: #cbd5e1 !important;

    margin-left: 12px;

    font-weight: 500;

    transition: .3s;

    position: relative;
  }

  .navbar-nav .nav-link:hover {
    color: white !important;
  }

  /* UNDERLINE EFFECT */

  .navbar-nav .nav-link::after {

    content: '';

    position: absolute;

    left: 0;
    bottom: -4px;

    width: 0%;
    height: 2px;

    background: #22d3ee;

    transition: .3s;
  }

  .navbar-nav .nav-link:hover::after {
    width: 100%;
  }

  /* LOGIN BUTTON */

  .btn-login {

    border: 1px solid rgba(255, 255, 255, .15);

    padding: 10px 24px !important;

    border-radius: 50px;

    backdrop-filter: blur(10px);

    transition: .3s !important;
  }

  .btn-login:hover {

    background: white;

    color: #0f172a !important;
  }

  /* REGISTER BUTTON */

  .btn-register {

    background: linear-gradient(45deg, #06b6d4, #3b82f6);

    border: none;

    padding: 10px 24px !important;

    border-radius: 50px;

    color: white !important;

    transition: .3s !important;

    box-shadow: 0 10px 25px rgba(59, 130, 246, .25);
  }

  .btn-register:hover {

    transform: translateY(-3px);

    box-shadow: 0 15px 35px rgba(59, 130, 246, .4);
  }

  /* LOGOUT BUTTON */

  .btn-logout {

    background: rgba(255, 255, 255, .08);

    border: 1px solid rgba(255, 255, 255, .1);

    padding: 10px 24px !important;

    border-radius: 50px;

    color: white !important;

    transition: .3s;
  }

  .btn-logout:hover {

    background: #ef4444 !important;

    border-color: #ef4444;

    color: white !important;
  }

  /* TOGGLER */

  .navbar-toggler {

    border: none !important;

    box-shadow: none !important;
  }

  .navbar-toggler i {
    color: white;
    font-size: 24px;
  }

  /* MOBILE */

  @media(max-width:991px) {

    .navbar-collapse {

      margin-top: 20px;

      background: rgba(15, 23, 42, .95);

      padding: 20px;

      border-radius: 20px;
    }

    .navbar-nav .nav-link {
      margin: 10px 0;
    }

    .btn-register,
    .btn-login,
    .btn-logout {

      display: inline-block;

      width: fit-content;
    }
  }
</style>

<nav class="navbar navbar-expand-lg custom-navbar fixed-top">

  <div class="container">

    <!-- LOGO -->

    <a class="navbar-brand" href="index.php">

      <i class="fa-solid fa-heart-pulse"></i>

      MediCare

    </a>

    <!-- TOGGLER -->

    <button class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#mainNav">

      <i class="fa-solid fa-bars"></i>

    </button>

    <!-- NAV LINKS -->

    <div class="collapse navbar-collapse" id="mainNav">

      <ul class="navbar-nav ms-auto align-items-lg-center">

        <?php if (isset($_SESSION['user_login'])): ?>

          <li class="nav-item ms-lg-3">

            <a class="nav-link btn-logout" href="logout.php">

              Logout

            </a>

          </li>

        <?php else: ?>

          <li class="nav-item ms-lg-3">

            <a class="nav-link btn-login" href="login.php">

              Login

            </a>

          </li>

          <li class="nav-item ms-lg-2">

            <a class="nav-link btn-register" href="register.php">

              Register

            </a>

          </li>

        <?php endif; ?>

      </ul>

    </div>

  </div>

</nav>