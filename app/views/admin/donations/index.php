<?php
/**
 * GOF3-light
 * Admin Donation Management
 *
 * Confirmed donations are stored with status = "confirmed".
 */

function h($v)
{
    return htmlspecialchars(
        (string)($v ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

/*
|--------------------------------------------------------------------------
| Controller summary
|--------------------------------------------------------------------------
| DonationsController provides:
|   confirmed_total
|   confirmed_count
|   pending_count
|   rejected_count
*/
$summary = is_array($summary ?? null)
    ? $summary
    : [];

$totalConfirmed = (float)(
    $summary['confirmed_total']
    ?? $totalConfirmed
    ?? 0
);

$confirmedCount = (int)(
    $summary['confirmed_count']
    ?? 0
);

$pendingCount = (int)(
    $summary['pending_count']
    ?? $pendingCount
    ?? 0
);

$rejectedCount = (int)(
    $summary['rejected_count']
    ?? $rejectedCount
    ?? 0
);

$currentStatus = $status ?? '';
$currentDonor = $donor ?? ($search ?? '');
$currentDateFrom = $dateFrom ?? ($from ?? '');
$currentDateTo = $dateTo ?? ($to ?? '');

$totalPages = (int)($totalPages ?? 0);
$currentPage = max(1, (int)($page ?? 1));

$donationCurrency = $donationCurrency ?? 'NGN';

$statusLabels = [
    'pending'   => 'Pending',
    'confirmed' => 'Confirmed',
    'rejected'  => 'Rejected',
    'refunded'  => 'Refunded',
    'cancelled' => 'Cancelled'
];
?>

<div class="container-fluid py-4">

    <!-- =========================================================
         HEADER
    ========================================================== -->

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1 text-gray-800">
                Donation Management
            </h1>

            <p class="text-muted mb-0">
                Manage, review and track foundation donations.
            </p>
        </div>

        <a
            href="<?= url('/admin/donations/create') ?>"
            class="btn btn-primary"
        >
            <i class="bi bi-plus-circle me-1"></i>
            Record Donation
        </a>

    </div>


    <!-- =========================================================
         STATISTICS
    ========================================================== -->

    <div class="row g-3 mb-4">

        <!-- CONFIRMED -->
        <div class="col-xl-4 col-md-6">

            <div class="card shadow-sm border-start border-success border-4 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="text-muted small text-uppercase fw-semibold">
                                Confirmed Donations
                            </div>

                            <div class="h4 mb-1 text-success fw-bold">

                                <?= h($donationCurrency) ?>

                                <?= number_format($totalConfirmed, 2) ?>

                            </div>

                        </div>

                        <div class="text-success fs-2">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>

                    </div>

                    <div class="small text-muted">

                        <?= number_format($confirmedCount) ?>

                        confirmed donation
                        <?= $confirmedCount === 1 ? 'record' : 'records' ?>

                    </div>

                    <div class="small text-muted mt-1">
                        Total confirmed amount
                    </div>

                </div>

            </div>

        </div>


        <!-- PENDING -->
        <div class="col-xl-4 col-md-6">

            <div class="card shadow-sm border-start border-warning border-4 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="text-muted small text-uppercase fw-semibold">
                                Pending
                            </div>

                            <div class="h4 mb-1 text-warning fw-bold">
                                <?= number_format($pendingCount) ?>
                            </div>

                        </div>

                        <div class="text-warning fs-2">
                            <i class="bi bi-clock-fill"></i>
                        </div>

                    </div>

                    <small class="text-muted">
                        Awaiting confirmation
                    </small>

                </div>

            </div>

        </div>


        <!-- REJECTED -->
        <div class="col-xl-4 col-md-6">

            <div class="card shadow-sm border-start border-danger border-4 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="text-muted small text-uppercase fw-semibold">
                                Rejected
                            </div>

                            <div class="h4 mb-1 text-danger fw-bold">
                                <?= number_format($rejectedCount) ?>
                            </div>

                        </div>

                        <div class="text-danger fs-2">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>

                    </div>

                    <small class="text-muted">
                        Rejected donation records
                    </small>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         FILTERS
    ========================================================== -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-white py-3">

            <h6 class="mb-0 fw-bold">
                <i class="bi bi-funnel me-1"></i>
                Search & Filter Donations
            </h6>

        </div>

        <div class="card-body">

            <form
                method="GET"
                action="<?= url('/admin/donations') ?>"
            >

                <div class="row g-3 align-items-end">

                    <!-- SEARCH -->

                    <div class="col-lg-4 col-md-6">

                        <label class="form-label fw-semibold">
                            Donor / Reference
                        </label>

                        <input
                            type="text"
                            name="donor"
                            class="form-control"
                            value="<?= h($currentDonor) ?>"
                            placeholder="Donor name, GOF ID or donation ref"
                        >

                    </div>


                    <!-- STATUS -->

                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                All Statuses
                            </option>

                            <?php foreach ($statusLabels as $value => $label): ?>

                                <option
                                    value="<?= h($value) ?>"
                                    <?= $currentStatus === $value ? 'selected' : '' ?>
                                >
                                    <?= h($label) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- FROM -->

                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            From Date
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="<?= h($currentDateFrom) ?>"
                        >

                    </div>


                    <!-- TO -->

                    <div class="col-lg-2 col-md-6">

                        <label class="form-label fw-semibold">
                            To Date
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="<?= h($currentDateTo) ?>"
                        >

                    </div>


                    <!-- BUTTONS -->

                    <div class="col-lg-2 col-md-6">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-primary flex-grow-1"
                            >
                                <i class="bi bi-search me-1"></i>
                                Filter
                            </button>

                            <a
                                href="<?= url('/admin/donations') ?>"
                                class="btn btn-outline-secondary"
                                title="Clear filters"
                            >
                                <i class="bi bi-x-lg"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <!-- =========================================================
         DONATION RECORDS
    ========================================================== -->

    <div class="card shadow-sm">

        <div class="card-header bg-white py-3">

            <div class="d-flex justify-content-between align-items-center">

                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-cash-stack me-1"></i>
                    Donation Records
                </h6>

                <span class="text-muted small">
                    Page <?= $currentPage ?>
                    of <?= max(1, $totalPages) ?>
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="px-3">
                                Donation Ref
                            </th>

                            <th>
                                Donor
                            </th>

                            <th>
                                Amount
                            </th>

                            <th>
                                Date
                            </th>

                            <th>
                                Payment
                            </th>

                            <th>
                                Program
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end px-3">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if (empty($donations)): ?>

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <div class="fs-1 mb-2">
                                        <i class="bi bi-wallet2"></i>
                                    </div>

                                    <h5>
                                        No donations found
                                    </h5>

                                    <p class="mb-3">
                                        No donation records match your current filters.
                                    </p>

                                    <a
                                        href="<?= url('/admin/donations/create') ?>"
                                        class="btn btn-primary"
                                    >
                                        <i class="bi bi-plus-circle me-1"></i>
                                        Record First Donation
                                    </a>

                                </div>

                            </td>

                        </tr>

                    <?php else: ?>

                        <?php foreach ($donations as $d): ?>

                            <?php

                            $rowStatus = strtolower(
                                (string)($d['status'] ?? '')
                            );

                            $statusBadge = match ($rowStatus) {

                                'confirmed'
                                    => 'bg-success',

                                'pending'
                                    => 'bg-warning text-dark',

                                'rejected'
                                    => 'bg-danger',

                                'refunded'
                                    => 'bg-danger',

                                'cancelled'
                                    => 'bg-secondary',

                                default
                                    => 'bg-light text-dark'
                            };

                            $statusIcon = match ($rowStatus) {

                                'confirmed'
                                    => 'bi-check-circle-fill',

                                'pending'
                                    => 'bi-clock-fill',

                                'rejected'
                                    => 'bi-x-circle-fill',

                                'refunded'
                                    => 'bi-arrow-counterclockwise',

                                'cancelled'
                                    => 'bi-slash-circle',

                                default
                                    => 'bi-info-circle'
                            };

                            $currency = $d['currency'] ?? 'NGN';

                            $amount = (float)(
                                $d['amount'] ?? 0
                            );

                            $paymentMethod = ucfirst(
                                str_replace(
                                    '_',
                                    ' ',
                                    (string)(
                                        $d['payment_method'] ?? ''
                                    )
                                )
                            );

                            $donorName =
                                $d['donor_name']
                                ?? 'Unknown Donor';

                            $programName =
                                $d['program_name']
                                ?? '';

                            $donationDate = !empty(
                                $d['donation_date']
                            )
                                ? date(
                                    'Y-m-d',
                                    strtotime(
                                        $d['donation_date']
                                    )
                                )
                                : '-';

                            ?>

                            <tr>

                                <!-- REF -->

                                <td class="px-3">

                                    <a
                                        href="<?= url('/admin/donations/' . (int)$d['id']) ?>"
                                        class="fw-semibold text-decoration-none"
                                    >
                                        <?= h(
                                            $d['donation_ref'] ?? '-'
                                        ) ?>
                                    </a>

                                </td>


                                <!-- DONOR -->

                                <td>

                                    <div class="fw-semibold">
                                        <?= h($donorName) ?>
                                    </div>

                                    <?php if (!empty($d['donor_gof_id'])): ?>

                                        <small class="text-muted">
                                            <?= h(
                                                $d['donor_gof_id']
                                            ) ?>
                                        </small>

                                    <?php endif; ?>

                                </td>


                                <!-- AMOUNT -->

                                <td>

                                    <span class="fw-bold">

                                        <?= h($currency) ?>

                                        <?= number_format(
                                            $amount,
                                            2
                                        ) ?>

                                    </span>

                                </td>


                                <!-- DATE -->

                                <td>
                                    <?= h($donationDate) ?>
                                </td>


                                <!-- PAYMENT -->

                                <td>

                                    <span class="text-capitalize">
                                        <?= h($paymentMethod) ?>
                                    </span>

                                </td>


                                <!-- PROGRAM -->

                                <td>

                                    <?php if ($programName): ?>

                                        <span class="badge bg-light text-dark border">
                                            <?= h($programName) ?>
                                        </span>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="badge <?= $statusBadge ?>"
                                    >

                                        <i
                                            class="bi <?= $statusIcon ?>"
                                        ></i>

                                        <?= h(
                                            ucfirst($rowStatus)
                                        ) ?>

                                    </span>

                                </td>


                                <!-- ACTIONS -->

                                <td class="text-end px-3">

                                    <div class="btn-group">

                                        <a
                                            href="<?= url('/admin/donations/' . (int)$d['id']) ?>"
                                            class="btn btn-sm btn-outline-primary"
                                            title="View donation"
                                        >
                                            <i class="bi bi-eye"></i>
                                            View
                                        </a>


                                        <?php if ($rowStatus === 'confirmed'): ?>

                                            <a
                                                href="<?= url('/admin/donations/' . (int)$d['id'] . '/receipt') ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-success"
                                                title="View receipt"
                                            >
                                                <i class="bi bi-receipt"></i>
                                                Receipt
                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- =========================================================
         PAGINATION
    ========================================================== -->

    <?php if ($totalPages > 1): ?>

        <?php

        $queryParams = [
            'donor'     => $currentDonor,
            'status'    => $currentStatus,
            'date_from' => $currentDateFrom,
            'date_to'   => $currentDateTo
        ];

        ?>

        <nav
            class="mt-4"
            aria-label="Donation pagination"
        >

            <ul class="pagination justify-content-center">

                <!-- PREVIOUS -->

                <?php

                $queryParams['page'] = max(
                    1,
                    $currentPage - 1
                );

                ?>

                <li
                    class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>"
                >

                    <a
                        class="page-link"
                        href="?<?= http_build_query($queryParams) ?>"
                    >
                        <i class="bi bi-chevron-left"></i>
                    </a>

                </li>


                <?php

                $startPage = max(
                    1,
                    $currentPage - 2
                );

                $endPage = min(
                    $totalPages,
                    $currentPage + 2
                );

                ?>


                <!-- FIRST PAGE -->

                <?php if ($startPage > 1): ?>

                    <?php $queryParams['page'] = 1; ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="?<?= http_build_query($queryParams) ?>"
                        >
                            1
                        </a>

                    </li>

                    <?php if ($startPage > 2): ?>

                        <li class="page-item disabled">

                            <span class="page-link">
                                …
                            </span>

                        </li>

                    <?php endif; ?>

                <?php endif; ?>


                <!-- PAGE NUMBERS -->

                <?php for (
                    $i = $startPage;
                    $i <= $endPage;
                    $i++
                ): ?>

                    <?php $queryParams['page'] = $i; ?>

                    <li
                        class="page-item <?= $currentPage === $i ? 'active' : '' ?>"
                    >

                        <a
                            class="page-link"
                            href="?<?= http_build_query($queryParams) ?>"
                        >
                            <?= $i ?>
                        </a>

                    </li>

                <?php endfor; ?>


                <!-- LAST PAGE -->

                <?php if ($endPage < $totalPages): ?>

                    <?php if (
                        $endPage < $totalPages - 1
                    ): ?>

                        <li class="page-item disabled">

                            <span class="page-link">
                                …
                            </span>

                        </li>

                    <?php endif; ?>


                    <?php
                    $queryParams['page'] = $totalPages;
                    ?>

                    <li class="page-item">

                        <a
                            class="page-link"
                            href="?<?= http_build_query($queryParams) ?>"
                        >
                            <?= $totalPages ?>
                        </a>

                    </li>

                <?php endif; ?>


                <!-- NEXT -->

                <?php

                $queryParams['page'] = min(
                    $totalPages,
                    $currentPage + 1
                );

                ?>

                <li
                    class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>"
                >

                    <a
                        class="page-link"
                        href="?<?= http_build_query($queryParams) ?>"
                    >
                        <i class="bi bi-chevron-right"></i>
                    </a>

                </li>

            </ul>

        </nav>

    <?php endif; ?>

</div>