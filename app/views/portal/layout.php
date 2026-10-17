<?php
if(session_status()===PHP_SESSION_NONE) session_start();
$currentUser = $_SESSION['user'] ?? null;
if(!$currentUser){ header('Location: /login'); exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Portal') ?> — GOF</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
  <?= $extraHead ?? '' ?>
  <style>
    .portal-navbar {
      background: white;
      box-shadow: var(--gof-shadow);
    }
    .portal-brand {
      color: var(--gof-green-dark);
      font-weight: 700;
    }
    .portal-main {
      padding-top: 30px;
      padding-bottom: 60px;
      min-height: calc(100vh - 140px);
    }
  </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg portal-navbar sticky-top py-3">
  <div class="container">
    <a class="navbar-brand portal-brand d-flex align-items-center" href="<?= url('/portal') ?>">
      <div class="gof-logo-icon" style="width:36px; height:36px; font-size:20px; margin-right:12px;">G</div>
      <span>GOF Portal</span>
    </a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#portalNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="portalNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4">
        <li class="nav-item"><a class="nav-link fw-medium" href="<?= url('/portal') ?>"><i class="bi bi-house me-1"></i> Dashboard</a></li>
        <li class="nav-item"><a class="nav-link fw-medium" href="<?= url('/portal/profile') ?>"><i class="bi bi-person me-1"></i> My Profile</a></li>
        <li class="nav-item"><a class="nav-link fw-medium" href="<?= url('/portal/history') ?>"><i class="bi bi-clock-history me-1"></i> History</a></li>
      </ul>
      <div class="dropdown">
        <button class="gof-user-btn dropdown-toggle" data-bs-toggle="dropdown">
          <div class="gof-avatar gof-avatar-sm"><?= strtoupper(substr($currentUser['first_name']??'U',0,1)) ?></div>
          <span class="ms-2"><?= htmlspecialchars(($currentUser['first_name']??'').' '.($currentUser['last_name']??'')) ?></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
          <li><a class="dropdown-item" href="<?= url('/portal/profile') ?>">Settings</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="<?= url('/logout') ?>">Sign Out</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<main class="portal-main">
  <div class="container">
    <?php if(!empty($_SESSION['flash_success'])): ?>
      <div class="alert alert-success alert-dismissible gof-flash fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    <?php if(!empty($_SESSION['flash_error'])): ?>
      <div class="alert alert-danger alert-dismissible gof-flash fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>
    
    <?= $content ?? '' ?>
  </div>
</main>

<footer class="bg-white border-top py-4 text-center">
  <div class="container text-muted small">
    &copy; <?= date('Y') ?> Garko Orphans Foundation. All rights reserved.
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('/assets/js/gof.js') ?>"></script>
<?= $extraScripts ?? '' ?>
</body>
</html>
