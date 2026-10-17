<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - GOF</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-sm border-0" style="width: 100%; max-width: 450px;">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <div class="gof-logo-icon mx-auto mb-3" style="width: 60px; height: 60px; font-size: 2rem; background: #0d6efd; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center;">G</div>
                    <h3 class="fw-bold">Reset Password</h3>
                    <p class="text-muted">Enter your new password below</p>
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

                <?php if(!empty($_SESSION['flash_info'])): ?>
                    <div class="alert alert-info">
                        <?= htmlspecialchars($_SESSION['flash_info']); unset($_SESSION['flash_info']); ?>
                    </div>
                <?php endif; ?>

                <form action="<?= url('/reset-password') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token ?? '') ?>">
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-medium">New Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required autofocus minlength="8">
                        <div class="form-text">Password must be at least 8 characters long.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-medium">Confirm Password</label>
                        <input type="password" class="form-control form-control-lg" id="confirm_password" name="confirm_password" required minlength="8">
                    </div>
                    
                    <div class="d-grid mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                    </div>
                    
                    <div class="text-center">
                        <a href="<?= url('/login') ?>" class="text-decoration-none text-muted">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Password confirmation validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match.');
            }
        });
    </script>
</body>
</html>
