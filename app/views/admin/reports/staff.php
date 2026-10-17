<style>
@media print {
    body * { visibility: hidden; }
    .print-area, .print-area * { visibility: visible; }
    .print-area { position: absolute; left: 0; top: 0; width: 100%; }
    .no-print { display: none !important; }
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Staff Report</h2>
        <button onclick="window.print()" class="btn btn-primary no-print">Print Report</button>
    </div>

    <div class="print-area">
        <h4 class="mb-3 d-none d-print-block text-center">Garko Orphans Foundation - Staff Report</h4>
        <div class="mb-3">
            <strong>Total Staff:</strong> <?= count($data) ?>
        </div>
        
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>GOF ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Joined Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td>S-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                    <td><?= htmlspecialchars($row['position']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="6" class="text-center">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
