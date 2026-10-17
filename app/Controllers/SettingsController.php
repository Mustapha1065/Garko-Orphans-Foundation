<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\AuditService;

class SettingsController extends BaseController {
    public function index() {
        $this->requirePermission('manage_settings');
        $db = Database::get();
        $settingsList = $db->query("SELECT * FROM settings")->fetchAll();
        $settings = [];
        foreach ($settingsList as $s) {
            $settings[$s['key_name']] = $s['value'];
        }
        $this->render('admin/settings/index.php', compact('settings'));
    }

    public function update() {
        $this->requirePermission('manage_settings');
        $this->validateCsrf();
        $db = Database::get();
        $userId = $this->currentUser()['id'] ?? null;

        $updateStmt = $db->prepare(
            "UPDATE settings SET value = :val, updated_by = :ub, updated_at = NOW() WHERE key_name = :key"
        );
        $insertStmt = $db->prepare(
            "INSERT INTO settings (group_name, key_name, value, updated_by, created_at, updated_at)
             VALUES ('general', :key, :val, :ub, NOW(), NOW())"
        );
        foreach ($_POST as $key => $val) {
            if ($key === 'csrf_token') {
                continue;
            }
            $updateStmt->execute([':val' => $val, ':ub' => $userId, ':key' => $key]);
            if ($updateStmt->rowCount() === 0) {
                $insertStmt->execute([':key' => $key, ':val' => $val, ':ub' => $userId]);
            }
        }

        $audit = new AuditService();
        $audit->log((int)$userId, 'update_settings', 'settings', 'settings', '0', 'Updated system settings');

        $this->flash('success', 'Settings updated.');
        header("Location: /admin/settings");
        exit;
    }
}
