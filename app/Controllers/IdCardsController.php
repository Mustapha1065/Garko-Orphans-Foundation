<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\BlocklistService;
use GOF\Services\QrService;

class IdCardsController extends BaseController
{
    /**
     * Display all ID cards.
     */
    public function index()
    {
        $this->requirePermission('view_id_cards');

        $db = Database::get();

        $page = isset($_GET['page'])
            ? max(1, (int) $_GET['page'])
            : 1;

        $limit = 20;
        $offset = ($page - 1) * $limit;

        $statusFilter = trim($_GET['status'] ?? '');

        $query = "
            SELECT
                i.*,
                u.first_name AS gen_first_name,
                u.last_name AS gen_last_name
            FROM id_cards i
            LEFT JOIN users u ON i.generated_by = u.id
            WHERE 1=1
        ";

        $params = [];

        if ($statusFilter !== '') {
            $query .= " AND i.status = :status";
            $params['status'] = $statusFilter;
        }

        $query .= "
            ORDER BY i.created_at DESC
            LIMIT $limit OFFSET $offset
        ";

        $stmt = $db->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();

        $idCards = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($idCards as &$card) {
            $card['person_name'] = $this->resolvePersonName(
                (string) $card['person_type'],
                (int) $card['person_id']
            );

            $card['person_gof_id'] = $this->resolveGofId(
                (string) $card['person_type'],
                (int) $card['person_id']
            );
        }

        unset($card);

        $countQuery = "
            SELECT COUNT(*)
            FROM id_cards
            WHERE 1=1
        ";

        $countParams = [];

        if ($statusFilter !== '') {
            $countQuery .= " AND status = :status";
            $countParams['status'] = $statusFilter;
        }

        $countStmt = $db->prepare($countQuery);

        foreach ($countParams as $key => $value) {
            $countStmt->bindValue(":$key", $value);
        }

        $countStmt->execute();

        $total = (int) $countStmt->fetchColumn();

        $totalPages = $total > 0
            ? (int) ceil($total / $limit)
            : 1;

        $this->render(
            'admin/idcards/index.php',
            compact(
                'idCards',
                'page',
                'totalPages',
                'statusFilter'
            )
        );
    }


    /**
     * Show one ID card.
     */
    public function show($id)
    {
        $this->requirePermission('view_id_cards');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT
                i.*,
                u.first_name AS gen_first_name,
                u.last_name AS gen_last_name
            FROM id_cards i
            LEFT JOIN users u ON i.generated_by = u.id
            WHERE i.id = ?
            LIMIT 1
        ");

        $stmt->execute([(int) $id]);

        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            header('Location: ' . url('/admin/idcards'));
            exit;
        }

        $card['person_name'] = $this->resolvePersonName(
            (string) $card['person_type'],
            (int) $card['person_id']
        );

        $card['gof_id'] = $this->resolveGofId(
            (string) $card['person_type'],
            (int) $card['person_id']
        );

