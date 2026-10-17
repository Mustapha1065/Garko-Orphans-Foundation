<?php
namespace GOF\Middleware;

use GOF\Services\AuthService;

class PermissionMiddleware {
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

        if (!$param) {
            return;
        }

        if (!$this->authService->hasPermission($param)) {
            http_response_code(403);
            if (file_exists(__DIR__ . '/../views/errors/403.php')) {
                include __DIR__ . '/../views/errors/403.php';
            } else {
                echo "403 Forbidden: You do not have permission to access this resource.";
            }
            exit;
        }
    }
}
