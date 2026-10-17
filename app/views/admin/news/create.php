<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Write Article</h2>
        <a href="<?= url('/admin/news') ?>" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/news/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                <div class="row mb-3">
                    <div class="col-md-9">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="General">
                </div>

                <div class="mb-3">
                    <label class="form-label">Excerpt (Short summary for lists)</label>
                    <textarea name="excerpt" class="form-control" rows="2"></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Content *</label>
                    <textarea name="content" class="form-control" rows="15" required></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Save Article</button>
                </div>
            </form>
        </div>
    </div>
</div>
