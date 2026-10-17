<?php
$stats = $stats ?? [];

$beneficiaries = (int)($stats['beneficiaries'] ?? 0);
$staff         = (int)($stats['staff'] ?? 0);
$donors        = (int)($stats['donors'] ?? 0);
$donations     = (int)($stats['donations'] ?? 0);
$donationTotal = (float)($stats['donation_total'] ?? 0);
$idCards       = (int)($stats['id_cards'] ?? 0);

if (!function_exists('report_h')) {
    function report_h($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>

<style>
    .reports-page {
        padding-bottom: 30px;
    }

    .reports-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .reports-header h2 {
        margin-bottom: 5px;
        font-weight: 700;
    }

    .reports-header p {
        margin-bottom: 0;
    }

    .report-stat-card {
        border: 0;
        border-radius: 14px;
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
        overflow: hidden;
    }

    .report-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,.08);
    }

    .report-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }

    .report-stat-label {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 3px;
    }

    .report-stat-value {
        font-size: 25px;
        font-weight: 700;
        line-height: 1.2;
    }

    .report-card {
        border-radius: 14px;
        border: 1px solid #e9ecef;
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .report-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,.08);
    }

    .report-icon {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 27px;
        margin-bottom: 15px;
    }

    .report-card h5 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .report-card p {
        min-height: 48px;
        font-size: 14px;
    }

    .report-card .btn {
        margin-top: 8px;
        border-radius: 8px;
        font-weight: 600;
    }

    .reports-section-title {
        font-size: 18px;
        font-weight: 700;
        margin: 30px 0 15px;
    }

    .quick-report {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        background: #fff;
        padding: 15px;
        height: 100%;
    }

    .quick-report a {
        text-decoration: none;
        font-weight: 600;
    }

    @media (max-width: 767px) {
        .reports-header {
            align-items: flex-start;
        }

        .report-card p {
            min-height: auto;
        }
    }
</style>

