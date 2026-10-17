<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$status = strtolower((string)($donation['status'] ?? 'pending'));

$statusBadge = match ($status) {
    'confirmed' => 'bg-success',
    'pending'   => 'bg-warning text-dark',
    'rejected'  => 'bg-danger',
    'refunded'  => 'bg-danger',
    'cancelled' => 'bg-secondary',
    default     => 'bg-light text-dark'
};

$statusIcon = match ($status) {
    'confirmed' => 'bi-check-circle-fill',
    'pending'   => 'bi-clock-fill',
    'rejected'  => 'bi-x-circle-fill',
    'refunded'  => 'bi-arrow-counterclockwise',
    'cancelled' => 'bi-slash-circle',
    default     => 'bi-info-circle'
};

$paymentMethod = ucfirst(
    str_replace('_', ' ', (string)($donation['payment_method'] ?? ''))
);

$amount = (float)($donation['amount'] ?? 0);

$donationDate = !empty($donation['donation_date'])
    ? date('F j, Y', strtotime($donation['donation_date']))
    : '-';

$createdAt = !empty($donation['created_at'])
    ? date('F j, Y g:i A', strtotime($donation['created_at']))
    : '-';

$confirmedAt = !empty($donation['confirmed_at'])
    ? date('F j, Y g:i A', strtotime($donation['confirmed_at']))
    : '-';

$donorName = $donation['donor_name'] ?? 'Unknown Donor';
$donorGofId = $donation['donor_gof_id'] ?? '';

$donorOrganization = $donation['organization_name'] ?? '';
$donorEmail = $donation['donor_email'] ?? '';
$donorPhone = $donation['donor_phone'] ?? '';

$recordedBy = $donation['recorded_by_name'] ?? 'System';
$confirmedBy = $donation['confirmed_by_name'] ?? 'System';
?>

