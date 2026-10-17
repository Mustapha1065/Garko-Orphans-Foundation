<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Users Management</h2>
        <a href="<?= url('/admin/users/create') ?>" class="btn btn-primary">Create User</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Account Type</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($u['account_type']) ?></span></td>
                        <td><?= htmlspecialchars($u['role_name'] ?? 'None') ?></td>
                        <td>
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($u['created_at']) ?></td>
                        <td>
                            <a href="<?= url('/admin/users/' . $u['id']) ?>" class="btn btn-sm btn-info">View</a>
                            <a href="<?= url('/admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
