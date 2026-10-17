<?php
namespace GOF\Models;

use PDO;

class Event extends BaseModel {
    protected string $table = 'events';

    public function getUpcoming(int $limit = 5): array {
        $stmt = $this->db->prepare("
            SELECT * FROM events 
            WHERE event_date >= CURDATE() AND is_public = 1 
            ORDER BY event_date ASC 
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPublic(int $limit = 20): array {
        $stmt = $this->db->prepare("
            SELECT * FROM events 
            WHERE is_public = 1 
            ORDER BY event_date DESC 
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(): int {
        return parent::count();
    }
}
