<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;
use GOF\Services\IdGenerator;

class DonorsController extends BaseController
{
    public function index()
    {
        $this->requirePermission('view_donors');

        $db = Database::get();

        $search = trim($_GET['search'] ?? '');
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $query = "SELECT d.*,
                         (
                            SELECT COALESCE(SUM(amount), 0)
                            FROM donations
                            WHERE donor_id = d.id
                            AND status = 'confirmed'
                         ) AS total_donated
                  FROM donors d";

        $params = [];

        if ($search !== '') {
            $query .= " WHERE
                        d.name LIKE ?
                        OR d.organization_name LIKE ?
                        OR d.gof_id LIKE ?
                        OR d.email LIKE ?";

            $searchParam = "%{$search}%";

            $params = [
                $searchParam,
                $searchParam,
                $searchParam,
                $searchParam
            ];
        }

        $query .= " ORDER BY d.created_at DESC
                    LIMIT $limit OFFSET $offset";

        $stmt = $db->prepare($query);
        $stmt->execute($params);

        $donors = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        /* Total records */
        $countQuery = "SELECT COUNT(*) FROM donors d";

        if ($search !== '') {

            $countQuery .= " WHERE
                            d.name LIKE ?
                            OR d.organization_name LIKE ?
                            OR d.gof_id LIKE ?
                            OR d.email LIKE ?";

            $stmtCount = $db->prepare($countQuery);
            $stmtCount->execute($params);

        } else {

            $stmtCount = $db->query($countQuery);
        }

        $total = (int)$stmtCount->fetchColumn();

        $totalPages = max(1, (int)ceil($total / $limit));

        $this->render(
            'admin/donors/index.php',
            compact(
                'donors',
                'search',
                'page',
                'totalPages'
            )
        );
    }


    public function create()
    {
        $this->requirePermission('manage_donors');

        $this->render('admin/donors/create.php');
    }


    public function store()
    {
        $this->requirePermission('manage_donors');

        $this->validateCsrf();

        $name = trim($_POST['name'] ?? '');
        $organization = trim($_POST['organization'] ?? '');
        $donorType = $_POST['donor_type'] ?? 'individual';
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $country = trim($_POST['country'] ?? 'Nigeria');
        $notes = trim($_POST['notes'] ?? '');

        if ($name === '') {
            $this->flash('error', 'Name is required.');
            $this->redirect('/admin/donors/create');
            return;
        }

        $db = Database::get();

        $userId = $this->currentUser()['id'] ?? null;

        $uploadedPhoto = null;

        $db->beginTransaction();

        try {

            /*
             * Generate GOF donor ID
             */
            $idGenerator = new IdGenerator();

            $seq = $idGenerator->next('donor');

            $gofId = $idGenerator->formatGOFId(
                'donor',
                $seq
            );


            /*
             * Upload donor photo
             */
            $uploadedPhoto = $this->handlePhotoUpload();


            /*
             * Check whether photo column exists.
             *
             * This makes the controller safer if an older database
             * does not yet contain the column.
             */
            $hasPhotoColumn = $this->hasPhotoColumn();


            if ($hasPhotoColumn) {

                $stmt = $db->prepare(
                    "INSERT INTO donors
                    (
                        gof_id,
                        name,
                        organization_name,
                        donor_type,
                        email,
                        phone,
                        address,
                        country,
                        notes,
                        photo,
                        registered_by,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
                );

                $stmt->execute([
                    $gofId,
                    $name,
                    $organization ?: null,
                    $donorType,
                    $email ?: null,
                    $phone ?: null,
                    $address ?: null,
                    $country,
                    $notes ?: null,
                    $uploadedPhoto,
                    $userId
                ]);

            } else {

                /*
                 * Fallback for an older donors table
                 * without photo column.
                 */
                $stmt = $db->prepare(
                    "INSERT INTO donors
                    (
                        gof_id,
                        name,
                        organization_name,
                        donor_type,
                        email,
                        phone,
                        address,
                        country,
                        notes,
                        registered_by,
                        created_at,
                        updated_at
                    )
                    VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())"
                );

                $stmt->execute([
                    $gofId,
                    $name,
                    $organization ?: null,
                    $donorType,
                    $email ?: null,
                    $phone ?: null,
                    $address ?: null,
                    $country,
                    $notes ?: null,
                    $userId
                ]);
            }


            $donorId = (int)$db->lastInsertId();


            /*
             * Audit
             */
            $audit = new AuditService();

            $audit->log(
                (int)$userId,
                'create_donor',
                'donor',
                'donor',
                (string)$donorId,
                "Registered donor {$name} ({$gofId})"
            );


            $db->commit();


            $this->flash(
                'success',
                'Donor registered successfully.'
            );

            $this->redirect('/admin/donors');

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            /*
             * Remove uploaded photo if database operation failed.
             */
            if ($uploadedPhoto) {
                $this->deletePhoto($uploadedPhoto);
            }

            error_log(
                'GOF Donor Store Error: ' . $e->getMessage()
            );

            $this->flash(
                'error',
                'Failed to register donor.'
            );

            $this->redirect('/admin/donors/create');
        }
    }


