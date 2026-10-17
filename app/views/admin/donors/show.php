<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$donorId = (int)($donor['id'] ?? 0);
$donorName = trim($donor['name'] ?? '');
$organization = trim($donor['organization_name'] ?? '');
$photo = trim($donor['photo'] ?? '');

$initial = strtoupper(substr($donorName ?: ($organization ?: 'D'), 0, 1));

$photoUrl = '';
if ($photo !== '') {
    $photoUrl = url('/' . ltrim($photo, '/'));
}
?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                Donor Profile
            </h1>
            <p class="text-muted mb-0">
                <?= h($donor['gof_id'] ?? '') ?>
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="<?= url('/admin/donors') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>
                Back
            </a>

            <a href="<?= url('/admin/donors/' . $donorId . '/edit') ?>"
               class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i>
                Edit Donor
            </a>
        </div>
    </div>


    <!-- Donor Profile Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <div class="row align-items-center">

                <!-- Photo -->
                <div class="col-md-3 text-center mb-4 mb-md-0">

                    <?php if ($photoUrl): ?>

                        <img src="<?= h($photoUrl) ?>"
                             alt="<?= h($donorName ?: 'Donor') ?>"
                             class="rounded-circle shadow-sm"
                             style="
                                width:150px;
                                height:150px;
                                object-fit:cover;
                                border:5px solid #f1f3f5;
                             ">

                    <?php else: ?>

                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                             style="
                                width:150px;
                                height:150px;
                                background:#e9ecef;
                                font-size:56px;
                                font-weight:600;
                                color:#495057;
                             ">
                            <?= h($initial) ?>
                        </div>

                    <?php endif; ?>

                </div>


                <!-- Basic Information -->
                <div class="col-md-5">

                    <h2 class="h4 mb-1">
                        <?= h($donorName ?: 'Unnamed Donor') ?>
                    </h2>

                    <?php if ($organization): ?>
                        <div class="text-muted mb-2">
                            <i class="bi bi-building me-1"></i>
                            <?= h($organization) ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-2">
                        <span class="badge bg-primary">
                            <?= h(ucfirst($donor['donor_type'] ?? 'individual')) ?>
                        </span>

                        <span class="badge bg-<?= ($donor['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                            <?= h(ucfirst($donor['status'] ?? 'active')) ?>
                        </span>
                    </div>

                    <div class="text-muted">
                        <strong>GOF ID:</strong>
                        <?= h($donor['gof_id'] ?? '-') ?>
                    </div>

                </div>


                <!-- ID Card Actions -->
                <div class="col-md-4 text-md-end mt-4 mt-md-0">

                    <?php if (!empty($donor['id_card_id'])): ?>

                        <div class="mb-2">
                            <span class="badge bg-success px-3 py-2">
                                <i class="bi bi-credit-card me-1"></i>
                                ID Card Generated
                            </span>
                        </div>

                        <div class="d-flex justify-content-md-end gap-2 flex-wrap">

                            <a href="<?= url('/admin/idcards/' . (int)$donor['id_card_id']) ?>"
                               class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>
                                View ID Card
                            </a>

                            <a href="<?= url('/admin/idcards/' . (int)$donor['id_card_id'] . '/print') ?>"
                               target="_blank"
                               class="btn btn-outline-dark">
                                <i class="bi bi-printer me-1"></i>
                                Print
                            </a>

                            <a href="<?= url('/admin/idcards/' . (int)$donor['id_card_id'] . '/pdf') ?>"
                               class="btn btn-outline-danger">
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                PDF
                            </a>

                        </div>

                    <?php else: ?>

                        <!-- Generate Donor ID -->
                        <form method="POST"
                              action="<?= url('/admin/idcards/generate') ?>"
                              class="d-inline">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                            <input type="hidden"
                                   name="person_type"
                                   value="donor">

                            <input type="hidden"
                                   name="person_id"
                                   value="<?= $donorId ?>">

                            <button type="submit"
                                    class="btn btn-success btn-lg">
                                <i class="bi bi-credit-card-2-front me-2"></i>
                                Generate Donor ID Card
                            </button>

                        </form>

                    <?php endif; ?>

                </div>

            </div>

        </div>
    </div>


    <!-- Contact & Donor Information -->
    <div class="row g-4 mb-4">

        <div class="col-lg-6">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-vcard me-2 text-primary"></i>
                        Donor Information
                    </h5>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">
                            Donor Type
                        </div>
                        <div class="col-sm-7 fw-semibold">
                            <?= h(ucfirst($donor['donor_type'] ?? '-')) ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">
                            Organization
                        </div>
                        <div class="col-sm-7">
                            <?= h($donor['organization_name'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">
                            GOF ID
                        </div>
                        <div class="col-sm-7">
                            <span class="badge bg-light text-dark border">
                                <?= h($donor['gof_id'] ?? '-') ?>
                            </span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">
                            Status
                        </div>
                        <div class="col-sm-7">
                            <?php if (($donor['status'] ?? 'active') === 'active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Inactive</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-5 text-muted">
                            Registered
                        </div>
                        <div class="col-sm-7">
                            <?= h($donor['created_at'] ?? '-') ?>
                        </div>
                    </div>

                </div>

            </div>

        </div>


        <div class="col-lg-6">

            <div class="card shadow-sm h-100">

                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-telephone me-2 text-primary"></i>
                        Contact Information
                    </h5>
                </div>

                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">
                            Email
                        </div>
                        <div class="col-sm-8">
                            <?php if (!empty($donor['email'])): ?>
                                <a href="mailto:<?= h($donor['email']) ?>">
                                    <?= h($donor['email']) ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">
                            Phone
                        </div>
                        <div class="col-sm-8">
                            <?= h($donor['phone'] ?? '-') ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">
                            Address
                        </div>
                        <div class="col-sm-8">
                            <?= nl2br(h($donor['address'] ?? '-')) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-4 text-muted">
                            Country
                        </div>
                        <div class="col-sm-8">
                            <?= h($donor['country'] ?? '-') ?>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- Donation Statistics -->
    <div class="row g-4 mb-4">

        <div class="col-md-4">

            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body">

                    <div class="text-muted small">
                        TOTAL DONATED
                    </div>

                    <div class="h3 mb-0 text-success">
                        ₦<?= number_format((float)($totalAmount ?? 0), 2) ?>
                    </div>

                </div>
            </div>

        </div>


        <div class="col-md-4">

            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body">

                    <div class="text-muted small">
                        CONFIRMED DONATIONS
                    </div>

                    <div class="h3 mb-0 text-primary">
                        <?= number_format((int)($totalCount ?? 0)) ?>
                    </div>

                </div>
            </div>

        </div>


        <div class="col-md-4">

            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body">

                    <div class="text-muted small">
                        LAST DONATION
                    </div>

                    <div class="h5 mb-0">
                        <?= h($lastDonation ?? 'No donation yet') ?>
                    </div>

                </div>
            </div>

        </div>

    </div>


    <!-- Donation History -->
    <div class="card shadow-sm">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                <i class="bi bi-cash-stack me-2 text-success"></i>
                Donation History
            </h5>

        </div>

        <div class="card-body p-0">

            <?php if (!empty($donations)): ?>

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Reference</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($donations as $d): ?>

                            <tr>

                                <td>
                                    <?= h($d['donation_date'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= h(
                                        $d['reference_no']
                                        ?? $d['transaction_reference']
                                        ?? $d['id']
                                        ?? '-'
                                    ) ?>
                                </td>

                                <td class="fw-semibold">
                                    ₦<?= number_format((float)($d['amount'] ?? 0), 2) ?>
                                </td>

                                <td>
                                    <?= h(
                                        ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $d['payment_method'] ?? '-'
                                            )
                                        )
                                    ) ?>
                                </td>

                                <td>

                                    <?php
                                    $status = strtolower($d['status'] ?? '');
                                    ?>

                                    <?php if ($status === 'confirmed'): ?>

                                        <span class="badge bg-success">
                                            Confirmed
                                        </span>

                                    <?php elseif ($status === 'pending'): ?>

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            <?= h(ucfirst($status ?: 'Unknown')) ?>
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="text-center py-5 text-muted">

                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>

                    <p class="mb-0">
                        No donations recorded for this donor yet.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- Notes -->
    <?php if (!empty($donor['notes'])): ?>

        <div class="card shadow-sm mt-4">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-journal-text me-2 text-primary"></i>
                    Internal Notes
                </h5>

            </div>

            <div class="card-body">
                <?= nl2br(h($donor['notes'])) ?>
            </div>

        </div>

    <?php endif; ?>

</div>