<?php

namespace GOF\Controllers;

use GOF\Services\DocumentService;

class DocumentsController extends BaseController
{
    private const ALLOWED_ENTITY_TYPES = ['beneficiary', 'donor', 'staff', 'program', 'volunteer', 'general'];

    /**
     * Upload a document attached to an existing, approved record.
     * FR-035: files must be validated (handled by FileUpload inside DocumentService).
     */
    public function upload()
    {
        $this->requirePermission('upload_documents');
        $this->validateCsrf();

        $entityType = $_POST['entity_type'] ?? '';
        $entityId = (int) ($_POST['entity_id'] ?? 0);
        $description = trim($_POST['description'] ?? '') ?: null;

        if (!in_array($entityType, self::ALLOWED_ENTITY_TYPES, true) || $entityId <= 0) {
            $this->flash('error', 'Invalid document target.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
            return;
        }

        if (empty($_FILES['document']) || ($_FILES['document']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $this->flash('error', 'Please choose a file to upload.');
            $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
            return;
        }

        $userId = (int) ($this->currentUser()['id'] ?? 0);

        try {
            (new DocumentService())->store($_FILES['document'], $entityType, $entityId, $userId, $description);
            $this->flash('success', 'Document uploaded successfully.');
        } catch (\Throwable $e) {
            $this->flash('error', 'Upload failed: ' . $e->getMessage());
        }

        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
    }

    /**
     * FR-035: downloads must validate ownership or permission; storage lives
     * outside the public web root so there is no predictable public URL.
     */
    public function download($id)
    {
        $this->requireAuth();
        $userId = (int) ($this->currentUser()['id'] ?? 0);
        (new DocumentService())->download((int) $id, $userId);
    }

    public function delete($id)
    {
        $this->requirePermission('delete_documents');
        $this->validateCsrf();

        $userId = (int) ($this->currentUser()['id'] ?? 0);
        $ok = (new DocumentService())->delete((int) $id, $userId);

        $this->flash($ok ? 'success' : 'error', $ok ? 'Document deleted.' : 'Document not found.');
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin');
    }
}
