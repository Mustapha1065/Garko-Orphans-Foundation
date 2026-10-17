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
        <h2>ID Cards Report</h2>
        <button onclick="window.print()" class="btn btn-primary no-print">Print Report</button>
    </div>

    <div class="print-area">
        <h4 class="mb-3 d-none d-print-block text-center">Garko Orphans Foundation - ID Cards Report</h4>
        <div class="mb-3">
            <strong>Total Cards Issued:</strong> <?= count($data) ?>
        </div>
        
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Card No</th>
                    <th>Person Type</th>
                    <th>Person ID</th>
                    <th>Status</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['card_no']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['person_type'])) ?></td>
                    <td><?= htmlspecialchars($row['person_id']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($row['status'])) ?></td>
                    <td><?= htmlspecialchars($row['issue_date']) ?></td>
                    <td><?= htmlspecialchars($row['expiry_date']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($data)): ?>
                <tr><td colspan="6" class="text-center">No records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
