<?php
$data = $data ?? [];
$summary = $summary ?? [];

if (!function_exists('donation_report_h')) {
    function donation_report_h($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$totalRecords = count($data);

$confirmedAmount = 0;
$pendingCount = 0;
$confirmedCount = 0;
$rejectedCount = 0;

foreach ($data as $row) {
    $status = strtolower(trim((string)($row['status'] ?? '')));
    $amount = (float)($row['amount'] ?? 0);

    if ($status === 'confirmed') {
        $confirmedCount++;
        $confirmedAmount += $amount;
    } elseif ($status === 'pending') {
        $pendingCount++;
    } elseif ($status === 'rejected') {
        $rejectedCount++;
    }
}

/*
 * Prefer controller summary when available.
 */
if (isset($summary['confirmed_amount'])) {
    $confirmedAmount = (float)$summary['confirmed_amount'];
}
?>

<style>
    .donation-report-page {
        padding-bottom: 30px;
    }

    .report-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .report-header h2 {
        margin-bottom: 4px;
        font-weight: 700;
    }

    .report-header p {
        margin-bottom: 0;
    }

    .filter-card {
        border: 0;
        border-radius: 14px;
    }

    .summary-card {
        border: 0;
        border-radius: 12px;
        height: 100%;
    }

    .summary-label {
        font-size: 13px;
        color: #6c757d;
    }

    .summary-value {
        font-size: 24px;
        font-weight: 700;
    }

    .report-table-card {
        border: 0;
        border-radius: 14px;
        overflow: hidden;
    }

    .report-table th {
        background: #f8f9fa;
        white-space: nowrap;
        font-size: 13px;
    }

    .report-table td {
        vertical-align: middle;
        font-size: 13px;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-confirmed {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-pending {
        background: #fff3cd;
        color: #664d03;
    }

    .status-rejected {
        background: #f8d7da;
        color: #842029;
    }

    .report-print-title {
        display: none;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            background: #fff !important;
        }

        body * {
            visibility: hidden;
        }

        .print-area,
        .print-area * {
            visibility: visible;
        }

        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .no-print {
            display: none !important;
        }

        .report-print-title {
            display: block !important;
            text-align: center;
            margin-bottom: 18px;
        }

        .report-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #000 !important;
            padding: 5px !important;
            font-size: 10px !important;
        }

        .report-table-card {
            box-shadow: none !important;
        }

        .status-badge {
            background: transparent !important;
            color: #000 !important;
            padding: 0 !important;
        }
    }

    @media (max-width: 767px) {
        .report-table {
            min-width: 1000px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<div class="container-fluid donation-report-page">

    <!-- Header -->
    <div class="report-header no-print">

        <div>
            <h2>Donation Report</h2>

            <p class="text-muted">
                Search, filter and review donation records.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a
                href="<?= url('/admin/reports') ?>"
                class="btn btn-outline-secondary"
            >
                ← Reports
            </a>

            <button
                type="button"
                onclick="window.print()"
                class="btn btn-primary"
            >
                🖨 Print Report
            </button>

        </div>

    </div>

    <!-- Filters -->
    <div class="card filter-card shadow-sm mb-4 no-print">

        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <!-- Search -->
                    <div class="col-lg-3">

                        <label class="form-label fw-semibold">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="<?= donation_report_h($_GET['search'] ?? '') ?>"
                            placeholder="Reference, donor or organization"
                        >

                    </div>

                    <!-- Status -->
                    <div class="col-md-4 col-lg-2">

                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                All Statuses
                            </option>

                            <option
                                value="pending"
                                <?= (($_GET['status'] ?? '') === 'pending') ? 'selected' : '' ?>
                            >
                                Pending
                            </option>

                            <option
                                value="confirmed"
                                <?= (($_GET['status'] ?? '') === 'confirmed') ? 'selected' : '' ?>
                            >
                                Confirmed
                            </option>

                            <option
                                value="rejected"
                                <?= (($_GET['status'] ?? '') === 'rejected') ? 'selected' : '' ?>
                            >
                                Rejected
                            </option>

                        </select>

                    </div>

                    <!-- Payment method -->
                    <div class="col-md-4 col-lg-2">

                        <label class="form-label fw-semibold">
                            Payment Method
                        </label>

                        <select name="payment_method" class="form-select">

                            <option value="">
                                All Methods
                            </option>

                            <?php
                            $paymentMethods = [
                                'cash' => 'Cash',
                                'bank_transfer' => 'Bank Transfer',
                                'card' => 'Card',
                                'online' => 'Online',
                                'mobile_money' => 'Mobile Money',
                                'other' => 'Other',
                            ];
                            ?>

                            <?php foreach ($paymentMethods as $value => $label): ?>

                                <option
                                    value="<?= donation_report_h($value) ?>"
                                    <?= (($_GET['payment_method'] ?? '') === $value) ? 'selected' : '' ?>
                                >
                                    <?= donation_report_h($label) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- From -->
                    <div class="col-md-6 col-lg-2">

                        <label class="form-label fw-semibold">
                            From
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            class="form-control"
                            value="<?= donation_report_h($_GET['date_from'] ?? '') ?>"
                        >

                    </div>

                    <!-- To -->
                    <div class="col-md-6 col-lg-2">

                        <label class="form-label fw-semibold">
                            To
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            class="form-control"
                            value="<?= donation_report_h($_GET['date_to'] ?? '') ?>"
                        >

                    </div>

                    <!-- Buttons -->
                    <div class="col-lg-1 d-flex align-items-end">

                        <button
                            type="submit"
                            class="btn btn-primary w-100"
                            title="Filter"
                        >
                            🔍
                        </button>

                    </div>

                </div>

                <div class="mt-3">

                    <a
                        href="<?= url('/admin/reports/donations') ?>"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        Reset Filters
                    </a>

                </div>

            </form>

        </div>

    </div>

    <!-- Printable area -->
    <div class="print-area">

        <!-- Print title -->
        <div class="report-print-title">

            <h3>GARKO ORPHANS FOUNDATION</h3>

            <h5>Donation Report</h5>

            <div style="font-size:11px;">
                Generated:
                <?= date('d M Y, h:i A') ?>
            </div>

        </div>

        <!-- Summary -->
        <div class="row g-3 mb-4 no-print">

            <div class="col-6 col-md-3">

                <div class="card summary-card shadow-sm">

                    <div class="card-body">

                        <div class="summary-label">
                            Total Records
                        </div>

                        <div class="summary-value">
                            <?= number_format($totalRecords) ?>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-md-3">

                <div class="card summary-card shadow-sm">

                    <div class="card-body">

                        <div class="summary-label text-success">
                            Confirmed
                        </div>

                        <div class="summary-value text-success">
                            <?= number_format($confirmedCount) ?>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-md-3">

                <div class="card summary-card shadow-sm">

                    <div class="card-body">

                        <div class="summary-label text-warning">
                            Pending
                        </div>

                        <div class="summary-value text-warning">
                            <?= number_format($pendingCount) ?>
                        </div>

                    </div>

                </div>

            </div>

            <div class="col-6 col-md-3">

                <div class="card summary-card shadow-sm">

                    <div class="card-body">

                        <div class="summary-label text-success">
                            Confirmed Amount
                        </div>

                        <div class="summary-value text-success">
                            ₦<?= number_format($confirmedAmount, 2) ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- Donation table -->
        <div class="card report-table-card shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h5 class="mb-1">
                            Donation Records
                        </h5>

                        <small class="text-muted">
                            <?= number_format($totalRecords) ?> record(s) found
                        </small>

                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-bordered table-striped report-table mb-0">

                        <thead>

                            <tr>

                                <th>#</th>
                                <th>Donation Ref</th>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Program</th>
                                <th>Status</th>
                                <th>Donation Date</th>
                                <th>Transaction Ref</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if (!empty($data)): ?>

                            <?php foreach ($data as $index => $row): ?>

                                <?php
                                $status = strtolower(
                                    trim((string)($row['status'] ?? ''))
                                );

                                $statusClass = 'status-pending';

                                if ($status === 'confirmed') {
                                    $statusClass = 'status-confirmed';
                                } elseif ($status === 'rejected') {
                                    $statusClass = 'status-rejected';
                                }

                                $donorName = trim(
                                    (string)($row['donor_name'] ?? '')
                                );

                                $organization = trim(
                                    (string)($row['donor_organization'] ?? '')
                                );

                                if ($donorName === '') {
                                    $donorName = 'Unknown Donor';
                                }

                                $donorDisplay = $donorName;

                                if ($organization !== '') {
                                    $donorDisplay .= ' (' . $organization . ')';
                                }

                                $donationRef =
                                    $row['donation_ref']
                                    ?? $row['reference']
                                    ?? ('DON-' . str_pad(
                                        (string)($row['id'] ?? ''),
                                        5,
                                        '0',
                                        STR_PAD_LEFT
                                    ));

                                $donationDate =
                                    $row['donation_date']
                                    ?? $row['created_at']
                                    ?? '';

                                if ($donationDate !== '') {
                                    $timestamp = strtotime($donationDate);

                                    if ($timestamp !== false) {
                                        $donationDate = date(
                                            'd M Y',
                                            $timestamp
                                        );
                                    }
                                }

                                $amount = (float)($row['amount'] ?? 0);

                                $currency =
                                    trim((string)($row['currency'] ?? 'NGN'));

                                if ($currency === '') {
                                    $currency = 'NGN';
                                }

                                $paymentMethod =
                                    trim((string)($row['payment_method'] ?? ''));

                                $programName =
                                    trim((string)($row['program_name'] ?? ''));

                                if ($programName === '') {
                                    $programName = 'General';
                                }

                                $transactionRef =
                                    trim((string)($row['transaction_ref'] ?? ''));
                                ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= donation_report_h($donationRef) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= donation_report_h($donorDisplay) ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= donation_report_h($currency) ?>
                                            <?= number_format($amount, 2) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= donation_report_h(
                                            ucwords(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $paymentMethod ?: 'N/A'
                                                )
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= donation_report_h($programName) ?>
                                    </td>

                                    <td>

                                        <span
                                            class="status-badge <?= $statusClass ?>"
                                        >
                                            <?= donation_report_h(
                                                ucfirst($status ?: 'Unknown')
                                            ) ?>
                                        </span>

                                    </td>

                                    <td>
                                        <?= donation_report_h(
                                            $donationDate ?: 'N/A'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= donation_report_h(
                                            $transactionRef ?: '—'
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>

                                <td
                                    colspan="9"
                                    class="text-center py-5 text-muted"
                                >
                                    No donation records found.

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>