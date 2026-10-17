<?php
/**
 * GOF3-light
 * Public Donation Success Page
 */

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

$donation = $donation ?? [];

$donationRef = $donation['donation_ref'] ?? '';
$amount = (float)($donation['amount'] ?? 0);
$currency = $donation['currency'] ?? 'NGN';
$paymentMethod = $donation['payment_method'] ?? 'bank';
$purpose = $donation['purpose'] ?? 'General Support';

$paymentLabel = match ($paymentMethod) {
    'card' => 'Card Payment',
    'bank' => 'Bank Transfer',
    default => ucfirst($paymentMethod)
};

$currencySymbol = $currency === 'NGN' ? '₦' : $currency . ' ';
?>

<style>
    .donation-success-page {
        min-height: 70vh;
        display: flex;
        align-items: center;
        padding: 70px 0;
        background:
            radial-gradient(circle at top right, rgba(25,135,84,.08), transparent 35%),
            radial-gradient(circle at bottom left, rgba(13,110,253,.08), transparent 35%),
            #f8f9fa;
    }

    .success-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 24px;
        box-shadow: 0 15px 50px rgba(0,0,0,.08);
        overflow: hidden;
    }

    .success-header {
        padding: 40px 30px 30px;
        text-align: center;
        background: linear-gradient(
            135deg,
            rgba(25,135,84,.08),
            rgba(13,110,253,.06)
        );
    }

    .success-icon {
        width: 86px;
        height: 86px;
        margin: 0 auto 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(25,135,84,.12);
        color: #198754;
        font-size: 42px;
    }

    .success-title {
        font-weight: 800;
        margin-bottom: 10px;
    }

    .success-subtitle {
        color: #6c757d;
        max-width: 620px;
        margin: 0 auto;
    }

    .reference-box {
        margin: 25px auto 0;
        max-width: 430px;
        background: #fff;
        border: 1px dashed #198754;
        border-radius: 14px;
        padding: 16px 20px;
    }

    .reference-label {
        display: block;
        color: #6c757d;
        font-size: .82rem;
        margin-bottom: 5px;
    }

    .reference-number {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: .5px;
        word-break: break-word;
    }

    .success-body {
        padding: 30px;
    }

    .status-box {
        border-radius: 14px;
        padding: 18px;
        background: #fff3cd;
        border: 1px solid #ffecb5;
        color: #664d03;
        margin-bottom: 25px;
    }

    .status-box i {
        font-size: 1.35rem;
    }

    .details-card {
        border: 1px solid #e9ecef;
        border-radius: 16px;
        overflow: hidden;
    }

    .details-title {
        padding: 17px 20px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-weight: 700;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 14px 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .detail-row:last-child {
        border-bottom: 0;
    }

    .detail-label {
        color: #6c757d;
    }

    .detail-value {
        font-weight: 600;
        text-align: right;
    }

    .amount-value {
        color: #198754;
        font-size: 1.25rem;
        font-weight: 800;
    }

    .next-step {
        height: 100%;
        border: 1px solid #e9ecef;
        border-radius: 16px;
        padding: 22px;
    }

    .step-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
        background: rgba(13,110,253,.1);
        color: #0d6efd;
        font-size: 1.3rem;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 30px;
    }

    @media (max-width: 575.98px) {
        .success-header,
        .success-body {
            padding: 25px 18px;
        }

        .detail-row {
            flex-direction: column;
            gap: 4px;
        }

        .detail-value {
            text-align: left;
        }
    }

    @media print {
        .page-hero,
        .action-buttons,
        nav,
        footer {
            display: none !important;
        }

        .donation-success-page {
            padding: 0;
            background: #fff;
        }

        .success-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>

<!-- Page Hero -->
<section class="page-hero bg-gof-primary">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center py-5">

                <h1 class="page-title text-white">
                    Donation Submitted
                </h1>

                <p class="page-subtitle text-white-50 mb-0">
                    Thank you for supporting Garko Orphans Foundation.
                </p>

            </div>
        </div>
    </div>
</section>

<!-- Success -->
<section class="donation-success-page">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-9 col-lg-10">

                <div class="success-card">

                    <!-- Header -->
                    <div class="success-header">

                        <div class="success-icon">
                            <i class="bi bi-check-lg"></i>
                        </div>

                        <h2 class="success-title">
                            Thank You for Your Donation
                        </h2>

                        <p class="success-subtitle">
                            Your donation request has been successfully recorded.
                            It will remain pending until the payment is verified
                            by the foundation.
                        </p>

                        <?php if ($donationRef !== ''): ?>

                            <div class="reference-box">

                                <span class="reference-label">
                                    Donation Reference
                                </span>

                                <div class="reference-number">
                                    <?= h($donationRef) ?>
                                </div>

                            </div>

                        <?php endif; ?>

                    </div>

                    <!-- Body -->
                    <div class="success-body">

                        <!-- Status -->
                        <div class="status-box">

                            <div class="d-flex gap-3 align-items-start">

                                <i class="bi bi-hourglass-split"></i>

                                <div>

                                    <strong>
                                        Payment verification pending
                                    </strong>

                                    <div class="small mt-1">
                                        Your donation has been recorded, but it
                                        has not yet been marked as confirmed.
                                        Please keep your donation reference for
                                        future communication.
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- Details -->
                        <div class="details-card mb-4">

                            <div class="details-title">
                                Donation Details
                            </div>

                            <?php if ($donationRef !== ''): ?>

                                <div class="detail-row">

                                    <span class="detail-label">
                                        Reference
                                    </span>

                                    <span class="detail-value">
                                        <?= h($donationRef) ?>
                                    </span>

                                </div>

                            <?php endif; ?>

                            <div class="detail-row">

                                <span class="detail-label">
                                    Amount
                                </span>

                                <span class="detail-value amount-value">
                                    <?= h($currencySymbol) ?>
                                    <?= number_format($amount, 2) ?>
                                </span>

                            </div>

                            <div class="detail-row">

                                <span class="detail-label">
                                    Purpose
                                </span>

                                <span class="detail-value">
                                    <?= h($purpose) ?>
                                </span>

                            </div>

                            <div class="detail-row">

                                <span class="detail-label">
                                    Payment Method
                                </span>

                                <span class="detail-value">
                                    <?= h($paymentLabel) ?>
                                </span>

                            </div>

                            <div class="detail-row">

                                <span class="detail-label">
                                    Status
                                </span>

                                <span class="detail-value">
                                    <span class="badge text-bg-warning">
                                        Pending
                                    </span>
                                </span>

                            </div>

                        </div>

                        <!-- Next steps -->
                        <div class="row g-4">

                            <div class="col-md-4">

                                <div class="next-step">

                                    <div class="step-icon">
                                        <i class="bi bi-receipt"></i>
                                    </div>

                                    <h5>
                                        Keep Your Reference
                                    </h5>

                                    <p class="text-muted small mb-0">
                                        Keep your donation reference number.
                                        It helps identify your donation.
                                    </p>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="next-step">

                                    <div class="step-icon">
                                        <i class="bi bi-shield-check"></i>
                                    </div>

                                    <h5>
                                        Verification
                                    </h5>

                                    <p class="text-muted small mb-0">
                                        The foundation will verify the payment
                                        before marking the donation as confirmed.
                                    </p>

                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="next-step">

                                    <div class="step-icon">
                                        <i class="bi bi-file-earmark-check"></i>
                                    </div>

                                    <h5>
                                        Official Receipt
                                    </h5>

                                    <p class="text-muted small mb-0">
                                        Once confirmed, an official donation
                                        receipt can be generated.
                                    </p>

                                </div>

                            </div>

                        </div>

                        <!-- Actions -->
                        <div class="action-buttons">

                            <a
                                href="<?= h(url('/')) ?>"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-house me-2"></i>
                                Back to Home
                            </a>

                            <a
                                href="<?= h(url('/donate')) ?>"
                                class="btn btn-gof-primary"
                            >
                                <i class="bi bi-heart me-2"></i>
                                Make Another Donation
                            </a>

                            <button
                                type="button"
                                class="btn btn-outline-primary"
                                onclick="window.print()"
                            >
                                <i class="bi bi-printer me-2"></i>
                                Print
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>