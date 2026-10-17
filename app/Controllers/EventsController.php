<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;

class EventsController extends BaseController
{
    public function index(): void
    {
        $this->requirePermission('manage_events');

        $db = Database::get();

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? '');
        $visibility = trim($_GET['visibility'] ?? '');

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(title LIKE ? OR description LIKE ? OR location LIKE ?)';
            $term = '%' . $search . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $allowedStatuses = [
            'upcoming',
            'ongoing',
            'completed',
            'cancelled'
        ];

        if (
            $status !== '' &&
            in_array($status, $allowedStatuses, true)
        ) {
            $where[] = 'status = ?';
            $params[] = $status;
        }

        if ($visibility === 'public') {
            $where[] = 'is_public = 1';
        } elseif ($visibility === 'private') {
            $where[] = 'is_public = 0';
        }

        $whereSql = '';

        if ($where) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        $stmt = $db->prepare("
            SELECT *
            FROM events
            {$whereSql}
            ORDER BY
                CASE
                    WHEN status = 'upcoming' THEN 1
                    WHEN status = 'ongoing' THEN 2
                    WHEN status = 'completed' THEN 3
                    WHEN status = 'cancelled' THEN 4
                    ELSE 5
                END,
                event_date ASC,
                event_time ASC,
                id DESC
        ");

        $stmt->execute($params);

        $events = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $summary = $db->query("
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(status = 'upcoming'), 0) AS upcoming,
                COALESCE(SUM(status = 'ongoing'), 0) AS ongoing,
                COALESCE(SUM(status = 'completed'), 0) AS completed,
                COALESCE(SUM(status = 'cancelled'), 0) AS cancelled,
                COALESCE(SUM(is_public = 1), 0) AS public_events
            FROM events
        ")->fetch(\PDO::FETCH_ASSOC);

        $this->render(
            'admin/events/index.php',
            compact(
                'events',
                'search',
                'status',
                'visibility',
                'summary'
            )
        );
    }


    public function create(): void
    {
        $this->requirePermission('manage_events');

        $this->render('admin/events/create.php');
    }


