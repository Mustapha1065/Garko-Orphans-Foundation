<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Programs</h2>
        <a href="<?= url('/admin/programs/create') ?>" class="btn btn-primary">+ Add Program</a>
    </div>

    <div class="card">
        <div class="card-body p-0 table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Budget</th>
                        <th>Manager</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($programs)): ?>
                    <tr><td colspan="8" class="text-center py-4">No programs found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($programs as $p): 
                            $badgeClass = match($p['status']) {
                                'active' => 'bg-success',
                                'completed' => 'bg-blue text-dark bg-info',
                                'planned' => 'bg-secondary',
                                'suspended' => 'bg-warning text-dark',
                                'archived' => 'bg-dark',
                                default => 'bg-light text-dark'
                            };
                        ?>
                        <tr>
                            <td><?= h($p['program_code']) ?></td>
                            <td><?= h($p['name']) ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= h(ucfirst($p['status'])) ?></span></td>
                            <td><?= $p['start_date'] ? date('Y-m-d', strtotime($p['start_date'])) : '-' ?></td>
                            <td><?= $p['end_date'] ? date('Y-m-d', strtotime($p['end_date'])) : '-' ?></td>
                            <td><?= h($p['currency']) ?> <?= number_format((float)$p['budget'], 2) ?></td>
                            <td><?= h($p['manager_name'] ?: '-') ?></td>
                            <td>
                                <a href="<?= url('/admin/programs/' . $p['id']) ?>" class="btn btn-sm btn-outline-info">View</a>
                                <a href="<?= url('/admin/programs/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
