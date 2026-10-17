<div class="container-fluid">
    <h2>ID Card Tokens</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Token</th>
                        <th>Card No</th>
                        <th>Status</th>
                        <th>Issued At</th>
                        <th>Expires At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tokens as $t): ?>
                    <tr>
                        <td><span class="text-muted"><?= htmlspecialchars(substr($t['token'], 0, 16)) ?>...</span></td>
                        <td><?= htmlspecialchars($t['card_no']) ?></td>
                        <td>
                            <?php if (!$t['revoked']): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Revoked</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($t['issued_at']) ?></td>
                        <td><?= htmlspecialchars($t['expires_at']) ?></td>
                        <td>
                            <?php if (!$t['revoked']): ?>
                            <form action="<?= url('/admin/idcards/revokeToken') ?>" method="POST" class="d-inline">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="token_id" value="<?= $t['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Revoke</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
