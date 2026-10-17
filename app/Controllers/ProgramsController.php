<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class ProgramsController extends BaseController
{
    public function index()
    {
        $this->requirePermission('manage_programs');

        $db = Database::get();

        $stmt = $db->query("
            SELECT
                p.*,
                CONCAT_WS(' ', u.first_name, u.last_name) AS manager_name
            FROM programs p
            LEFT JOIN users u ON p.manager_id = u.id
            ORDER BY p.created_at DESC
        ");

        $programs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/programs/index.php',
            compact('programs')
        );
    }

    public function create()
    {
        $this->requirePermission('manage_programs');

        $db = Database::get();

        $staff = $db->query("
            SELECT
                id,
                CONCAT_WS(' ', first_name, last_name) AS name
            FROM users
            WHERE status = 'active'
            ORDER BY first_name ASC, last_name ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/programs/create.php',
            compact('staff')
        );
    }

    public function store()
    {
        $this->requirePermission('manage_programs');
        $this->validateCsrf();

        $program_code = trim($_POST['program_code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $objectives = trim($_POST['objectives'] ?? '');
        $start_date = !empty($_POST['start_date'])
            ? $_POST['start_date']
            : null;
        $end_date = !empty($_POST['end_date'])
            ? $_POST['end_date']
            : null;
        $status = $_POST['status'] ?? 'planning';
        $budget = ($_POST['budget'] ?? '') !== ''
            ? $_POST['budget']
            : null;
        $currency = trim($_POST['currency'] ?? 'NGN');
        $manager_id = ($_POST['manager_id'] ?? '') !== ''
            ? $_POST['manager_id']
            : null;

        if (empty($name)) {
            $this->flash(
                'error',
                'Program name is required.'
            );

            $this->redirect('/admin/programs/create');
            return;
        }

        $db = Database::get();

        try {
            // Check program code if supplied
            if (!empty($program_code)) {
                $check = $db->prepare("
                    SELECT COUNT(*)
                    FROM programs
                    WHERE program_code = ?
                ");

                $check->execute([$program_code]);

                if ($check->fetchColumn() > 0) {
                    $this->flash(
                        'error',
                        'Program code already exists.'
                    );

                    $this->redirect('/admin/programs/create');
                    return;
                }
            }

            $stmt = $db->prepare("
                INSERT INTO programs
                (
                    program_code,
                    name,
                    description,
                    objectives,
                    start_date,
                    end_date,
                    status,
                    budget,
                    currency,
                    manager_id,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    NOW(),
                    NOW()
                )
            ");

            $stmt->execute([
                $program_code ?: null,
                $name,
                $description ?: null,
                $objectives ?: null,
                $start_date,
                $end_date,
                $status,
                $budget,
                $currency,
                $manager_id
            ]);

            $user = $this->currentUser();
            $userId = $user['id'] ?? null;

            if ($userId) {
                (new \GOF\Services\AuditService())->log(
                    (int) $userId,
                    'create_program',
                    'programs',
                    'program',
                    (string) $db->lastInsertId(),
                    "Created program: {$name}"
                );
            }

            $this->flash(
                'success',
                'Program created successfully.'
            );

            $this->redirect('/admin/programs');

        } catch (\Exception $e) {

            $this->flash(
                'error',
                'Failed to create program: ' . $e->getMessage()
            );

            $this->redirect('/admin/programs/create');
        }
    }

    public function show($id)
    {
        $this->requirePermission('manage_programs');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT
                p.*,
                CONCAT_WS(' ', u.first_name, u.last_name) AS manager_name
            FROM programs p
            LEFT JOIN users u ON p.manager_id = u.id
            WHERE p.id = ?
        ");

        $stmt->execute([$id]);

        $program = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$program) {
            $this->flash(
                'error',
                'Program not found.'
            );

            $this->redirect('/admin/programs');
            return;
        }

        $this->render(
            'admin/programs/show.php',
            compact('program')
        );
    }

    public function edit($id)
    {
        $this->requirePermission('manage_programs');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT *
            FROM programs
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $program = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$program) {
            $this->flash(
                'error',
                'Program not found.'
            );

            $this->redirect('/admin/programs');
            return;
        }

        $staff = $db->query("
            SELECT
                id,
                CONCAT_WS(' ', first_name, last_name) AS name
            FROM users
            WHERE status = 'active'
            ORDER BY first_name ASC, last_name ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/programs/edit.php',
            compact('program', 'staff')
        );
    }

    public function update($id)
    {
        $this->requirePermission('manage_programs');
        $this->validateCsrf();

        $program_code = trim($_POST['program_code'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $objectives = trim($_POST['objectives'] ?? '');
        $start_date = !empty($_POST['start_date'])
            ? $_POST['start_date']
            : null;
        $end_date = !empty($_POST['end_date'])
            ? $_POST['end_date']
            : null;
        $status = $_POST['status'] ?? 'planning';
        $budget = ($_POST['budget'] ?? '') !== ''
            ? $_POST['budget']
            : null;
        $currency = trim($_POST['currency'] ?? 'NGN');
        $manager_id = ($_POST['manager_id'] ?? '') !== ''
            ? $_POST['manager_id']
            : null;

        if (empty($name)) {
            $this->flash(
                'error',
                'Program name is required.'
            );

            $this->redirect(
                "/admin/programs/{$id}/edit"
            );

            return;
        }

        $db = Database::get();

        try {
            // Check program code against other programs
            if (!empty($program_code)) {
                $check = $db->prepare("
                    SELECT COUNT(*)
                    FROM programs
                    WHERE program_code = ?
                    AND id != ?
                ");

                $check->execute([
                    $program_code,
                    $id
                ]);

                if ($check->fetchColumn() > 0) {
                    $this->flash(
                        'error',
                        'Program code already exists.'
                    );

                    $this->redirect(
                        "/admin/programs/{$id}/edit"
                    );

                    return;
                }
            }

            $stmt = $db->prepare("
                UPDATE programs
                SET
                    program_code = ?,
                    name = ?,
                    description = ?,
                    objectives = ?,
                    start_date = ?,
                    end_date = ?,
                    status = ?,
                    budget = ?,
                    currency = ?,
                    manager_id = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $program_code ?: null,
                $name,
                $description ?: null,
                $objectives ?: null,
                $start_date,
                $end_date,
                $status,
                $budget,
                $currency,
                $manager_id,
                $id
            ]);

            $user = $this->currentUser();
            $userId = $user['id'] ?? null;

            if ($userId) {
                (new \GOF\Services\AuditService())->log(
                    (int) $userId,
                    'update_program',
                    'programs',
                    'program',
                    (string) $id,
                    "Updated program: {$name}"
                );
            }

            $this->flash(
                'success',
                'Program updated successfully.'
            );

            $this->redirect('/admin/programs');

        } catch (\Exception $e) {

            $this->flash(
                'error',
                'Failed to update program: ' . $e->getMessage()
            );

            $this->redirect(
                "/admin/programs/{$id}/edit"
            );
        }
    }

    public function delete($id)
    {
        $this->requirePermission('manage_programs');
        $this->validateCsrf();

        $db = Database::get();

        try {
            $stmt = $db->prepare("
                DELETE FROM programs
                WHERE id = ?
            ");

            $stmt->execute([$id]);

            $user = $this->currentUser();
            $userId = $user['id'] ?? null;

            if ($userId) {
                (new \GOF\Services\AuditService())->log(
                    (int) $userId,
                    'delete_program',
                    'programs',
                    'program',
                    (string) $id,
                    "Deleted program ID: {$id}"
                );
            }

            $this->flash(
                'success',
                'Program deleted successfully.'
            );

        } catch (\Exception $e) {

            $this->flash(
                'error',
                'Failed to delete program: ' . $e->getMessage()
            );
        }

        $this->redirect('/admin/programs');
    }
}