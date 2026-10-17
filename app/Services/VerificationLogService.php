<?php
namespace GOF\Services;

use GOF\Helpers\Database;

class VerificationLogService
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::get();
    }

    public function logAttempt(?string $ip, ?int $idCardId, ?int $tokenId, string $result, ?string $userAgent = null): bool
    {
        $stmt = $this->db->prepare(
            'INSERT INTO verification_attempts (id_card_id, token_id, ip_address, user_agent, result, created_at) VALUES (:id_card_id, :token_id, :ip, :user_agent, :result, NOW())'
        );
        return $stmt->execute([
            ':id_card_id' => $idCardId,
            ':token_id' => $tokenId,
            ':ip' => $ip,
            ':user_agent' => $userAgent,
            ':result' => $result,
        ]);
    }

    public function countRecentByIp(?string $ip, int $seconds): int
    {
        $since = date('Y-m-d H:i:s', time() - $seconds);
        $stmt = $this->db->prepare('SELECT COUNT(*) as c FROM verification_attempts WHERE ip_address = :ip AND created_at >= :since');
        $stmt->execute([':ip' => $ip, ':since' => $since]);
        $r = $stmt->fetch();
        return (int)($r['c'] ?? 0);
    }

    public function countRecentByToken(int $tokenId, int $seconds): int
    {
        $since = date('Y-m-d H:i:s', time() - $seconds);
        $stmt = $this->db->prepare('SELECT COUNT(*) as c FROM verification_attempts WHERE token_id = :token_id AND created_at >= :since');
        $stmt->execute([':token_id' => $tokenId, ':since' => $since]);
        $r = $stmt->fetch();
        return (int)($r['c'] ?? 0);
    }
}
