<?php
if(session_status()===PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>404 Not Found - GOF</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

<div class="text-center">
    <div class="login-logo-circle mx-auto mb-4" style="background:var(--gof-green); color:white;">G</div>
    <h1 class="display-1 fw-bold text-muted mb-2">404</h1>
    <h3 class="fw-semibold mb-3">Page Not Found</h3>
    <p class="text-muted mb-4">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
    <a href="<?= url('/') ?>" class="btn btn-gof-primary me-2"><i class="bi bi-house me-2"></i>Go to Homepage</a>
    <a href="<?= url('/admin') ?>" class="btn btn-outline-gof"><i class="bi bi-grid me-2"></i>Go to Admin Dashboard</a>
</div>

</body>
</html>
