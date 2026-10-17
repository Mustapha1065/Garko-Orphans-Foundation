<?php
namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;
use GOF\Models\Beneficiary as BeneficiaryModel;

class EducationController extends BaseController {

    public function index() {
        $this->requireAuth();
        $this->requirePermission('manage_education');

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
        $stmt = $db->prepare("SELECT * FROM education_records WHERE beneficiary_id = ? ORDER BY start_date DESC");
        $stmt->execute([$beneficiaryId]);
        $educationRecords = $stmt->fetchAll();

        $this->render('admin/education/index.php', compact('beneficiary', 'educationRecords'));
    }

    public function store() {
        $this->requireAuth();
        $this->requirePermission('manage_education');
        $this->validateCsrf();

        $beneficiaryId = (int)($_POST['beneficiary_id'] ?? 0);
        $userId = $this->currentUser()['id'] ?? null;

        $db = Database::get();
        try {
            $stmt = $db->prepare("INSERT INTO education_records (beneficiary_id, institution_name, institution_type, course_level, academic_session, enrollment_status, performance, remarks, start_date, end_date, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([
                $beneficiaryId,
                $_POST['institution'] ?? '',
                $_POST['institution_type'] ?: null,
                $_POST['course_level'] ?: null,
                $_POST['academic_session'] ?: null,
                $_POST['enrollment_status'] ?: null,
                $_POST['performance'] ?: null,
                $_POST['notes'] ?: null,
                $_POST['start_date'] ?: null,
                $_POST['end_date'] ?: null,
                $userId,
            ]);
            $educationId = (int)$db->lastInsertId();

            $audit = new AuditService();
            $audit->log((int)$userId, 'add_education', 'education', 'education_records', (string)$educationId, "Added education record for beneficiary #{$beneficiaryId}");

            $this->flash('success', 'Education record added successfully.');
        } catch (\Exception $e) {
            $this->flash('error', 'Failed to add education record: ' . $e->getMessage());
        }
        $this->redirect('/admin/education?beneficiary_id=' . $beneficiaryId);
    }

    public function update() {
        $this->requireAuth();
        $this->requirePermission('manage_education');
        $this->validateCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $beneficiaryId = (int)($_POST['beneficiary_id'] ?? 0);
        $userId = $this->currentUser()['id'] ?? null;

        $db = Database::get();
        try {
            $stmt = $db->prepare("UPDATE education_records SET institution_name=?, institution_type=?, course_level=?, academic_session=?, enrollment_status=?, performance=?, remarks=?, start_date=?, end_date=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([
                $_POST['institution'] ?? '',
                $_POST['institution_type'] ?: null,
                $_POST['course_level'] ?: null,
                $_POST['academic_session'] ?: null,
                $_POST['enrollment_status'] ?: null,
                $_POST['performance'] ?: null,
                $_POST['notes'] ?: null,
                $_POST['start_date'] ?: null,
                $_POST['end_date'] ?: null,
                $id
            ]);

            $audit = new AuditService();
            $audit->log((int)$userId, 'update_education', 'education', 'education_records', (string)$id, "Updated education record #{$id}");

            $this->flash('success', 'Education record updated successfully.');
        } catch (\Exception $e) {
            $this->flash('error', 'Failed to update record: ' . $e->getMessage());
        }
        $this->redirect('/admin/education?beneficiary_id=' . $beneficiaryId);
    }
}
