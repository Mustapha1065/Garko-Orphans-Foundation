<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ID Verification - GOF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .gof-header { background-color: #1B5E3B; color: white; padding: 20px 0; text-align: center; }
        .verify-card { max-width: 500px; margin: 40px auto; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); padding: 30px; text-align: center; }
        .status-icon { font-size: 64px; margin-bottom: 20px; }
        .status-verified { color: #198754; }
        .status-invalid { color: #dc3545; }
        .status-revoked { color: #dc3545; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee; text-align: left; }
        .detail-label { color: #6c757d; font-weight: bold; }
        .detail-value { font-weight: 500; }
        .footer { text-align: center; margin-top: 40px; color: #6c757d; font-size: 14px; }
    </style>
</head>
<body>

<div class="gof-header">
    <h2>GOF ID Verification</h2>
</div>

<div class="container">
    <div class="verify-card">
        <?php if ($status === 'VERIFIED'): ?>
            <div class="status-icon status-verified">✓</div>
            <h2 class="status-verified mb-4">VERIFIED</h2>
        <?php elseif ($status === 'REVOKED'): ?>
            <div class="status-icon status-revoked">✗</div>
            <h2 class="status-revoked mb-4">REVOKED</h2>
        <?php else: ?>
            <div class="status-icon status-invalid">!</div>
            <h2 class="status-invalid mb-4">INVALID / EXPIRED</h2>
        <?php endif; ?>

        <?php if ($card): ?>
            <div class="details mt-4">
                <div class="detail-row">
                    <span class="detail-label">Name</span>
                    <span class="detail-value"><?= htmlspecialchars($card['person_name']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Category</span>
                    <span class="detail-value"><?= htmlspecialchars(ucfirst($card['person_type'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">GOF ID</span>
                    <span class="detail-value"><?= htmlspecialchars($card['gof_id']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Card Number</span>
                    <span class="detail-value"><?= htmlspecialchars($card['card_no']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Issue Date</span>
                    <span class="detail-value"><?= htmlspecialchars($card['issue_date']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Expiry Date</span>
                    <span class="detail-value"><?= htmlspecialchars($card['expiry_date']) ?></span>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted">The scanned QR code is invalid or the ID card does not exist.</p>
        <?php endif; ?>

        <div class="mt-4 text-muted" style="font-size: 12px;">
            This verification was issued by Garko Orphans Foundation.
        </div>
    </div>
</div>

<div class="footer pb-4">
    <p>Garko Orphans Foundation<br>Garko, Kano State, Nigeria | info@gof.org.ng</p>
</div>

</body>
</html>
