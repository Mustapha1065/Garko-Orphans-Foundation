<?php
namespace GOF\Services;

use GOF\Helpers\Database;
use PDO;

class NotificationService {
    private PDO $db;

    public function __construct() {
        $this->db = Database::get();
    }

    public function send(int $userId, string $type, string $title, string $message, ?string $link = null): void {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, type, title, message, link, is_read, created_at)
            VALUES (:user_id, :type, :title, :message, :link, 0, NOW())
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':type' => $type,
            ':title' => $title,
            ':message' => $message,
            ':link' => $link
        ]);
    }

    public function getUnreadCount(int $userId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :uid AND is_read = 0");
        $stmt->execute([':uid' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public function getRecent(int $userId, int $limit = 10): array {
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim");
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function markRead(int $notifId, int $userId): void {
        $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :uid")
            ->execute([':id' => $notifId, ':uid' => $userId]);
    }

    public function markAllRead(int $userId): void {
        $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid AND is_read = 0")
            ->execute([':uid' => $userId]);
    }

    public function getAll(int $userId, int $page = 1, int $perPage = 20): array {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :lim OFFSET :off");
        $stmt->bindValue(':uid', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
