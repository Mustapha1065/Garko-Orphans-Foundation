<?php
namespace GOF\Models;

use PDO;

class News extends BaseModel {
    protected string $table = 'news';

    public function getPublished(int $limit = 10, int $offset = 0): array {
        $stmt = $this->db->prepare("
            SELECT * FROM news 
            WHERE status = 'published' 
            ORDER BY published_at DESC, created_at DESC 
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM news WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function count(array $filters = []): int {
        if (!empty($filters['status'])) {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM news WHERE status = :status");
            $stmt->execute([':status' => $filters['status']]);
            return (int)$stmt->fetchColumn();
        }
        return parent::count();
    }
}
