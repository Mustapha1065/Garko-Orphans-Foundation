<div class="container-fluid">
    <h2>Verification Attempts</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Token (Truncated)</th>
                        <th>Status</th>
                        <th>Reason</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attempts as $a): ?>
                    <tr>
                        <td><?= htmlspecialchars($a['ip_address']) ?></td>
                        <td><?= htmlspecialchars(substr($a['token'], 0, 10)) ?>...</td>
                        <td>
                            <?php if ($a['success']): ?>
                                <span class="badge bg-success">Success</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Failed</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($a['reason']) ?></td>
                        <td><?= htmlspecialchars($a['created_at']) ?></td>
                        <td>
                            <form action="<?= url('/admin/idcards/blockIp') ?>" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="ip" value="<?= htmlspecialchars($a['ip_address']) ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Block IP</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
