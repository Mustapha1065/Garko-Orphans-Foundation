<?php

namespace GOF\Controllers;

use GOF\Helpers\Database;

class RolesController extends BaseController {
    public function index() {
        $this->requirePermission('manage_users');
        $db = Database::get();
        $roles = $db->query("SELECT r.*, 
                            (SELECT COUNT(*) FROM users WHERE role_id = r.id) as users_count,
                            (SELECT COUNT(*) FROM role_permissions WHERE role_id = r.id) as permissions_count
                            FROM roles r")->fetchAll();
        $this->render('admin/roles/index.php', compact('roles'));
    }

    public function create() {
        $this->requirePermission('manage_users');
        $db = Database::get();
        $permissions = $db->query("SELECT * FROM permissions")->fetchAll();
        $groupedPerms = [];
        foreach ($permissions as $p) {
            $groupedPerms[$p['module']][] = $p;
        }
        $this->render('admin/roles/create.php', compact('groupedPerms'));
    }

    public function store() {
        $this->requirePermission('manage_users');
        $this->validateCsrf();
        $db = Database::get();
        
        $stmt = $db->prepare("INSERT INTO roles (name, description, slug) VALUES (?, ?, ?)");
        $slug = strtolower(str_replace(' ', '_', $_POST['name']));
        $stmt->execute([$_POST['name'], $_POST['description'], $slug]);
        $roleId = $db->lastInsertId();
        
        if (!empty($_POST['permissions'])) {
            $stmt = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($_POST['permissions'] as $pId) {
                $stmt->execute([$roleId, $pId]);
            }
        }
        
        $_SESSION['success'] = 'Role created.';
        header("Location: /admin/roles");
        exit;
    }

    public function edit($id) {
        $this->requirePermission('manage_users');
        $db = Database::get();
        $stmt = $db->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$id]);
        $role = $stmt->fetch();
        
        $permissions = $db->query("SELECT * FROM permissions")->fetchAll();
        $groupedPerms = [];
        foreach ($permissions as $p) {
            $groupedPerms[$p['module']][] = $p;
        }
        
        $stmt = $db->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$id]);
        $assigned = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $this->render('admin/roles/edit.php', compact('role', 'groupedPerms', 'assigned'));
    }

    public function update($id) {
        $this->requirePermission('manage_users');
        $this->validateCsrf();
        $db = Database::get();
        
        $stmt = $db->prepare("UPDATE roles SET name=?, description=? WHERE id=?");
        $stmt->execute([$_POST['name'], $_POST['description'], $id]);
        
        $stmt = $db->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$id]);
        
        if (!empty($_POST['permissions'])) {
            $stmt = $db->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            foreach ($_POST['permissions'] as $pId) {
                $stmt->execute([$id, $pId]);
            }
        }
        
        $_SESSION['success'] = 'Role updated.';
        header("Location: /admin/roles");
        exit;
    }
}
