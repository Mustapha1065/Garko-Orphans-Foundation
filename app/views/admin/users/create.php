<div class="container-fluid">
    <h2>Create User</h2>
    
    <div class="card">
        <div class="card-body">
            <form action="<?= url('/admin/users') ?>" method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>First Name *</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Password *</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Confirm Password *</label>
                        <input type="password" name="password_confirm" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Account Type</label>
                        <select name="account_type" class="form-select" required>
                            <option value="staff">Staff</option>
                            <option value="super_admin">Super Admin</option>
                            <option value="beneficiary">Beneficiary</option>
                            <option value="donor">Donor</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Role</label>
                        <select name="role_id" class="form-select">
                            <option value="">None</option>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Create User</button>
                <a href="<?= url('/admin/users') ?>" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
