<?php 
function h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
$badgeClass = match($program['status']) {
    'active' => 'bg-success',
    'completed' => 'bg-info',
    'planned' => 'bg-secondary',
    'suspended' => 'bg-warning text-dark',
    'archived' => 'bg-dark',
    default => 'bg-light text-dark'
};
?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Program: <?= h($program['name']) ?></h2>
        <div>
            <a href="<?= url('/admin/programs/' . $program['id'] . '/edit') ?>" class="btn btn-primary">Edit Program</a>
            <a href="<?= url('/admin/programs') ?>" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Status</h6>
                    <span class="badge <?= $badgeClass ?> fs-6 mb-3"><?= h(ucfirst($program['status'])) ?></span>
                    <h6 class="text-muted text-uppercase">Code</h6>
                    <p class="fw-bold"><?= h($program['program_code']) ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Budget</h6>
                    <h4 class="text-success"><?= h($program['currency']) ?> <?= number_format((float)$program['budget'], 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Timeline</h6>
                    <p class="mb-0 text-nowrap"><strong>Start:</strong> <?= $program['start_date'] ? date('M j, Y', strtotime($program['start_date'])) : '-' ?></p>
                    <p class="mb-0 text-nowrap"><strong>End:</strong> <?= $program['end_date'] ? date('M j, Y', strtotime($program['end_date'])) : '-' ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-light h-100">
                <div class="card-body text-center">
                    <h6 class="text-muted text-uppercase">Manager</h6>
                    <p class="fw-bold"><?= h($program['manager_name'] ?: 'Unassigned') ?></p>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="programTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="beneficiaries-tab" data-bs-toggle="tab" data-bs-target="#beneficiaries" type="button" role="tab">Beneficiaries (<?= count($beneficiaries ?? []) ?>)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="donations-tab" data-bs-toggle="tab" data-bs-target="#donations" type="button" role="tab">Linked Donations (<?= count($donations ?? []) ?>)</button>
        </li>
    </ul>

    <div class="tab-content" id="programTabsContent">
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h5>Description</h5>
                    <p><?= nl2br(h($program['description'] ?: 'No description provided.')) ?></p>
                    
                    <h5 class="mt-4">Objectives</h5>
                    <p><?= nl2br(h($program['objectives'] ?: 'No objectives specified.')) ?></p>
                </div>
            </div>
        </div>
        
        <div class="tab-pane fade" id="beneficiaries" role="tabpanel">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr><th>Name</th><th>Contact</th><th>Enrolled Date</th></tr>
                        </thead>
                        <tbody>
                            <?php if(empty($beneficiaries)): ?>
                                <tr><td colspan="3" class="text-center py-3">No beneficiaries linked to this program.</td></tr>
                            <?php else: ?>
                                <?php foreach($beneficiaries as $ben): ?>
                                    <tr>
                                        <td><?= h($ben['name']) ?></td>
                                        <td><?= h($ben['phone'] ?? $ben['email'] ?? '-') ?></td>
                                        <td><?= date('Y-m-d', strtotime($ben['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="donations" role="tabpanel">
            <div class="card">
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead class="table-light">
                            <tr><th>Ref</th><th>Donor</th><th>Amount</th><th>Date</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if(empty($donations)): ?>
                                <tr><td colspan="5" class="text-center py-3">No donations linked to this program.</td></tr>
                            <?php else: ?>
                                <?php 
                                $totalLinked = 0;
                                foreach($donations as $d): 
                                    if($d['status'] === 'confirmed') $totalLinked += $d['amount'];
                                ?>
                                    <tr>
                                        <td><a href="<?= url('/admin/donations/' . $d['id']) ?>"><?= h($d['donation_ref']) ?></a></td>
                                        <td><?= h($d['donor_name']) ?></td>
                                        <td><?= h($d['currency']) ?> <?= number_format($d['amount'], 2) ?></td>
                                        <td><?= date('Y-m-d', strtotime($d['donation_date'])) ?></td>
                                        <td><?= h(ucfirst($d['status'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if($totalLinked > 0): ?>
                                    <tr class="table-success fw-bold">
                                        <td colspan="2" class="text-end">Total Confirmed Linked Donations:</td>
                                        <td colspan="3"><?= h($program['currency']) ?> <?= number_format($totalLinked, 2) ?></td>
                                    </tr>
                                <?php endif; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
