<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;
use GOF\Services\IdGenerator;
use GOF\Services\RBACService;

class StaffController extends BaseController
{
    private const ROLE_SUBQUERY =
        "(SELECT r.id
          FROM user_roles ur
          JOIN roles r ON r.id = ur.role_id
          WHERE ur.user_id = u.id
          ORDER BY ur.assigned_at ASC
          LIMIT 1)";

    private const ROLE_NAME_SUBQUERY =
        "(SELECT r.name
          FROM user_roles ur
          JOIN roles r ON r.id = ur.role_id
          WHERE ur.user_id = u.id
          ORDER BY ur.assigned_at ASC
          LIMIT 1)";


    /*
    |--------------------------------------------------------------------------
    | Staff List
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $this->requireAuth();
        $this->requirePermission('view_staff');

        $db = Database::get();

        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $search = trim($_GET['search'] ?? '');

        $where = [];
        $params = [];

        if ($search !== '') {

            $where[] = "
                (
                    u.first_name LIKE :s
                    OR u.last_name LIKE :s
                    OR s.department LIKE :s
                    OR s.position LIKE :s
                )
            ";

            $params[':s'] = "%{$search}%";
        }

        $whereClause = $where
            ? 'WHERE ' . implode(' AND ', $where)
            : '';


        /*
        |--------------------------------------------------------------------------
        | Total
        |--------------------------------------------------------------------------
        */
        $stmt = $db->prepare("
            SELECT COUNT(*)
            FROM staff s
            JOIN users u ON s.user_id = u.id
            {$whereClause}
        ");

        $stmt->execute($params);

        $total = (int)$stmt->fetchColumn();

        $totalPages = (int)ceil($total / $limit);


        /*
        |--------------------------------------------------------------------------
        | Staff Records
        |--------------------------------------------------------------------------
        */
        $sql = "
            SELECT
                s.*,
                u.first_name,
                u.last_name,
                u.email,
                u.status AS user_status,

                " . self::ROLE_SUBQUERY . " AS role_id,

                " . self::ROLE_NAME_SUBQUERY . " AS role_name

            FROM staff s

            JOIN users u
                ON s.user_id = u.id

            {$whereClause}

            ORDER BY u.first_name ASC

            LIMIT {$limit}
            OFFSET {$offset}
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        $staffList = $stmt->fetchAll();


        $this->render(
            'admin/staff/index.php',
            compact(
                'staffList',
                'page',
                'totalPages',
                'search'
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
        $this->requirePermission('create_staff');

        $db = Database::get();

        $roles = $db
            ->query("SELECT * FROM roles ORDER BY name ASC")
            ->fetchAll();

        $this->render(
            'admin/staff/create.php',
            compact('roles')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Store Staff
    |--------------------------------------------------------------------------
    */
    public function store()
    {
        $this->requireAuth();
        $this->requirePermission('create_staff');
        $this->validateCsrf();

        $db = Database::get();

        $fn = trim($_POST['first_name'] ?? '');
        $ln = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = $_POST['role_id'] ?? null;

        if (!$fn || !$ln || !$email || !$password) {

            $this->flash(
                'error',
                'First name, last name, email, and password are required.'
            );

            $this->redirect('/admin/staff/create');
        }


        /*
        |--------------------------------------------------------------------------
        | Check Email
        |--------------------------------------------------------------------------
        */
        $stmt = $db->prepare("
            SELECT id
            FROM users
            WHERE email = :email
        ");

        $stmt->execute([
            ':email' => $email
        ]);

        if ($stmt->fetch()) {

            $this->flash(
                'error',
                'Email is already in use.'
            );

            $this->redirect('/admin/staff/create');
        }


        $uploadedPhoto = null;


        try {

            $db->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Upload Staff Photo
            |--------------------------------------------------------------------------
            */
            $uploadedPhoto = $this->handlePhotoUpload();


            /*
            |--------------------------------------------------------------------------
            | Create User
            |--------------------------------------------------------------------------
            */
            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $db->prepare("
                INSERT INTO users
                (
                    first_name,
                    last_name,
                    email,
                    password_hash,
                    account_type,
                    status,
                    created_at,
                    updated_at
                )
                VALUES
                (
                    ?,
                    ?,
                    ?,
                    ?,
                    'staff',
                    'active',
                    NOW(),
                    NOW()
                )
            ");

            $stmt->execute([
                $fn,
                $ln,
                $email,
                $hash
            ]);

            $userId = (int)$db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | Generate GOF Staff ID
            |--------------------------------------------------------------------------
            */
            $idGenerator = new IdGenerator();

            $seq = $idGenerator->next('staff');

            $gofId = $idGenerator->formatGOFId(
                'staff',
                $seq
            );


            /*
            |--------------------------------------------------------------------------
            | Create Staff Record
            |--------------------------------------------------------------------------
            */
            $stmt = $db->prepare("
                INSERT INTO staff
                (
                    user_id,
                    gof_id,
                    full_name,
                    phone,
                    department,
                    position,
                    employment_type,
                    employment_date,
                    emergency_contact_name,
                    emergency_contact_phone,
                    photo,
                    status,
                    created_by,
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
                    ?,
                    'active',
                    ?,
                    NOW(),
                    NOW()
                )
            ");

            $stmt->execute([
                $userId,
                $gofId,
                trim("$fn $ln"),
                $_POST['phone'] ?? null,
                $_POST['department'] ?? null,
                $_POST['position'] ?? null,
                $_POST['employment_type'] ?? null,
                $_POST['employment_date'] ?? null,
                $_POST['emergency_contact_name'] ?? null,
                $_POST['emergency_contact_phone'] ?? null,
                $uploadedPhoto,
                $this->currentUser()['id'] ?? null
            ]);

            $staffId = (int)$db->lastInsertId();


            /*
            |--------------------------------------------------------------------------
            | Assign Role
            |--------------------------------------------------------------------------
            */
            if ($roleId) {

                $rbac = new RBACService();

                $rbac->assignRole(
                    $userId,
                    (int)$roleId,
                    (int)($this->currentUser()['id'] ?? 0)
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */
            $audit = new AuditService();

            $audit->log(
                (int)($this->currentUser()['id'] ?? 0),
                'create_staff',
                'staff',
                'staff',
                (string)$staffId,
                "Created staff member {$gofId}"
            );


            $db->commit();


            $this->flash(
                'success',
                'Staff member created successfully.'
            );

            $this->redirect('/admin/staff');

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }


            /*
            |--------------------------------------------------------------------------
            | Delete Uploaded Photo If Creation Failed
            |--------------------------------------------------------------------------
            */
            if ($uploadedPhoto) {
                $this->deletePhoto($uploadedPhoto);
            }


            $this->flash(
                'error',
                'Failed to create staff member: ' . $e->getMessage()
            );

            $this->redirect('/admin/staff/create');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Show Staff
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $this->requireAuth();
        $this->requirePermission('view_staff');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT
                s.*,
                u.first_name,
                u.last_name,
                u.email,
                u.status AS user_status,

                " . self::ROLE_SUBQUERY . " AS role_id,

                " . self::ROLE_NAME_SUBQUERY . " AS role_name

            FROM staff s

            JOIN users u
                ON s.user_id = u.id

            WHERE s.id = ?
        ");

        $stmt->execute([$id]);

        $staff = $stmt->fetch();


        if (!$staff) {

            $this->flash(
                'error',
                'Staff not found.'
            );

            $this->redirect('/admin/staff');
        }


        /*
        |--------------------------------------------------------------------------
        | ID Card
        |--------------------------------------------------------------------------
        */
        $stmt = $db->prepare("
            SELECT *
            FROM id_cards
            WHERE person_type = 'staff'
              AND person_id = ?
            ORDER BY created_at DESC
            LIMIT 1
        ");

        $stmt->execute([
            $staff['id']
        ]);

        $idCard = $stmt->fetch() ?: null;


        $this->render(
            'admin/staff/show.php',
            compact(
                'staff',
                'idCard'
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
        $this->requirePermission('edit_staff');

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT
                s.*,
                u.first_name,
                u.last_name,
                u.email,

                " . self::ROLE_SUBQUERY . " AS role_id

            FROM staff s

            JOIN users u
                ON s.user_id = u.id

            WHERE s.id = ?
        ");

        $stmt->execute([$id]);

        $staff = $stmt->fetch();


        if (!$staff) {

            $this->flash(
                'error',
                'Staff not found.'
            );

            $this->redirect('/admin/staff');
        }


        $roles = $db
            ->query("SELECT * FROM roles ORDER BY name ASC")
            ->fetchAll();


        $this->render(
            'admin/staff/edit.php',
            compact(
                'staff',
                'roles'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Update Staff
    |--------------------------------------------------------------------------
    */
    public function update($id)
    {
        $this->requireAuth();
        $this->requirePermission('edit_staff');
        $this->validateCsrf();

        $db = Database::get();


        /*
        |--------------------------------------------------------------------------
        | Get Existing Staff
        |--------------------------------------------------------------------------
        */
        $stmt = $db->prepare("
            SELECT
                user_id,
                photo
            FROM staff
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $staff = $stmt->fetch();


        if (!$staff) {

            $this->flash(
                'error',
                'Staff not found.'
            );

            $this->redirect('/admin/staff');
        }


        $oldPhoto = trim(
            (string)($staff['photo'] ?? '')
        );

        $newPhoto = null;


        $fn = trim($_POST['first_name'] ?? '');
        $ln = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $roleId = $_POST['role_id'] ?? null;


        if (!$fn || !$ln || !$email) {

            $this->flash(
                'error',
                'First name, last name, and email are required.'
            );

            $this->redirect(
                '/admin/staff/' . $id . '/edit'
            );
        }


        try {

            /*
            |--------------------------------------------------------------------------
            | Upload New Photo
            |--------------------------------------------------------------------------
            */
            $newPhoto = $this->handlePhotoUpload();


            /*
            |--------------------------------------------------------------------------
            | Keep Old Photo If No New Photo
            |--------------------------------------------------------------------------
            */
            $photoToSave = $newPhoto ?: $oldPhoto;


            $db->beginTransaction();


            /*
            |--------------------------------------------------------------------------
            | Update User
            |--------------------------------------------------------------------------
            */
            if ($password) {

                $hash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $db->prepare("
                    UPDATE users
                    SET
                        first_name = ?,
                        last_name = ?,
                        email = ?,
                        password_hash = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");

                $stmt->execute([
                    $fn,
                    $ln,
                    $email,
                    $hash,
                    $staff['user_id']
                ]);

            } else {

                $stmt = $db->prepare("
                    UPDATE users
                    SET
                        first_name = ?,
                        last_name = ?,
                        email = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");

                $stmt->execute([
                    $fn,
                    $ln,
                    $email,
                    $staff['user_id']
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Sync Single Role
            |--------------------------------------------------------------------------
            */
            $db->prepare("
                DELETE FROM user_roles
                WHERE user_id = ?
            ")->execute([
                $staff['user_id']
            ]);


            if ($roleId) {

                $rbac = new RBACService();

                $rbac->assignRole(
                    (int)$staff['user_id'],
                    (int)$roleId,
                    (int)($this->currentUser()['id'] ?? 0)
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Update Staff
            |--------------------------------------------------------------------------
            */
            $stmt = $db->prepare("
                UPDATE staff
                SET
                    full_name = ?,
                    phone = ?,
                    department = ?,
                    position = ?,
                    employment_type = ?,
                    employment_date = ?,
                    emergency_contact_name = ?,
                    emergency_contact_phone = ?,
                    photo = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                trim("$fn $ln"),
                $_POST['phone'] ?? null,
                $_POST['department'] ?? null,
                $_POST['position'] ?? null,
                $_POST['employment_type'] ?? null,
                $_POST['employment_date'] ?? null,
                $_POST['emergency_contact_name'] ?? null,
                $_POST['emergency_contact_phone'] ?? null,
                $photoToSave,
                $id
            ]);


            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */
            $audit = new AuditService();

            $audit->log(
                (int)($this->currentUser()['id'] ?? 0),
                'update_staff',
                'staff',
                'staff',
                (string)$id,
                "Updated staff member #{$id}"
            );


            $db->commit();


            /*
            |--------------------------------------------------------------------------
            | Delete Old Photo After Successful Update
            |--------------------------------------------------------------------------
            */
            if (
                $newPhoto &&
                $oldPhoto &&
                $oldPhoto !== $newPhoto
            ) {
                $this->deletePhoto($oldPhoto);
            }


            $this->flash(
                'success',
                'Staff member updated successfully.'
            );

            $this->redirect('/admin/staff');

        } catch (\Throwable $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }


            /*
            |--------------------------------------------------------------------------
            | Delete New Photo If Update Failed
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
                '/admin/staff/' . $id . '/edit'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Upload Staff Photo
    |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | No Photo Selected
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
        if (
            ($file['error'] ?? UPLOAD_ERR_OK)
            !== UPLOAD_ERR_OK
        ) {
            throw new \RuntimeException(
                'Photo upload failed. Please try again.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Maximum 5 MB
        |--------------------------------------------------------------------------
        */
        if (
            ($file['size'] ?? 0)
            > (5 * 1024 * 1024)
        ) {
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

        $mime = $finfo->file(
            $file['tmp_name']
        );


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
        | Verify Real Image
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | Staff Photo Directory
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
            . 'staff';


        /*
        |--------------------------------------------------------------------------
        | Create Directory Automatically
        |--------------------------------------------------------------------------
        */
        if (!is_dir($uploadDirectory)) {

            if (!mkdir(
                $uploadDirectory,
                0755,
                true
            )) {
                throw new \RuntimeException(
                    'Unable to create staff photo directory.'
                );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Check Writable
        |--------------------------------------------------------------------------
        */
        if (!is_writable($uploadDirectory)) {
            throw new \RuntimeException(
                'Staff photo directory is not writable.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Secure Filename
        |--------------------------------------------------------------------------
        */
        $extension = $allowedTypes[$mime];

        $filename =
            'staff_'
            . bin2hex(random_bytes(16))
            . '.'
            . $extension;


        $destination =
            $uploadDirectory
            . DIRECTORY_SEPARATOR
            . $filename;


        /*
        |--------------------------------------------------------------------------
        | Move File
        |--------------------------------------------------------------------------
        */
        if (!move_uploaded_file(
            $file['tmp_name'],
            $destination
        )) {
            throw new \RuntimeException(
                'Unable to save uploaded staff photo.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Database Path
        |--------------------------------------------------------------------------
        */
        return 'uploads/photos/staff/' . $filename;
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Staff Photo
    |--------------------------------------------------------------------------
    */
    private function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Security Check
        |--------------------------------------------------------------------------
        */
        $prefix = 'uploads/photos/staff/';

        if (
            strpos($photo, $prefix) !== 0
        ) {
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