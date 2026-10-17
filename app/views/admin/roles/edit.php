<div class="container-fluid">
    <h2>Edit Role</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="<?= url('/admin/roles/' . $role['id'] . '/update') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Role Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($role['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label>Description</label>
                        <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($role['description']) ?>">
                    </div>
                </div>

                <h4 class="mt-4 mb-3">Permissions</h4>
                
                <div class="row">
                    <?php foreach ($groupedPerms as $module => $perms): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light fw-bold text-uppercase">
                                <?= htmlspecialchars($module) ?>
                            </div>
                            <div class="card-body">
                                <?php foreach ($perms as $p): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $p['id'] ?>" id="perm_<?= $p['id'] ?>" <?= in_array($p['id'], $assigned) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="perm_<?= $p['id'] ?>">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Role</button>
                    <a href="<?= url('/admin/roles') ?>" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
