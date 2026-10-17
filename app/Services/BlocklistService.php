<?php
namespace GOF\Services;

use GOF\Helpers\Database;

class BlocklistService
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::get();
    }

    public function isBlocked(?string $ip): bool
    {
        if (!$ip) return false;
        $stmt = $this->db->prepare(
            'SELECT id FROM blocked_ips WHERE ip_address = :ip AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1'
        );
        $stmt->execute([':ip' => $ip]);
        return (bool)$stmt->fetch();
    }

    public function block(string $ip, ?int $byUserId = null, ?string $reason = null, ?string $expiresAt = null): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO blocked_ips (ip_address, reason, blocked_by, expires_at, created_at, updated_at) VALUES (:ip, :reason, :by, :expires_at, NOW(), NOW())'
        );
        return $stmt->execute([
            ':ip' => $ip,
            ':reason' => $reason,
            ':by' => $byUserId,
            ':expires_at' => $expiresAt,
        ]);
    }

    public function unblock(string $ip): bool
    {
        $stmt = $this->db->prepare('DELETE FROM blocked_ips WHERE ip_address = :ip');
        return $stmt->execute([':ip' => $ip]);
    }

    public function listRecent(int $limit = 100): array
    {
        $stmt = $this->db->prepare('SELECT * FROM blocked_ips ORDER BY created_at DESC LIMIT :lim');
        $stmt->bindValue(':lim', (int)$limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
