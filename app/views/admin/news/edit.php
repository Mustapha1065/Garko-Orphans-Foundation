<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Article</h2>
        <a href="<?= url('/admin/news') ?>" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/news/' . $article['id'] . '/update') ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">

                <div class="row mb-3">
                    <div class="col-md-9">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="<?= h($article['title']) ?>" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= $article['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $article['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-control" value="<?= h($article['category']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Excerpt</label>
                    <textarea name="excerpt" class="form-control" rows="2"><?= h($article['excerpt']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label">Content *</label>
                    <textarea name="content" class="form-control" rows="15" required><?= h($article['content']) ?></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">Update Article</button>
                </div>
            </form>
        </div>
    </div>
</div>
