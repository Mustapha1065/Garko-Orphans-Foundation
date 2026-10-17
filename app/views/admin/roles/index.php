<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Roles & Permissions</h2>
        <a href="<?= url('/admin/roles/create') ?>" class="btn btn-primary">Create Role</a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Role Name</th>
                        <th>Slug</th>
                        <th>Users Count</th>
                        <th>Permissions Count</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= htmlspecialchars($r['slug']) ?></td>
                        <td><?= htmlspecialchars($r['users_count']) ?></td>
                        <td><?= htmlspecialchars($r['permissions_count']) ?></td>
                        <td>
                            <?php if (isset($r['is_system']) && $r['is_system']): ?>
                                <span class="badge bg-secondary">System</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Custom</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= url('/admin/roles/' . $r['id'] . '/edit') ?>" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
