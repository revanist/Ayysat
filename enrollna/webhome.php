<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AYYSAT Admission Portal</title>
  <link href="css/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="css/webhome.css">
</head>

<body>

  <nav class="navbar navbar-expand-lg bg-academy shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand text-white fw-bold d-flex align-items-center" href="#">
        <span class="fs-4 tracking-wide text-yellow">AYYSAT</span>
      </a>

      <button class="navbar-toggler border-white" type="button" data-bs-toggle="collapse"
        data-bs-target="#mainSchoolMenu">
        <span class="navbar-toggler-icon justify-content-center""></span>
      </button>
      
      <div class=" collapse navbar-collapse" id="mainSchoolMenu">
          <ul class="navbar-nav ms-auto align-items-center gap-2">
            <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
            <li class="nav-item"><a class="nav-link me-lg-3" href="#">Contact</a></li>
            <li class="nav-item">
              <a class="btn btn-apply shadow-sm" href="auth/login.php">Login</a>
            </li>
          </ul>
    </div>
    </div>
  </nav>

  <header class="hero-section d-flex align-items-center">
    <div class="container hero-content">
      <div class="row align-items-center g-5">

        <div class="col-lg-6">
          <div class="badge-admission mb-4 shadow-sm">Admission Open 2026-27</div>
          <h1 class="hero-title mb-3">
            Shape YOU For A <br>
            <span class="text-yellow">Bright Future</span> Today!
          </h1>
          <p class="lead mb-4 text-white-50">
            Providing high-quality education with state-of-the-art technology, expert faculty guidance, and a
            comprehensive curriculum built for creative individuals.
          </p>
          <div class="d-flex gap-3">
            <a href="enrollment.php" class="btn btn-warning btn-lg px-4 fw-bold text-dark">Enroll Now</a>
            <a href="#tours" class="btn btn-outline-light btn-lg px-4">Virtual Tour</a>
          </div>
        </div>

      </div>
    </div>
  </header>
  <script src="js/bootstrap.bundle.js"></script>
</body>

</html>