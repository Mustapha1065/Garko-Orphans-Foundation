<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class AuditController extends BaseController {
    public function index() {
        $this->requirePermission('view_audit_logs');
        $db = Database::get();
        $logs = $db->query("SELECT a.*, u.first_name, u.last_name FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 50")->fetchAll();
        $this->render('admin/audit/index.php', compact('logs'));
    }
}
