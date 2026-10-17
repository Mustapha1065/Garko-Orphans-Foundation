<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - GOF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm border-0" style="width: 100%; max-width: 450px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="gof-logo-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 2rem; background: #0d6efd; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">G</div>
                    <h3 class="fw-bold">Forgot Password</h3>
                    <p class="text-muted">Enter your email to receive reset instructions</p>
                </div>
                
                <?php if(!empty($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if(!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?= url('/forgot-password') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                    
                    <div class="mb-4">
                        <label for="email" class="form-label fw-medium">Email Address</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" required autofocus>
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
                    </div>
                    
                    <div class="text-center">
                        <a href="<?= url('/login') ?>" class="text-decoration-none text-muted">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
