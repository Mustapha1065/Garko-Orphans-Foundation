<?php
namespace GOF\Models;

use PDO;

class Role extends BaseModel {
    protected string $table = 'roles';

    public function getPermissions(int $roleId): array {
        $stmt = $this->db->prepare("
            SELECT p.* FROM permissions p
            JOIN role_permissions rp ON rp.permission_id = p.id
            WHERE rp.role_id = :role_id
        ");
        $stmt->execute([':role_id' => $roleId]);
        return $stmt->fetchAll();
    }

    public function assignPermission(int $roleId, int $permissionId): bool {
        $stmt = $this->db->prepare("
            INSERT IGNORE INTO role_permissions (role_id, permission_id)
            VALUES (:role_id, :permission_id)
        ");
        return $stmt->execute([':role_id' => $roleId, ':permission_id' => $permissionId]);
    }

    public function revokePermission(int $roleId, int $permissionId): bool {
        $stmt = $this->db->prepare("
            DELETE FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id
        ");
        return $stmt->execute([':role_id' => $roleId, ':permission_id' => $permissionId]);
    }

    public function syncPermissions(int $roleId, array $permissionIds): bool {
        $this->db->beginTransaction();
        try {
            // Remove all existing permissions
            $this->db->prepare("DELETE FROM role_permissions WHERE role_id = :role_id")
                ->execute([':role_id' => $roleId]);
            
            // Add new permissions
            if (!empty($permissionIds)) {
                $stmt = $this->db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)");
                foreach ($permissionIds as $permissionId) {
                    $stmt->execute([':role_id' => $roleId, ':permission_id' => $permissionId]);
                }
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
