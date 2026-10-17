<?php $pageTitle = 'Notifications'; $breadcrumb = '<li class="breadcrumb-item active">Notifications</li>'; ?>
<div class="container-fluid py-4">
    <div class="gof-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0">Your Notifications</h5>
            <form action="<?= url('/admin/notifications/mark-all-read') ?>" method="POST" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Mark all as read</button>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle gof-table">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;"></th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($notifications as $n): ?>
                    <tr class="<?= !$n['is_read'] ? 'fw-bold bg-light' : '' ?>">
                        <td class="text-center">
                            <?php if(!$n['is_read']): ?>
                                <span class="badge bg-primary p-1 rounded-circle"><span class="visually-hidden">New</span></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars(ucfirst($n['type'] ?? 'info')) ?></span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-dark"><?= htmlspecialchars($n['title'] ?? '') ?></span>
                                <small class="text-muted"><?= htmlspecialchars($n['message'] ?? '') ?></small>
                            </div>
                        </td>
                        <td><small class="text-muted"><?= date('M j, Y H:i', strtotime($n['created_at'])) ?></small></td>
                        <td class="text-end">
                            <?php if(!$n['is_read']): ?>
                            <form action="<?= url('/admin/notifications/mark-read') ?>" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                                <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-light" title="Mark as read"><i class="bi bi-check2"></i></button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($notifications)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">You have no notifications.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Placeholder -->
        <nav aria-label="Page navigation" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#"><?= $page ?></a></li>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div>
