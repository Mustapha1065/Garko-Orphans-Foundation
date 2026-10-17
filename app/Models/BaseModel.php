<?php
namespace GOF\Models;

use GOF\Helpers\Database;
use PDO;

abstract class BaseModel {
    protected PDO $db;
    protected string $table;
    
    public function __construct() {
        $this->db = Database::get();
    }
    
    public function find(int $id): ?array {
        $s = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $s->execute([':id' => $id]);
        return $s->fetch() ?: null;
    }
    
    public function all(int $limit = 100, int $offset = 0): array {
        $s = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT :lim OFFSET :off");
        $s->bindValue(':lim', $limit, PDO::PARAM_INT);
        $s->bindValue(':off', $offset, PDO::PARAM_INT);
        $s->execute();
        return $s->fetchAll();
    }
    
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
    
    public function create(array $data): int {
        $cols = implode(',', array_keys($data));
        $placeholders = implode(',', array_map(fn($k) => ":$k", array_keys($data)));
        $params = [];
        foreach ($data as $k => $v) {
            $params[":$k"] = $v;
        }
        $s = $this->db->prepare("INSERT INTO {$this->table} ($cols) VALUES ($placeholders)");
        $s->execute($params);
        return (int)$this->db->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $sets = implode(',', array_map(fn($k) => "$k=:$k", array_keys($data)));
        $params = [':id' => $id];
        foreach ($data as $k => $v) {
            $params[":$k"] = $v;
        }
        $s = $this->db->prepare("UPDATE {$this->table} SET $sets, updated_at = NOW() WHERE id = :id");
        return $s->execute($params);
    }

    public function delete(int $id): bool {
        $s = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $s->execute([':id' => $id]);
    }
}
