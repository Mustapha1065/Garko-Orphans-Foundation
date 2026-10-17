<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class ContactsController extends BaseController {
    public function index() {
        $this->requirePermission('manage_contacts');
        $db = Database::get();

        $status = $_GET['status'] ?? '';
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $where = "1=1";
        $params = [];
        if (!empty($status)) {
            $where .= " AND status = ?";
            $params[] = $status;
        }

        $stmt = $db->prepare("SELECT * FROM contact_messages WHERE $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
        $stmt->execute($params);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $countStmt = $db->prepare("SELECT COUNT(*) FROM contact_messages WHERE $where");
        $countStmt->execute($params);
        $total = $countStmt->fetchColumn();
        $totalPages = ceil($total / $limit);

        $this->render('admin/contacts/index.php', compact('messages', 'status', 'page', 'totalPages'));
    }

    public function markRead($id) {
        $this->requirePermission('manage_contacts');
        $this->validateCsrf();

        $db = Database::get();
        $stmt = $db->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ? AND status = 'unread'");
        $stmt->execute([$id]);

        $this->flash('success', 'Message marked as read.');
        $this->redirect('/admin/contacts');
    }
}
