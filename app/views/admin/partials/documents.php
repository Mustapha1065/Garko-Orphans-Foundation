<?php
/**
 * Reusable documents panel. Expects:
 *   $documents   - array of rows from DocumentService::getForEntity()
 *   $entityType  - one of beneficiary|donor|staff|program|volunteer|general
 *   $entityId    - int
 */
?>
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Documents</span>
    </div>
    <div class="card-body">
        <?php if (empty($documents)): ?>
            <p class="text-muted mb-3">No documents uploaded yet.</p>
        <?php else: ?>
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Description</th>
                        <th>Uploaded</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($documents as $doc): ?>
                        <tr>
                            <td><?= htmlspecialchars($doc['original_name']) ?></td>
                            <td><?= htmlspecialchars($doc['description'] ?? '') ?></td>
                            <td><?= htmlspecialchars($doc['created_at']) ?></td>
                            <td class="text-end">
                                <a href="<?= url('/admin/documents/' . $doc['id'] . '/download') ?>" class="btn btn-sm btn-outline-secondary">Download</a>
                                <form method="POST" action="<?= url('/admin/documents/' . $doc['id'] . '/delete') ?>" class="d-inline" onsubmit="return confirm('Delete this document?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <form method="POST" action="<?= url('/admin/documents/upload') ?>" enctype="multipart/form-data" class="row g-2 align-items-end mt-2">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
            <input type="hidden" name="entity_type" value="<?= htmlspecialchars($entityType) ?>">
            <input type="hidden" name="entity_id" value="<?= (int) $entityId ?>">
            <div class="col-auto">
                <input type="file" name="document" class="form-control form-control-sm" required>
            </div>
            <div class="col-auto">
                <input type="text" name="description" class="form-control form-control-sm" placeholder="Description (optional)">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>