    public function store(): void
    {
        $this->requirePermission('manage_events');

        $this->validateEventCsrf();

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $eventDate = trim($_POST['event_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $eventTime = trim($_POST['event_time'] ?? '');
        $status = trim($_POST['status'] ?? 'upcoming');
        $isPublic = isset($_POST['is_public']) ? 1 : 0;

        if ($title === '') {
            $this->flash('error', 'Event title is required.');
            $this->redirect('/admin/events/create');
            return;
        }

        if (mb_strlen($title) > 200) {
            $this->flash('error', 'Event title must not exceed 200 characters.');
            $this->redirect('/admin/events/create');
            return;
        }

        if ($eventDate === '' || !$this->validDate($eventDate)) {
            $this->flash('error', 'Please enter a valid event date.');
            $this->redirect('/admin/events/create');
            return;
        }

        if ($endDate !== '' && !$this->validDate($endDate)) {
            $this->flash('error', 'Please enter a valid end date.');
            $this->redirect('/admin/events/create');
            return;
        }

        if ($endDate !== '' && $endDate < $eventDate) {
            $this->flash('error', 'The end date cannot be earlier than the event date.');
            $this->redirect('/admin/events/create');
            return;
        }

        if ($eventTime !== '' && !$this->validTime($eventTime)) {
            $this->flash('error', 'Please enter a valid event time.');
            $this->redirect('/admin/events/create');
            return;
        }

        $allowedStatuses = [
            'upcoming',
            'ongoing',
            'completed',
            'cancelled'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'upcoming';
        }

        $db = Database::get();
        $imagePath = null;

        try {

            if ($this->hasUploadedImage('image')) {
                $imagePath = $this->uploadEventImage(
                    $_FILES['image']
                );
            }

            $stmt = $db->prepare("
                INSERT INTO events
                (
                    title,
                    description,
                    event_date,
                    end_date,
                    event_time,
                    location,
                    status,
                    is_public,
                    image,
                    created_at,
                    updated_at
                )
                VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->execute([
                $title,
                $description !== '' ? $description : null,
                $eventDate,
                $endDate !== '' ? $endDate : null,
                $eventTime !== '' ? $eventTime : null,
                $location !== '' ? $location : null,
                $status,
                $isPublic,
                $imagePath
            ]);

            $eventId = (int)$db->lastInsertId();

            $this->audit(
                'create_event',
                $eventId,
                "Created event {$title}"
            );

            $this->flash(
                'success',
                'Event created successfully.'
            );

            $this->redirect('/admin/events');
            return;

        } catch (\Throwable $e) {

            if ($imagePath !== null) {
                $this->deleteEventImage($imagePath);
            }

            error_log(
                'GOF Events Create Error: ' .
                $e->getMessage()
            );

            $this->flash(
                'error',
                'Failed to create event. Please try again.'
            );

            $this->redirect('/admin/events/create');
            return;
        }
    }


    public function edit($id): void
    {
        $this->requirePermission('manage_events');

        $id = (int)$id;

        if ($id <= 0) {
            $this->flash('error', 'Invalid event.');
            $this->redirect('/admin/events');
            return;
        }

        $db = Database::get();

        $stmt = $db->prepare("
            SELECT *
            FROM events
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $event = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$event) {
            $this->flash('error', 'Event not found.');
            $this->redirect('/admin/events');
            return;
        }

        $this->render(
            'admin/events/edit.php',
            compact('event')
        );
    }


    public function update($id): void
    {
        $this->requirePermission('manage_events');

        /*
         * IMPORTANT:
         * Do NOT use BaseController::validateCsrf() here.
         * This controller accepts all token names used by the
         * current GOF3-light forms.
         */
        $this->validateEventCsrf();

        $id = (int)$id;

        if ($id <= 0) {
            $this->flash('error', 'Invalid event.');
            $this->redirect('/admin/events');
            return;
        }

        $db = Database::get();

        $existingStmt = $db->prepare("
            SELECT *
            FROM events
            WHERE id = ?
            LIMIT 1
        ");

        $existingStmt->execute([$id]);

        $existing = $existingStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$existing) {
            $this->flash('error', 'Event not found.');
            $this->redirect('/admin/events');
            return;
        }

        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $eventDate = trim($_POST['event_date'] ?? '');
        $endDate = trim($_POST['end_date'] ?? '');
        $eventTime = trim($_POST['event_time'] ?? '');
        $status = trim($_POST['status'] ?? 'upcoming');
        $isPublic = isset($_POST['is_public']) ? 1 : 0;

        if ($title === '') {
            $this->flash('error', 'Event title is required.');
            $this->redirect("/admin/events/{$id}/edit");
            return;
        }

        if (mb_strlen($title) > 200) {
            $this->flash('error', 'Event title must not exceed 200 characters.');
            $this->redirect("/admin/events/{$id}/edit");
            return;
        }

        if ($eventDate === '' || !$this->validDate($eventDate)) {
            $this->flash('error', 'Please enter a valid event date.');
            $this->redirect("/admin/events/{$id}/edit");
            return;
        }

        if ($endDate !== '' && !$this->validDate($endDate)) {
            $this->flash('error', 'Please enter a valid end date.');
            $this->redirect("/admin/events/{$id}/edit");
            return;
        }

        if ($endDate !== '' && $endDate < $eventDate) {
            $this->flash('error', 'The end date cannot be earlier than the event date.');
            $this->redirect("/admin/events/{$id}/edit");
            return;
        }

        if ($eventTime !== '' && !$this->validTime($eventTime)) {
            $this->flash('error', 'Please enter a valid event time.');
            $this->redirect("/admin/events/{$id}/edit");
            return;
        }

        $allowedStatuses = [
            'upcoming',
            'ongoing',
            'completed',
            'cancelled'
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'upcoming';
        }

        $newImage = null;

        try {

            if ($this->hasUploadedImage('image')) {
                $newImage = $this->uploadEventImage(
                    $_FILES['image']
                );
            }

            $removeImage =
                isset($_POST['remove_image']);

            if ($newImage !== null) {

                $imageToSave = $newImage;

            } elseif ($removeImage) {

                $imageToSave = null;

            } else {

                $imageToSave =
                    $existing['image'] ?? null;
            }

            $stmt = $db->prepare("
                UPDATE events
                SET
                    title = ?,
                    description = ?,
                    event_date = ?,
                    end_date = ?,
                    event_time = ?,
                    location = ?,
                    status = ?,
                    is_public = ?,
                    image = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $title,
                $description !== '' ? $description : null,
                $eventDate,
                $endDate !== '' ? $endDate : null,
                $eventTime !== '' ? $eventTime : null,
                $location !== '' ? $location : null,
                $status,
                $isPublic,
                $imageToSave,
                $id
            ]);

            if (
                $newImage !== null &&
                !empty($existing['image']) &&
                $existing['image'] !== $newImage
            ) {
                $this->deleteEventImage(
                    $existing['image']
                );
            }

            if (
                $removeImage &&
                $newImage === null &&
                !empty($existing['image'])
            ) {
                $this->deleteEventImage(
                    $existing['image']
                );
            }

            $this->audit(
                'update_event',
                $id,
                "Updated event {$title}"
            );

            $this->flash(
                'success',
                'Event updated successfully.'
            );

            $this->redirect('/admin/events');
            return;

        } catch (\Throwable $e) {

            if ($newImage !== null) {
                $this->deleteEventImage($newImage);
            }

            error_log(
                'GOF Events Update Error: ' .
                $e->getMessage()
            );

            $this->flash(
                'error',
                'Failed to update event: ' .
                $e->getMessage()
            );

            $this->redirect(
                "/admin/events/{$id}/edit"
            );

            return;
        }
    }


    public function delete($id): void
    {
        $this->requirePermission('manage_events');

        $this->validateEventCsrf();

        $id = (int)$id;

        if ($id <= 0) {
            $this->flash('error', 'Invalid event.');
            $this->redirect('/admin/events');
            return;
        }

        $db = Database::get();

        try {

            $stmt = $db->prepare("
                SELECT *
                FROM events
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$id]);

            $event = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$event) {
                $this->flash('error', 'Event not found.');
                $this->redirect('/admin/events');
                return;
            }

            $delete = $db->prepare("
                DELETE FROM events
                WHERE id = ?
            ");

            $delete->execute([$id]);

            if (!empty($event['image'])) {
                $this->deleteEventImage(
                    $event['image']
                );
            }

            $this->audit(
                'delete_event',
                $id,
                "Deleted event {$event['title']}"
            );

            $this->flash(
                'success',
                'Event deleted successfully.'
            );

        } catch (\Throwable $e) {

            error_log(
                'GOF Events Delete Error: ' .
                $e->getMessage()
            );

            $this->flash(
                'error',
                'Failed to delete event.'
            );
        }

        $this->redirect('/admin/events');
    }


    /*
     * ==========================================================
     * CSRF
     * ==========================================================
     */

    private function validateEventCsrf(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $submitted =
            $_POST['_token']
            ?? $_POST['csrf_token']
            ?? $_POST['_csrf']
            ?? $_POST['token']
            ?? '';

        /*
         * Accept the token from all known session locations.
         */
        $storedTokens = [];

        if (!empty($_SESSION['_csrf'])) {
            $storedTokens[] =
                (string)$_SESSION['_csrf'];
        }

        if (!empty($_SESSION['csrf_token'])) {
            $storedTokens[] =
                (string)$_SESSION['csrf_token'];
        }

        if (!empty($_SESSION['csrf'])) {
            $storedTokens[] =
                (string)$_SESSION['csrf'];
        }

        /*
         * Also ask the application's Csrf helper for its token.
         * This makes the controller compatible with the existing
         * GOF3-light Csrf implementation.
         */
        try {

            if (class_exists('\GOF\Helpers\Csrf')) {

                $helperToken =
                    \GOF\Helpers\Csrf::token();

                if ($helperToken !== '') {
                    $storedTokens[] =
                        (string)$helperToken;
                }
            }

        } catch (\Throwable $ignored) {
        }

        /*
         * Remove duplicates.
         */
        $storedTokens =
            array_values(
                array_unique($storedTokens)
            );

        $valid = false;

        if (
            $submitted !== '' &&
            $storedTokens
        ) {

            foreach ($storedTokens as $stored) {

                if (
                    hash_equals(
                        (string)$stored,
                        (string)$submitted
                    )
                ) {
                    $valid = true;
                    break;
                }
            }
        }

        if ($valid) {
            return;
        }

        /*
         * Do not silently continue with an invalid request.
         */
        http_response_code(419);

        echo '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invalid Form Submission</title>

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    background: #f5f7f9;
    font-family: Arial, Helvetica, sans-serif;
}

.card {
    width: 100%;
    max-width: 500px;
    background: #fff;
    border-radius: 16px;
    padding: 35px;
    text-align: center;
    box-shadow: 0 10px 35px rgba(0,0,0,.08);
}

.icon {
    width: 65px;
    height: 65px;
    border-radius: 50%;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff0f0;
    color: #dc3545;
    font-size: 30px;
}

h2 {
    margin: 0 0 10px;
    color: #212529;
}

p {
    color: #6c757d;
    line-height: 1.6;
}

a {
    display: inline-block;
    margin-top: 15px;
    padding: 11px 20px;
    border-radius: 8px;
    background: #198754;
    color: #fff;
    text-decoration: none;
}

a:hover {
    background: #157347;
    color: #fff;
}
</style>
</head>

<body>

<div class="card">

    <div class="icon">
        !
    </div>

    <h2>Invalid Form Submission</h2>

    <p>
        Your security token is invalid or has expired.
        Please return to the event page and submit the form again.
    </p>

    <a href="/GOF3-light/public/admin/events">
        Return to Events
    </a>

</div>

</body>
</html>';

        exit;
    }


