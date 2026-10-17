<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class GalleryController extends BaseController {
    public function index() {
        $this->requirePermission('manage_gallery');
        $db = Database::get();

        $stmt = $db->query("SELECT ga.*, (SELECT COUNT(*) FROM gallery_images WHERE album_id = ga.id) as image_count 
                            FROM gallery_albums ga ORDER BY ga.created_at DESC");
        $albums = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->render('admin/gallery/index.php', compact('albums'));
    }

    public function create() {
        $this->requirePermission('manage_gallery');
        $db = Database::get();
        $events = $db->query("SELECT id, title FROM events ORDER BY title ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $this->render('admin/gallery/create.php', compact('events'));
    }

    public function store() {
        $this->requirePermission('manage_gallery');
        $this->validateCsrf();

        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $event_id = !empty($_POST['event_id']) ? $_POST['event_id'] : null;
        $is_public = isset($_POST['is_public']) ? 1 : 0;

        if (empty($name)) {
            $this->flash('error', 'Album name is required.');
            $this->redirect('/admin/gallery/create');
            return;
        }

        $db = Database::get();
        try {
            $stmt = $db->prepare("INSERT INTO gallery_albums (name, description, event_id, is_public, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$name, $description, $event_id, $is_public]);
            
            $userId = $this->currentUser()['id'] ?? null;
            (new \GOF\Services\AuditService())->log((int)$userId, 'create_album', 'gallery', 'gallery_album', (string)$db->lastInsertId(), "Created gallery album {$name}");

            $this->flash('success', 'Album created successfully.');
            $this->redirect('/admin/gallery');
        } catch (\Exception $e) {
            $this->flash('error', 'Failed to create album.');
            $this->redirect('/admin/gallery/create');
        }
    }

    public function upload($albumId) {
        $this->requirePermission('manage_gallery');
        $this->validateCsrf();

        if (empty($_FILES['images']['name'][0])) {
            $this->flash('error', 'No images selected.');
            $this->redirect('/admin/gallery');
            return;
        }

        $uploadDir = __DIR__ . '/../../../public/uploads/gallery/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $db = Database::get();
        $successCount = 0;
        $userId = $this->currentUser()['id'] ?? null;

        $db->beginTransaction();
        try {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    $fileName = $_FILES['images']['name'][$key];
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $newName = uniqid('gal_', true) . '.' . $ext;
                        if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                            $filePath = 'uploads/gallery/' . $newName;
                            
                            $stmt = $db->prepare("INSERT INTO gallery_images (album_id, file_path, uploaded_by, created_at) VALUES (?, ?, ?, NOW())");
                            $stmt->execute([$albumId, $filePath, $userId]);
                            $successCount++;
                        }
                    }
                }
            }

            if ($successCount > 0) {
                (new \GOF\Services\AuditService())->log((int)$userId, 'upload_images', 'gallery', 'gallery_album', (string)$albumId, "Uploaded {$successCount} images to album ID {$albumId}");
                $db->commit();
                $this->flash('success', "Successfully uploaded {$successCount} images.");
            } else {
                $db->rollBack();
                $this->flash('error', 'No valid images were uploaded.');
            }
        } catch (\Exception $e) {
            $db->rollBack();
            $this->flash('error', 'An error occurred during upload.');
        }

        $this->redirect('/admin/gallery');
    }
}
