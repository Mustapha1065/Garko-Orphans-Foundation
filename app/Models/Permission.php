<?php
namespace GOF\Models;

use PDO;

class Permission extends BaseModel {
    protected string $table = 'permissions';

    public function getByModule(string $module): array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE module = :module ORDER BY name");
        $stmt->execute([':module' => $module]);
        return $stmt->fetchAll();
    }

    public function getAllGroupedByModule(): array {
        $stmt = $this->db->query("SELECT * FROM {$this->table} ORDER BY module, name");
        $permissions = $stmt->fetchAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $module = $permission['module'] ?? 'general';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $permission;
        }
        
        return $grouped;
    }
}