    /*
     * ==========================================================
     * IMAGE UPLOAD
     * ==========================================================
     */

    private function hasUploadedImage(
        string $field
    ): bool {

        return isset($_FILES[$field]) &&
            is_array($_FILES[$field]) &&
            (
                ($_FILES[$field]['error']
                    ?? UPLOAD_ERR_NO_FILE)
                !== UPLOAD_ERR_NO_FILE
            );
    }


    private function uploadEventImage(
        array $file
    ): string {

        if (
            !isset($file['error']) ||
            $file['error'] !== UPLOAD_ERR_OK
        ) {
            throw new \RuntimeException(
                'Event image upload failed.'
            );
        }

        if (
            empty($file['tmp_name']) ||
            !is_uploaded_file(
                $file['tmp_name']
            )
        ) {
            throw new \RuntimeException(
                'Invalid uploaded event image.'
            );
        }

        $maxSize =
            5 * 1024 * 1024;

        if (
            ($file['size'] ?? 0) <= 0 ||
            $file['size'] > $maxSize
        ) {
            throw new \RuntimeException(
                'Event image must be 5 MB or smaller.'
            );
        }

        $finfo =
            new \finfo(FILEINFO_MIME_TYPE);

        $mime =
            $finfo->file(
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

        if (
            @getimagesize(
                $file['tmp_name']
            ) === false
        ) {
            throw new \RuntimeException(
                'The uploaded file is not a valid image.'
            );
        }

        $directory =
            dirname(__DIR__, 2) .
            '/public/uploads/events';

        if (!is_dir($directory)) {

            if (
                !mkdir(
                    $directory,
                    0755,
                    true
                ) &&
                !is_dir($directory)
            ) {
                throw new \RuntimeException(
                    'Unable to create event image directory.'
                );
            }
        }

        $filename =
            'event_' .
            date('Ymd_His') .
            '_' .
            bin2hex(
                random_bytes(8)
            ) .
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
                'Unable to save event image.'
            );
        }

