<?php function h($v){return htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');} ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Beneficiaries</h1>
        <a href="<?= url('/admin/beneficiaries/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Register Beneficiary
        </a>
    </div>

    <!-- Status Badges Row -->
    <div class="row mb-4">
        <?php
        $badges = [
            'pending' => ['label' => 'Pending', 'bg' => 'warning', 'text' => 'dark'],
            'active' => ['label' => 'Active', 'bg' => 'success', 'text' => 'white'],
            'inactive' => ['label' => 'Inactive', 'bg' => 'secondary', 'text' => 'white'],
            'graduated' => ['label' => 'Graduated', 'bg' => 'primary', 'text' => 'white']
        ];
        foreach ($badges as $k => $b):
            $count = $statusCounts[$k] ?? 0;
        ?>
        <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
            <div class="card shadow-sm border-0 border-start border-4 border-<?= $b['bg'] ?>">
                <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                    <div class="fw-semibold text-muted text-uppercase small"><?= $b['label'] ?></div>
                    <div class="fs-4 fw-bold text-<?= $b['bg'] ?>"><?= $count ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('/admin/beneficiaries') ?>" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Name</label>
                    <input type="text" name="name" class="form-control form-control-sm" value="<?= h($filters['name']) ?>" placeholder="Search name">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">GOF ID</label>
                    <input type="text" name="gof_id" class="form-control form-control-sm" value="<?= h($filters['gof_id']) ?>" placeholder="ID">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="graduated" <?= $filters['status'] === 'graduated' ? 'selected' : '' ?>>Graduated</option>
                        <option value="transferred" <?= $filters['status'] === 'transferred' ? 'selected' : '' ?>>Transferred</option>
                        <option value="suspended" <?= $filters['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                        <option value="archived" <?= $filters['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Gender</label>
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="Male" <?= $filters['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= $filters['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">State</label>
                    <input type="text" name="state" class="form-control form-control-sm" value="<?= h($filters['state']) ?>" placeholder="State">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel"></i></button>
                    <a href="<?= url('/admin/beneficiaries') ?>" class="btn btn-sm btn-light border w-100"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>GOF ID</th>
                        <th>Full Name</th>
                        <th>Gender</th>
                        <th>State</th>
                        <th>Status</th>
                        <th>Registration Date</th>
                        <th>Created By</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($beneficiaries)): ?>
                        <tr><td colspan="8" class="text-center py-4 text-muted">No beneficiaries found.</td></tr>
                    <?php else: ?>
                        <?php foreach($beneficiaries as $b): ?>
                        <tr>
                            <td><span class="badge bg-light text-dark border"><?= h($b['gof_id']) ?></span></td>
                            <td class="fw-semibold"><?= h($b['full_name']) ?></td>
                            <td><?= h($b['gender']) ?></td>
                            <td><?= h($b['state']) ?></td>
                            <td>
                                <?php
                                $color = 'secondary';
                                if($b['status']==='pending') $color = 'warning text-dark';
                                elseif($b['status']==='active') $color = 'success';
                                elseif($b['status']==='graduated') $color = 'primary';
                                elseif($b['status']==='transferred') $color = 'info text-dark';
                                elseif($b['status']==='suspended') $color = 'danger';
                                elseif($b['status']==='archived') $color = 'dark';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= h(ucfirst($b['status'])) ?></span>
                            </td>
                            <td><?= h(substr($b['created_at'],0,10)) ?></td>
                            <td><?= h($b['creator_name'] ?? 'System') ?></td>
                            <td class="text-end">
                                <div class="btn-group">
                                    <a href="<?= url('/admin/beneficiaries/' . $b['id']) ?>" class="btn btn-sm btn-light border" title="View"><i class="bi bi-eye"></i></a>
                                    <a href="<?= url('/admin/beneficiaries/' . $b['id'] . '/edit') ?>" class="btn btn-sm btn-light border" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <?php if($b['status']==='pending'): ?>
                                    <form method="POST" action="<?= url('/admin/beneficiaries/' . $b['id'] . '/approve') ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(($totalPages ?? 1) > 1): ?>
        <div class="card-footer bg-white">
            <nav aria-label="Page navigation">
                <ul class="pagination mb-0 justify-content-end">
                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query($filters) ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</div>
