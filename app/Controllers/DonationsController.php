<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;

class DonationsController extends BaseController
{
    /**
     * ---------------------------------------------------------
     * CSRF VALIDATION
     * ---------------------------------------------------------
     * Accept both token names used by the existing GOF views.
     */
    private function validateDonationCsrf(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = $_POST['_token']
            ?? $_POST['_csrf']
            ?? $_POST['csrf_token']
            ?? '';

        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (
            empty($token) ||
            empty($sessionToken) ||
            !hash_equals((string)$sessionToken, (string)$token)
        ) {
            $this->flash('error', 'Invalid form submission. Please try again.');
            return false;
        }

        return true;
    }


    /**
     * ---------------------------------------------------------
     * DONATION LIST
     * ---------------------------------------------------------
     */
    public function index(): void
    {
        $this->requirePermission('view_donations');

        $db = Database::get();

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $from   = trim($_GET['from'] ?? '');
        $to     = trim($_GET['to'] ?? '');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = ['1=1'];
        $params = [];

        if ($search !== '') {
            $where[] = "(
                d.donation_ref LIKE ?
                OR d.transaction_ref LIKE ?
                OR dr.name LIKE ?
                OR dr.email LIKE ?
                OR dr.gof_id LIKE ?
            )";

            $term = '%' . $search . '%';

            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if ($status !== '') {
            $where[] = 'd.status = ?';
            $params[] = $status;
        }

        if ($from !== '') {
            $where[] = 'DATE(d.donation_date) >= ?';
            $params[] = $from;
        }

        if ($to !== '') {
            $where[] = 'DATE(d.donation_date) <= ?';
            $params[] = $to;
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT
                d.*,
                dr.name AS donor_name,
                dr.email AS donor_email,
                dr.phone AS donor_phone,
                dr.gof_id AS donor_gof_id,
                p.name AS program_name
            FROM donations d
            LEFT JOIN donors dr ON dr.id = d.donor_id
            LEFT JOIN programs p ON p.id = d.program_id
            WHERE {$whereSql}
            ORDER BY d.created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $donations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $countStmt = $db->prepare("
            SELECT COUNT(*)
            FROM donations d
            LEFT JOIN donors dr ON dr.id = d.donor_id
            LEFT JOIN programs p ON p.id = d.program_id
            WHERE {$whereSql}
        ");

        $countStmt->execute($params);

        $total = (int)$countStmt->fetchColumn();

        $totalPages = max(1, (int)ceil($total / $limit));

        $summary = [
            'confirmed_count' => 0,
            'pending_count'   => 0,
            'rejected_count'  => 0,
            'confirmed_total' => 0,
        ];

        $summaryStmt = $db->query("
            SELECT
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) AS confirmed_count,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                COALESCE(
                    SUM(
                        CASE
                            WHEN status = 'confirmed' THEN amount
                            ELSE 0
                        END
                    ),
                    0
                ) AS confirmed_total
            FROM donations
        ");

        $summaryRow = $summaryStmt->fetch(\PDO::FETCH_ASSOC);

        if ($summaryRow) {
            $summary = [
                'confirmed_count' => (int)($summaryRow['confirmed_count'] ?? 0),
                'pending_count'   => (int)($summaryRow['pending_count'] ?? 0),
                'rejected_count'  => (int)($summaryRow['rejected_count'] ?? 0),
                'confirmed_total' => (float)($summaryRow['confirmed_total'] ?? 0),
            ];
        }

        $this->render(
            'admin/donations/index.php',
            compact(
                'donations',
                'search',
                'status',
                'from',
                'to',
                'page',
                'totalPages',
                'summary'
            )
        );
    }


    /**
     * ---------------------------------------------------------
     * CREATE DONATION
     * ---------------------------------------------------------
     */
    public function create(): void
    {
        $this->requirePermission('manage_donations');

        $db = Database::get();

        $donors = $db->query("
            SELECT id, name, email, phone, gof_id
            FROM donors
            ORDER BY name ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $programs = $db->query("
            SELECT id, name
            FROM programs
            WHERE status = 'active'
            ORDER BY name ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/donations/create.php',
            compact('donors', 'programs')
        );
    }


    /**
     * ---------------------------------------------------------
     * STORE DONATION
     * ---------------------------------------------------------
     */
    public function store(): void
    {
        $this->requirePermission('manage_donations');

        if (!$this->validateDonationCsrf()) {
            $this->redirect('/admin/donations/create');
            return;
        }

        $db = Database::get();

        $donorId = (int)($_POST['donor_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $currency = trim($_POST['currency'] ?? 'NGN');
        $donationDate = trim($_POST['donation_date'] ?? date('Y-m-d'));
        $paymentMethod = trim($_POST['payment_method'] ?? '');
        $transactionRef = trim($_POST['transaction_ref'] ?? '');
        $programId = !empty($_POST['program_id'])
            ? (int)$_POST['program_id']
            : null;
        $purpose = trim($_POST['purpose'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($donorId <= 0) {
            $this->flash('error', 'Please select a donor.');
            $this->redirect('/admin/donations/create');
            return;
        }

        if ($amount <= 0) {
            $this->flash('error', 'Donation amount must be greater than zero.');
            $this->redirect('/admin/donations/create');
            return;
        }

        if ($paymentMethod === '') {
            $this->flash('error', 'Please select a payment method.');
            $this->redirect('/admin/donations/create');
            return;
        }

        if ($transactionRef !== '') {
            $check = $db->prepare("
                SELECT id
                FROM donations
                WHERE transaction_ref = ?
                LIMIT 1
            ");

            $check->execute([$transactionRef]);

            if ($check->fetchColumn()) {
                $this->flash(
                    'error',
                    'That transaction reference already exists.'
                );

                $this->redirect('/admin/donations/create');
                return;
            }
        }

        $donorCheck = $db->prepare("
            SELECT id
            FROM donors
            WHERE id = ?
            LIMIT 1
        ");

        $donorCheck->execute([$donorId]);

        if (!$donorCheck->fetchColumn()) {
            $this->flash('error', 'Selected donor was not found.');
            $this->redirect('/admin/donations/create');
            return;
        }

        if ($programId !== null) {
            $programCheck = $db->prepare("
                SELECT id
                FROM programs
                WHERE id = ?
                LIMIT 1
            ");

            $programCheck->execute([$programId]);

            if (!$programCheck->fetchColumn()) {
                $programId = null;
            }
        }

        try {
            $year = date('Y');

            $prefix = "DON-{$year}-";

            $lastStmt = $db->prepare("
                SELECT donation_ref
                FROM donations
                WHERE donation_ref LIKE ?
                ORDER BY id DESC
                LIMIT 1
            ");

            $lastStmt->execute([$prefix . '%']);

            $lastRef = $lastStmt->fetchColumn();

            $nextNumber = 1;

            if ($lastRef) {
                $parts = explode('-', $lastRef);
                $lastNumber = (int)end($parts);
                $nextNumber = $lastNumber + 1;
            }

            $donationRef = sprintf(
                'DON-%s-%05d',
                $year,
                $nextNumber
            );

            $db->beginTransaction();

            $stmt = $db->prepare("
                INSERT INTO donations (
                    donation_ref,
                    donor_id,
                    amount,
                    currency,
                    donation_date,
                    payment_method,
                    transaction_ref,
                    program_id,
                    status,
                    notes,
                    created_at,
                    updated_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW()
                )
            ");

            $stmt->execute([
                $donationRef,
                $donorId,
                $amount,
                $currency,
                $donationDate,
                $paymentMethod,
                $transactionRef !== '' ? $transactionRef : null,
                $programId,
                trim(
                    ($purpose !== '' ? 'Purpose: ' . $purpose . "\n" : '') .
                    $notes
                ),
            ]);

            $id = (int)$db->lastInsertId();

            $userId = $this->currentUser()['id'] ?? null;

            if ($userId) {
                try {
                    (new AuditService())->log(
                        (int)$userId,
                        'create',
                        'donation',
                        'donations',
                        (string)$id,
                        "Created donation {$donationRef}"
                    );
                } catch (\Throwable $ignored) {
                    // Audit failure must not destroy the donation.
                }
            }

            $db->commit();

            $this->flash(
                'success',
                "Donation {$donationRef} recorded successfully."
            );

            $this->redirect('/admin/donations/' . $id);
            return;

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            error_log(
                'GOF Donation Store Error: ' . $e->getMessage()
            );

            $this->flash(
                'error',
                'Unable to save the donation. Please try again.'
            );

            $this->redirect('/admin/donations/create');
            return;
        }
    }


    /**
     * ---------------------------------------------------------
     * SHOW DONATION
     * ---------------------------------------------------------
     */
    public function show($id): void
    {
        $this->requirePermission('view_donations');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT
                d.*,
                dr.name AS donor_name,
                dr.email AS donor_email,
                dr.phone AS donor_phone,
                dr.gof_id AS donor_gof_id,
                p.name AS program_name,
                CONCAT_WS(
                    ' ',
                    ru.first_name,
                    ru.last_name
                ) AS recorded_by_name,
                CONCAT_WS(
                    ' ',
                    cu.first_name,
                    cu.last_name
                ) AS confirmed_by_name
            FROM donations d
            LEFT JOIN donors dr
                ON dr.id = d.donor_id
            LEFT JOIN programs p
                ON p.id = d.program_id
            LEFT JOIN users ru
                ON ru.id = d.recorded_by
            LEFT JOIN users cu
                ON cu.id = d.confirmed_by
            WHERE d.id = ?
            LIMIT 1
        ");

        try {
            $stmt->execute([(int)$id]);
            $donation = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {

            /*
             * Some older GOF installations may not have
             * recorded_by / confirmed_by columns.
             */
            $fallback = $db->prepare("
                SELECT
                    d.*,
                    dr.name AS donor_name,
                    dr.email AS donor_email,
                    dr.phone AS donor_phone,
                    dr.gof_id AS donor_gof_id,
                    p.name AS program_name
                FROM donations d
                LEFT JOIN donors dr
                    ON dr.id = d.donor_id
                LEFT JOIN programs p
                    ON p.id = d.program_id
                WHERE d.id = ?
                LIMIT 1
            ");

            $fallback->execute([(int)$id]);
            $donation = $fallback->fetch(\PDO::FETCH_ASSOC);
        }

        if (!$donation) {
            $this->flash('error', 'Donation not found.');
            $this->redirect('/admin/donations');
            return;
        }

        $this->render(
            'admin/donations/show.php',
            compact('donation')
        );
    }


    /**
     * ---------------------------------------------------------
     * CONFIRM DONATION
     * ---------------------------------------------------------
     */
    public function confirm($id): void
    {
        $this->requirePermission('manage_donations');

        if (!$this->validateDonationCsrf()) {
            $this->redirect('/admin/donations/' . (int)$id);
            return;
        }

        $db = Database::get();

        try {
            $stmt = $db->prepare("
                SELECT id, donation_ref, status
                FROM donations
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([(int)$id]);

            $donation = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$donation) {
                $this->flash('error', 'Donation not found.');
                $this->redirect('/admin/donations');
                return;
            }

            if ($donation['status'] === 'confirmed') {
                $this->flash(
                    'info',
                    'This donation is already confirmed.'
                );

                $this->redirect('/admin/donations/' . (int)$id);
                return;
            }

            $userId = $this->currentUser()['id'] ?? null;

            /*
             * Try the full schema first.
             */
            try {
                $update = $db->prepare("
                    UPDATE donations
                    SET
                        status = 'confirmed',
                        confirmed_by = ?,
                        confirmed_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?
                ");

                $update->execute([
                    $userId,
                    (int)$id
                ]);

            } catch (\Throwable $e) {

                /*
                 * Fallback for older schema.
                 */
                $update = $db->prepare("
                    UPDATE donations
                    SET
                        status = 'confirmed',
                        updated_at = NOW()
                    WHERE id = ?
                ");

                $update->execute([(int)$id]);
            }

            if ($update->rowCount() < 1) {
                $this->flash(
                    'error',
                    'The donation could not be confirmed.'
                );

                $this->redirect('/admin/donations/' . (int)$id);
                return;
            }

            if ($userId) {
                try {
                    (new AuditService())->log(
                        (int)$userId,
                        'confirm',
                        'donation',
                        'donations',
                        (string)$id,
                        "Confirmed donation {$donation['donation_ref']}"
                    );
                } catch (\Throwable $ignored) {
                }
            }

            $this->flash(
                'success',
                'Donation confirmed successfully.'
            );

        } catch (\Throwable $e) {

            error_log(
                'GOF Donation Confirm Error: ' . $e->getMessage()
            );

            $this->flash(
                'error',
                'Unable to confirm donation.'
            );
        }

        $this->redirect('/admin/donations/' . (int)$id);
    }


    /**
     * ---------------------------------------------------------
     * REJECT DONATION
     * ---------------------------------------------------------
     */
    public function reject($id): void
    {
        $this->requirePermission('manage_donations');

        if (!$this->validateDonationCsrf()) {
            $this->redirect('/admin/donations/' . (int)$id);
            return;
        }

        $reason = trim(
            $_POST['reason']
            ?? $_POST['rejection_reason']
            ?? ''
        );

        if ($reason === '') {
            $reason = 'Donation rejected by administrator.';
        }

        if (strlen($reason) > 1000) {
            $reason = substr($reason, 0, 1000);
        }

        $db = Database::get();

        try {
            $stmt = $db->prepare("
                SELECT id, donation_ref, status
                FROM donations
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([(int)$id]);

            $donation = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$donation) {
                $this->flash('error', 'Donation not found.');
                $this->redirect('/admin/donations');
                return;
            }

            if ($donation['status'] === 'confirmed') {
                $this->flash(
                    'error',
                    'A confirmed donation cannot be rejected.'
                );

                $this->redirect('/admin/donations/' . (int)$id);
                return;
            }

            /*
             * Store the reason in notes so this works with
             * installations that do not have rejection_reason.
             */
            $existingStmt = $db->prepare("
                SELECT notes
                FROM donations
                WHERE id = ?
                LIMIT 1
            ");

            $existingStmt->execute([(int)$id]);

            $existingNotes = (string)$existingStmt->fetchColumn();

            $newNotes = trim(
                $existingNotes .
                "\n\nRejection reason: " .
                $reason
            );

            $update = $db->prepare("
                UPDATE donations
                SET
                    status = 'rejected',
                    notes = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $update->execute([
                $newNotes,
                (int)$id
            ]);

            $userId = $this->currentUser()['id'] ?? null;

            if ($userId) {
                try {
                    (new AuditService())->log(
                        (int)$userId,
                        'reject',
                        'donation',
                        'donations',
                        (string)$id,
                        "Rejected donation {$donation['donation_ref']}"
                    );
                } catch (\Throwable $ignored) {
                }
            }

            $this->flash(
                'success',
                'Donation rejected successfully.'
            );

        } catch (\Throwable $e) {

            error_log(
                'GOF Donation Reject Error: ' . $e->getMessage()
            );

            $this->flash(
                'error',
                'Unable to reject donation.'
            );
        }

        $this->redirect('/admin/donations/' . (int)$id);
    }


    /**
     * ---------------------------------------------------------
     * OFFICIAL RECEIPT
     * ---------------------------------------------------------
     */
    public function receipt($id): void
    {
        $this->requirePermission('view_donations');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT
                d.*,
                dr.name AS donor_name,
                dr.email AS donor_email,
                dr.phone AS donor_phone,
                dr.gof_id AS donor_gof_id,
                p.name AS program_name,
                CONCAT_WS(
                    ' ',
                    cu.first_name,
                    cu.last_name
                ) AS confirmed_by_name
            FROM donations d
            LEFT JOIN donors dr
                ON dr.id = d.donor_id
            LEFT JOIN programs p
                ON p.id = d.program_id
            LEFT JOIN users cu
                ON cu.id = d.confirmed_by
            WHERE d.id = ?
            LIMIT 1
        ");

        try {
            $stmt->execute([(int)$id]);
            $donation = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {

            $fallback = $db->prepare("
                SELECT
                    d.*,
                    dr.name AS donor_name,
                    dr.email AS donor_email,
                    dr.phone AS donor_phone,
                    dr.gof_id AS donor_gof_id,
                    p.name AS program_name
                FROM donations d
                LEFT JOIN donors dr
                    ON dr.id = d.donor_id
                LEFT JOIN programs p
                    ON p.id = d.program_id
                WHERE d.id = ?
                LIMIT 1
            ");

            $fallback->execute([(int)$id]);
            $donation = $fallback->fetch(\PDO::FETCH_ASSOC);
        }

        if (!$donation) {
            $this->flash('error', 'Donation not found.');
            $this->redirect('/admin/donations');
            return;
        }

        if (($donation['status'] ?? '') !== 'confirmed') {
            $this->flash(
                'error',
                'A receipt is available only for confirmed donations.'
            );

            $this->redirect('/admin/donations/' . (int)$id);
            return;
        }

        $this->renderBare(
            'admin/donations/receipt.php',
            compact('donation')
        );
    }
}