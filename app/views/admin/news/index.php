<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>News & Articles</h2>
        <a href="<?= url('/admin/news/create') ?>" class="btn btn-primary">+ Write Article</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Author</th>
                        <th>Published At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                    <tr><td colspan="6" class="text-center py-4">No articles found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($articles as $a): 
                            $badgeClass = match($a['status']) {
                                'published' => 'bg-success',
                                'draft' => 'bg-secondary',
                                'archived' => 'bg-dark',
                                default => 'bg-light text-dark'
                            };
                        ?>
                        <tr>
                            <td><?= h($a['title']) ?></td>
                            <td><?= h($a['category']) ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= h(ucfirst($a['status'])) ?></span></td>
                            <td><?= h($a['author_name'] ?: 'System') ?></td>
                            <td><?= $a['published_at'] ? date('M j, Y H:i', strtotime($a['published_at'])) : '-' ?></td>
                            <td>
                                <a href="<?= url('/admin/news/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                <?php if($a['status'] === 'draft'): ?>
                                    <form method="POST" action="<?= url('/admin/news/' . $a['id'] . '/publish') ?>" class="d-inline" onsubmit="return confirm('Publish this article?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-success">Publish</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
