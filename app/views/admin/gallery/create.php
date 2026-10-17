<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Create Album</h2>
        <a href="<?= url('/admin/gallery') ?>" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/gallery/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                <div class="mb-3">
                    <label class="form-label">Album Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Linked Event (Optional)</label>
                    <select name="event_id" class="form-select">
                        <option value="">-- No Event --</option>
                        <?php foreach($events as $e): ?>
                            <option value="<?= $e['id'] ?>"><?= h($e['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="is_public" name="is_public" value="1" checked>
                    <label class="form-check-label" for="is_public">Make album visible to public</label>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Create Album</button>
                </div>
            </form>
        </div>
    </div>
</div>
