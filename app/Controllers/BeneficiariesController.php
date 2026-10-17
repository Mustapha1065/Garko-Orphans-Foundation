<?php

namespace GOF\Controllers;

use GOF\Models\Beneficiary as BeneficiaryModel;
use GOF\Services\AuditService;
use GOF\Services\IdGenerator;
use GOF\Helpers\Database;

class BeneficiariesController extends BaseController
{
    /*
    |--------------------------------------------------------------------------
    | Beneficiary List
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('view_beneficiaries');

        $model = new BeneficiaryModel();

        $filters = [
            'gof_id' => $_GET['gof_id'] ?? '',
            'name'   => $_GET['name'] ?? '',
            'status' => $_GET['status'] ?? '',
            'gender' => $_GET['gender'] ?? '',
            'state'  => $_GET['state'] ?? ''
        ];

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $beneficiaries = $model->search($filters, $limit, $offset);
        $total = $model->count($filters);
        $totalPages = (int)ceil($total / $limit);

        $statusCounts = $model->countByStatus();

        $this->render(
            'admin/beneficiaries/index.php',
            compact(
                'beneficiaries',
                'filters',
                'page',
                'totalPages',
                'statusCounts'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Create Form
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        $this->requireAuth();
        $this->requirePermission('create_beneficiary');

        $this->render('admin/beneficiaries/create.php');
    }


    /*
    |--------------------------------------------------------------------------
    | Store Beneficiary
    |--------------------------------------------------------------------------
    */
    public function store()
    {
        $this->requireAuth();
        $this->requirePermission('create_beneficiary');
        $this->validateCsrf();

        $db = Database::get();

        $fullName = trim($_POST['full_name'] ?? '');

        if ($fullName === '') {
            $this->flash('error', 'Full name is required.');
            $this->redirect('/admin/beneficiaries/create');
        }

        $userId = $this->currentUser()['id'] ?? null;

        $uploadedPhoto = null;

        try {

            $db->beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Upload Photo
            |--------------------------------------------------------------------------
            */
            $uploadedPhoto = $this->handlePhotoUpload();

            /*
            |--------------------------------------------------------------------------
            | Insert Beneficiary
            |--------------------------------------------------------------------------
            */
            $stmt = $db->prepare("
                INSERT INTO beneficiaries
                (
                    full_name,
                    date_of_birth,
                    gender,
                    phone,
                    address,
                    state,
                    lga,
                    notes,
                    photo,
                    status,
                    created_by,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW()
                )
            ");

            $stmt->execute([
                $fullName,
                $_POST['dob'] ?: null,
                $_POST['gender'] ?: null,
                $_POST['phone'] ?: null,
                $_POST['address'] ?: null,
                $_POST['state'] ?: null,
                $_POST['lga'] ?: null,
                $_POST['notes'] ?: null,
                $uploadedPhoto,
                $userId
            ]);

            $newId = (int)$db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | Guardian
            |--------------------------------------------------------------------------
            */
            $guardianName = trim($_POST['guardian_name'] ?? '');

            if ($guardianName !== '') {

                $stmtG = $db->prepare("
                    INSERT INTO guardians
                    (
                        beneficiary_id,
                        full_name,
                        relationship,
                        phone,
                        is_emergency,
                        created_at
                    )
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");

                $stmtG->execute([
                    $newId,
                    $guardianName,
                    $_POST['guardian_relationship'] ?: null,
                    $_POST['guardian_phone'] ?: null,
                    isset($_POST['guardian_is_emergency']) ? 1 : 0
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */
            $audit = new AuditService();

            $audit->log(
                (int)$userId,
                'create_beneficiary',
                'beneficiary',
                'beneficiary',
                (string)$newId,
                "Registered beneficiary {$fullName} (pending approval)"
            );


            $db->commit();


            $this->flash(
                'success',
                'Beneficiary registered successfully. Pending approval.'
            );

            $this->redirect('/admin/beneficiaries');

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            /*
            |--------------------------------------------------------------------------
            | Remove Uploaded Photo If Database Insert Failed
            |--------------------------------------------------------------------------
            */
            if ($uploadedPhoto) {
                $this->deletePhoto($uploadedPhoto);
            }

            $this->flash(
                'error',
                'Failed to register beneficiary: ' . $e->getMessage()
            );

            $this->redirect('/admin/beneficiaries/create');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show Beneficiary
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $this->requireAuth();
        $this->requirePermission('view_beneficiaries');

        $model = new BeneficiaryModel();

        $beneficiary = $model->findWithDetails((int)$id);

        if (!$beneficiary) {

            $this->flash(
                'error',
                'Beneficiary not found.'
            );

            $this->redirect('/admin/beneficiaries');
        }


        $db = Database::get();

        /*
        |--------------------------------------------------------------------------
        | ID Card
        |--------------------------------------------------------------------------
        */
        $stmt = $db->prepare("
            SELECT *
            FROM id_cards
            WHERE person_type = 'beneficiary'
              AND person_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $idCard = $stmt->fetch() ?: null;


        /*
        |--------------------------------------------------------------------------
        | Documents
        |--------------------------------------------------------------------------
        */
        $documents = (
            new \GOF\Services\DocumentService()
        )->getForEntity(
            'beneficiary',
            (int)$id
        );


        $this->render(
            'admin/beneficiaries/show.php',
            compact(
                'beneficiary',
                'idCard',
                'documents'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Form
    |--------------------------------------------------------------------------
    */
    public function edit($id)
    {
        $this->requireAuth();
        $this->requirePermission('edit_beneficiary');

        $model = new BeneficiaryModel();

        $beneficiary = $model->findWithDetails((int)$id);

        if (!$beneficiary) {

            $this->flash(
                'error',
                'Beneficiary not found.'
            );

            $this->redirect('/admin/beneficiaries');
        }

        $this->render(
            'admin/beneficiaries/edit.php',
            compact('beneficiary')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Beneficiary
    |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        $this->requireAuth();
        $this->requirePermission('edit_beneficiary');
        $this->validateCsrf();

        $db = Database::get();

        $fullName = trim($_POST['full_name'] ?? '');

        if ($fullName === '') {

            $this->flash(
                'error',
                'Full name is required.'
            );

            $this->redirect(
                '/admin/beneficiaries/' . $id . '/edit'
            );
        }

        $userId = $this->currentUser()['id'] ?? null;

        $newPhoto = null;
        $oldPhoto = null;

        try {

            /*
            |--------------------------------------------------------------------------
            | Get Existing Photo
            |--------------------------------------------------------------------------
            */
            $existingStmt = $db->prepare("
                SELECT photo
                FROM beneficiaries
                WHERE id = ?
                LIMIT 1
            ");

            $existingStmt->execute([$id]);

            $existingBeneficiary = $existingStmt->fetch(
                \PDO::FETCH_ASSOC
            );

            if (!$existingBeneficiary) {

                $this->flash(
                    'error',
                    'Beneficiary not found.'
                );

                $this->redirect('/admin/beneficiaries');
            }

            $oldPhoto = trim(
                (string)($existingBeneficiary['photo'] ?? '')
            );


            /*
            |--------------------------------------------------------------------------
            | Upload New Photo If Provided
            |--------------------------------------------------------------------------
            */
            $newPhoto = $this->handlePhotoUpload();


            /*
            |--------------------------------------------------------------------------
            | If No New Photo Was Uploaded,
            | Keep Existing Photo
            |--------------------------------------------------------------------------
            */
            $photoToSave = $newPhoto ?: $oldPhoto;


            $db->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Update Beneficiary
            |--------------------------------------------------------------------------
            */
            $stmt = $db->prepare("
                UPDATE beneficiaries
                SET
                    full_name = ?,
                    date_of_birth = ?,
                    gender = ?,
                    phone = ?,
                    address = ?,
                    state = ?,
                    lga = ?,
                    notes = ?,
                    photo = ?,
                    status = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $fullName,
                $_POST['dob'] ?: null,
                $_POST['gender'] ?: null,
                $_POST['phone'] ?: null,
                $_POST['address'] ?: null,
                $_POST['state'] ?: null,
                $_POST['lga'] ?: null,
                $_POST['notes'] ?: null,
                $photoToSave,
                $_POST['status'] ?? 'pending',
                $id
            ]);


            /*
            |--------------------------------------------------------------------------
            | Replace Guardian
            |--------------------------------------------------------------------------
            */
            $db->prepare("
                DELETE FROM guardians
                WHERE beneficiary_id = ?
            ")->execute([$id]);


            $guardianName = trim(
                $_POST['guardian_name'] ?? ''
            );

            if ($guardianName !== '') {

                $stmtG = $db->prepare("
                    INSERT INTO guardians
                    (
                        beneficiary_id,
                        full_name,
                        relationship,
                        phone,
                        is_emergency,
                        created_at
                    )
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");

                $stmtG->execute([
                    $id,
                    $guardianName,
                    $_POST['guardian_relationship'] ?: null,
                    $_POST['guardian_phone'] ?: null,
                    isset($_POST['guardian_is_emergency']) ? 1 : 0
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */
            $audit = new AuditService();

            $audit->log(
                (int)$userId,
                'update_beneficiary',
                'beneficiary',
                'beneficiary',
                (string)$id,
                "Updated beneficiary #{$id}"
            );


            $db->commit();


            /*
            |--------------------------------------------------------------------------
            | Delete Old Photo Only After Successful Database Update
            |--------------------------------------------------------------------------
            */
            if ($newPhoto && $oldPhoto && $oldPhoto !== $newPhoto) {
                $this->deletePhoto($oldPhoto);
            }


            $this->flash(
                'success',
                'Beneficiary updated successfully.'
            );

            $this->redirect(
                '/admin/beneficiaries/' . $id
            );

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            /*
            |--------------------------------------------------------------------------
            | Delete New Photo If Database Update Failed
            |--------------------------------------------------------------------------
            */
            if ($newPhoto) {
                $this->deletePhoto($newPhoto);
            }

            $this->flash(
                'error',
                'Update failed: ' . $e->getMessage()
            );

            $this->redirect(
                '/admin/beneficiaries/' . $id . '/edit'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Approve Beneficiary
    |--------------------------------------------------------------------------
    */
    public function approve($id)
    {
        $this->requireAuth();
        $this->requirePermission('approve_beneficiary');
        $this->validateCsrf();

        $db = Database::get();

        $userId = $this->currentUser()['id'] ?? null;

        try {

            $db->beginTransaction();

            $idGenerator = new IdGenerator();

            $seq = $idGenerator->next('beneficiary');

            $gofId = $idGenerator->formatGOFId(
                'beneficiary',
                $seq
            );

            $stmt = $db->prepare("
                UPDATE beneficiaries
                SET
                    status = 'active',
                    gof_id = ?,
                    approved_by = ?,
                    approved_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
                  AND status = 'pending'
            ");

            $stmt->execute([
                $gofId,
                $userId,
                $id
            ]);


            if ($stmt->rowCount() > 0) {

                $audit = new AuditService();

                $audit->log(
                    (int)$userId,
                    'approve_beneficiary',
                    'beneficiary',
                    'beneficiary',
                    (string)$id,
                    "Approved beneficiary #{$id} and issued GOF ID {$gofId}"
                );

                $db->commit();

                $this->flash(
                    'success',
                    "Beneficiary approved successfully. GOF ID: {$gofId}"
                );

            } else {

                $db->rollBack();

                $this->flash(
                    'error',
                    'Beneficiary could not be approved (it may not be pending).'
                );
            }

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $this->flash(
                'error',
                'Approval failed: ' . $e->getMessage()
            );
        }

        $this->redirect(
            '/admin/beneficiaries/' . $id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reject Beneficiary
    |--------------------------------------------------------------------------
    */
    public function reject($id)
    {
        $this->requireAuth();
        $this->requirePermission('approve_beneficiary');
        $this->validateCsrf();

        $db = Database::get();

        $userId = $this->currentUser()['id'] ?? null;

        try {

            $db->beginTransaction();

            $stmt = $db->prepare("
                UPDATE beneficiaries
                SET
                    status = 'rejected',
                    rejected_by = ?,
                    rejected_at = NOW(),
                    rejection_reason = ?,
                    updated_at = NOW()
                WHERE id = ?
                  AND status = 'pending'
            ");

            $stmt->execute([
                $userId,
                $_POST['rejection_reason'] ?? '',
                $id
            ]);


            if ($stmt->rowCount() > 0) {

                $audit = new AuditService();

                $audit->log(
                    (int)$userId,
                    'reject_beneficiary',
                    'beneficiary',
                    'beneficiary',
                    (string)$id,
                    "Rejected beneficiary #{$id}"
                );

                $db->commit();

                $this->flash(
                    'success',
                    'Beneficiary rejected.'
                );

            } else {

                $db->rollBack();

                $this->flash(
                    'error',
                    'Beneficiary could not be rejected (it may not be pending).'
                );
            }

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            $this->flash(
                'error',
                'Rejection failed: ' . $e->getMessage()
            );
        }

        $this->redirect(
            '/admin/beneficiaries/' . $id
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Archive Beneficiary
    |--------------------------------------------------------------------------
    */
    public function archive($id)
    {
        $this->requireAuth();
        $this->requirePermission('archive_beneficiary');
        $this->validateCsrf();

        $db = Database::get();

        $userId = $this->currentUser()['id'] ?? null;

        try {

            $stmt = $db->prepare("
                UPDATE beneficiaries
                SET
                    status = 'archived',
                    archived_by = ?,
                    archived_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $userId,
                $id
            ]);


            $audit = new AuditService();

            $audit->log(
                (int)$userId,
                'archive_beneficiary',
                'beneficiary',
                'beneficiary',
                (string)$id,
                "Archived beneficiary #{$id}"
            );


            $this->flash(
                'success',
                'Beneficiary archived.'
            );

        } catch (\Throwable $e) {

            $this->flash(
                'error',
                'Archive failed: ' . $e->getMessage()
            );
        }

        $this->redirect(
            '/admin/beneficiaries'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Handle Beneficiary Photo Upload
    |--------------------------------------------------------------------------
    */
    private function handlePhotoUpload(): ?string
    {
        /*
        |--------------------------------------------------------------------------
        | No Photo Selected
        |--------------------------------------------------------------------------
        */
        if (
            !isset($_FILES['photo']) ||
            !is_array($_FILES['photo'])
        ) {
            return null;
        }


        $file = $_FILES['photo'];


        /*
        |--------------------------------------------------------------------------
        | No Upload
        |--------------------------------------------------------------------------
        */
        if (
            ($file['error'] ?? UPLOAD_ERR_NO_FILE)
            === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Upload Error
        |--------------------------------------------------------------------------
        */
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {

            throw new \RuntimeException(
                'Photo upload failed. Please try again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Maximum Size: 5 MB
        |--------------------------------------------------------------------------
        */
        $maxSize = 5 * 1024 * 1024;

        if (($file['size'] ?? 0) > $maxSize) {

            throw new \RuntimeException(
                'Photo is too large. Maximum allowed size is 5 MB.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Uploaded File
        |--------------------------------------------------------------------------
        */
        if (!is_uploaded_file($file['tmp_name'])) {

            throw new \RuntimeException(
                'Invalid photo upload.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Detect MIME Type
        |--------------------------------------------------------------------------
        */
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        $mime = $finfo->file($file['tmp_name']);


        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];


        if (!isset($allowedTypes[$mime])) {

            throw new \RuntimeException(
                'Invalid photo format. Please use JPG, PNG, or WebP.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Verify Actual Image
        |--------------------------------------------------------------------------
        */
        $imageInfo = @getimagesize($file['tmp_name']);

        if ($imageInfo === false) {

            throw new \RuntimeException(
                'The uploaded file is not a valid image.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Upload Directory
        |--------------------------------------------------------------------------
        */
        $uploadDirectory =
            dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . 'uploads'
            . DIRECTORY_SEPARATOR
            . 'photos'
            . DIRECTORY_SEPARATOR
            . 'beneficiaries';


        /*
        |--------------------------------------------------------------------------
        | Create Directory Automatically
        |--------------------------------------------------------------------------
        */
        if (!is_dir($uploadDirectory)) {

            if (!mkdir($uploadDirectory, 0755, true)) {

                throw new \RuntimeException(
                    'Unable to create beneficiary photo directory.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Check Directory Writable
        |--------------------------------------------------------------------------
        */
        if (!is_writable($uploadDirectory)) {

            throw new \RuntimeException(
                'Beneficiary photo directory is not writable.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Generate Secure Random Filename
        |--------------------------------------------------------------------------
        */
        $extension = $allowedTypes[$mime];

        $filename =
            'beneficiary_'
            . bin2hex(random_bytes(16))
            . '.'
            . $extension;


        $destination =
            $uploadDirectory
            . DIRECTORY_SEPARATOR
            . $filename;


        /*
        |--------------------------------------------------------------------------
        | Move Uploaded File
        |--------------------------------------------------------------------------
        */
        if (!move_uploaded_file(
            $file['tmp_name'],
            $destination
        )) {

            throw new \RuntimeException(
                'Unable to save uploaded photo.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Relative Path Stored In Database
        |--------------------------------------------------------------------------
        */
        return 'uploads/photos/beneficiaries/' . $filename;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Beneficiary Photo
    |--------------------------------------------------------------------------
    */
    private function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Security: Only Delete Beneficiary Photo Paths
        |--------------------------------------------------------------------------
        */
        $prefix = 'uploads/photos/beneficiaries/';

        if (strpos($photo, $prefix) !== 0) {
            return;
        }


        $fullPath =
            dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'public'
            . DIRECTORY_SEPARATOR
            . str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $photo
            );


        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }
}