    public function show($id)
    {
        $this->requirePermission('view_donors');

        $db = Database::get();

        $stmt = $db->prepare(
            "SELECT * FROM donors WHERE id = ?"
        );

        $stmt->execute([$id]);

        $donor = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$donor) {

            $this->flash(
                'error',
                'Donor not found.'
            );

            $this->redirect('/admin/donors');

            return;
        }


        /*
         * Donation history
         */
        $stmtDonations = $db->prepare(
            "SELECT *
             FROM donations
             WHERE donor_id = ?
             ORDER BY donation_date DESC, created_at DESC"
        );

        $stmtDonations->execute([$id]);

        $donations = $stmtDonations->fetchAll(
            \PDO::FETCH_ASSOC
        );


        /*
         * Donation statistics
         */
        $totalAmount = 0;
        $totalCount = 0;
        $lastDonation = null;

        foreach ($donations as $donation) {

            if (($donation['status'] ?? '') === 'confirmed') {

                $totalAmount += (float)$donation['amount'];

                $totalCount++;

                if (
                    $lastDonation === null ||
                    $donation['donation_date'] > $lastDonation
                ) {
                    $lastDonation =
                        $donation['donation_date'];
                }
            }
        }


        $this->render(
            'admin/donors/show.php',
            compact(
                'donor',
                'donations',
                'totalAmount',
                'totalCount',
                'lastDonation'
            )
        );
    }


    public function edit($id)
    {
        $this->requirePermission('manage_donors');

        $db = Database::get();

        $stmt = $db->prepare(
            "SELECT *
             FROM donors
             WHERE id = ?"
        );

        $stmt->execute([$id]);

        $donor = $stmt->fetch(
            \PDO::FETCH_ASSOC
        );

        if (!$donor) {

            $this->flash(
                'error',
                'Donor not found.'
            );

            $this->redirect('/admin/donors');

            return;
        }


        $this->render(
            'admin/donors/edit.php',
            compact('donor')
        );
    }


    public function update($id)
    {
        $this->requirePermission('manage_donors');

        $this->validateCsrf();

        $name = trim($_POST['name'] ?? '');
        $organization = trim($_POST['organization'] ?? '');
        $donorType = $_POST['donor_type'] ?? 'individual';
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $country = trim($_POST['country'] ?? 'Nigeria');
        $notes = trim($_POST['notes'] ?? '');
        $status = trim($_POST['status'] ?? 'active');


        if ($name === '') {

            $this->flash(
                'error',
                'Name is required.'
            );

            $this->redirect(
                '/admin/donors/' . (int)$id . '/edit'
            );

            return;
        }


        $db = Database::get();

        $userId =
            $this->currentUser()['id'] ?? null;


        /*
         * Get current donor first.
         */
        $stmt = $db->prepare(
            "SELECT *
             FROM donors
             WHERE id = ?"
        );

        $stmt->execute([$id]);

        $existingDonor =
            $stmt->fetch(\PDO::FETCH_ASSOC);


        if (!$existingDonor) {

            $this->flash(
                'error',
                'Donor not found.'
            );

            $this->redirect('/admin/donors');

            return;
        }


        $oldPhoto =
            trim((string)(
                $existingDonor['photo'] ?? ''
            ));

        $newPhoto = null;


        $db->beginTransaction();

        try {

            /*
             * Upload replacement photo only
             * when the user selected a new one.
             */
            $newPhoto = $this->handlePhotoUpload();


            $hasPhotoColumn =
                $this->hasPhotoColumn();


            if ($hasPhotoColumn) {

                /*
                 * If no new photo was selected,
                 * preserve the existing photo.
                 */
                $photoToSave =
                    $newPhoto !== null
                        ? $newPhoto
                        : ($oldPhoto !== ''
                            ? $oldPhoto
                            : null);


                $stmt = $db->prepare(
                    "UPDATE donors SET
                        name = ?,
                        organization_name = ?,
                        donor_type = ?,
                        email = ?,
                        phone = ?,
                        address = ?,
                        country = ?,
                        notes = ?,
                        status = ?,
                        photo = ?,
                        updated_at = NOW()
                     WHERE id = ?"
                );

                $stmt->execute([
                    $name,
                    $organization ?: null,
                    $donorType,
                    $email ?: null,
                    $phone ?: null,
                    $address ?: null,
                    $country,
                    $notes ?: null,
                    $status,
                    $photoToSave,
                    $id
                ]);

            } else {

                $stmt = $db->prepare(
                    "UPDATE donors SET
                        name = ?,
                        organization_name = ?,
                        donor_type = ?,
                        email = ?,
                        phone = ?,
                        address = ?,
                        country = ?,
                        notes = ?,
                        status = ?,
                        updated_at = NOW()
                     WHERE id = ?"
                );

                $stmt->execute([
                    $name,
                    $organization ?: null,
                    $donorType,
                    $email ?: null,
                    $phone ?: null,
                    $address ?: null,
                    $country,
                    $notes ?: null,
                    $status,
                    $id
                ]);
            }


            /*
             * Audit
             */
            $audit = new AuditService();

            $audit->log(
                (int)$userId,
                'update_donor',
                'donor',
                'donor',
                (string)$id,
                "Updated donor {$name} (ID: {$id})"
            );


            $db->commit();


            /*
             * Delete old photo only after
             * successful database update.
             */
            if (
                $newPhoto !== null &&
                $oldPhoto !== '' &&
                $oldPhoto !== $newPhoto
            ) {
                $this->deletePhoto($oldPhoto);
            }


            $this->flash(
                'success',
                'Donor updated successfully.'
            );

            $this->redirect(
                '/admin/donors/' . (int)$id
            );

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }


            /*
             * Remove newly uploaded photo
             * when update failed.
             */
            if ($newPhoto !== null) {
                $this->deletePhoto($newPhoto);
            }


            error_log(
                'GOF Donor Update Error: ' .
                $e->getMessage()
            );


            $this->flash(
                'error',
                'Failed to update donor.'
            );

            $this->redirect(
                '/admin/donors/' . (int)$id . '/edit'
            );
        }
    }


    /*
     * ==========================================================
     * PHOTO UPLOAD
     * ==========================================================
     */
    private function handlePhotoUpload(): ?string
    {
        if (
            !isset($_FILES['photo']) ||
            !is_array($_FILES['photo'])
        ) {
            return null;
        }


        $file = $_FILES['photo'];


        /*
         * No file selected.
         */
        if (
            ($file['error'] ?? UPLOAD_ERR_NO_FILE)
            === UPLOAD_ERR_NO_FILE
        ) {
            return null;
        }


        if (
            ($file['error'] ?? UPLOAD_ERR_OK)
            !== UPLOAD_ERR_OK
        ) {
            throw new \RuntimeException(
                'Photo upload failed.'
            );
        }


        /*
         * Maximum 5 MB.
         */
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            throw new \RuntimeException(
                'Photo must not exceed 5 MB.'
            );
        }


        if (!is_uploaded_file($file['tmp_name'])) {
            throw new \RuntimeException(
                'Invalid uploaded file.'
            );
        }


        /*
         * Detect actual MIME type.
         */
        $finfo = new \finfo(
            FILEINFO_MIME_TYPE
        );

        $mime = $finfo->file(
            $file['tmp_name']
        );


        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];


        if (!isset($allowed[$mime])) {
            throw new \RuntimeException(
                'Only JPG, PNG and WebP images are allowed.'
            );
        }


        /*
         * Confirm it is actually an image.
         */
        if (
            @getimagesize(
                $file['tmp_name']
            ) === false
        ) {
            throw new \RuntimeException(
                'The uploaded file is not a valid image.'
            );
        }


        /*
         * Storage directory.
         */
        $directory =
            __DIR__ .
            '/../../public/uploads/photos/donors';


        if (!is_dir($directory)) {

            if (
                !mkdir(
                    $directory,
                    0755,
                    true
                )
            ) {
                throw new \RuntimeException(
                    'Unable to create donor photo directory.'
                );
            }
        }


        /*
         * Random filename.
         */
        $filename =
            'donor_' .
            bin2hex(random_bytes(16)) .
            '.' .
            $allowed[$mime];


        $destination =
            $directory .
            DIRECTORY_SEPARATOR .
            $filename;


        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination
            )
        ) {
            throw new \RuntimeException(
                'Unable to save donor photo.'
            );
        }


        /*
         * Relative path saved in database.
         */
        return
            'uploads/photos/donors/' .
            $filename;
    }


    /*
     * ==========================================================
     * DELETE PHOTO
     * ==========================================================
     */
    private function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }


        $photo = ltrim($photo, '/');


        /*
         * Security restriction:
         * only donor photos can be deleted here.
         */
        if (
            strpos(
                $photo,
                'uploads/photos/donors/'
            ) !== 0
        ) {
            return;
        }


        $fullPath =
            __DIR__ .
            '/../../public/' .
            $photo;


        if (
            is_file($fullPath)
        ) {
            @unlink($fullPath);
        }
    }


    /*
     * ==========================================================
     * CHECK PHOTO COLUMN
     * ==========================================================
     */
    private function hasPhotoColumn(): bool
    {
        $db = Database::get();

        $stmt = $db->query(
            "SHOW COLUMNS FROM donors LIKE 'photo'"
        );

        return (bool)$stmt->fetch(
            \PDO::FETCH_ASSOC
        );
    }
}