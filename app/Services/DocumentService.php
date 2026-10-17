<?php
namespace GOF\Services;

use GOF\Helpers\Database;
use GOF\Helpers\FileUpload;
use PDO;

class DocumentService {
    private PDO $db;
    private FileUpload $uploader;

    public function __construct() {
        $this->db = Database::get();
        $this->uploader = new FileUpload();
    }

    public function store(array $file, string $entityType, int $entityId, int $uploadedBy, ?string $description = null): array {
        $filename = $this->uploader->uploadDocument($file);
        
        $stmt = $this->db->prepare("
            INSERT INTO documents (entity_type, entity_id, filename, original_name, description, uploaded_by, created_at)
            VALUES (:et, :ei, :fn, :on, :desc, :ub, NOW())
        ");
        $stmt->execute([
            ':et' => $entityType,
            ':ei' => $entityId,
            ':fn' => $filename,
            ':on' => $file['name'],
            ':desc' => $description,
            ':ub' => $uploadedBy
        ]);
        
        $docId = (int)$this->db->lastInsertId();
        
        $s = $this->db->prepare("SELECT * FROM documents WHERE id = :id");
        $s->execute([':id' => $docId]);
        return $s->fetch();
    }

    public function getForEntity(string $entityType, int $entityId): array {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE entity_type = :et AND entity_id = :ei ORDER BY created_at DESC");
        $stmt->execute([':et' => $entityType, ':ei' => $entityId]);
        return $stmt->fetchAll();
    }

    public function download(int $docId, int $requestingUserId): void {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id = :id");
        $stmt->execute([':id' => $docId]);
        $doc = $stmt->fetch();
        
        if (!$doc) {
            http_response_code(404);
            echo "Document not found";
            exit;
        }

        $rbac = new RBACService();
        $canManage = $rbac->userHasPermission($requestingUserId, 'download_documents');
        
        if (!$canManage) {
            // Depending on entity type, check ownership... basic check for now
            if ($doc['uploaded_by'] !== $requestingUserId) {
                http_response_code(403);
                echo "Access denied";
                exit;
            }
        }

        $config = require __DIR__ . '/../../config/config.php';
        $path = rtrim($config['upload']['docs_path'], '/') . '/' . $doc['filename'];
        
        if (!file_exists($path)) {
            http_response_code(404);
            echo "File missing on disk";
            exit;
        }

        (new AuditService())->log($requestingUserId, 'downloaded_document', 'documents', $doc['entity_type'], (string)$doc['entity_id'], "Downloaded " . $doc['original_name']);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($doc['original_name']) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function delete(int $docId, int $requestingUserId): bool {
        $stmt = $this->db->prepare("SELECT * FROM documents WHERE id = :id");
        $stmt->execute([':id' => $docId]);
        $doc = $stmt->fetch();
        
        if (!$doc) return false;

        $config = require __DIR__ . '/../../config/config.php';
        $this->uploader->deleteFile($doc['filename'], $config['upload']['docs_path']);
        
        $this->db->prepare("DELETE FROM documents WHERE id = :id")->execute([':id' => $docId]);
        (new AuditService())->log($requestingUserId, 'deleted_document', 'documents', $doc['entity_type'], (string)$doc['entity_id'], "Deleted " . $doc['original_name']);
        
        return true;
    }
}
