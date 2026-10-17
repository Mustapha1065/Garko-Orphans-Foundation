<?php
namespace GOF\Services;

use GOF\Helpers\Database;
use PDO;

class SettingsService {
    private PDO $db;
    private static array $cache = [];

    public function __construct() {
        $this->db = Database::get();
        if (empty(self::$cache)) {
            $this->loadAll();
        }
    }

    public function get(string $key, mixed $default = null): mixed {
        return self::$cache[$key] ?? $default;
    }

    public function set(string $key, mixed $value, int $updatedBy, string $group = 'general'): void {
        $stmt = $this->db->prepare("
            INSERT INTO settings (group_name, key_name, value, updated_by, created_at, updated_at)
            VALUES (:group, :key, :val, :ub, NOW(), NOW())
            ON DUPLICATE KEY UPDATE value = :val2, updated_by = :ub2, updated_at = NOW()
        ");
        $strVal = (string)$value;
        $stmt->execute([
            ':group' => $group,
            ':key' => $key,
            ':val' => $strVal,
            ':ub' => $updatedBy,
            ':val2' => $strVal,
            ':ub2' => $updatedBy
        ]);
        self::$cache[$key] = $strVal;
    }

    public function getGroup(string $group): array {
        $stmt = $this->db->prepare("SELECT key_name, value FROM settings WHERE group_name = :group");
        $stmt->execute([':group' => $group]);
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[$row['key_name']] = $row['value'];
        }
        return $results;
    }

    public function setMany(array $keyValuePairs, string $group, int $updatedBy): void {
        foreach ($keyValuePairs as $k => $v) {
            $this->set($k, $v, $updatedBy, $group);
        }
    }

    public function loadAll(): void {
        $stmt = $this->db->query("SELECT key_name, value FROM settings");
        while ($row = $stmt->fetch()) {
            self::$cache[$row['key_name']] = $row['value'];
        }
    }
}
