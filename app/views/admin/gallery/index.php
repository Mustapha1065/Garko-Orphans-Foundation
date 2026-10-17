<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Gallery Albums</h2>
        <a href="<?= url('/admin/gallery/create') ?>" class="btn btn-primary">+ Create Album</a>
    </div>

    <div class="row">
        <?php if (empty($albums)): ?>
            <div class="col-12 text-center py-5 text-muted">No albums found. Create one to get started.</div>
        <?php else: ?>
            <?php foreach ($albums as $a): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <!-- Placeholder cover image -->
                    <div style="height: 200px; background: linear-gradient(135deg, #28a745, #20c997);" class="w-100 rounded-top d-flex align-items-center justify-content-center text-white">
                        <i class="bi bi-images" style="font-size: 3rem;"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-truncate"><?= h($a['name']) ?></h5>
                        <p class="card-text text-muted small mb-3"><?= date('M j, Y', strtotime($a['created_at'])) ?></p>
                        <span class="badge bg-secondary mb-2"><?= $a['image_count'] ?> images</span>
                        <?php if(!$a['is_public']): ?>
                            <span class="badge bg-warning text-dark">Private</span>
                        <?php endif; ?>
                        
                        <div class="mt-3 d-flex justify-content-between">
                            <form method="POST" action="<?= url('/admin/gallery/' . $a['id'] . '/upload') ?>" enctype="multipart/form-data" class="d-inline d-flex w-100 me-2">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                                <input type="file" name="images[]" multiple class="form-control form-control-sm me-1" accept="image/jpeg, image/png, image/gif" required>
                                <button type="submit" class="btn btn-sm btn-success">Upload</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
