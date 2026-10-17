<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;
use GOF\Services\RBACService;

class UsersController extends BaseController {

    private const ROLE_NAME_SUBQUERY = "(SELECT r.name FROM user_roles ur JOIN roles r ON r.id = ur.role_id WHERE ur.user_id = u.id ORDER BY ur.assigned_at ASC LIMIT 1)";
    private const ROLE_ID_SUBQUERY = "(SELECT r.id FROM user_roles ur JOIN roles r ON r.id = ur.role_id WHERE ur.user_id = u.id ORDER BY ur.assigned_at ASC LIMIT 1)";

    public function index() {
        $this->requirePermission('manage_users');
        $db = Database::get();
        $users = $db->query("SELECT u.*, " . self::ROLE_NAME_SUBQUERY . " as role_name FROM users u ORDER BY u.created_at DESC")->fetchAll();
        $this->render('admin/users/index.php', compact('users'));
    }

    public function create() {
        $this->requirePermission('manage_users');
        $db = Database::get();
        $roles = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
        $this->render('admin/users/create.php', compact('roles'));
    }

    public function store() {
        $this->requirePermission('manage_users');
        $this->validateCsrf();
        $db = Database::get();

        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, password_hash, phone, account_type, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())");
        $stmt->execute([
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['email'],
            $hash,
            $_POST['phone'] ?? null,
            $_POST['account_type'],
        ]);
        $userId = (int)$db->lastInsertId();

        if (!empty($_POST['role_id'])) {
            $rbac = new RBACService();
            $rbac->assignRole($userId, (int)$_POST['role_id'], (int)($this->currentUser()['id'] ?? 0));
        }

        $this->flash('success', 'User created.');
        header("Location: /admin/users");
        exit;
    }

    public function show($id) {
        $this->requirePermission('manage_users');
        $db = Database::get();
        $stmt = $db->prepare("SELECT u.*, " . self::ROLE_NAME_SUBQUERY . " as role_name FROM users u WHERE u.id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        $rbac = new RBACService();
        $permissions = $user ? $rbac->getUserPermissions((int)$user['id']) : [];

        $this->render('admin/users/show.php', compact('user', 'permissions'));
    }

    public function edit($id) {
        $this->requirePermission('manage_users');
        $db = Database::get();
        $stmt = $db->prepare("SELECT u.*, " . self::ROLE_ID_SUBQUERY . " as role_id FROM users u WHERE u.id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        $roles = $db->query("SELECT * FROM roles ORDER BY name ASC")->fetchAll();
        $this->render('admin/users/edit.php', compact('user', 'roles'));
    }

    public function update($id) {
        $this->requirePermission('manage_users');
        $this->validateCsrf();
        $db = Database::get();

        if (!empty($_POST['password'])) {
            $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET first_name=?, last_name=?, email=?, password_hash=?, phone=?, account_type=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $hash, $_POST['phone'], $_POST['account_type'], $id]);
        } else {
            $stmt = $db->prepare("UPDATE users SET first_name=?, last_name=?, email=?, phone=?, account_type=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone'], $_POST['account_type'], $id]);
        }

        $db->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$id]);
        if (!empty($_POST['role_id'])) {
            $rbac = new RBACService();
            $rbac->assignRole((int)$id, (int)$_POST['role_id'], (int)($this->currentUser()['id'] ?? 0));
        }

        $this->flash('success', 'User updated.');
        header("Location: /admin/users");
        exit;
    }

    public function deactivate($id) {
        $this->requirePermission('manage_users');
        $this->validateCsrf();
        $db = Database::get();
        $stmt = $db->prepare("UPDATE users SET status='inactive' WHERE id=?");
        $stmt->execute([$id]);
        $this->flash('success', 'User deactivated.');
        header("Location: /admin/users/$id");
        exit;
    }

    public function activate($id) {
        $this->requirePermission('manage_users');
        $this->validateCsrf();
        $db = Database::get();
        $stmt = $db->prepare("UPDATE users SET status='active' WHERE id=?");
        $stmt->execute([$id]);
        $this->flash('success', 'User activated.');
        header("Location: /admin/users/$id");
        exit;
    }
}