        return 'uploads/events/' .
            $filename;
    }


    private function deleteEventImage(
        ?string $image
    ): void {

        if (
            !$image ||
            trim($image) === ''
        ) {
            return;
        }

        $image = str_replace(
            ['\\', '..'],
            ['/',''],
            $image
        );

        $relative =
            ltrim($image, '/');

        if (
            !str_starts_with(
                $relative,
                'uploads/events/'
            )
        ) {
            return;
        }

        $fullPath =
            dirname(__DIR__, 2) .
            '/public/' .
            $relative;

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }


    /*
     * ==========================================================
     * VALIDATION
     * ==========================================================
     */

    private function validDate(
        string $date
    ): bool {

        $parsed =
            \DateTime::createFromFormat(
                'Y-m-d',
                $date
            );

        return
            $parsed !== false &&
            $parsed->format('Y-m-d') === $date;
    }


    private function validTime(
        string $time
    ): bool {

        $parsed =
            \DateTime::createFromFormat(
                'H:i',
                $time
            );

        if (
            $parsed !== false &&
            $parsed->format('H:i') === $time
        ) {
            return true;
        }

        $parsed =
            \DateTime::createFromFormat(
                'H:i:s',
                $time
            );

        return
            $parsed !== false &&
            $parsed->format('H:i:s') === $time;
    }


    /*
     * ==========================================================
     * AUDIT
     * ==========================================================
     */

    private function audit(
        string $action,
        int $eventId,
        string $description
    ): void {

        try {

            $userId =
                $this->currentUser()['id']
                ?? null;

            if (
                class_exists(
                    AuditService::class
                )
            ) {

                (new AuditService())->log(
                    $userId !== null
                        ? (int)$userId
                        : null,
                    $action,
                    'event',
                    'event',
                    (string)$eventId,
                    $description
                );
            }

        } catch (\Throwable $ignored) {
            /*
             * Audit failure must never stop
             * the event operation.
             */
        }
    }
}