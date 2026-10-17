<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Program: <?= h($program['name']) ?></h2>
        <a href="<?= url('/admin/programs/' . $program['id']) ?>" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/programs/' . $program['id'] . '/update') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label">Program Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= h($program['name']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="planned" <?= $program['status'] == 'planned' ? 'selected' : '' ?>>Planned</option>
                            <option value="active" <?= $program['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="completed" <?= $program['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="suspended" <?= $program['status'] == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            <option value="archived" <?= $program['status'] == 'archived' ? 'selected' : '' ?>>Archived</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="<?= h($program['start_date']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="<?= h($program['end_date']) ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Budget</label>
                        <input type="number" step="0.01" name="budget" class="form-control" value="<?= h($program['budget']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="NGN" <?= $program['currency'] == 'NGN' ? 'selected' : '' ?>>NGN</option>
                            <option value="USD" <?= $program['currency'] == 'USD' ? 'selected' : '' ?>>USD</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Program Manager</label>
                        <select name="manager_id" class="form-select">
                            <option value="">-- Unassigned --</option>
                            <?php foreach($staff as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= $program['manager_id'] == $u['id'] ? 'selected' : '' ?>><?= h($u['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= h($program['description']) ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Objectives</label>
                    <textarea name="objectives" class="form-control" rows="3"><?= h($program['objectives']) ?></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Update Program</button>
                </div>
            </form>
        </div>
    </div>
</div>
