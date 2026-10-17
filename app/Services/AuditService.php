<?php
namespace GOF\Services;

use GOF\Helpers\Database;
use PDO;

class AuditService {
    private PDO $db;

    public function __construct() {
        $this->db = Database::get();
    }

    public function log(int $userId, string $action, string $module, string $entityType, string $entityId, ?string $description = null): void {
        try {
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            $stmt = $this->db->prepare("
                INSERT INTO audit_logs (user_id, action, module, entity_type, entity_id, description, ip_address, user_agent, created_at)
                VALUES (:user_id, :action, :module, :entity_type, :entity_id, :description, :ip, :user_agent, NOW())
            ");
            
            $stmt->execute([
                ':user_id' => $userId,
                ':action' => $action,
                ':module' => $module,
                ':entity_type' => $entityType,
                ':entity_id' => $entityId,
                ':description' => $description,
                ':ip' => $ip,
                ':user_agent' => $userAgent
            ]);
        } catch (\Exception $e) {
            // Silently fail to prevent audit logging from breaking main flow
            error_log("Audit logging failed: " . $e->getMessage());
        }
    }
}
