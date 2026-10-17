<div class="container-fluid">
    <h2>Audit Logs</h2>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Module</th>
                        <th>Entity ID</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= htmlspecialchars(($log['first_name'] ?? 'System') . ' ' . ($log['last_name'] ?? '')) ?></td>
                        <td><span class="badge bg-secondary"><?= htmlspecialchars($log['action']) ?></span></td>
                        <td><?= htmlspecialchars(ucfirst($log['module'])) ?></td>
                        <td><?= htmlspecialchars($log['entity_id'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($log['description']) ?></td>
                        <td><?= htmlspecialchars($log['ip_address']) ?></td>
                        <td><?= htmlspecialchars($log['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