<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1 text-gray-800">
                Donation Details
            </h1>

            <p class="text-muted mb-0">
                View and manage this donation record.
            </p>
        </div>

        <div class="d-flex gap-2">

            <?php if ($status === 'confirmed'): ?>

                <a
                    href="<?= url('/admin/donations/' . (int)$donation['id'] . '/receipt') ?>"
                    target="_blank"
                    class="btn btn-primary"
                >
                    <i class="bi bi-receipt"></i>
                    View / Print Receipt
                </a>

            <?php endif; ?>

            <a
                href="<?= url('/admin/donations') ?>"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

        </div>

    </div>


    <div class="row">

        <!-- Main Details -->
        <div class="col-lg-8">

            <div class="card shadow-sm mb-4">

                <!-- Card Header -->
                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted d-block">
                                Donation Reference
                            </small>

                            <h5 class="mb-0">
                                <span class="badge bg-dark">
                                    <?= h($donation['donation_ref'] ?? '-') ?>
                                </span>
                            </h5>
                        </div>

                        <div>
                            <span class="badge <?= $statusBadge ?> fs-6">

                                <i class="bi <?= $statusIcon ?>"></i>

                                <?= h(ucfirst($status)) ?>

                            </span>
                        </div>

                    </div>

                </div>


                <div class="card-body">

                    <!-- Amount -->
                    <div class="text-center py-3 mb-4">

                        <div class="text-muted text-uppercase small mb-2">
                            Donation Amount
                        </div>

                        <div class="display-5 fw-bold text-success">

                            <?= h($donation['currency'] ?? 'NGN') ?>

                            <?= number_format($amount, 2) ?>

                        </div>

                    </div>


                    <!-- Donation Information -->
                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <tbody>

                                <tr>
                                    <th class="bg-light" style="width: 30%;">
                                        Donor
                                    </th>

                                    <td>

                                        <?php if (!empty($donation['donor_id'])): ?>

                                            <a
                                                href="<?= url('/admin/donors/' . (int)$donation['donor_id']) ?>"
                                                class="fw-semibold text-decoration-none"
                                            >
                                                <?= h($donorName) ?>
                                            </a>

                                        <?php else: ?>

                                            <?= h($donorName) ?>

                                        <?php endif; ?>


                                        <?php if ($donorGofId): ?>

                                            <span class="badge bg-secondary ms-2">
                                                <?= h($donorGofId) ?>
                                            </span>

                                        <?php endif; ?>

                                    </td>
                                </tr>


                                <?php if ($donorOrganization): ?>

                                    <tr>
                                        <th class="bg-light">
                                            Organization
                                        </th>

                                        <td>
                                            <?= h($donorOrganization) ?>
                                        </td>
                                    </tr>

                                <?php endif; ?>


                                <?php if ($donorEmail): ?>

                                    <tr>
                                        <th class="bg-light">
                                            Donor Email
                                        </th>

                                        <td>
                                            <a href="mailto:<?= h($donorEmail) ?>">
                                                <?= h($donorEmail) ?>
                                            </a>
                                        </td>
                                    </tr>

                                <?php endif; ?>


                                <?php if ($donorPhone): ?>

                                    <tr>
                                        <th class="bg-light">
                                            Donor Phone
                                        </th>

                                        <td>
                                            <?= h($donorPhone) ?>
                                        </td>
                                    </tr>

                                <?php endif; ?>


                                <tr>
                                    <th class="bg-light">
                                        Donation Date
                                    </th>

                                    <td>
                                        <?= h($donationDate) ?>
                                    </td>
                                </tr>


                                <tr>
                                    <th class="bg-light">
                                        Payment Method
                                    </th>

                                    <td>
                                        <?= h($paymentMethod) ?>
                                    </td>
                                </tr>


                                <tr>
                                    <th class="bg-light">
                                        Transaction Reference
                                    </th>

                                    <td>

                                        <?php if (!empty($donation['transaction_ref'])): ?>

                                            <code>
                                                <?= h($donation['transaction_ref']) ?>
                                            </code>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Not provided
                                            </span>

                                        <?php endif; ?>

                                    </td>
                                </tr>


                                <tr>
                                    <th class="bg-light">
                                        Purpose
                                    </th>

                                    <td>
                                        <?= h($donation['purpose'] ?: 'General Donation') ?>
                                    </td>
                                </tr>


                                <tr>
                                    <th class="bg-light">
                                        Linked Program
                                    </th>

                                    <td>
                                        <?= h($donation['program_name'] ?? 'No specific program') ?>
                                    </td>
                                </tr>


                                <tr>
                                    <th class="bg-light">
                                        Recorded By
                                    </th>

                                    <td>

                                        <?= h($recordedBy) ?>

                                        <div class="small text-muted">
                                            <?= h($createdAt) ?>
                                        </div>

                                    </td>
                                </tr>


                                <?php if ($status === 'confirmed'): ?>

                                    <tr>
                                        <th class="bg-light">
                                            Confirmed By
                                        </th>

                                        <td>

                                            <?= h($confirmedBy) ?>

                                            <div class="small text-muted">
                                                <?= h($confirmedAt) ?>
                                            </div>

                                        </td>
                                    </tr>

                                <?php endif; ?>


                                <?php if (!empty($donation['notes'])): ?>

                                    <tr>
                                        <th class="bg-light">
                                            Notes
                                        </th>

                                        <td>
                                            <?= nl2br(h($donation['notes'])) ?>
                                        </td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            <!-- Pending Actions -->
            <?php if ($status === 'pending'): ?>

                <div class="card shadow-sm border-warning mb-4">

                    <div class="card-header bg-warning bg-opacity-10">
                        <h6 class="mb-0 fw-bold">
                            <i class="bi bi-exclamation-circle"></i>
                            Donation Requires Action
                        </h6>
                    </div>

                    <div class="card-body">

                        <p class="text-muted">
                            This donation is currently pending. Review the payment
                            information before confirming or rejecting it.
                        </p>

                        <div class="d-flex flex-wrap gap-2">

                            <!-- Confirm -->
                            <form
                                method="POST"
                                action="<?= url('/admin/donations/' . (int)$donation['id'] . '/confirm') ?>"
                                onsubmit="return confirm('Are you sure you want to confirm this donation?');"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= h(\GOF\Helpers\Csrf::token()) ?>"
                                >

                                <button
                                    type="submit"
                                    class="btn btn-success"
                                >
                                    <i class="bi bi-check-circle"></i>
                                    Confirm Donation
                                </button>

                            </form>


                            <!-- Reject -->
                            <form
                                method="POST"
                                action="<?= url('/admin/donations/' . (int)$donation['id'] . '/reject') ?>"
                                onsubmit="return confirmRejectDonation();"
                                id="rejectDonationForm"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= h(\GOF\Helpers\Csrf::token()) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="reason"
                                    id="rejectionReason"
                                    value=""
                                >

                                <button
                                    type="submit"
                                    class="btn btn-outline-danger"
                                >
                                    <i class="bi bi-x-circle"></i>
                                    Reject Donation
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <!-- Confirmed -->
            <?php if ($status === 'confirmed'): ?>

                <div class="card shadow-sm border-success mb-4">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div class="me-3 text-success fs-2">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>

                            <div>
                                <h5 class="mb-1">
                                    Donation Confirmed
                                </h5>

                                <p class="text-muted mb-0">
                                    This donation has been confirmed and an official
                                    receipt is available.
                                </p>
                            </div>

                        </div>

                        <div class="mt-3">

                            <a
                                href="<?= url('/admin/donations/' . (int)$donation['id'] . '/receipt') ?>"
                                target="_blank"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-printer"></i>
                                Open Official Receipt
                            </a>

                        </div>

                    </div>

                </div>

            <?php endif; ?>


            <!-- Rejected -->
            <?php if ($status === 'rejected'): ?>

                <div class="card shadow-sm border-danger mb-4">

                    <div class="card-body">

                        <div class="d-flex align-items-center">

                            <div class="me-3 text-danger fs-2">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>

                            <div>
                                <h5 class="mb-1">
                                    Donation Rejected
                                </h5>

                                <p class="text-muted mb-0">
                                    This donation was rejected and cannot be issued
                                    an official receipt.
                                </p>
                            </div>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        </div>


        <!-- Right Sidebar -->
        <div class="col-lg-4">

            <!-- Status Card -->
            <div class="card shadow-sm mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold">
                        <i class="bi bi-info-circle"></i>
                        Donation Status
                    </h6>
                </div>

                <div class="card-body text-center">

                    <div class="mb-3">

                        <span class="badge <?= $statusBadge ?> fs-5 px-4 py-2">

                            <i class="bi <?= $statusIcon ?>"></i>

                            <?= h(ucfirst($status)) ?>

                        </span>

                    </div>

                    <?php if ($status === 'pending'): ?>

                        <p class="text-muted small mb-0">
                            Awaiting confirmation by an authorized finance officer
                            or administrator.
                        </p>

                    <?php elseif ($status === 'confirmed'): ?>

                        <p class="text-success small mb-0">
                            Payment has been confirmed successfully.
                        </p>

                    <?php elseif ($status === 'rejected'): ?>

                        <p class="text-danger small mb-0">
                            This donation has been rejected.
                        </p>

                    <?php endif; ?>

                </div>

            </div>


            <!-- Reference Card -->
            <div class="card shadow-sm mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold">
                        <i class="bi bi-hash"></i>
                        Reference
                    </h6>
                </div>

                <div class="card-body">

                    <small class="text-muted d-block mb-1">
                        Donation Reference
                    </small>

                    <code class="fs-6">
                        <?= h($donation['donation_ref'] ?? '-') ?>
                    </code>

                </div>

            </div>


            <!-- Quick Actions -->
            <div class="card shadow-sm">

                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold">
                        <i class="bi bi-lightning-charge"></i>
                        Quick Actions
                    </h6>
                </div>

                <div class="card-body">

                    <a
                        href="<?= url('/admin/donors/' . (int)$donation['donor_id']) ?>"
                        class="btn btn-outline-primary w-100 mb-2"
                    >
                        <i class="bi bi-person"></i>
                        View Donor
                    </a>


                    <?php if ($status === 'confirmed'): ?>

                        <a
                            href="<?= url('/admin/donations/' . (int)$donation['id'] . '/receipt') ?>"
                            target="_blank"
                            class="btn btn-outline-success w-100"
                        >
                            <i class="bi bi-receipt"></i>
                            View Receipt
                        </a>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>


<script>
function confirmRejectDonation()
{
    const reason = prompt(
        'Please enter the reason for rejecting this donation:'
    );

    if (reason === null) {
        return false;
    }

    if (reason.trim() === '') {
        alert('A rejection reason is required.');
        return false;
    }

    document.getElementById('rejectionReason').value = reason.trim();

    return true;
}
</script>