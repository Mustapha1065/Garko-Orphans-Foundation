<?php function h($v){return htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');} ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Staff Members</h1>
        <a href="<?= url('/admin/staff/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Staff Member
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('/admin/staff') ?>" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search by name, department, or position" value="<?= h($search ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-search me-1"></i> Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Avatar/Name</th>
                        <th>GOF ID</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($staffList)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No staff found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($staffList as $staff): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="gof-avatar gof-avatar-sm me-3">
                                        <?= strtoupper(substr($staff['first_name'],0,1)) ?>
                                    </div>
                                    <div>
                                        <div class="fw-semibold"><?= h($staff['first_name'] . ' ' . $staff['last_name']) ?></div>
                                        <div class="small text-muted"><?= h($staff['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?= h($staff['gof_id']) ?></span></td>
                            <td><?= h($staff['department']) ?></td>
                            <td><?= h($staff['position']) ?></td>
                            <td><?= h($staff['role_name'] ?? 'None') ?></td>
                            <td>
                                <?php if($staff['user_status'] === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary"><?= h($staff['user_status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= url('/admin/staff/' . $staff['id']) ?>" class="btn btn-sm btn-light border" title="View"><i class="bi bi-eye"></i></a>
                                <a href="<?= url('/admin/staff/' . $staff['id'] . '/edit') ?>" class="btn btn-sm btn-light border" title="Edit"><i class="bi bi-pencil"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (($totalPages ?? 1) > 1): ?>
        <div class="card-footer bg-white">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0 justify-content-end">
                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
