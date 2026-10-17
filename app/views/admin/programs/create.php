<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add New Program</h2>
        <a href="<?= url('/admin/programs') ?>" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/programs/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Program Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="planned">Planned</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Budget</label>
                        <input type="number" step="0.01" name="budget" class="form-control" value="0.00">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="NGN">NGN</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Program Manager</label>
                        <select name="manager_id" class="form-select">
                            <option value="">-- Unassigned --</option>
                            <?php foreach($staff as $u): ?>
                                <option value="<?= $u['id'] ?>"><?= h($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Objectives</label>
                    <textarea name="objectives" class="form-control" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Create Program</button>
                </div>
            </form>
        </div>
    </div>
</div>
