<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\BlocklistService;
use GOF\Services\VerificationLogService;

class VerifyController extends BaseController {
    public function verify() {
        $token = $_GET['token'] ?? ($_GET['st'] ?? '');
        $db = Database::get();
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        $blocklist = new BlocklistService();
        $verificationLog = new VerificationLogService();

        if ($blocklist->isBlocked($ip)) {
            die("Access Denied");
        }

        $stmt = $db->prepare(
            "SELECT c.*, t.id as token_id, t.revoked as token_revoked, t.expires_at as token_expires_at
             FROM id_cards c
             JOIN id_card_tokens t ON c.id = t.id_card_id
             WHERE t.token = ?"
        );
        $stmt->execute([$token]);
        $card = $stmt->fetch();

        $status = 'INVALID';
        if ($card) {
            $tokenExpired = $card['token_expires_at'] && strtotime($card['token_expires_at']) < time();
            $cardExpired = $card['expiry_date'] && strtotime($card['expiry_date']) < time();

            if ($card['status'] === 'active' && !$card['token_revoked'] && !$tokenExpired && !$cardExpired) {
                $status = 'VERIFIED';
            } elseif ($card['status'] === 'revoked' || $card['token_revoked']) {
                $status = 'REVOKED';
            } else {
                $status = 'EXPIRED';
            }

            $verificationLog->logAttempt($ip, (int)$card['id'], (int)$card['token_id'], $status, $userAgent);

            $card['person_name'] = $this->resolvePersonName($card['person_type'], (int)$card['person_id']);
            $card['gof_id'] = $this->resolveGofId($card['person_type'], (int)$card['person_id']);
        } else {
            $verificationLog->logAttempt($ip, null, null, 'INVALID', $userAgent);
        }

        $this->renderBare('verify/index.php', compact('card', 'status'));
    }
}
