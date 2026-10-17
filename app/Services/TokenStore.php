<?php
namespace GOF\Services;

use GOF\Helpers\Database;

class TokenStore
{
    protected $db;
    public function __construct()
    {
        $this->db = Database::get();
    }

    public function save(string $token, int $idCardId, ?int $issuedBy, ?int $expiresAt = null): bool
    {
        $stmt = $this->db->prepare('INSERT INTO id_card_tokens (id_card_id, token, issued_by, issued_at, expires_at) VALUES (:cid, :t, :by, NOW(), :exp)');
        return $stmt->execute([':cid'=>$idCardId, ':t'=>$token, ':by'=>$issuedBy, ':exp'=>$expiresAt ? date('Y-m-d H:i:s', $expiresAt) : null]);
    }

    public function isRevoked(string $token): bool
    {
        $stmt = $this->db->prepare('SELECT revoked FROM id_card_tokens WHERE token = :t LIMIT 1');
        $stmt->execute([':t'=>$token]);
        $r = $stmt->fetch();
        return !$r ? true : (bool)$r['revoked'];
    }

    public function revoke(string $token, ?int $byUserId = null, ?string $reason = null): bool
    {
        $stmt = $this->db->prepare('UPDATE id_card_tokens SET revoked = 1, revoked_at = NOW(), revoked_by = :by, revoke_reason = :reason WHERE token = :t');
        return $stmt->execute([':by'=>$byUserId, ':reason'=>$reason, ':t'=>$token]);
    }

    public function listAll(): array
    {
        $stmt = $this->db->query('SELECT t.*, ic.card_no, ic.person_type, ic.person_id, u.email as issued_by_email FROM id_card_tokens t LEFT JOIN id_cards ic ON t.id_card_id = ic.id LEFT JOIN users u ON t.issued_by = u.id ORDER BY t.issued_at DESC');
        return $stmt->fetchAll();
    }
}
