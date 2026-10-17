<?php
namespace GOF\Services;

use GOF\Models\User;
use GOF\Helpers\Database;
use PDO;

class AuthService {
    private PDO $db;
    private User $userModel;

    public function __construct() {
        $this->db = Database::get();
        $this->userModel = new User();
    }

    public function attempt(string $email, string $password): ?array {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
        if ($this->isBlocked($email, $ip)) {
            return null;
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            $this->recordAttempt($email, $ip, false);
            return null;
        }

        if ($user['status'] !== 'active') {
            $this->recordAttempt($email, $ip, false);
            return null;
        }

        if ($this->userModel->isLocked($user['id'])) {
            return null;
        }

        if (password_verify($password, $user['password_hash'])) {
            $this->recordAttempt($email, $ip, true);
            $this->userModel->resetFailedAttempts($user['id']);
            $this->userModel->updateLastLogin($user['id']);
            
            // Load user roles and permissions
            $user['roles'] = $this->userModel->getRoles($user['id']);
            $user['permissions'] = array_column($this->userModel->getPermissions($user['id']), 'slug');
            
            unset($user['password_hash']);
            return $user;
        }

        $this->userModel->incrementFailedAttempts($user['id']);
        
        // Lock account after 5 failed attempts
        if ($user['failed_login_attempts'] >= 4) {
            $this->userModel->lockAccount($user['id']);
        }
        
        $this->recordAttempt($email, $ip, false);
        return null;
    }

    public function loginUser(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        $_SESSION['_session_start'] = time();
        $_SESSION['_last_activity'] = time();
    }

    public function logout(): void {
        unset($_SESSION['user']);
        unset($_SESSION['_session_start']);
        unset($_SESSION['_last_activity']);
        session_destroy();
        session_start();
        session_regenerate_id(true);
    }

    public function user(): ?array {
        if (!isset($_SESSION['user'])) {
            return null;
        }
        
        // Check session timeout (30 minutes of inactivity)
        $config = require __DIR__ . '/../../config/config.php';
        $lifetime = $config['session']['lifetime'];
        
        if (isset($_SESSION['_last_activity']) && (time() - $_SESSION['_last_activity']) > $lifetime) {
            $this->logout();
            return null;
        }
        
        $_SESSION['_last_activity'] = time();
        return $_SESSION['user'];
    }

    public function check(): bool {
        return $this->user() !== null;
    }

    public function id(): ?int {
        return $this->user()['id'] ?? null;
    }

    public function hasPermission(string $permission): bool {
        $user = $this->user();
        if (!$user) {
            return false;
        }
        
        // Super admin has all permissions
        if (($user['account_type'] ?? '') === 'super_admin') {
            return true;
        }
        
        return in_array($permission, $user['permissions'] ?? []);
    }

    public function isBlocked(string $email, string $ip): bool {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM login_attempts 
            WHERE (email = :email OR ip_address = :ip) AND successful = 0 AND created_at > (NOW() - INTERVAL 15 MINUTE)
        ");
        $stmt->execute([':email' => $email, ':ip' => $ip]);
        return (int)$stmt->fetchColumn() >= 5;
    }

    public function recordAttempt(?string $email, string $ip, bool $success): void {
        $stmt = $this->db->prepare("INSERT INTO login_attempts (email, ip_address, successful, user_agent, created_at) VALUES (:email, :ip, :success, :user_agent, NOW())");
        $stmt->execute([
            ':email' => $email,
            ':ip' => $ip,
            ':success' => $success ? 1 : 0,
            ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    public function createPasswordResetToken(string $email): ?string {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return null;
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->db->prepare("
            INSERT INTO password_resets (email, token, created_at, expires_at)
            VALUES (:email, :token, NOW(), :expires)
        ");
        $stmt->execute([
            ':email' => $email,
            ':token' => $token,
            ':expires' => $expires
        ]);

        return $token;
    }

    public function validatePasswordResetToken(string $token): ?string {
        $stmt = $this->db->prepare("
            SELECT email FROM password_resets
            WHERE token = :token AND expires_at > NOW()
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([':token' => $token]);
        return $stmt->fetchColumn() ?: null;
    }

    public function resetPassword(string $token, string $newPassword): bool {
        $email = $this->validatePasswordResetToken($token);
        if (!$email) {
            return false;
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return false;
        }

        $result = $this->userModel->updatePassword($user['id'], $newPassword);
        
        if ($result) {
            // Delete used tokens
            $stmt = $this->db->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute([':email' => $email]);
        }

        return $result;
    }

    public function cleanupOldTokens(): void {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE expires_at < NOW()");
        $stmt->execute();
    }

    public function cleanupOldLoginAttempts(): void {
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE created_at < (NOW() - INTERVAL 30 DAY)");
        $stmt->execute();
    }
}