<div class="container-fluid reports-page">

    <!-- Header -->
    <div class="reports-header">
        <div>
            <h2>System Reports</h2>
            <p class="text-muted">
                Monitor organizational data and generate detailed reports.
            </p>
        </div>

        <div>
            <a href="<?= url('/admin/dashboard') ?>" class="btn btn-outline-secondary">
                ← Dashboard
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3">

        <!-- Beneficiaries -->
        <div class="col-6 col-xl-2">
            <div class="card report-stat-card shadow-sm">
                <div class="card-body">
                    <div class="report-stat-icon bg-success-subtle text-success mb-3">
                        👥
                    </div>

                    <div class="report-stat-label">
                        Beneficiaries
                    </div>

                    <div class="report-stat-value">
                        <?= report_h(number_format($beneficiaries)) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff -->
        <div class="col-6 col-xl-2">
            <div class="card report-stat-card shadow-sm">
                <div class="card-body">
                    <div class="report-stat-icon bg-info-subtle text-info mb-3">
                        👔
                    </div>

                    <div class="report-stat-label">
                        Staff
                    </div>

                    <div class="report-stat-value">
                        <?= report_h(number_format($staff)) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donors -->
        <div class="col-6 col-xl-2">
            <div class="card report-stat-card shadow-sm">
                <div class="card-body">
                    <div class="report-stat-icon bg-primary-subtle text-primary mb-3">
                        🤝
                    </div>

                    <div class="report-stat-label">
                        Donors
                    </div>

                    <div class="report-stat-value">
                        <?= report_h(number_format($donors)) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donations -->
        <div class="col-6 col-xl-2">
            <div class="card report-stat-card shadow-sm">
                <div class="card-body">
                    <div class="report-stat-icon bg-warning-subtle text-warning mb-3">
                        💰
                    </div>

                    <div class="report-stat-label">
                        Donations
                    </div>

                    <div class="report-stat-value">
                        <?= report_h(number_format($donations)) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donation Amount -->
        <div class="col-6 col-xl-2">
            <div class="card report-stat-card shadow-sm">
                <div class="card-body">
                    <div class="report-stat-icon bg-success-subtle text-success mb-3">
                        ₦
                    </div>

                    <div class="report-stat-label">
                        Confirmed Amount
                    </div>

                    <div class="report-stat-value" style="font-size:20px;">
                        ₦<?= report_h(number_format($donationTotal, 2)) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ID Cards -->
        <div class="col-6 col-xl-2">
            <div class="card report-stat-card shadow-sm">
                <div class="card-body">
                    <div class="report-stat-icon bg-secondary-subtle text-secondary mb-3">
                        🪪
                    </div>

                    <div class="report-stat-label">
                        Active ID Cards
                    </div>

                    <div class="report-stat-value">
                        <?= report_h(number_format($idCards)) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Report Categories -->
    <div class="reports-section-title">
        Detailed Reports
    </div>

    <div class="row g-4">

        <!-- Beneficiaries -->
        <div class="col-md-6 col-xl-4">
            <div class="card report-card shadow-sm border-success">
                <div class="card-body text-center p-4">

                    <div class="report-icon bg-success-subtle text-success">
                        👥
                    </div>

                    <h5 class="text-success">
                        Beneficiaries
                    </h5>

                    <p class="text-muted">
                        View beneficiary records, registration status,
                        gender information, and detailed beneficiary data.
                    </p>

                    <a
                        href="<?= url('/admin/reports/beneficiaries') ?>"
                        class="btn btn-outline-success"
                    >
                        Generate Report
                    </a>

                </div>
            </div>
        </div>

        <!-- Donations -->
        <div class="col-md-6 col-xl-4">
            <div class="card report-card shadow-sm border-warning">
                <div class="card-body text-center p-4">

                    <div class="report-icon bg-warning-subtle text-warning">
                        💰
                    </div>

                    <h5 class="text-warning">
                        Donations
                    </h5>

                    <p class="text-muted">
                        Analyze donations, payment methods, statuses,
                        dates, donors, programs, and confirmed amounts.
                    </p>

                    <a
                        href="<?= url('/admin/reports/donations') ?>"
                        class="btn btn-outline-warning"
                    >
                        Generate Report
                    </a>

                </div>
            </div>
        </div>

        <!-- Donors -->
        <div class="col-md-6 col-xl-4">
            <div class="card report-card shadow-sm border-primary">
                <div class="card-body text-center p-4">

                    <div class="report-icon bg-primary-subtle text-primary">
                        🤝
                    </div>

                    <h5 class="text-primary">
                        Donors
                    </h5>

                    <p class="text-muted">
                        Search donor records by name, organization,
                        contact information, type, and GOF ID.
                    </p>

                    <a
                        href="<?= url('/admin/reports/donors') ?>"
                        class="btn btn-outline-primary"
                    >
                        Generate Report
                    </a>

                </div>
            </div>
        </div>

        <!-- Staff -->
        <div class="col-md-6 col-xl-4">
            <div class="card report-card shadow-sm border-info">
                <div class="card-body text-center p-4">

                    <div class="report-icon bg-info-subtle text-info">
                        👔
                    </div>

                    <h5 class="text-info">
                        Staff
                    </h5>

                    <p class="text-muted">
                        Review staff records, employment status,
                        gender, contact information, and GOF IDs.
                    </p>

                    <a
                        href="<?= url('/admin/reports/staff') ?>"
                        class="btn btn-outline-info"
                    >
                        Generate Report
                    </a>

                </div>
            </div>
        </div>

        <!-- ID Cards -->
        <div class="col-md-6 col-xl-4">
            <div class="card report-card shadow-sm border-secondary">
                <div class="card-body text-center p-4">

                    <div class="report-icon bg-secondary-subtle text-secondary">
                        🪪
                    </div>

                    <h5 class="text-secondary">
                        ID Cards
                    </h5>

                    <p class="text-muted">
                        Review active and revoked ID cards,
                        card types, card numbers, and GOF identification.
                    </p>

                    <a
                        href="<?= url('/admin/reports/idcards') ?>"
                        class="btn btn-outline-secondary"
                    >
                        Generate Report
                    </a>

                </div>
            </div>
        </div>

        <!-- Support -->
        <div class="col-md-6 col-xl-4">
            <div class="card report-card shadow-sm border-dark">
                <div class="card-body text-center p-4">

                    <div class="report-icon bg-dark-subtle text-dark">
                        🎧
                    </div>

                    <h5 class="text-dark">
                        Support
                    </h5>

                    <p class="text-muted">
                        Support tickets, resolution times, and active
                        inquiries will be available in a future module.
                    </p>

                    <button
                        type="button"
                        class="btn btn-outline-dark"
                        disabled
                    >
                        Coming Soon
                    </button>

                </div>
            </div>
        </div>

    </div>

    <!-- Quick Access -->
    <div class="reports-section-title">
        Quick Access
    </div>

    <div class="row g-3">

        <div class="col-md-4">
            <div class="quick-report">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Beneficiary Report</strong>
                    </span>

                    <a href="<?= url('/admin/reports/beneficiaries') ?>">
                        Open →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="quick-report">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <strong>Donation Report</strong>
                    </span>

                    <a href="<?= url('/admin/reports/donations') ?>">
                        Open →
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="quick-report">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <strong>ID Card Report</strong>
                    </span>

                    <a href="<?= url('/admin/reports/idcards') ?>">
                        Open →
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>