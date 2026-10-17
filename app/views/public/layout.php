<?php
if(session_status()===PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Home') ?> — Garko Orphans Foundation</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
  <?= $extraHead ?? '' ?>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg gof-public-navbar sticky-top">
  <div class="container">
    <a class="navbar-brand gof-public-brand" href="<?= url('/') ?>">
      <div class="gof-logo-icon" style="margin-right:10px; width:40px; height:40px; font-size:24px;">G</div>
      <span>GOF</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar" aria-controls="publicNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="publicNavbar">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link gof-nav-link-public <?= ($_SERVER['REQUEST_URI']==='/')?'active fw-bold text-success':'' ?>" href="<?= url('/') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link gof-nav-link-public <?= (strpos($_SERVER['REQUEST_URI'],'/about')===0)?'active fw-bold text-success':'' ?>" href="<?= url('/about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link gof-nav-link-public <?= (strpos($_SERVER['REQUEST_URI'],'/programs')===0)?'active fw-bold text-success':'' ?>" href="<?= url('/programs') ?>">Programs</a>
        </li>
        <li class="nav-item">
          <a class="nav-link gof-nav-link-public <?= (strpos($_SERVER['REQUEST_URI'],'/events')===0)?'active fw-bold text-success':'' ?>" href="<?= url('/events') ?>">Events</a>
        </li>
        <li class="nav-item">
          <a class="nav-link gof-nav-link-public <?= (strpos($_SERVER['REQUEST_URI'],'/news')===0)?'active fw-bold text-success':'' ?>" href="<?= url('/news') ?>">News</a>
        </li>
        <li class="nav-item">
          <a class="nav-link gof-nav-link-public <?= (strpos($_SERVER['REQUEST_URI'],'/gallery')===0)?'active fw-bold text-success':'' ?>" href="<?= url('/gallery') ?>">Gallery</a>
        </li>
        <li class="nav-item">
          <a class="nav-link gof-nav-link-public <?= (strpos($_SERVER['REQUEST_URI'],'/contact')===0)?'active fw-bold text-success':'' ?>" href="<?= url('/contact') ?>">Contact</a>
        </li>
      </ul>
      <div class="d-flex">
        <a href="<?= url('/login') ?>" class="btn btn-outline-gof me-2">Login</a>
        <a href="<?= url('/donate') ?>" class="btn btn-gof-secondary fw-bold px-4">Donate Now</a>
      </div>
    </div>
  </div>
</nav>

<!-- Main Content -->
<main>
  <?php if(!empty($_SESSION['flash_success'])): ?>
    <div class="container mt-4">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>

  <?= $content ?? '' ?>
</main>

<!-- Footer -->
<footer class="footer-section">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 mb-4 mb-lg-0">
        <h4 class="footer-heading d-flex align-items-center">
            <div class="gof-logo-icon bg-white text-success me-2" style="width:30px; height:30px; font-size:18px;">G</div>
            Garko Orphans Foundation
        </h4>
        <p class="text-white-50">Empowering Orphans, Transforming Lives. Our mission is to provide education, healthcare, and support to orphans in need, ensuring a brighter future for the next generation.</p>
        <div class="mt-4">
            <a href="#" class="text-white me-3 fs-5"><i class="bi bi-facebook"></i></a>
            <a href="#" class="text-white me-3 fs-5"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="text-white me-3 fs-5"><i class="bi bi-instagram"></i></a>
            <a href="#" class="text-white fs-5"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 mb-4 mb-lg-0 offset-lg-1">
        <h4 class="footer-heading">Quick Links</h4>
        <div class="footer-links">
          <a href="<?= url('/about') ?>">About Us</a>
          <a href="<?= url('/programs') ?>">Our Programs</a>
          <a href="<?= url('/events') ?>">Events</a>
          <a href="<?= url('/news') ?>">Latest News</a>
          <a href="<?= url('/gallery') ?>">Gallery</a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
        <h4 class="footer-heading">Support</h4>
        <div class="footer-links">
          <a href="<?= url('/donate') ?>">Donate</a>
          <a href="<?= url('/volunteer') ?>">Volunteer</a>
          <a href="<?= url('/sponsor') ?>">Sponsor a Child</a>
          <a href="<?= url('/contact') ?>">Contact Us</a>
          <a href="<?= url('/faq') ?>">FAQ</a>
        </div>
      </div>
      <div class="col-lg-3 col-md-12">
        <h4 class="footer-heading">Contact Info</h4>
        <ul class="list-unstyled text-white-50">
          <li class="mb-2"><i class="bi bi-geo-alt me-2 text-warning"></i> 123 Foundation Road, Garko, Kano State</li>
          <li class="mb-2"><i class="bi bi-telephone me-2 text-warning"></i> +234 123 456 7890</li>
          <li class="mb-2"><i class="bi bi-envelope me-2 text-warning"></i> info@gof.org.ng</li>
        </ul>
      </div>
    </div>
    <hr class="mt-5 mb-4 border-secondary">
    <div class="row align-items-center">
      <div class="col-md-6 text-center text-md-start">
        <p class="text-white-50 mb-0">&copy; <?= date('Y') ?> Garko Orphans Foundation. All rights reserved.</p>
      </div>
      <div class="col-md-6 text-center text-md-end mt-3 mt-md-0 footer-links">
        <a href="<?= url('/privacy') ?>" class="d-inline-block me-3 mb-0">Privacy Policy</a>
        <a href="<?= url('/terms') ?>" class="d-inline-block mb-0">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('/assets/js/gof.js') ?>"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
