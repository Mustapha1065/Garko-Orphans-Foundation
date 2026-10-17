<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class ReportsController extends BaseController
{
    /**
     * Reports dashboard
     */
    public function index(): void
    {
        $this->requirePermission('view_reports');

        $db = Database::get();

        $stats = [
            'beneficiaries'  => 0,
            'staff'          => 0,
            'donors'         => 0,
            'donations'      => 0,
            'donation_total' => 0,
            'id_cards'       => 0,
        ];

        try {
            $stats['beneficiaries'] = (int)$db
                ->query("SELECT COUNT(*) FROM beneficiaries")
                ->fetchColumn();

            $stats['staff'] = (int)$db
                ->query("SELECT COUNT(*) FROM staff")
                ->fetchColumn();

            $stats['donors'] = (int)$db
                ->query("SELECT COUNT(*) FROM donors")
                ->fetchColumn();

            $stats['donations'] = (int)$db
                ->query("SELECT COUNT(*) FROM donations")
                ->fetchColumn();

            $stats['donation_total'] = (float)$db
                ->query("
                    SELECT COALESCE(SUM(amount), 0)
                    FROM donations
                    WHERE status = 'confirmed'
                ")
                ->fetchColumn();

            $stats['id_cards'] = (int)$db
                ->query("
                    SELECT COUNT(*)
                    FROM id_cards
                    WHERE status = 'active'
                ")
                ->fetchColumn();
        } catch (\Throwable $e) {
            // Keep dashboard available if an optional statistic fails.
        }

        $this->render(
            'admin/reports/index.php',
            compact('stats')
        );
    }


    /**
     * Beneficiary report
     */
    public function beneficiaries(): void
    {
        $this->requirePermission('view_reports');

        $db = Database::get();

        $query = "
            SELECT *
            FROM beneficiaries
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($_GET['status'])) {
            $query .= " AND status = ?";
            $params[] = trim($_GET['status']);
        }

        if (!empty($_GET['gender'])) {
            $query .= " AND gender = ?";
            $params[] = trim($_GET['gender']);
        }

        if (!empty($_GET['search'])) {
            $query .= "
                AND (
                    full_name LIKE ?
                    OR gof_id LIKE ?
                    OR phone LIKE ?
                )
            ";

            $search = '%' . trim($_GET['search']) . '%';

            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $query .= " ORDER BY id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $data = $stmt->fetchAll();

        $this->render(
            'admin/reports/beneficiaries.php',
            compact('data')
        );
    }


    /**
     * Donation report
     */
    public function donations(): void
    {
        $this->requirePermission('view_reports');

        $db = Database::get();

        /*
         * Do NOT assume donors.organization exists.
         *
         * We use the donor table only for the donor name.
         */
        $query = "
            SELECT
                d.*,
                dr.name AS donor_name,
                p.name AS program_name
            FROM donations d
            LEFT JOIN donors dr
                ON dr.id = d.donor_id
            LEFT JOIN programs p
                ON p.id = d.program_id
            WHERE 1 = 1
        ";

        $params = [];

        /*
         * Status filter
         */
        if (!empty($_GET['status'])) {
            $query .= " AND d.status = ?";
            $params[] = trim($_GET['status']);
        }

        /*
         * Payment method filter
         */
        if (!empty($_GET['payment_method'])) {
            $query .= " AND d.payment_method = ?";
            $params[] = trim($_GET['payment_method']);
        }

        /*
         * Date from
         */
        if (!empty($_GET['date_from'])) {
            $query .= " AND DATE(d.donation_date) >= ?";
            $params[] = trim($_GET['date_from']);
        }

        /*
         * Date to
         */
        if (!empty($_GET['date_to'])) {
            $query .= " AND DATE(d.donation_date) <= ?";
            $params[] = trim($_GET['date_to']);
        }

        /*
         * Search
         *
         * Only search columns known to exist.
         */
        if (!empty($_GET['search'])) {

            $query .= "
                AND (
                    d.donation_ref LIKE ?
                    OR d.transaction_ref LIKE ?
                    OR dr.name LIKE ?
                )
            ";

            $search = '%' . trim($_GET['search']) . '%';

            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $query .= " ORDER BY d.id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $data = $stmt->fetchAll();


        /*
         * Donation summary
         */
        $summaryQuery = "
            SELECT
                COUNT(*) AS total_records,
                COALESCE(
                    SUM(
                        CASE
                            WHEN status = 'confirmed'
                            THEN amount
                            ELSE 0
                        END
                    ),
                    0
                ) AS confirmed_amount
            FROM donations
            WHERE 1 = 1
        ";

        $summaryParams = [];

        if (!empty($_GET['status'])) {
            $summaryQuery .= " AND status = ?";
            $summaryParams[] = trim($_GET['status']);
        }

        if (!empty($_GET['date_from'])) {
            $summaryQuery .= " AND DATE(donation_date) >= ?";
            $summaryParams[] = trim($_GET['date_from']);
        }

        if (!empty($_GET['date_to'])) {
            $summaryQuery .= " AND DATE(donation_date) <= ?";
            $summaryParams[] = trim($_GET['date_to']);
        }

        $summaryStmt = $db->prepare($summaryQuery);
        $summaryStmt->execute($summaryParams);

        $summary = $summaryStmt->fetch();

        $this->render(
            'admin/reports/donations.php',
            compact('data', 'summary')
        );
    }


    /**
     * Donor report
     */
    public function donors(): void
    {
        $this->requirePermission('view_reports');

        $db = Database::get();

        $query = "
            SELECT *
            FROM donors
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($_GET['search'])) {

            $query .= "
                AND (
                    name LIKE ?
                    OR email LIKE ?
                    OR phone LIKE ?
                    OR gof_id LIKE ?
                )
            ";

            $search = '%' . trim($_GET['search']) . '%';

            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($_GET['type'])) {
            $query .= " AND donor_type = ?";
            $params[] = trim($_GET['type']);
        }

        $query .= " ORDER BY id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $data = $stmt->fetchAll();

        $this->render(
            'admin/reports/donors.php',
            compact('data')
        );
    }


    /**
     * Staff report
     */
    public function staff(): void
    {
        $this->requirePermission('view_reports');

        $db = Database::get();

        $query = "
            SELECT *
            FROM staff
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($_GET['status'])) {
            $query .= " AND status = ?";
            $params[] = trim($_GET['status']);
        }

        if (!empty($_GET['gender'])) {
            $query .= " AND gender = ?";
            $params[] = trim($_GET['gender']);
        }

        if (!empty($_GET['search'])) {

            $query .= "
                AND (
                    full_name LIKE ?
                    OR gof_id LIKE ?
                    OR phone LIKE ?
                    OR email LIKE ?
                )
            ";

            $search = '%' . trim($_GET['search']) . '%';

            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $query .= " ORDER BY id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $data = $stmt->fetchAll();

        $this->render(
            'admin/reports/staff.php',
            compact('data')
        );
    }


    /**
     * ID card report
     */
    public function idcards(): void
    {
        $this->requirePermission('view_reports');

        $db = Database::get();

        $query = "
            SELECT *
            FROM id_cards
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($_GET['status'])) {
            $query .= " AND status = ?";
            $params[] = trim($_GET['status']);
        }

        if (!empty($_GET['person_type'])) {
            $query .= " AND person_type = ?";
            $params[] = trim($_GET['person_type']);
        }

        if (!empty($_GET['search'])) {

            $query .= "
                AND (
                    card_no LIKE ?
                    OR gof_id LIKE ?
                )
            ";

            $search = '%' . trim($_GET['search']) . '%';

            $params[] = $search;
            $params[] = $search;
        }

        $query .= " ORDER BY id DESC";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $data = $stmt->fetchAll();

        $this->render(
            'admin/reports/idcards.php',
            compact('data')
        );
    }
}