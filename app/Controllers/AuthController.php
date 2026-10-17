<?php
namespace GOF\Controllers;

class AuthController extends BaseController
{
    private \GOF\Services\AuthService $authService;

    public function __construct() {
        $this->authService = new \GOF\Services\AuthService();
    }

    public function showLogin(): void
    {
        if ($this->authService->check()) {
            $this->redirect('/admin');
        }
        $this->renderBare('auth/login.php', ['pageTitle' => 'Login - GOF']);
    }

    public function login(): void
    {
        $this->validateCsrf();
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $this->flash('error', 'Please enter email and password.');
            $this->redirect('/login');
        }

        $user = $this->authService->attempt($email, $password);

        if ($user) {
            $this->authService->loginUser($user);
            $intended = $_SESSION['_intended'] ?? '/admin';
            unset($_SESSION['_intended']);
            $this->redirect($intended);
        } else {
            $this->flash('error', 'Invalid email or password, or account is locked/inactive.');
            $this->redirect('/login');
        }
    }

    public function logout(): void
    {
        $this->authService->logout();
        $this->flash('success', 'You have been logged out successfully.');
        $this->redirect('/login');
    }

    public function showForgotPassword(): void
    {
        $this->renderBare('auth/forgot_password.php', ['pageTitle' => 'Forgot Password - GOF']);
    }

    public function forgotPassword(): void
    {
        $this->validateCsrf();
        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $this->flash('error', 'Please enter your email address.');
            $this->redirect('/forgot-password');
        }

        $token = $this->authService->createPasswordResetToken($email);

        if ($token) {
            // In production, send email with reset link
            // $resetLink = "http://yourdomain.com/reset-password?token=" . $token;
            // Send email using EmailService
            
            // For now, show the token in development
            if (getenv('APP_ENV') === 'development') {
                $this->flash('info', "Reset token (development): $token");
            }
        }

        // Always show success message to prevent email enumeration
        $this->flash('success', 'If an account exists for that email, you will receive reset instructions.');
        $this->redirect('/forgot-password');
    }

    public function showResetPassword(): void
    {
        $token = $_GET['token'] ?? '';
        
        if (empty($token)) {
            $this->flash('error', 'Invalid reset token.');
            $this->redirect('/forgot-password');
        }

        $email = $this->authService->validatePasswordResetToken($token);
        
        if (!$email) {
            $this->flash('error', 'Invalid or expired reset token.');
            $this->redirect('/forgot-password');
        }

        $this->renderBare('auth/reset_password.php', [
            'pageTitle' => 'Reset Password - GOF',
            'token' => $token
        ]);
    }

    public function resetPassword(): void
    {
        $this->validateCsrf();
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($token) || empty($password)) {
            $this->flash('error', 'Please fill in all fields.');
            $this->redirect('/reset-password?token=' . $token);
        }

        if ($password !== $confirmPassword) {
            $this->flash('error', 'Passwords do not match.');
            $this->redirect('/reset-password?token=' . $token);
        }

        if (strlen($password) < 8) {
            $this->flash('error', 'Password must be at least 8 characters long.');
            $this->redirect('/reset-password?token=' . $token);
        }

        $result = $this->authService->resetPassword($token, $password);

        if ($result) {
            $this->flash('success', 'Your password has been reset successfully. Please login with your new password.');
            $this->redirect('/login');
        } else {
            $this->flash('error', 'Invalid or expired reset token.');
            $this->redirect('/forgot-password');
        }
    }
}
