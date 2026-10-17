<?php function h($v){return htmlspecialchars((string)($v??''),ENT_QUOTES,'UTF-8');} ?>
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Support History</h1>
            <p class="text-muted mb-0">Beneficiary: <strong><?= h($beneficiary['full_name']) ?> (<?= h($beneficiary['gof_id']) ?>)</strong></p>
        </div>
        <a href="<?= url('/admin/beneficiaries/' . $beneficiary['id']) ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 text-primary"><i class="bi bi-list-ul me-2"></i>History</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Amount/Qty</th>
                        <th>Date</th>
                        <th>Program</th>
                        <th>Staff</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($supportRecords)): ?>
                        <tr><td colspan="7" class="text-center py-4 text-muted">No support records found.</td></tr>
                    <?php else: ?>
                        <?php foreach($supportRecords as $sr): ?>
                        <tr>
                            <td><span class="badge bg-info text-dark"><?= h($sr['support_type']) ?></span></td>
                            <td><?= h($sr['description']) ?></td>
                            <td>
                                <?php if($sr['amount'] > 0): ?>
                                    <?= h($sr['currency'] . ' ' . number_format($sr['amount'], 2)) ?>
                                <?php endif; ?>
                                <?php if($sr['quantity']): ?>
                                    <small class="text-muted ms-1">(Qty: <?= h($sr['quantity']) ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td><?= h($sr['support_date'] ?? $sr['date'] ?? '') ?></td>
                            <td><?= h($sr['program_name'] ?? '-') ?></td>
                            <td><?= h($sr['staff_name'] ?? '-') ?></td>
                            <td><?= h($sr['notes'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 text-success"><i class="bi bi-plus-circle me-2"></i>Add New Support</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= url('/admin/support/store') ?>">
                <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="beneficiary_id" value="<?= h($beneficiary['id']) ?>">
                
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Support Type *</label>
                        <select name="support_type" class="form-select" required>
                            <option value="">Select...</option>
                            <option value="Education">Education</option>
                            <option value="Food">Food</option>
                            <option value="Clothing">Clothing</option>
                            <option value="Medical">Medical</option>
                            <option value="Financial">Financial</option>
                            <option value="Materials">Materials</option>
                            <option value="Shelter">Shelter</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Description *</label>
                        <input type="text" name="description" class="form-control" placeholder="e.g. School fees for 2025" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Program</label>
                        <select name="program_id" class="form-select">
                            <option value="">None</option>
                            <?php foreach($programs ?? [] as $p): ?>
                                <option value="<?= h($p['id']) ?>"><?= h($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Currency</label>
                        <select name="currency" class="form-select">
                            <option value="NGN">NGN</option>
                            <option value="USD">USD</option>
                            <option value="EUR">EUR</option>
                            <option value="GBP">GBP</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Support Date *</label>
                        <input type="date" name="support_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="text-end">
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Save Support Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
