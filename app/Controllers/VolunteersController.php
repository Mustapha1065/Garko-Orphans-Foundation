<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;

class VolunteersController extends BaseController
{
    /**
     * Volunteer applications dashboard
     */
    public function index(): void
    {
        $this->requirePermission('manage_volunteers');

        $db = Database::get();

        $status = trim($_GET['status'] ?? '');
        $search = trim($_GET['search'] ?? '');

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = ['1=1'];
        $params = [];

        /*
         * Status filter
         */
        if ($status !== '') {

            $allowedStatuses = [
                'pending',
                'reviewed',
                'approved',
                'rejected',
                'contacted'
            ];

            if (in_array($status, $allowedStatuses, true)) {
                $where[] = 'status = ?';
                $params[] = $status;
            } else {
                $status = '';
            }
        }

        /*
         * Search
         */
        if ($search !== '') {

            $where[] = '(
                first_name LIKE ?
                OR last_name LIKE ?
                OR email LIKE ?
                OR phone LIKE ?
                OR skills LIKE ?
                OR interest LIKE ?
                OR occupation LIKE ?
                OR application_ref LIKE ?
            )';

            $searchValue = '%' . $search . '%';

            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
            $params[] = $searchValue;
        }

        $whereSql = implode(' AND ', $where);

        /*
         * Get applications
         */
        $sql = "
            SELECT *
            FROM volunteer_applications
            WHERE {$whereSql}
            ORDER BY created_at DESC
            LIMIT {$limit} OFFSET {$offset}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $applications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /*
         * Count applications
         */
        $countSql = "
            SELECT COUNT(*)
            FROM volunteer_applications
            WHERE {$whereSql}
        ";

        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);

        $total = (int)$countStmt->fetchColumn();

        $totalPages = max(
            1,
            (int)ceil($total / $limit)
        );

        /*
         * Summary
         */
        $summaryRows = $db->query("
            SELECT status, COUNT(*) AS count
            FROM volunteer_applications
            GROUP BY status
        ")->fetchAll(\PDO::FETCH_KEY_PAIR);

        $summary = [];

        foreach (
            [
                'pending',
                'reviewed',
                'approved',
                'rejected',
                'contacted'
            ] as $item
        ) {
            $summary[$item] = (int)($summaryRows[$item] ?? 0);
        }

        $summary['total'] =
            $summary['pending']
            + $summary['reviewed']
            + $summary['approved']
            + $summary['rejected']
            + $summary['contacted'];

        $this->render(
            'admin/volunteers/index.php',
            compact(
                'applications',
                'status',
                'search',
                'page',
                'totalPages',
                'summary'
            )
        );
    }


    /**
     * Review / approve / reject / contact volunteer
     */
    public function review($id): void
    {
        $this->requirePermission('manage_volunteers');

        /*
         * Make sure the session exists.
         */
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        /*
         * Accept the existing CSRF fields used
         * by the GOF3-light volunteer forms.
         */
        $submittedToken =
            $_POST['_token']
            ?? $_POST['_csrf']
            ?? $_POST['csrf_token']
            ?? '';

        $sessionToken =
            $_SESSION['csrf_token']
            ?? '';

        /*
         * CSRF validation
         */
        if (
            $submittedToken === ''
            || $sessionToken === ''
            || !hash_equals(
                (string)$sessionToken,
                (string)$submittedToken
            )
        ) {
            $this->flash(
                'error',
                'Invalid form submission. Please try again.'
            );

            $this->redirect('/admin/volunteers');
            return;
        }

        /*
         * Validate application ID
         */
        $id = (int)$id;

        if ($id <= 0) {
            $this->flash(
                'error',
                'Invalid volunteer application.'
            );

            $this->redirect('/admin/volunteers');
            return;
        }

        /*
         * Validate action
         */
        $action = trim($_POST['action'] ?? '');

        $validActions = [
            'reviewed',
            'approved',
            'rejected',
            'contacted'
        ];

        if (!in_array($action, $validActions, true)) {
            $this->flash(
                'error',
                'Invalid volunteer action.'
            );

            $this->redirect('/admin/volunteers');
            return;
        }

        /*
         * Optional message entered by administrator.
         */
        $decisionMessage = trim(
            $_POST['decision_message'] ?? ''
        );

        $db = Database::get();

        $user = $this->currentUser();

        $userId = isset($user['id'])
            ? (int)$user['id']
            : null;

        try {

            /*
             * Confirm the application exists.
             */
            $check = $db->prepare("
                SELECT *
                FROM volunteer_applications
                WHERE id = ?
                LIMIT 1
            ");

            $check->execute([$id]);

            $application = $check->fetch(
                \PDO::FETCH_ASSOC
            );

            if (!$application) {

                $this->flash(
                    'error',
                    'Volunteer application not found.'
                );

                $this->redirect('/admin/volunteers');
                return;
            }

            /*
             * Prevent unnecessary duplicate status changes.
             *
             * However, allow the administrator to update
             * the decision message even when the status
             * remains unchanged.
             */
            $currentStatus = strtolower(
                (string)($application['status'] ?? '')
            );

            if (
                $currentStatus === $action
                && $decisionMessage === ''
            ) {

                $this->flash(
                    'success',
                    'Application is already marked as '
                    . ucfirst($action) . '.'
                );

                $this->redirect('/admin/volunteers');
                return;
            }

            /*
             * Generate a useful default decision message
             * when the administrator did not provide one.
             */
            if ($decisionMessage === '') {

                switch ($action) {

                    case 'approved':

                        $decisionMessage =
                            'Your volunteer application has been approved. '
                            . 'Garko Orphans Foundation will contact you '
                            . 'with further information.';

                        break;

                    case 'rejected':

                        $decisionMessage =
                            'Your volunteer application was not approved '
                            . 'at this time.';

                        break;

                    case 'reviewed':

                        $decisionMessage =
                            'Your volunteer application has been reviewed '
                            . 'by the Garko Orphans Foundation team.';

                        break;

                    case 'contacted':

                        $decisionMessage =
                            'The Garko Orphans Foundation team has contacted '
                            . 'you regarding your volunteer application.';

                        break;
                }
            }

            /*
             * Update application status and decision.
             */
            $stmt = $db->prepare("
                UPDATE volunteer_applications
                SET
                    status = ?,
                    decision_message = ?,
                    reviewed_by = ?,
                    reviewed_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $action,
                $decisionMessage,
                $userId,
                $id
            ]);

            /*
             * Audit trail.
             */
            try {

                $audit = new AuditService();

                $audit->log(
                    (int)$userId,
                    'update_status',
                    'volunteer',
                    'volunteer_application',
                    (string)$id,
                    "Marked volunteer application #{$id} as {$action}"
                );

            } catch (\Throwable $auditError) {

                /*
                 * Do not undo a successful status update
                 * because of an audit logging problem.
                 */
            }

            /*
             * Success message.
             */
            $this->flash(
                'success',
                'Volunteer application marked as '
                . ucfirst($action) . '.'
            );

        } catch (\Throwable $e) {

            /*
             * Do not expose database/internal errors
             * to the administrator.
             */
            $this->flash(
                'error',
                'Failed to update volunteer application.'
            );
        }

        /*
         * Return to volunteer management.
         */
        $this->redirect('/admin/volunteers');
    }
}