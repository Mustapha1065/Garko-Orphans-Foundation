<?php
$data = $data ?? [];

if (!function_exists('report_beneficiary_h')) {
    function report_beneficiary_h($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$totalRecords = count($data);

$activeCount = 0;
$inactiveCount = 0;
$graduatedCount = 0;

foreach ($data as $row) {
    $status = strtolower(trim((string)($row['status'] ?? '')));

    if ($status === 'active') {
        $activeCount++;
    } elseif ($status === 'inactive') {
        $inactiveCount++;
    } elseif ($status === 'graduated') {
        $graduatedCount++;
    }
}
?>

<style>
    .beneficiary-report-page {
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

    .report-filter-card {
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
        font-size: 25px;
        font-weight: 700;
    }

    .table-card {
        border: 0;
        border-radius: 14px;
        overflow: hidden;
    }

    .report-table th {
        white-space: nowrap;
        font-size: 13px;
        background: #f8f9fa;
    }

    .report-table td {
        vertical-align: middle;
        font-size: 14px;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-inactive {
        background: #e2e3e5;
        color: #41464b;
    }

    .status-graduated {
        background: #cfe2ff;
        color: #084298;
    }

    .report-print-title {
        display: none;
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 12mm;
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
            padding: 6px !important;
            font-size: 11px !important;
        }

        .table-card {
            box-shadow: none !important;
            border: 0 !important;
        }

        .status-badge {
            border: 0 !important;
            background: transparent !important;
            color: #000 !important;
            padding: 0 !important;
        }
    }

    @media (max-width: 767px) {
        .report-table {
            min-width: 850px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<div class="container-fluid beneficiary-report-page">

    <!-- Header -->
    <div class="report-header no-print">
        <div>
            <h2>Beneficiary Report</h2>
            <p class="text-muted">
                Search, filter and print beneficiary records.
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
    <div class="card report-filter-card shadow-sm mb-4 no-print">
        <div class="card-body">

            <form method="GET">

                <div class="row g-3">

                    <!-- Search -->
                    <div class="col-lg-4">
                        <label class="form-label fw-semibold">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="<?= report_beneficiary_h($_GET['search'] ?? '') ?>"
                            placeholder="Name, GOF ID or phone"
                        >
                    </div>

                    <!-- Status -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label fw-semibold">
                            Status
                        </label>

                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>

                            <option
                                value="active"
                                <?= (($_GET['status'] ?? '') === 'active') ? 'selected' : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                <?= (($_GET['status'] ?? '') === 'inactive') ? 'selected' : '' ?>
                            >
                                Inactive
                            </option>

                            <option
                                value="graduated"
                                <?= (($_GET['status'] ?? '') === 'graduated') ? 'selected' : '' ?>
                            >
                                Graduated
                            </option>
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="col-md-4 col-lg-2">
                        <label class="form-label fw-semibold">
                            Gender
                        </label>

                        <select name="gender" class="form-select">
                            <option value="">All</option>

                            <option
                                value="male"
                                <?= (($_GET['gender'] ?? '') === 'male') ? 'selected' : '' ?>
                            >
                                Male
                            </option>

                            <option
                                value="female"
                                <?= (($_GET['gender'] ?? '') === 'female') ? 'selected' : '' ?>
                            >
                                Female
                            </option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="col-md-4 col-lg-4 d-flex align-items-end gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary flex-grow-1"
                        >
                            Filter
                        </button>

                        <a
                            href="<?= url('/admin/reports/beneficiaries') ?>"
                            class="btn btn-outline-secondary"
                        >
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>
    </div>

    <!-- Printable Area -->
    <div class="print-area">

        <!-- Print title -->
        <div class="report-print-title">
            <h3>GARKO ORPHANS FOUNDATION</h3>
            <h5>Beneficiary Report</h5>

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
                            Active
                        </div>

                        <div class="summary-value text-success">
                            <?= number_format($activeCount) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card summary-card shadow-sm">
                    <div class="card-body">
                        <div class="summary-label text-secondary">
                            Inactive
                        </div>

                        <div class="summary-value text-secondary">
                            <?= number_format($inactiveCount) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card summary-card shadow-sm">
                    <div class="card-body">
                        <div class="summary-label text-primary">
                            Graduated
                        </div>

                        <div class="summary-value text-primary">
                            <?= number_format($graduatedCount) ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Table -->
        <div class="card table-card shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h5 class="mb-1">
                            Beneficiary Records
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
                                <th>GOF ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>State</th>
                                <th>Status</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (!empty($data)): ?>

                            <?php foreach ($data as $index => $row): ?>

                                <?php
                                $status = strtolower(
                                    trim((string)($row['status'] ?? ''))
                                );

                                $statusClass = 'status-inactive';

                                if ($status === 'active') {
                                    $statusClass = 'status-active';
                                } elseif ($status === 'graduated') {
                                    $statusClass = 'status-graduated';
                                }

                                $fullName = trim(
                                    (string)($row['full_name'] ?? '')
                                );

                                /*
                                 * Fallback for older records/schema.
                                 */
                                if ($fullName === '') {
                                    $fullName = trim(
                                        (string)($row['first_name'] ?? '') .
                                        ' ' .
                                        (string)($row['last_name'] ?? '')
                                    );
                                }

                                $gofId = $row['gof_id'] ?? '';

                                if ($gofId === '') {
                                    $gofId = 'B-' .
                                        str_pad(
                                            (string)($row['id'] ?? ''),
                                            5,
                                            '0',
                                            STR_PAD_LEFT
                                        );
                                }

                                $registrationDate = $row['created_at'] ?? '';

                                if ($registrationDate !== '') {
                                    $timestamp = strtotime($registrationDate);

                                    if ($timestamp !== false) {
                                        $registrationDate = date(
                                            'd M Y',
                                            $timestamp
                                        );
                                    }
                                }
                                ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= report_beneficiary_h($gofId) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= report_beneficiary_h($fullName ?: 'N/A') ?>
                                    </td>

                                    <td>
                                        <?= report_beneficiary_h(
                                            ucfirst(
                                                (string)($row['gender'] ?? 'N/A')
                                            )
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= report_beneficiary_h(
                                            $row['state'] ?? 'N/A'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="status-badge <?= $statusClass ?>">
                                            <?= report_beneficiary_h(
                                                ucfirst($status ?: 'Unknown')
                                            ) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= report_beneficiary_h(
                                            $registrationDate ?: 'N/A'
                                        ) ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td
                                    colspan="7"
                                    class="text-center py-5 text-muted"
                                >
                                    No beneficiary records found.
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