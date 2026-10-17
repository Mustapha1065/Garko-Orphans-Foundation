<?php
namespace GOF\Models;

use PDO;

class Staff extends BaseModel {
    protected string $table = 'staff';

    public function allWithUser(int $limit = 50, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT s.*, u.email, CONCAT(u.first_name, ' ', u.last_name) as name, u.status as user_status
            FROM staff s
            JOIN users u ON s.user_id = u.id
            ORDER BY s.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findWithUser(int $staffId): ?array {
        $stmt = $this->db->prepare("
            SELECT s.*, u.email, CONCAT(u.first_name, ' ', u.last_name) as name, u.status as user_status
            FROM staff s
            JOIN users u ON s.user_id = u.id
            WHERE s.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $staffId]);
        return $stmt->fetch() ?: null;
    }

    public function count(): int {
        return parent::count();
    }
}
