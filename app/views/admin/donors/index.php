<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Donors</h2>
        <a href="<?= url('/admin/donors/create') ?>" class="btn btn-primary">+ Register Donor</a>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= url('/admin/donors') ?>" class="d-flex gap-2">
                <input type="text" name="search" class="form-control" placeholder="Search by name, organization, code, email..." value="<?= h($search ?? '') ?>">
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Donor Code</th>
                        <th>Name</th>
                        <th>Organization</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Donated (₦)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($donors)): ?>
                    <tr><td colspan="9" class="text-center py-4">No donors found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($donors as $donor): 
                            $badgeClass = match($donor['donor_type'] ?? '') {
                                'individual' => 'bg-primary',
                                'organization' => 'bg-success',
                                'government' => 'bg-warning text-dark',
                                'foundation' => 'bg-info',
                                'ngo' => 'bg-info text-dark',
                                default => 'bg-secondary'
                            };
                        ?>
                        <tr>
                            <td><?= h($donor['gof_id']) ?></td>
                            <td><?= h($donor['name']) ?></td>
                            <td><?= h($donor['organization_name']) ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= h(ucfirst($donor['donor_type'])) ?></span></td>
                            <td><?= h($donor['email']) ?></td>
                            <td><?= h($donor['phone']) ?></td>
                            <td><?= number_format((float)($donor['total_donated'] ?? 0), 2) ?></td>
                            <td><?= h(ucfirst($donor['status'] ?? 'Active')) ?></td>
                            <td>
                                <a href="<?= url('/admin/donors/' . $donor['id']) ?>" class="btn btn-sm btn-outline-info">View</a>
                                <a href="<?= url('/admin/donors/' . $donor['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (($totalPages ?? 0) > 1): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>
