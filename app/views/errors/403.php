<?php
if(session_status()===PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>403 Forbidden - GOF</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 bg-light">

<div class="text-center">
    <div class="login-logo-circle mx-auto mb-4 bg-danger text-white"><i class="bi bi-shield-lock"></i></div>
    <h1 class="display-1 fw-bold text-muted mb-2">403</h1>
    <h3 class="fw-semibold mb-3">Access Denied</h3>
    <p class="text-muted mb-4">You do not have permission to access this resource or page.</p>
    <button onclick="history.back()" class="btn btn-outline-secondary me-2"><i class="bi bi-arrow-left me-2"></i>Go Back</button>
    <a href="<?= url('/') ?>" class="btn btn-gof-primary"><i class="bi bi-house me-2"></i>Go to Homepage</a>
</div>

</body>
</html>