        $stmt = $db->prepare("
            SELECT
                h.*,
                u.first_name,
                u.last_name
            FROM id_card_history h
            LEFT JOIN users u ON h.user_id = u.id
            WHERE h.id_card_id = ?
            ORDER BY h.created_at DESC
        ");

        $stmt->execute([(int) $id]);

        $history = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/idcards/show.php',
            compact('card', 'history')
        );
    }


    /**
     * Generate a new ID card.
     *
     * person_id MUST refer to:
     * beneficiaries.id
     * staff.id
     * donors.id
     */
    public function generate()
    {
        $this->requirePermission('generate_id_cards');
        $this->validateCsrf();

        $type = trim($_POST['person_type'] ?? '');
        $submittedPersonId = (int) ($_POST['person_id'] ?? 0);

        $allowedTypes = [
            'beneficiary',
            'staff',
            'donor'
        ];

        if (
            !in_array($type, $allowedTypes, true) ||
            $submittedPersonId <= 0
        ) {
            $_SESSION['error'] = 'Invalid person type or ID.';

            header(
                'Location: ' . url('/admin/idcards')
            );

            exit;
        }

        $db = Database::get();

        /*
         * Convert an old users.id submission into
         * the correct beneficiary/staff table ID
         * when necessary.
         */
        $personId = $this->normalizePersonId(
            $type,
            $submittedPersonId
        );

        if ($personId <= 0) {
            $_SESSION['error'] =
                'The selected person could not be found.';

            header(
                'Location: ' . url('/admin/idcards')
            );

            exit;
        }

        /*
         * Confirm that the final ID belongs to
         * the requested person type.
         */
        $person = $this->getPerson(
            $type,
            $personId
        );

        if (!$person) {
            $_SESSION['error'] =
                'The selected person could not be found.';

            header(
                'Location: ' . url('/admin/idcards')
            );

            exit;
        }

        /*
         * Prevent two active cards for the same person.
         */
        $stmt = $db->prepare("
            SELECT id, card_no
            FROM id_cards
            WHERE person_type = ?
              AND person_id = ?
              AND status = 'active'
            LIMIT 1
        ");

        $stmt->execute([
            $type,
            $personId
        ]);

        $existingCard = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($existingCard) {
            $_SESSION['error'] =
                'This person already has an active ID card: ' .
                $existingCard['card_no'];

            header(
                'Location: ' .
                url('/admin/idcards/' . (int)$existingCard['id'])
            );

            exit;
        }

        /*
         * Card number.
         *
         * B = Beneficiary
         * S = Staff
         * D = Donor
         */
        $prefixMap = [
            'beneficiary' => 'B',
            'staff'       => 'S',
            'donor'       => 'D'
        ];

        $prefix = $prefixMap[$type];

        $cardNo = $prefix
            . '-' . date('Y')
            . '-' . str_pad(
                $personId,
                5,
                '0',
                STR_PAD_LEFT
            );

        /*
         * Make sure the card number itself is unique.
         */
        $baseCardNo = $cardNo;
        $counter = 1;

        while (true) {
            $stmt = $db->prepare("
                SELECT id
                FROM id_cards
                WHERE card_no = ?
                LIMIT 1
            ");

            $stmt->execute([$cardNo]);

            if (!$stmt->fetch()) {
                break;
            }

            $cardNo =
                $baseCardNo .
                '-' .
                $counter;

            $counter++;
        }

        /*
         * Create secure verification token.
         */
        $qrToken = bin2hex(random_bytes(16));

        $issueDate = date('Y-m-d');

        $expiryDate = date(
            'Y-m-d',
            strtotime('+5 years')
        );

        $currentUser = $this->currentUser();

        $generatedBy =
            isset($currentUser['id'])
                ? (int) $currentUser['id']
                : null;

        /*
         * Verification URL.
         */
        $verifyUrl = url(
            '/verify?token=' .
            urlencode($qrToken)
        );

        /*
         * QR directory.
         */
        $qrDir = __DIR__ .
            '/../../public/uploads/qrcodes';

        if (!is_dir($qrDir)) {
            if (!mkdir($qrDir, 0777, true) && !is_dir($qrDir)) {
                $_SESSION['error'] =
                    'Unable to create QR code directory.';

                header(
                    'Location: ' . url('/admin/idcards')
                );

                exit;
            }
        }

        $qrPath =
            $qrDir .
            '/' .
            $qrToken .
            '.png';

        /*
         * Generate QR BEFORE inserting the card.
         *
         * This prevents a database record from being
         * created when QR generation fails.
         */
        try {

            (new QrService())->generate(
                $verifyUrl,
                $qrPath
            );

        } catch (\Throwable $e) {

            $_SESSION['error'] =
                'ID card QR generation failed: ' .
                $e->getMessage();

            header(
                'Location: ' .
                url('/admin/idcards')
            );

            exit;
        }

        /*
         * Start database transaction.
         */
        $db->beginTransaction();

        try {

            /*
             * Insert ID card.
             */
            $stmt = $db->prepare("
                INSERT INTO id_cards
                (
                    card_no,
                    person_type,
                    person_id,
                    qr_token,
                    issue_date,
                    expiry_date,
                    status,
                    generated_by,
                    created_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    'active',
                    ?,
                    NOW()
                )
            ");

            $stmt->execute([
                $cardNo,
                $type,
                $personId,
                $qrToken,
                $issueDate,
                $expiryDate,
                $generatedBy
            ]);

            $id = (int) $db->lastInsertId();

            /*
             * Save verification token.
             */
            $stmt = $db->prepare("
                INSERT INTO id_card_tokens
                (
                    id_card_id,
                    token,
                    expires_at
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $id,
                $qrToken,
                date(
                    'Y-m-d H:i:s',
                    strtotime('+5 years')
                )
            ]);

            /*
             * Save history.
             */
            $stmt = $db->prepare("
                INSERT INTO id_card_history
                (
                    id_card_id,
                    action,
                    user_id,
                    created_at
                )
                VALUES (?, 'issued', ?, NOW())
            ");

            $stmt->execute([
                $id,
                $generatedBy
            ]);

            /*
             * Audit log.
             */
            $stmt = $db->prepare("
                INSERT INTO audit_logs
                (
                    user_id,
                    action,
                    module,
                    entity_id,
                    description,
                    ip_address,
                    created_at
                )
                VALUES
                (
                    ?,
                    'issue_id_card',
                    'id_cards',
                    ?,
                    'Generated new ID card',
                    ?,
                    NOW()
                )
            ");

            $stmt->execute([
                $generatedBy,
                $id,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);

            /*
             * Commit everything.
             */
            $db->commit();

        } catch (\Throwable $e) {

            /*
             * Undo database changes if anything failed.
             */
            if ($db->inTransaction()) {
                $db->rollBack();
            }

            /*
             * Remove QR file because no card exists.
             */
            if (file_exists($qrPath)) {
                @unlink($qrPath);
            }

            $_SESSION['error'] =
                'Unable to save ID card: ' .
                $e->getMessage();

            header(
                'Location: ' . url('/admin/idcards')
            );

            exit;
        }

        /*
         * Success.
         */
        $_SESSION['success'] =
            'ID Card generated successfully for ' .
            $this->personDisplayName($type, $person);

        /*
         * IMPORTANT:
         * Go directly to the newly created card.
         */
        header(
            'Location: ' .
            url('/admin/idcards/' . $id)
        );

        exit;
    }


    /**
     * Revoke ID card.
     */
    public function revoke($id)
    {
        $this->requirePermission('revoke_id_cards');
        $this->validateCsrf();

        $db = Database::get();

        $reason = trim(
            $_POST['reason'] ?? ''
        );

        $stmt = $db->prepare("
            UPDATE id_cards
            SET status = 'revoked'
            WHERE id = ?
        ");

        $stmt->execute([(int) $id]);

        $currentUser = $this->currentUser();
        $userId = $currentUser['id'] ?? null;

        $stmt = $db->prepare("
            UPDATE id_card_tokens
            SET
                revoked = 1,
                revoked_by = ?,
                revoked_at = NOW(),
                revoke_reason = ?
            WHERE id_card_id = ?
              AND revoked = 0
        ");

        $stmt->execute([
            $userId,
            $reason,
            (int) $id
        ]);

        $stmt = $db->prepare("
            INSERT INTO id_card_history
            (
                id_card_id,
                action,
                reason,
                user_id,
                created_at
            )
            VALUES (?, 'revoked', ?, ?, NOW())
        ");

        $stmt->execute([
            (int) $id,
            $reason,
            $userId
        ]);

        $stmt = $db->prepare("
            INSERT INTO audit_logs
            (
                user_id,
                action,
                module,
                entity_id,
                description,
                ip_address,
                created_at
            )
            VALUES
            (
                ?,
                'revoke_id_card',
                'id_cards',
                ?,
                'Revoked ID card',
                ?,
                NOW()
            )
        ");

        $stmt->execute([
            $userId,
            (int) $id,
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        $_SESSION['success'] =
            'ID Card revoked.';

        header(
            'Location: ' .
            url('/admin/idcards/' . (int) $id)
        );

        exit;
    }


    /**
     * Reissue ID card.
     */
    public function reissue($id)
    {
        $this->requirePermission('generate_id_cards');
        $this->validateCsrf();

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT *
            FROM id_cards
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([(int) $id]);

        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            header(
                'Location: ' .
                url('/admin/idcards')
            );

            exit;
        }

        /*
         * Do not allow reissue if the old card is
         * already replaced.
         */
        if (($card['status'] ?? '') === 'replaced') {
            $_SESSION['error'] =
                'This ID card has already been replaced.';

            header(
                'Location: ' .
                url('/admin/idcards/' . (int)$id)
            );

            exit;
        }

        $stmt = $db->prepare("
            UPDATE id_cards
            SET status = 'replaced'
            WHERE id = ?
        ");

        $stmt->execute([(int) $id]);

        $currentUser = $this->currentUser();
        $userId = $currentUser['id'] ?? null;

        $stmt = $db->prepare("
            INSERT INTO id_card_history
            (
                id_card_id,
                action,
                user_id,
                created_at
            )
            VALUES (?, 'replaced', ?, NOW())
        ");

        $stmt->execute([
            (int) $id,
            $userId
        ]);

        /*
         * Generate a completely new card for the
         * same person.
         */
        $_POST['person_type'] =
            $card['person_type'];

        $_POST['person_id'] =
            $card['person_id'];

        $this->generate();
    }


    /**
     * PDF / printable card.
     */
    public function downloadPdf($id)
    {
        $this->requirePermission('view_id_cards');

        $this->printCard($id);
    }


    /**
     * Display printable ID card.
     */
    public function printCard($id)
    {
        $this->requirePermission('view_id_cards');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT *
            FROM id_cards
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([(int) $id]);

        $card = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$card) {
            die('Card not found');
        }

        $card['person_name'] =
            $this->resolvePersonName(
                (string) $card['person_type'],
                (int) $card['person_id']
            );

        $card['gof_id'] =
            $this->resolveGofId(
                (string) $card['person_type'],
                (int) $card['person_id']
            );

        $this->renderBare(
            'idcard/template.php',
            ['card' => $card]
        );
    }


    /**
     * Verification tokens.
     */
    public function tokens()
    {
        $this->requirePermission('view_id_cards');

        $db = Database::get();

        $tokens = $db->query("
            SELECT
                t.*,
                c.card_no
            FROM id_card_tokens t
            JOIN id_cards c
                ON t.id_card_id = c.id
            ORDER BY t.issued_at DESC
            LIMIT 100
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/idcards/tokens.php',
            compact('tokens')
        );
    }


    /**
     * Revoke verification token.
     */
    public function revokeToken()
    {
        $this->requirePermission('revoke_id_cards');
        $this->validateCsrf();

        $id = (int) (
            $_POST['token_id'] ?? 0
        );

        $db = Database::get();

        $currentUser = $this->currentUser();
        $userId = $currentUser['id'] ?? null;

        $stmt = $db->prepare("
            UPDATE id_card_tokens
            SET
                revoked = 1,
                revoked_by = ?,
                revoked_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $userId,
            $id
        ]);

        $_SESSION['success'] =
            'Token revoked.';

        header(
            'Location: ' .
            url('/admin/idcards/tokens')
        );

        exit;
    }


    /**
     * Verification attempts.
     */
    public function attempts()
    {
        $this->requirePermission('view_id_cards');

        $db = Database::get();

        $attempts = $db->query("
            SELECT *
            FROM verification_attempts
            ORDER BY created_at DESC
            LIMIT 200
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/idcards/attempts.php',
            compact('attempts')
        );
    }


    /**
     * Block IP.
     */
    public function blockIp()
    {
        $this->requirePermission('revoke_id_cards');
        $this->validateCsrf();

        $ip = trim(
            $_POST['ip'] ?? ''
        );

        $reason = trim(
            $_POST['reason'] ?? ''
        );

        $reason = $reason !== ''
            ? $reason
            : null;

        if ($ip !== '') {

            $blocklist =
                new BlocklistService();

            $currentUser =
                $this->currentUser();

            $blocklist->block(
                $ip,
                $currentUser['id'] ?? null,
                $reason
            );
        }

        $this->flash(
            'success',
            'IP Blocked.'
        );

        header(
            'Location: ' .
            url('/admin/idcards/attempts')
        );

        exit;
    }


    /**
     * Unblock IP.
     */
    public function unblockIp()
    {
        $this->requirePermission('revoke_id_cards');
        $this->validateCsrf();

        $ip = trim(
            $_POST['ip'] ?? ''
        );

        if ($ip !== '') {

            $blocklist =
                new BlocklistService();

            $blocklist->unblock($ip);
        }

        $this->flash(
            'success',
            'IP Unblocked.'
        );

        header(
            'Location: ' .
            url('/admin/idcards/attempts')
        );

        exit;
    }


    /**
     * ID card analytics.
     */
    public function analytics()
    {
        $this->requirePermission('view_id_cards');

        $db = Database::get();

        $stats = [
            'total_24h' => $db->query("
                SELECT COUNT(*)
                FROM verification_attempts
                WHERE created_at >=
                    NOW() - INTERVAL 1 DAY
            ")->fetchColumn()
        ];

        $this->render(
            'admin/idcards/analytics.php',
            compact('stats')
        );
    }


    /**
     * Normalize submitted person ID.
     *
     * Correct relationships:
     *
     * beneficiaries -> beneficiaries.id
     * staff         -> staff.id
     * donors        -> donors.id
     */
    private function normalizePersonId(
        string $type,
        int $submittedId
    ): int {
        $db = Database::get();

        $tables = [
            'beneficiary' => 'beneficiaries',
            'staff'       => 'staff',
            'donor'       => 'donors'
        ];

        if (
            !isset($tables[$type]) ||
            $submittedId <= 0
        ) {
            return 0;
        }

        $table = $tables[$type];

        /*
         * First assume the supplied ID is the
         * actual target-table ID.
         */
        $stmt = $db->prepare("
            SELECT id
            FROM {$table}
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $submittedId
        ]);

        $id = $stmt->fetchColumn();

        if ($id !== false) {
            return (int) $id;
        }

        /*
         * Backward compatibility with old forms
         * that submitted users.id.
         */
        if (
            $type === 'staff' ||
            $type === 'beneficiary'
        ) {
            $stmt = $db->prepare("
                SELECT id
                FROM {$table}
                WHERE user_id = ?
                LIMIT 1
            ");

            $stmt->execute([
                $submittedId
            ]);

            $id = $stmt->fetchColumn();

            if ($id !== false) {
                return (int) $id;
            }
        }

        return 0;
    }


    /**
     * Retrieve the actual person record.
     */
    private function getPerson(
        string $type,
        int $personId
    ): ?array {
        $db = Database::get();

        $tables = [
            'beneficiary' => 'beneficiaries',
            'staff'       => 'staff',
            'donor'       => 'donors'
        ];

        if (
            !isset($tables[$type]) ||
            $personId <= 0
        ) {
            return null;
        }

        $table = $tables[$type];

        $stmt = $db->prepare("
            SELECT *
            FROM {$table}
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $personId
        ]);

        $person = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $person ?: null;
    }


    /**
     * Check whether person exists.
     */
    private function personExists(
        string $type,
        int $personId
    ): bool {
        return $this->getPerson(
            $type,
            $personId
        ) !== null;
    }


    /**
     * Get a safe display name.
     */
    private function personDisplayName(
        string $type,
        array $person
    ): string {
        if (
            $type === 'beneficiary' ||
            $type === 'staff'
        ) {
            return trim(
                (string) (
                    $person['full_name'] ??
                    'Unknown'
                )
            );
        }

        if ($type === 'donor') {
            $organization =
                trim(
                    (string) (
                        $person['organization_name'] ??
                        ''
                    )
                );

            if ($organization !== '') {
                return $organization;
            }

            return trim(
                (string) (
                    $person['name'] ??
                    'Unknown'
                )
            );
        }

        return 'Unknown';
    }
}