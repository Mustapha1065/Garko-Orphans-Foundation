<?php
$pageTitle = 'Dashboard';
$breadcrumb = '<li class="breadcrumb-item active">Dashboard</li>';
?>

<style>
    /* Dashboard chart sizing */
    .dashboard-chart-card {
        height: 360px;
    }

    .dashboard-chart-wrapper {
        position: relative;
        height: 270px;
        width: 100%;
    }

    .dashboard-status-card {
        height: 360px;
    }

    .dashboard-status-wrapper {
        position: relative;
        height: 270px;
        width: 100%;
        max-width: 300px;
        margin: 0 auto;
    }

    @media (max-width: 991.98px) {
        .dashboard-chart-card,
        .dashboard-status-card {
            height: 340px;
        }

        .dashboard-chart-wrapper,
        .dashboard-status-wrapper {
            height: 250px;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-chart-card,
        .dashboard-status-card {
            height: 320px;
        }

        .dashboard-chart-wrapper,
        .dashboard-status-wrapper {
            height: 235px;
        }
    }
</style>


<div class="container-fluid py-4">

    <!-- Top Row -->
    <div class="row g-4 mb-4">

        <div class="col-12 col-md-6 col-lg-3">
            <div class="gof-card stat-card p-4 text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-people fs-1"></i>
                </div>

                <h5 class="text-muted fw-normal mb-1">
                    Total Beneficiaries
                </h5>

                <h3 class="fw-bold mb-0">
                    <?= number_format($stats['total_beneficiaries'] ?? 0) ?>
                </h3>
            </div>
        </div>


        <div class="col-12 col-md-6 col-lg-3">
            <div class="gof-card stat-card p-4 text-center">
                <div class="text-success mb-2">
                    <i class="bi bi-check-circle fs-1"></i>
                </div>

                <h5 class="text-muted fw-normal mb-1">
                    Active Beneficiaries
                </h5>

                <h3 class="fw-bold mb-0">
                    <?= number_format($stats['active_beneficiaries'] ?? 0) ?>
                </h3>
            </div>
        </div>


        <div class="col-12 col-md-6 col-lg-3">

            <a href="<?= url('/admin/beneficiaries?status=pending') ?>"
               class="text-decoration-none text-dark">

                <div class="gof-card stat-card p-4 text-center hover-shadow">

                    <div class="text-warning mb-2">
                        <i class="bi bi-hourglass fs-1"></i>
                    </div>

                    <h5 class="text-muted fw-normal mb-1">
                        Pending Registrations
                    </h5>

                    <h3 class="fw-bold mb-0">
                        <?= number_format($stats['pending_beneficiaries'] ?? 0) ?>
                    </h3>

                </div>

            </a>

        </div>


        <div class="col-12 col-md-6 col-lg-3">

            <div class="gof-card stat-card p-4 text-center">

                <div class="text-primary mb-2">
                    <i class="bi bi-person-badge fs-1"></i>
                </div>

                <h5 class="text-muted fw-normal mb-1">
                    Total Staff
                </h5>

                <h3 class="fw-bold mb-0">
                    <?= number_format($stats['total_staff'] ?? 0) ?>
                </h3>

            </div>

        </div>

    </div>


    <!-- Row 2 -->
    <div class="row g-4 mb-4">

        <div class="col-12 col-md-6 col-lg-3">

            <div class="gof-card stat-card p-4 text-center">

                <div class="text-danger mb-2">
                    <i class="bi bi-heart fs-1"></i>
                </div>

                <h5 class="text-muted fw-normal mb-1">
                    Total Donors
                </h5>

                <h3 class="fw-bold mb-0">
                    <?= number_format($stats['total_donors'] ?? 0) ?>
                </h3>

            </div>

        </div>


        <div class="col-12 col-md-6 col-lg-3">

            <div class="gof-card stat-card p-4 text-center">

                <div class="text-primary mb-2">
                    <i class="bi bi-cash fs-1"></i>
                </div>

                <h5 class="text-muted fw-normal mb-1">
                    Donations Recorded
                </h5>

                <h3 class="fw-bold mb-0">
                    <?= number_format($stats['total_donations'] ?? 0) ?>
                </h3>

            </div>

        </div>


        <div class="col-12 col-md-6 col-lg-3">

            <div class="gof-card stat-card p-4 text-center">

                <div class="text-success mb-2">
                    <i class="bi bi-currency-exchange fs-1"></i>
                </div>

                <h5 class="text-muted fw-normal mb-1">
                    Donation Amount
                </h5>

                <h3 class="fw-bold mb-0">
                    ₦<?= number_format($stats['total_donation_amount'] ?? 0) ?>
                </h3>

            </div>

        </div>


        <div class="col-12 col-md-6 col-lg-3">

            <div class="gof-card stat-card p-4 text-center">

                <div class="text-info mb-2">
                    <i class="bi bi-kanban fs-1"></i>
                </div>

                <h5 class="text-muted fw-normal mb-1">
                    Active Programs
                </h5>

                <h3 class="fw-bold mb-0">
                    <?= number_format($stats['active_programs'] ?? 0) ?>
                </h3>

            </div>

        </div>

    </div>


    <!-- Row 3 -->
    <div class="row g-4 mb-4">

        <div class="col-6 col-md-3">

            <div class="gof-card p-3 text-center">

                <div class="text-secondary mb-1">
                    <i class="bi bi-calendar-event fs-3"></i>
                </div>

                <div class="small text-muted">
                    Upcoming Events
                </div>

                <div class="fw-bold fs-5">
                    <?= number_format($stats['upcoming_events'] ?? 0) ?>
                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="gof-card p-3 text-center">

                <div class="text-secondary mb-1">
                    <i class="bi bi-credit-card-2-front fs-3"></i>
                </div>

                <div class="small text-muted">
                    Active ID Cards
                </div>

                <div class="fw-bold fs-5">
                    <?= number_format($stats['active_id_cards'] ?? 0) ?>
                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="gof-card p-3 text-center">

                <div class="text-secondary mb-1">
                    <i class="bi bi-envelope fs-3"></i>
                </div>

                <div class="small text-muted">
                    Unread Messages
                </div>

                <div class="fw-bold fs-5">
                    <?= number_format($stats['pending_contacts'] ?? 0) ?>
                </div>

            </div>

        </div>


        <div class="col-6 col-md-3">

            <div class="gof-card p-3 text-center">

                <div class="text-secondary mb-1">
                    <i class="bi bi-hand-thumbs-up fs-3"></i>
                </div>

                <div class="small text-muted">
                    Pending Volunteers
                </div>

                <div class="fw-bold fs-5">
                    <?= number_format($stats['pending_volunteers'] ?? 0) ?>
                </div>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- COMPACT CHARTS -->
    <!-- ========================================================= -->

    <div class="row g-4 mb-4">

        <!-- Donations Chart -->
        <div class="col-12 col-lg-8">

            <div class="gof-card p-4 dashboard-chart-card">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h5 class="fw-bold mb-1">
                            Donations
                        </h5>

                        <div class="small text-muted">
                            Last 6 Months
                        </div>
                    </div>

                    <i class="bi bi-bar-chart-line text-primary fs-4"></i>

                </div>


                <div class="dashboard-chart-wrapper">

                    <canvas id="donationsChart"></canvas>

                </div>

            </div>

        </div>


        <!-- Beneficiary Status Chart -->
        <div class="col-12 col-lg-4">

            <div class="gof-card p-4 dashboard-status-card">

                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>
                        <h5 class="fw-bold mb-1">
                            Beneficiary Status
                        </h5>

                        <div class="small text-muted">
                            Current distribution
                        </div>
                    </div>

                    <i class="bi bi-pie-chart text-success fs-4"></i>

                </div>


                <div class="dashboard-status-wrapper">

                    <canvas id="statusChart"></canvas>

                </div>

            </div>

        </div>

    </div>


    <!-- Recent Registrations -->
    <div class="row g-4 mb-4">

        <div class="col-lg-8">

            <div class="gof-card p-4 h-100">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <h5 class="fw-bold mb-0">
                        Recent Registrations
                    </h5>

                    <a href="<?= url('/admin/beneficiaries') ?>"
                       class="btn btn-sm btn-outline-primary">
                        View All
                    </a>

                </div>


                <div class="table-responsive">

                    <table class="table table-hover gof-table align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>GOF ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>

                        </thead>


                        <tbody>

                        <?php foreach (($recentBeneficiaries ?? []) as $b): ?>

                            <?php

                            $beneficiaryName =
                                trim(
                                    (string) (
                                        $b['full_name']
                                        ?? $b['name']
                                        ?? 'Unknown'
                                    )
                                );

                            $beneficiaryStatus =
                                (string) (
                                    $b['status']
                                    ?? 'unknown'
                                );

                            $beneficiaryId =
                                (int) (
                                    $b['id']
                                    ?? 0
                                );

                            $createdAt =
                                $b['created_at']
                                ?? null;

                            ?>

                            <tr>

                                <td>

                                    <span class="badge bg-light text-dark border">

                                        <?= htmlspecialchars(
                                            $b['gof_id'] ?? 'N/A',
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $beneficiaryName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </td>


                                <td>

                                    <?php if ($beneficiaryStatus === 'active'): ?>

                                        <span class="badge bg-success">
                                            Active
                                        </span>

                                    <?php elseif ($beneficiaryStatus === 'pending'): ?>

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">

                                            <?= htmlspecialchars(
                                                ucfirst($beneficiaryStatus),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <td>

                                    <?= $createdAt
                                        ? date(
                                            'M j, Y',
                                            strtotime($createdAt)
                                        )
                                        : 'N/A'
                                    ?>

                                </td>


                                <td>

                                    <?php if ($beneficiaryId > 0): ?>

                                        <a href="<?= url(
                                            '/admin/beneficiaries/' .
                                            $beneficiaryId
                                        ) ?>"
                                           class="btn btn-sm btn-light">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            —
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                        <?php if (empty($recentBeneficiaries)): ?>

                            <tr>

                                <td colspan="5"
                                    class="text-center py-3 text-muted">

                                    No recent registrations.

                                </td>

                            </tr>

                        <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <!-- Right Widgets -->
        <div class="col-lg-4">


            <!-- Pending Approvals -->
            <div class="gof-card p-4 mb-4">

                <h6 class="fw-bold text-uppercase text-muted mb-3">
                    Pending Approvals
                </h6>


                <div class="d-flex flex-column gap-3">

                    <?php foreach (($pendingApprovals ?? []) as $p): ?>

                        <?php

                        $pendingName =
                            trim(
                                (string) (
                                    $p['full_name']
                                    ?? $p['name']
                                    ?? 'Unknown'
                                )
                            );

                        $pendingId =
                            (int) (
                                $p['id']
                                ?? 0
                            );

                        $pendingCreated =
                            $p['created_at']
                            ?? null;

                        ?>


                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">

                            <div>

                                <div class="fw-medium">

                                    <?= htmlspecialchars(
                                        $pendingName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                                <div class="small text-muted">

                                    <?= $pendingCreated
                                        ? date(
                                            'M j, Y',
                                            strtotime($pendingCreated)
                                        )
                                        : 'N/A'
                                    ?>

                                </div>

                            </div>


                            <?php if ($pendingId > 0): ?>

                                <a href="<?= url(
                                    '/admin/beneficiaries/' .
                                    $pendingId .
                                    '/approve'
                                ) ?>"
                                   class="btn btn-sm btn-success">

                                    Approve

                                </a>

                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>


                    <?php if (empty($pendingApprovals)): ?>

                        <div class="text-muted small text-center py-2">

                            No pending approvals.

                        </div>

                    <?php endif; ?>

                </div>

            </div>


            <!-- Recent Donations -->
            <div class="gof-card p-4 mb-4">

                <h6 class="fw-bold text-uppercase text-muted mb-3">
                    Recent Donations
                </h6>


                <div class="d-flex flex-column gap-3">

                    <?php foreach (($recentDonations ?? []) as $d): ?>

                        <?php

                        $donorName =
                            trim(
                                (string) (
                                    $d['donor_name']
                                    ?? $d['name']
                                    ?? 'Unknown Donor'
                                )
                            );

                        $donationAmount =
                            (float) (
                                $d['amount']
                                ?? 0
                            );

                        $donationCreated =
                            $d['created_at']
                            ?? null;

                        ?>


                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">

                            <div>

                                <div class="fw-medium">

                                    <?= htmlspecialchars(
                                        $donorName,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>

                                </div>

                                <div class="small text-muted">

                                    <?= $donationCreated
                                        ? date(
                                            'M j, Y',
                                            strtotime($donationCreated)
                                        )
                                        : 'N/A'
                                    ?>

                                </div>

                            </div>


                            <span class="badge bg-primary rounded-pill">

                                ₦<?= number_format(
                                    $donationAmount
                                ) ?>

                            </span>

                        </div>

                    <?php endforeach; ?>


                    <?php if (empty($recentDonations)): ?>

                        <div class="text-muted small text-center py-2">

                            No recent donations.

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- Quick Actions -->
    <div class="gof-card p-4">

        <h5 class="fw-bold mb-4">
            Quick Actions
        </h5>


        <div class="d-flex flex-wrap gap-3">

            <a href="<?= url('/admin/beneficiaries/create') ?>"
               class="btn btn-outline-primary">

                <i class="bi bi-person-plus me-2"></i>

                Register Beneficiary

            </a>


            <a href="<?= url('/admin/donors/create') ?>"
               class="btn btn-outline-danger">

                <i class="bi bi-heart me-2"></i>

                Add Donor

            </a>


            <a href="<?= url('/admin/donations/create') ?>"
               class="btn btn-outline-success">

                <i class="bi bi-cash me-2"></i>

                Record Donation

            </a>


            <a href="<?= url('/admin/idcards') ?>"
               class="btn btn-outline-secondary">

                <i class="bi bi-credit-card-2-front me-2"></i>

                ID Cards

            </a>


            <a href="<?= url('/admin/programs/create') ?>"
               class="btn btn-outline-info">

                <i class="bi bi-kanban me-2"></i>

                Add Program

            </a>


            <a href="<?= url('/admin/reports') ?>"
               class="btn btn-outline-dark">

                <i class="bi bi-file-earmark-bar-graph me-2"></i>

                View Reports

            </a>

        </div>

    </div>

</div>


<?php

/* ============================================================
   PREPARE CHART DATA
   ============================================================ */

$donLabels = [];
$donData = [];

foreach (($donationsByMonth ?? []) as $row) {

    if (!empty($row['month'])) {

        $donLabels[] = date(
            'M Y',
            strtotime($row['month'] . '-01')
        );

    } else {

        $donLabels[] = 'N/A';

    }

    $donData[] = (float) (
        $row['total'] ?? 0
    );
}


$statLabels = [];
$statData = [];

foreach (($beneficiaryByStatus ?? []) as $row) {

    $statLabels[] = ucfirst(
        (string) (
            $row['status'] ?? 'Unknown'
        )
    );

    $statData[] = (int) (
        $row['c'] ?? 0
    );
}


ob_start();

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* ========================================================
       DONATIONS CHART
       ======================================================== */

    const donationsCanvas =
        document.getElementById('donationsChart');

    if (donationsCanvas) {

        const donCtx =
            donationsCanvas.getContext('2d');

        new Chart(donCtx, {

            type: 'bar',

            data: {

                labels:
                    <?= json_encode($donLabels) ?>,

                datasets: [{

                    label: 'Donations (₦)',

                    data:
                        <?= json_encode($donData) ?>,

                    backgroundColor: '#0d6efd',

                    borderRadius: 5,

                    barThickness: 30,

                    maxBarThickness: 35

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        callbacks: {

                            label: function(context) {

                                return ' ₦' +
                                    Number(
                                        context.raw || 0
                                    ).toLocaleString();

                            }

                        }

                    }

                },

                scales: {

                    x: {

                        grid: {
                            display: false
                        },

                        ticks: {
                            maxRotation: 0
                        }

                    },

                    y: {

                        beginAtZero: true,

                        ticks: {

                            callback: function(value) {

                                return '₦' +
                                    Number(value).toLocaleString();

                            }

                        }

                    }

                }

            }

        });

    }


    /* ========================================================
       BENEFICIARY STATUS CHART
       ======================================================== */

    const statusCanvas =
        document.getElementById('statusChart');

    if (statusCanvas) {

        const statCtx =
            statusCanvas.getContext('2d');

        new Chart(statCtx, {

            type: 'doughnut',

            data: {

                labels:
                    <?= json_encode($statLabels) ?>,

                datasets: [{

                    data:
                        <?= json_encode($statData) ?>,

                    backgroundColor: [

                        '#198754',
                        '#ffc107',
                        '#6c757d',
                        '#dc3545'

                    ],

                    borderWidth: 2,

                    borderColor: '#ffffff'

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '65%',

                plugins: {

                    legend: {

                        position: 'bottom',

                        labels: {

                            usePointStyle: true,

                            padding: 14,

                            boxWidth: 10

                        }

                    }

                }

            }

        });

    }

});

</script>

<?php

$extraScripts = ob_get_clean();

?>