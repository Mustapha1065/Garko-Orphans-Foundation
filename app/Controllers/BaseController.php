<?php
namespace GOF\Controllers;

abstract class BaseController
{
    protected function render(string $view, array $data = []): void
    {
        if(session_status() === PHP_SESSION_NONE) session_start();
        extract($data);
        ob_start();
        $viewFile = __DIR__ . '/../views/' . $view;
        if(!file_exists($viewFile)) {
            http_response_code(404);
            include __DIR__ . '/../views/errors/404.php';
            exit;
        }
        include $viewFile;
        $content = ob_get_clean();
        include __DIR__ . '/../views/admin/layout.php';
    }

    protected function renderPublic(string $view, array $data = []): void
    {
        if(session_status() === PHP_SESSION_NONE) session_start();
        extract($data);
        ob_start();
        include __DIR__ . '/../views/' . $view;
        $content = ob_get_clean();
        include __DIR__ . '/../views/public/layout.php';
    }

    /**
     * Render a view that is already a complete, standalone HTML document
     * (e.g. a printable ID card or the public QR verification page) without
     * wrapping it in the admin or public layout.
     */
    protected function renderBare(string $view, array $data = []): void
    {
        if(session_status() === PHP_SESSION_NONE) session_start();
        extract($data);
        $viewFile = __DIR__ . '/../views/' . $view;
        if(!file_exists($viewFile)) {
            http_response_code(404);
            include __DIR__ . '/../views/errors/404.php';
            exit;
        }
        include $viewFile;
    }

    protected function redirect(string $url): void
    {
        // Get the base path from the app configuration or server
        $baseUrl = 'http://localhost/GOF3-light/public';
        
        // Try to load from config if available
        $configPath = __DIR__ . '/../../config/config.php';
        if (file_exists($configPath)) {
            $config = require $configPath;
            $baseUrl = $config['app']['url'] ?? $baseUrl;
        }
        
        // If URL starts with http, redirect as-is (full URL)
        if (strpos($url, 'http') === 0) {
            $redirectUrl = $url;
        } else {
            // Relative URL, prepend the base URL
            $baseUrl = rtrim($baseUrl, '/');
            $url = ltrim($url, '/');
            $redirectUrl = $baseUrl . '/' . $url;
        }
        
        header('Location: ' . $redirectUrl);
        exit;
    }

    protected function currentUser(): ?array
    {
        if(session_status() === PHP_SESSION_NONE) session_start();
        return $_SESSION['user'] ?? null;
    }

    protected function requireAuth(): void
    {
        if(!$this->currentUser()) {
            $_SESSION['_intended'] = $_SERVER['REQUEST_URI'] ?? '/admin';
            $this->redirect('/login');
        }
    }

    protected function requirePermission(string $perm): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        // Super admin always passes
        if(($user['account_type'] ?? '') === 'super_admin') return;
        $rbac = new \GOF\Services\RBACService();
        if(!$rbac->userHasPermission((int)$user['id'], $perm)) {
            http_response_code(403);
            include __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }

    protected function json(mixed $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function validateCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? null;
        if(!\GOF\Helpers\Csrf::validate($token)) {
            $_SESSION['flash_error'] = 'Invalid form submission. Please try again.';
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
        }
    }

    protected function resolvePersonName(string $type, int $id): string
    {
        $db = \GOF\Helpers\Database::get();
        if ($type === 'beneficiary') {
            $stmt = $db->prepare("SELECT full_name FROM beneficiaries WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch();
            return $res ? $res['full_name'] : 'Unknown';
        } elseif ($type === 'staff') {
            $stmt = $db->prepare("SELECT full_name FROM staff WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch();
            return $res ? $res['full_name'] : 'Unknown';
        } elseif ($type === 'donor') {
            $stmt = $db->prepare("SELECT name, organization_name FROM donors WHERE id = ?");
            $stmt->execute([$id]);
            $res = $stmt->fetch();
            if (!$res) return 'Unknown';
            return $res['organization_name'] ?: $res['name'];
        }
        return 'Unknown';
    }

    protected function resolveGofId(string $type, int $id): string
    {
        $db = \GOF\Helpers\Database::get();
        $table = ['beneficiary' => 'beneficiaries', 'staff' => 'staff', 'donor' => 'donors'][$type] ?? null;
        if (!$table) return '';
        $stmt = $db->prepare("SELECT gof_id FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        return (string) ($stmt->fetchColumn() ?: '');
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash_' . $type] = $message;
    }
}
