<?php
/**
 * GOF3-light
 * Public Volunteer Application Status
 */

if (!function_exists('volunteer_status_h')) {
    function volunteer_status_h($value): string
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

$application = $application ?? null;
$error = $error ?? '';
$searched = $searched ?? false;
$reference = $reference ?? '';
$email = $email ?? '';

$status = strtolower(
    (string)($application['status'] ?? '')
);

$statusLabels = [
    'pending'   => 'Pending Review',
    'reviewed'  => 'Under Review',
    'approved'  => 'Approved',
    'rejected'  => 'Not Approved',
    'contacted' => 'Contacted'
];

$statusLabel =
    $statusLabels[$status]
    ?? ucfirst($status ?: 'Unknown');

$statusClass = match ($status) {
    'approved' => 'status-approved',
    'rejected' => 'status-rejected',
    'reviewed' => 'status-reviewed',
    'contacted' => 'status-contacted',
    default => 'status-pending'
};

$fullName = trim(
    ($application['first_name'] ?? '')
    . ' '
    . ($application['last_name'] ?? '')
);

$submittedDate = '';

if (!empty($application['created_at'])) {
    $timestamp = strtotime(
        (string)$application['created_at']
    );

    if ($timestamp !== false) {
        $submittedDate = date(
            'F j, Y',
            $timestamp
        );
    }
}

$reviewedDate = '';

if (!empty($application['reviewed_at'])) {
    $timestamp = strtotime(
        (string)$application['reviewed_at']
    );

    if ($timestamp !== false) {
        $reviewedDate = date(
            'F j, Y',
            $timestamp
        );
    }
}
?>

<style>
    .volunteer-status-page {
        padding-bottom: 70px;
    }

    .volunteer-status-hero {
        background: linear-gradient(
            135deg,
            #b79500 0%,
            #c9a900 50%,
            #9d8500 100%
        );
        padding: 48px 20px;
    }

    .volunteer-status-hero h1 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .volunteer-status-hero p {
        color: rgba(255,255,255,.88);
        margin-bottom: 0;
    }

    .status-search-card {
        max-width: 760px;
        margin: -35px auto 45px;
        position: relative;
        z-index: 2;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 12px 35px rgba(0,0,0,.10);
    }

    .status-search-card .card-body {
        padding: 30px;
    }

    .status-search-title {
        font-weight: 700;
        color: #333;
        margin-bottom: 6px;
    }

    .status-search-description {
        color: #777;
        margin-bottom: 25px;
    }

    .status-result-card {
        max-width: 900px;
        margin: 0 auto;
        border: 0;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0,0,0,.08);
        overflow: hidden;
    }

    .status-result-header {
        padding: 25px 30px;
        border-bottom: 1px solid #eee;
        background: #fafafa;
    }

    .status-result-header h3 {
        margin: 0;
        font-weight: 700;
    }

    .status-result-body {
        padding: 30px;
    }

    .application-reference {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 8px;
        background: #f5f1d8;
        color: #806d00;
        font-weight: 700;
        letter-spacing: .4px;
    }

    .application-status {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        border-radius: 50px;
        font-size: .9rem;
        font-weight: 700;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-reviewed {
        background: #cfe2ff;
        color: #084298;
    }

    .status-approved {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-rejected {
        background: #f8d7da;
        color: #842029;
    }

    .status-contacted {
        background: #cff4fc;
        color: #055160;
    }

    .info-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 18px;
        height: 100%;
    }

    .info-label {
        display: block;
        color: #777;
        font-size: .82rem;
        margin-bottom: 5px;
    }

    .info-value {
        color: #333;
        font-weight: 600;
        word-break: break-word;
    }

    .decision-box {
        margin-top: 25px;
        padding: 22px;
        border-radius: 12px;
        background: #f8f9fa;
        border-left: 4px solid #c9a900;
    }

    .decision-box h5 {
        font-weight: 700;
        margin-bottom: 10px;
    }

    .decision-box p {
        margin: 0;
        color: #555;
        line-height: 1.7;
        white-space: pre-line;
    }

    .pending-message {
        margin-top: 25px;
        padding: 20px;
        background: #fff8df;
        border-radius: 12px;
        color: #725d00;
    }

    .error-box {
        max-width: 760px;
        margin: -20px auto 30px;
        position: relative;
        z-index: 3;
    }

    .status-timeline {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-top: 35px;
        position: relative;
    }

    .status-timeline::before {
        content: "";
        position: absolute;
        top: 17px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: #ddd;
        z-index: 0;
    }

    .timeline-item {
        position: relative;
        z-index: 1;
        text-align: center;
        flex: 1;
    }

    .timeline-dot {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        margin: 0 auto 9px;
        background: #ddd;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        border: 4px solid #fff;
        box-shadow: 0 0 0 1px #ddd;
    }

    .timeline-item.active .timeline-dot {
        background: #c9a900;
        box-shadow: 0 0 0 1px #c9a900;
    }

    .timeline-item.completed .timeline-dot {
        background: #198754;
        box-shadow: 0 0 0 1px #198754;
    }

    .timeline-item.rejected-step .timeline-dot {
        background: #dc3545;
        box-shadow: 0 0 0 1px #dc3545;
    }

    .timeline-label {
        font-size: .78rem;
        color: #777;
    }

    .help-card {
        max-width: 900px;
        margin: 35px auto 0;
        padding: 25px;
        border-radius: 16px;
        background: #f8f9fa;
        text-align: center;
    }

    @media (max-width: 767px) {
        .volunteer-status-hero {
            padding: 38px 15px;
        }

        .status-search-card {
            margin-left: 15px;
            margin-right: 15px;
        }

        .status-search-card .card-body,
        .status-result-body {
            padding: 22px;
        }

        .status-timeline {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .status-timeline::before {
            top: 15px;
            bottom: 15px;
            left: 17px;
            right: auto;
            width: 2px;
            height: auto;
        }

        .timeline-item {
            display: flex;
            align-items: center;
            text-align: left;
            gap: 12px;
        }

        .timeline-dot {
            margin: 0;
            flex-shrink: 0;
        }
    }

    @media print {
        .volunteer-status-hero,
        .status-search-card,
        .help-card,
        .btn {
            display: none !important;
        }

        .status-result-card {
            box-shadow: none;
        }
    }
</style>

<div class="volunteer-status-page">

    <!-- HERO -->
    <section class="volunteer-status-hero">
        <div class="container text-center">
            <h1>Volunteer Application Status</h1>
            <p>
                Check the current status of your volunteer application.
            </p>
        </div>
    </section>


    <!-- SEARCH -->
    <div class="container">

        <div class="card status-search-card">

            <div class="card-body">

                <h3 class="status-search-title">
                    Check Your Application
                </h3>

                <p class="status-search-description">
                    Enter the application reference and email address
                    you used when applying.
                </p>

                <?php if ($error !== ''): ?>

                    <div class="alert alert-danger">
                        <strong>Unable to find application.</strong><br>
                        <?= volunteer_status_h($error) ?>
                    </div>

                <?php endif; ?>


                <form
                    method="GET"
                    action="<?= url('/volunteer/status') ?>"
                >

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label
                                for="reference"
                                class="form-label"
                            >
                                Application Reference
                            </label>

                            <input
                                type="text"
                                id="reference"
                                name="reference"
                                class="form-control"
                                value="<?= volunteer_status_h($reference) ?>"
                                placeholder="VOL-2026-00001"
                                required
                                autocomplete="off"
                            >

                            <small class="text-muted">
                                Example: VOL-2026-00001
                            </small>

                        </div>


                        <div class="col-md-6">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email Address
                            </label>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                value="<?= volunteer_status_h($email) ?>"
                                placeholder="Your application email"
                                required
                                autocomplete="email"
                            >

                        </div>


                        <div class="col-12">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="fas fa-search me-1"></i>
                                Check Application Status
                            </button>

                            <a
                                href="<?= url('/volunteer') ?>"
                                class="btn btn-outline-secondary ms-2"
                            >
                                Apply to Volunteer
                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <?php if ($application): ?>

            <!-- RESULT -->
            <div class="card status-result-card">

                <div class="status-result-header">

                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                        <div>
                            <h3>
                                Application Details
                            </h3>

                            <div class="mt-2">
                                <span class="application-reference">
                                    <?= volunteer_status_h(
                                        $application['application_ref']
                                    ) ?>
                                </span>
                            </div>
                        </div>


                        <span
                            class="application-status <?= volunteer_status_h($statusClass) ?>"
                        >
                            <i class="fas fa-circle"></i>

                            <?= volunteer_status_h($statusLabel) ?>
                        </span>

                    </div>

                </div>


                <div class="status-result-body">

                    <!-- BASIC INFORMATION -->
                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="info-box">

                                <span class="info-label">
                                    Applicant
                                </span>

                                <div class="info-value">
                                    <?= volunteer_status_h(
                                        $fullName
                                    ) ?>
                                </div>

                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="info-box">

                                <span class="info-label">
                                    Email
                                </span>

                                <div class="info-value">
                                    <?= volunteer_status_h(
                                        $application['email']
                                    ) ?>
                                </div>

                            </div>

                        </div>


                        <?php if (!empty($application['interest'])): ?>

                            <div class="col-md-6">

                                <div class="info-box">

                                    <span class="info-label">
                                        Volunteer Interest
                                    </span>

                                    <div class="info-value">
                                        <?= volunteer_status_h(
                                            $application['interest']
                                        ) ?>
                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if (!empty($application['availability'])): ?>

                            <div class="col-md-6">

                                <div class="info-box">

                                    <span class="info-label">
                                        Availability
                                    </span>

                                    <div class="info-value">
                                        <?= volunteer_status_h(
                                            $application['availability']
                                        ) ?>
                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if ($submittedDate !== ''): ?>

                            <div class="col-md-6">

                                <div class="info-box">

                                    <span class="info-label">
                                        Application Submitted
                                    </span>

                                    <div class="info-value">
                                        <?= volunteer_status_h(
                                            $submittedDate
                                        ) ?>
                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if ($reviewedDate !== ''): ?>

                            <div class="col-md-6">

                                <div class="info-box">

                                    <span class="info-label">
                                        Last Reviewed
                                    </span>

                                    <div class="info-value">
                                        <?= volunteer_status_h(
                                            $reviewedDate
                                        ) ?>
                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- STATUS TIMELINE -->
                    <div class="status-timeline">

                        <?php
                        $timelineStatuses = [
                            'pending',
                            'reviewed',
                            'approved'
                        ];

                        $statusOrder = [
                            'pending' => 1,
                            'reviewed' => 2,
                            'approved' => 3,
                            'contacted' => 4
                        ];

                        $currentOrder =
                            $statusOrder[$status] ?? 1;
                        ?>

                        <?php foreach (
                            $timelineStatuses
                            as $index => $timelineStatus
                        ): ?>

                            <?php
                            $timelineOrder =
                                $statusOrder[
                                    $timelineStatus
                                ];

                            $timelineClass = '';

                            if (
                                $status === 'rejected'
                                && $timelineStatus === 'reviewed'
                            ) {
                                $timelineClass =
                                    'completed';
                            } elseif (
                                $currentOrder >
                                $timelineOrder
                            ) {
                                $timelineClass =
                                    'completed';
                            } elseif (
                                $currentOrder ===
                                $timelineOrder
                            ) {
                                $timelineClass =
                                    'active';
                            }
                            ?>

                            <div
                                class="timeline-item <?= volunteer_status_h($timelineClass) ?>"
                            >

                                <div class="timeline-dot">
                                    <?= $index + 1 ?>
                                </div>

                                <div class="timeline-label">

                                    <?php if (
                                        $timelineStatus === 'pending'
                                    ): ?>
                                        Submitted
                                    <?php elseif (
                                        $timelineStatus === 'reviewed'
                                    ): ?>
                                        Reviewed
                                    <?php else: ?>
                                        Decision
                                    <?php endif; ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <?php if ($status === 'pending'): ?>

                        <div class="pending-message">

                            <strong>
                                <i class="fas fa-clock me-1"></i>
                                Your application is awaiting review.
                            </strong>

                            <p class="mb-0 mt-2">
                                Our team will review your application.
                                Please keep your application reference
                                for future status checks.
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if (
                        !empty(
                            $application['decision_message']
                        )
                    ): ?>

                        <div class="decision-box">

                            <h5>
                                <i class="fas fa-comment-alt me-2"></i>
                                Message from Garko Orphans Foundation
                            </h5>

                            <p>
                                <?= volunteer_status_h(
                                    $application['decision_message']
                                ) ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <?php if ($status === 'approved'): ?>

                        <div class="alert alert-success mt-4 mb-0">

                            <strong>
                                <i class="fas fa-check-circle me-1"></i>
                                Your application has been approved.
                            </strong>

                            <div class="mt-2">
                                Please keep this reference available
                                when communicating with the foundation.
                            </div>

                        </div>

                    <?php elseif ($status === 'rejected'): ?>

                        <div class="alert alert-secondary mt-4 mb-0">

                            <strong>
                                <i class="fas fa-info-circle me-1"></i>
                                Application decision
                            </strong>

                            <div class="mt-2">
                                Thank you for your interest in supporting
                                Garko Orphans Foundation.
                            </div>

                        </div>

                    <?php endif; ?>

                </div>

            </div>


        <?php elseif ($searched && $error === ''): ?>

            <div class="alert alert-warning text-center">
                No application was found.
            </div>

        <?php endif; ?>


        <!-- HELP -->
        <div class="help-card">

            <h5 class="fw-bold">
                Need help?
            </h5>

            <p class="text-muted mb-3">
                If you have lost your application reference or
                need assistance with your volunteer application,
                please contact Garko Orphans Foundation.
            </p>

            <a
                href="<?= url('/contact') ?>"
                class="btn btn-outline-primary"
            >
                <i class="fas fa-envelope me-1"></i>
                Contact Us
            </a>

        </div>

    </div>

</div>