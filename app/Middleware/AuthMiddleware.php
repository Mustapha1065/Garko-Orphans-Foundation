<?php
namespace GOF\Middleware;

use GOF\Services\AuthService;

class AuthMiddleware {
    private AuthService $authService;

    public function __construct() {
        $this->authService = new AuthService();
    }

    public function handle($param = null): void {
        if (!$this->authService->check()) {
            $_SESSION['_intended'] = $_SERVER['REQUEST_URI'] ?? '/admin';
            header('Location: /login');
            exit;
        }

        $user = $this->authService->user();
        if (($user['status'] ?? '') !== 'active') {
            $this->authService->logout();
            header('Location: /login');
            exit;
        }
    }
}
