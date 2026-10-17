<?php
namespace GOF\Controllers;

class NotificationsController extends BaseController
{
    public function index(): void
    {
        $this->requireAuth();
        $user = $this->currentUser();
        $db = \GOF\Helpers\Database::get();
        
        // Simple pagination
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :uid ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':uid', $user['id'], \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll();
        
        $this->render('admin/notifications/index.php', compact('notifications', 'page'));
    }

    public function markRead(): void
    {
        $this->requireAuth();
        $this->validateCsrf();
        $id = $_POST['id'] ?? null;
        if ($id) {
            $db = \GOF\Helpers\Database::get();
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id AND user_id = :uid");
            $stmt->execute([':id' => $id, ':uid' => $this->currentUser()['id']]);
        }
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/notifications');
    }

    public function markAllRead(): void
    {
        $this->requireAuth();
        $db = \GOF\Helpers\Database::get();
        $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = :uid");
        $stmt->execute([':uid' => $this->currentUser()['id']]);
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/notifications');
    }
}
