<?php
require_once __DIR__ . '/functions/student_auth.php';
start_student_session();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EYYSAT Admission Portal</title>
  <link href="css/bootstrap.css" rel="stylesheet">
  <link rel="stylesheet" href="css/all.min.css">
  <link rel="stylesheet" href="css/webhome.css">
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-academy shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#home">
        <img src="img/eyysat.png" alt="EYYSAT Logo" class="navbar-logo">
        <span class="fs-4 fw-bold text-yellow">EYYSAT</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainSchoolMenu"
        aria-controls="mainSchoolMenu" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainSchoolMenu">
        <ul class="navbar-nav ms-auto align-items-lg-center gap-2">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="#home">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
          <li class="nav-item"><a class="nav-link" href="#programs">Programs</a></li>
          <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
          <li class="nav-item">
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'student') { ?>
              <a class="btn btn-outline-warning btn-sm px-4" href="student/profile.php">Profile</a>
            <?php } else { ?>
              <a class="btn btn-outline-warning btn-sm px-4" href="auth/login.php">Login</a>
            <?php } ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <main>
    <section id="home" class="hero-section d-flex align-items-center py-5">
      <div class="container">
        <div class="row align-items-center gy-5">
          <div class="col-lg-6">
            <span class="badge-admission mb-3 shadow-sm">Admission Open 2026-27</span>
            <h1 class="hero-title mb-4">Build your future with expert guidance and innovative learning.</h1>
            <p class="lead text-white-75 mb-4">
              EYYSAT offers a modern admission experience with strong academic support, digital classrooms, and
              career-focused pathways.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-3 mb-3">
              <a href="enrollment.php" class="btn btn-warning btn-lg px-4 fw-semibold">
                <i class="fa-solid fa-rocket me-2"></i>Apply Now
              </a>
              <a href="#programs" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                <i class="fa-solid fa-graduation-cap me-2"></i>Explore Programs
              </a>
            </div>
          </div>
    </section>

    <section id="about" class="section-light py-5">
      <div class="container">
        <div class="row align-items-center gy-4">
          <div class="col-lg-6">
            <h2 class="section-title">A modern academy built for ambitious learners.</h2>
            <p class="text-muted mb-4">
              EYYSAT combines strong academic programs with mentoring and technology to prepare students for real-world
              success.
            </p>
            <ul class="list-unstyled text-muted mb-0">
              <li class="mb-2">• Personalized admission support and career guidance.</li>
              <li class="mb-2">• Skills-based curriculum designed for the next generation.</li>
              <li class="mb-2">• Secure, fast, and mobile-friendly enrollment workflow.</li>
            </ul>
          </div>
          <div class="col-lg-6">
            <div class="feature-panel rounded-4 p-4 shadow-sm bg-white">
              <div class="d-flex align-items-center mb-3">
                <div class="feature-icon bg-academy text-white rounded-3 me-3">
                  <i class="fa-solid fa-award"></i>
                </div>
                <div>
                  <h5 class="mb-1">Recognized excellence</h5>
                  <p class="mb-0 text-muted">Quality education with trusted outcomes.</p>
                </div>
              </div>
              <div class="d-flex align-items-center mb-3">
                <div class="feature-icon bg-yellow text-academy rounded-3 me-3">
                  <i class="fa-solid fa-laptop-code"></i>
                </div>
                <div>
                  <h5 class="mb-1">Digital learning</h5>
                  <p class="mb-0 text-muted">Interactive lessons, online resources, and campus access.</p>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <div class="feature-icon bg-orange text-white rounded-3 me-3">
                  <i class="fa-solid fa-users"></i>
                </div>
                <div>
                  <h5 class="mb-1">Community support</h5>
                  <p class="mb-0 text-muted">Collaborative learning with teachers and peers.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="programs" class="py-5">
      <div class="container">
        <div class="text-center mb-5">
          <h2 class="section-title">Programs Designed for Every Learner</h2>
          <p class="text-muted mb-0">Choose the right path with flexible admission and supportive academics.</p>
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4">
              <div class="card-body p-4">
                <div class="mb-3">
                  <i class="fa-solid fa-pen-to-square" style="font-size: 2.5rem; color: #f39c12;"></i>
                </div>
                <h5 class="card-title">Basic Enrollment</h5>
                <p class="card-text text-muted">Ideal for first-time applicants who want a fast and guided admission process.</p>
                <a href="enrollment.php" class="stretched-link text-warning fw-bold">Start Your Application →</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4">
              <div class="card-body p-4">
                <div class="mb-3">
                  <i class="fa-solid fa-award" style="font-size: 2.5rem; color: var(--academy-yellow);"></i>
                </div>
                <h5 class="card-title">Scholarship Support</h5>
                <p class="card-text text-muted">Apply for scholarship mentoring and financial assistance resources.</p>
                <a href="auth/login.php" class="stretched-link text-warning fw-bold">Learn More →</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4">
              <div class="card-body p-4">
                <div class="mb-3">
                  <i class="fa-solid fa-user-check" style="font-size: 2.5rem; color: var(--academy-blue);"></i>
                </div>
                <h5 class="card-title">Student Services</h5>
                <p class="card-text text-muted">Get help with enrollment status, profile updates, and student support.</p>
                <a href="student/profile.php#enrollment" class="stretched-link text-warning fw-bold">View Profile →</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" class="section-light py-5">
      <div class="container">
        <div class="row align-items-center gy-4">
          <div class="col-lg-8">
            <h2 class="section-title">Ready to join? Let’s get you started.</h2>
            <p class="text-muted mb-0">Contact the admissions team or begin your registration today.</p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <a href="enrollment.php" class="btn btn-warning btn-lg px-4 fw-semibold">
              <i class="fa-solid fa-right-to-bracket me-2"></i>Register Now
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer bg-academy text-white py-4">
    <div class="container text-center">
      <p class="mb-2"><i class="fa-solid fa-envelope me-2"></i>admissions@eyysat.edu</p>
      <p class="mb-0">© 2026 EYYSAT Admission Portal. All rights reserved.</p>
    </div>
  </footer>

  <script src="js/bootstrap.bundle.js"></script>
</body>

</html>