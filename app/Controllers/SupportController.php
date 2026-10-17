<?php
namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;
use GOF\Models\Beneficiary as BeneficiaryModel;

class SupportController extends BaseController {

    public function index() {
        $this->requireAuth();
        $this->requirePermission('manage_support');

        $beneficiaryId = (int)($_GET['beneficiary_id'] ?? 0);
        if (!$beneficiaryId) {
            $this->flash('error', 'Beneficiary ID required.');
            $this->redirect('/admin/beneficiaries');
        }

        $model = new BeneficiaryModel();
        $beneficiary = $model->find($beneficiaryId);
        if (!$beneficiary) {
            $this->flash('error', 'Beneficiary not found.');
            $this->redirect('/admin/beneficiaries');
        }

        $db = Database::get();
        $stmt = $db->prepare("SELECT bs.*, p.name as program_name, CONCAT(u.first_name, ' ', u.last_name) as staff_name 
                              FROM beneficiary_support bs 
                              LEFT JOIN programs p ON bs.program_id = p.id 
                              LEFT JOIN users u ON bs.recorded_by_user_id = u.id 
                              WHERE bs.beneficiary_id = ? ORDER BY bs.support_date DESC");
        $stmt->execute([$beneficiaryId]);
        $supportRecords = $stmt->fetchAll();

        $programs = $db->query("SELECT id, name FROM programs ORDER BY name ASC")->fetchAll();

        $this->render('admin/support/index.php', compact('beneficiary', 'supportRecords', 'programs'));
    }

    public function store() {
        $this->requireAuth();
        $this->requirePermission('manage_support');
        $this->validateCsrf();

        $beneficiaryId = (int)($_POST['beneficiary_id'] ?? 0);
        $userId = $this->currentUser()['id'] ?? null;

        $db = Database::get();
        try {
            $stmt = $db->prepare("INSERT INTO beneficiary_support (beneficiary_id, support_type, description, quantity, amount, currency, support_date, program_id, recorded_by_user_id, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $beneficiaryId,
                $_POST['support_type'] ?? '',
                $_POST['description'] ?? '',
                $_POST['quantity'] ?: null,
                $_POST['amount'] ?: null,
                $_POST['currency'] ?? 'NGN',
                $_POST['support_date'] ?? date('Y-m-d'),
                $_POST['program_id'] ?: null,
                $userId,
                $_POST['notes'] ?: null,
            ]);
            $supportId = (int)$db->lastInsertId();

            $audit = new AuditService();
            $audit->log((int)$userId, 'add_support', 'support', 'beneficiary_support', (string)$supportId, "Added support record for beneficiary #{$beneficiaryId}");

            $this->flash('success', 'Support record added successfully.');
        } catch (\Exception $e) {
            $this->flash('error', 'Failed to add support: ' . $e->getMessage());
        }
        $this->redirect('/admin/support?beneficiary_id=' . $beneficiaryId);
    }
}
