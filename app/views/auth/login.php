<?php
if(session_status()===PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign In - GOF Management Platform</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
</head>
<body>

<div class="login-split">
    <div class="login-left">
        <div class="login-left-content text-center text-md-start">
            <div class="login-logo-circle mx-auto mx-md-0">G</div>
            <h1 class="login-title">Garko Orphans Foundation<br>Management Platform</h1>
            <p class="login-subtitle">Empowering Orphans, Transforming Lives</p>
        </div>
    </div>
    <div class="login-right">
        <div class="login-form-container">
            <h3 class="mb-4 fw-bold">Sign In</h3>
            
            <?php if(!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if(!empty($_SESSION['flash_success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="<?= url('/login') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                
                <div class="mb-3">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required autofocus placeholder="name@example.com">
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label mb-0" for="password">Password</label>
                        <a href="<?= url('/forgot-password') ?>" class="text-decoration-none small text-muted">Forgot password?</a>
                    </div>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter your password">
                        <button class="btn btn-outline-secondary password-toggle" type="button" data-target="password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-gof-primary w-100 py-2 fs-5">Sign In</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="<?= url('/') ?>" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset('/assets/js/gof.js') ?>"></script>
</body>
</html>
