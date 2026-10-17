<?php
namespace GOF\Services;

use GOF\Helpers\Database;
use PDO;

class RBACService {
    private PDO $db;
    private static array $cache = [];

    public function __construct() {
        $this->db = Database::get();
    }

    public function userHasPermission(int $userId, string $slug): bool {
        if (isset(self::$cache[$userId][$slug])) {
            return self::$cache[$userId][$slug];
        }

        // Check if user is super admin
        $stmt = $this->db->prepare("SELECT account_type FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        if ($stmt->fetchColumn() === 'super_admin') {
            self::$cache[$userId][$slug] = true;
            return true;
        }

        // 1. Check user_permissions explicitly
        $stmt = $this->db->prepare("
            SELECT up.granted 
            FROM user_permissions up
            JOIN permissions p ON p.id = up.permission_id
            WHERE up.user_id = :user_id AND p.slug = :slug
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId, ':slug' => $slug]);
        $override = $stmt->fetchColumn();
        
        if ($override !== false) {
            $hasPerm = (bool)$override;
            self::$cache[$userId][$slug] = $hasPerm;
            return $hasPerm;
        }

        // 2. Check via roles
        $stmt = $this->db->prepare("
            SELECT 1 
            FROM user_roles ur
            JOIN role_permissions rp ON rp.role_id = ur.role_id
            JOIN permissions p ON p.id = rp.permission_id
            WHERE ur.user_id = :user_id AND p.slug = :slug
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId, ':slug' => $slug]);
        $hasPerm = (bool)$stmt->fetchColumn();
        
        self::$cache[$userId][$slug] = $hasPerm;
        return $hasPerm;
    }

    public function getUserPermissions(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT p.slug
            FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            JOIN user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = :uid
            UNION
            SELECT p.slug
            FROM permissions p
            JOIN user_permissions up ON up.permission_id = p.id
            WHERE up.user_id = :uid2 AND up.granted = 1
        ");
        $stmt->execute([':uid' => $userId, ':uid2' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getUserRole(int $userId): ?array {
        $stmt = $this->db->prepare("
            SELECT r.* FROM roles r
            JOIN user_roles ur ON ur.role_id = r.id
            WHERE ur.user_id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function getRoles(): array {
        return $this->db->query("SELECT * FROM roles ORDER BY name")->fetchAll();
    }

    public function getPermissions(): array {
        $perms = $this->db->query("SELECT * FROM permissions ORDER BY module, name")->fetchAll();
        $grouped = [];
        foreach ($perms as $p) {
            $grouped[$p['module']][] = $p;
        }
        return $grouped;
    }

    public function assignRole(int $userId, int $roleId, int $assignedBy): void {
        $stmt = $this->db->prepare("INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by) VALUES (:uid, :rid, :ab)");
        $stmt->execute([':uid' => $userId, ':rid' => $roleId, ':ab' => $assignedBy]);
    }

    public function revokeRole(int $userId, int $roleId): void {
        $this->db->prepare("DELETE FROM user_roles WHERE user_id = :uid AND role_id = :rid")
            ->execute([':uid' => $userId, ':rid' => $roleId]);
    }

    public function grantPermission(int $userId, int $permId, int $grantedBy): void {
        $stmt = $this->db->prepare("
            INSERT INTO user_permissions (user_id, permission_id, granted, granted_by) 
            VALUES (:uid, :pid, 1, :gb)
            ON DUPLICATE KEY UPDATE granted = 1
        ");
        $stmt->execute([':uid' => $userId, ':pid' => $permId, ':gb' => $grantedBy]);
    }

    public function revokePermissionOverride(int $userId, int $permId): void {
        $this->db->prepare("DELETE FROM user_permissions WHERE user_id = :uid AND permission_id = :pid")
            ->execute([':uid' => $userId, ':pid' => $permId]);
    }
}
