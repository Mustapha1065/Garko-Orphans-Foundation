<?php
namespace GOF\Models;

use PDO;

class User extends BaseModel {
    protected string $table = 'users';

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        return parent::create($data);
    }

    public function updatePassword(int $id, string $password): bool {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password_hash = :hash, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    public function incrementFailedAttempts(int $id): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET failed_login_attempts = failed_login_attempts + 1 WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function resetFailedAttempts(int $id): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET failed_login_attempts = 0, locked_until = NULL WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function lockAccount(int $id, int $minutes = 15): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET locked_until = DATE_ADD(NOW(), INTERVAL :min MINUTE) WHERE id = :id");
        return $stmt->execute([':min' => $minutes, ':id' => $id]);
    }

    public function isLocked(int $id): bool {
        $stmt = $this->db->prepare("SELECT locked_until FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $lockedUntil = $stmt->fetchColumn();
        return $lockedUntil && strtotime($lockedUntil) > time();
    }

    public function updateLastLogin(int $id): bool {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET last_login_at = NOW() WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function getRoles(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT r.* FROM roles r
            JOIN user_roles ur ON ur.role_id = r.id
            WHERE ur.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getPermissions(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT DISTINCT p.* FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = :user_id
            UNION
            SELECT p.* FROM permissions p
            JOIN user_permissions up ON up.permission_id = p.id
            WHERE up.user_id = :user_id2 AND up.granted = 1
        ");
        $stmt->execute([':user_id' => $userId, ':user_id2' => $userId]);
        return $stmt->fetchAll();
    }

    public function hasPermission(int $userId, string $permissionSlug): bool {
        // Check if super admin
        $stmt = $this->db->prepare("SELECT account_type FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        if ($stmt->fetchColumn() === 'super_admin') {
            return true;
        }

        // Check user_permissions override
        $stmt = $this->db->prepare("
            SELECT up.granted FROM user_permissions up
            JOIN permissions p ON p.id = up.permission_id
            WHERE up.user_id = :user_id AND p.slug = :slug
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId, ':slug' => $permissionSlug]);
        $override = $stmt->fetchColumn();
        
        if ($override !== false) {
            return (bool)$override;
        }

        // Check via roles
        $stmt = $this->db->prepare("
            SELECT 1 FROM user_roles ur
            JOIN role_permissions rp ON rp.role_id = ur.role_id
            JOIN permissions p ON p.id = rp.permission_id
            WHERE ur.user_id = :user_id AND p.slug = :slug
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId, ':slug' => $permissionSlug]);
        return (bool)$stmt->fetchColumn();
    }
}
