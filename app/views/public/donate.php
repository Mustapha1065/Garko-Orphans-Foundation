<?php
/**
 * GOF3-light
 * Public Donation Page
 *
 * Connected to:
 *   POST /donate
 *   GET  /donate/success
 *
 * Expected variables:
 *   $programs  - optional list of active programs
 */

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

/* ---------------------------------------------------------
 * Previous form data
 * --------------------------------------------------------- */
$old = $_SESSION['old_donation'] ?? [];
unset($_SESSION['old_donation']);

$selectedAmount  = $old['amount'] ?? '';
$selectedPurpose = $old['purpose'] ?? 'general';
$selectedPayment = $old['payment_method'] ?? 'bank';
$selectedProgram = $old['program_id'] ?? '';

$programs = is_array($programs ?? null) ? $programs : [];

$purposeLabels = [
    'general'    => 'General Support',
    'education'  => 'Education',
    'healthcare' => 'Healthcare',
    'shelter'    => 'Shelter & Care',
    'skills'     => 'Skills Development',
    'other'      => 'Other',
];
?>

<style>
/* =========================================================
   DONATION PAGE
   ========================================================= */

.donate-hero {
    position: relative;
    overflow: hidden;
}

.donate-hero::before,
.donate-hero::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
}

.donate-hero::before {
    width: 360px;
    height: 360px;
    background: rgba(255,255,255,.05);
    left: -150px;
    bottom: -210px;
}

.donate-hero::after {
    width: 280px;
    height: 280px;
    background: rgba(255,255,255,.07);
    right: -80px;
    top: -80px;
}

.donation-section {
    position: relative;
}

.impact-card,
.donation-form-card,
.give-card,
.trust-card,
.payment-card {
    background: #fff;
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 18px;
    box-shadow: 0 8px 30px rgba(0,0,0,.06);
}

.impact-card {
    padding: 30px 22px;
    height: 100%;
    transition: transform .2s ease, box-shadow .2s ease;
}

.impact-card:hover,
.give-card:hover,
.trust-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 35px rgba(0,0,0,.09);
}

.impact-icon,
.give-icon {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
}

.impact-title {
    font-weight: 700;
    margin-bottom: 10px;
}

.impact-description,
.give-description {
    color: #6c757d;
    margin-bottom: 0;
}

.donation-form-card {
    padding: 34px;
}

.form-section-title {
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.amount-btn {
    min-height: 52px;
    font-weight: 600;
    border-radius: 10px;
}

.amount-btn.active {
    color: #fff;
    background: var(--bs-primary, #0d6efd);
    border-color: var(--bs-primary, #0d6efd);
}

.payment-option {
    border: 1px solid #dee2e6;
    border-radius: 12px;
    padding: 16px;
    height: 100%;
    cursor: pointer;
    transition: .2s ease;
}

.payment-option:hover,
.payment-option.selected {
    border-color: var(--bs-primary, #0d6efd);
    background: rgba(13,110,253,.05);
}

.payment-option input {
    margin-top: 4px;
}

.payment-option-title {
    font-weight: 600;
}

.donation-summary {
    background: #f8f9fa;
    border-radius: 14px;
    padding: 20px;
    border: 1px solid #e9ecef;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    padding: 9px 0;
}

.summary-row + .summary-row {
    border-top: 1px solid #e9ecef;
}

.summary-total {
    font-size: 1.35rem;
    font-weight: 800;
}

.payment-info-card {
    border-radius: 18px;
    border: 1px solid #dbeafe;
    background: #f8fbff;
    padding: 26px;
}

.give-card {
    padding: 28px 20px;
    height: 100%;
}

.trust-card {
    padding: 25px 18px;
    height: 100%;
}

.donation-note {
    font-size: .875rem;
    color: #6c757d;
}

.required-star {
    color: #dc3545;
}

.form-control,
.form-select {
    min-height: 46px;
}

textarea.form-control {
    min-height: 110px;
}

.donation-form-card .btn-lg {
    min-height: 54px;
}

@media (max-width: 767.98px) {
    .donation-form-card {
        padding: 20px;
    }

    .payment-option {
        padding: 14px;
    }

    .summary-row {
        align-items: flex-start;
    }
}
</style>


<!-- =========================================================
     HERO
     ========================================================= -->
<section class="page-hero bg-gof-primary donate-hero">
    <div class="container position-relative" style="z-index:2;">
        <div class="row">
            <div class="col-12 text-center py-5">

                <h1 class="page-title text-white">
                    Support Our Mission
                </h1>

                <p class="page-subtitle text-white-50">
                    Your generosity can help create better opportunities
                    for vulnerable children.
                </p>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-center mb-0">

                        <li class="breadcrumb-item">
                            <a href="<?= h(url('/')) ?>" class="text-white-50">
                                Home
                            </a>
                        </li>

                        <li class="breadcrumb-item active text-white"
                            aria-current="page">
                            Donate
                        </li>

                    </ol>
                </nav>

            </div>
        </div>
    </div>
</section>


<!-- =========================================================
     IMPACT
     ========================================================= -->
<section class="py-5">
    <div class="container">

        <div class="text-center mb-5">
            <h2 class="section-title">
                Your Impact
            </h2>

            <p class="text-muted">
                Every contribution can help support our programs and beneficiaries.
            </p>
        </div>

        <div class="row g-4">

            <div class="col-lg-4">
                <div class="impact-card text-center">

                    <div class="impact-icon bg-primary bg-opacity-10 mx-auto">
                        <i class="bi bi-mortarboard text-primary fs-2"></i>
                    </div>

                    <h4 class="impact-title">
                        ₦5,000
                    </h4>

                    <p class="impact-description">
                        Can contribute toward educational materials
                        and school support.
                    </p>

                </div>
            </div>


            <div class="col-lg-4">
                <div class="impact-card text-center">

                    <div class="impact-icon bg-success bg-opacity-10 mx-auto">
                        <i class="bi bi-heart-pulse text-success fs-2"></i>
                    </div>

                    <h4 class="impact-title">
                        ₦10,000
                    </h4>

                    <p class="impact-description">
                        Can contribute toward healthcare and
                        wellbeing initiatives.
                    </p>

                </div>
            </div>


            <div class="col-lg-4">
                <div class="impact-card text-center">

                    <div class="impact-icon bg-warning bg-opacity-10 mx-auto">
                        <i class="bi bi-house-heart text-warning fs-2"></i>
                    </div>

                    <h4 class="impact-title">
                        ₦25,000
                    </h4>

                    <p class="impact-description">
                        Can contribute toward food, care and
                        other essential support.
                    </p>

                </div>
            </div>

        </div>
    </div>
</section>


<!-- =========================================================
     DONATION FORM
     ========================================================= -->
<section class="py-5 bg-light donation-section" id="donation-form">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Make a Donation
            </h2>

            <p class="text-muted">
                Choose an amount and provide your details below.
            </p>

        </div>


        <div class="row justify-content-center">

            <div class="col-xl-8 col-lg-9">

                <div class="donation-form-card">

                    <form
                        id="donationForm"
                        method="POST"
                        action="<?= h(url('/donate')) ?>"
                        novalidate
                    >

                        <!-- CSRF -->
                        <?php if (class_exists('\GOF\Helpers\Csrf')): ?>

                            <input
                                type="hidden"
                                name="_token"
                                value="<?= h(\GOF\Helpers\Csrf::token()) ?>"
                            >

                        <?php elseif (isset($_SESSION['csrf_token'])): ?>

                            <input
                                type="hidden"
                                name="_token"
                                value="<?= h($_SESSION['csrf_token']) ?>"
                            >

                        <?php endif; ?>


                        <!-- =================================================
                             AMOUNT
                             ================================================= -->
                        <div class="mb-4">

                            <div class="form-section-title">
                                <i class="bi bi-cash-coin text-primary"></i>
                                Donation Amount
                            </div>


                            <?php
                            $quickAmounts = [
                                5000  => '₦5,000',
                                10000 => '₦10,000',
                                25000 => '₦25,000',
                                50000 => '₦50,000'
                            ];
                            ?>

                            <div class="row g-2 mb-3">

                                <?php foreach ($quickAmounts as $amount => $label): ?>

                                    <div class="col-6 col-md-3">

                                        <button
                                            type="button"
                                            class="btn btn-outline-primary amount-btn w-100 <?= ((string)$selectedAmount === (string)$amount) ? 'active' : '' ?>"
                                            data-amount="<?= $amount ?>"
                                        >
                                            <?= h($label) ?>
                                        </button>

                                    </div>

                                <?php endforeach; ?>

                            </div>


                            <label for="amount" class="form-label">
                                Custom Amount (₦)
                                <span class="required-star">*</span>
                            </label>

                            <input
                                type="number"
                                class="form-control form-control-lg"
                                id="amount"
                                name="amount"
                                value="<?= h($selectedAmount) ?>"
                                min="1000"
                                step="100"
                                placeholder="Enter donation amount"
                                required
                            >

                            <div class="form-text">
                                Minimum donation amount: ₦1,000.
                            </div>

                        </div>


                        <!-- =================================================
                             DONOR INFORMATION
                             ================================================= -->
                        <div class="mb-4">

                            <div class="form-section-title">
                                <i class="bi bi-person text-primary"></i>
                                Donor Information
                            </div>


                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label for="name" class="form-label">
                                        Full Name
                                        <span class="required-star">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="name"
                                        name="name"
                                        value="<?= h($old['name'] ?? '') ?>"
                                        maxlength="150"
                                        placeholder="Enter your full name"
                                        required
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label for="email" class="form-label">
                                        Email Address
                                        <span class="required-star">*</span>
                                    </label>

                                    <input
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        name="email"
                                        value="<?= h($old['email'] ?? '') ?>"
                                        maxlength="150"
                                        placeholder="you@example.com"
                                        required
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label for="phone" class="form-label">
                                        Phone Number
                                    </label>

                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="phone"
                                        name="phone"
                                        value="<?= h($old['phone'] ?? '') ?>"
                                        maxlength="30"
                                        placeholder="08012345678"
                                    >

                                </div>


                                <div class="col-md-6">

                                    <label for="purpose" class="form-label">
                                        Donation Purpose
                                        <span class="required-star">*</span>
                                    </label>

                                    <select
                                        class="form-select"
                                        id="purpose"
                                        name="purpose"
                                        required
                                    >

                                        <?php foreach ($purposeLabels as $value => $label): ?>

                                            <option
                                                value="<?= h($value) ?>"
                                                <?= $selectedPurpose === $value ? 'selected' : '' ?>
                                            >
                                                <?= h($label) ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                             PROGRAM
                             ================================================= -->
                        <?php if (!empty($programs)): ?>

                            <div class="mb-4">

                                <div class="form-section-title">
                                    <i class="bi bi-diagram-3 text-primary"></i>
                                    Support a Program
                                </div>

                                <label for="program_id" class="form-label">
                                    Program
                                    <span class="text-muted">(Optional)</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="program_id"
                                    name="program_id"
                                >

                                    <option value="">
                                        General Foundation Support
                                    </option>

                                    <?php foreach ($programs as $program): ?>

                                        <?php
                                        $programId = $program['id'] ?? '';
                                        $programName = $program['name'] ?? 'Program';
                                        ?>

                                        <option
                                            value="<?= h($programId) ?>"
                                            <?= ((string)$selectedProgram === (string)$programId) ? 'selected' : '' ?>
                                        >
                                            <?= h($programName) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                                <div class="form-text">
                                    Choose a specific active program if you would like
                                    your donation associated with it.
                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- =================================================
                             PAYMENT METHOD
                             ================================================= -->
                        <div class="mb-4">

                            <div class="form-section-title">
                                <i class="bi bi-wallet2 text-primary"></i>
                                Payment Method
                            </div>


                            <div class="row g-3">

                                <div class="col-md-6">

                                    <label class="payment-option d-block
                                        <?= $selectedPayment === 'bank' ? 'selected' : '' ?>">

                                        <div class="d-flex gap-3">

                                            <input
                                                class="form-check-input payment-radio"
                                                type="radio"
                                                name="payment_method"
                                                value="bank"
                                                <?= $selectedPayment === 'bank' ? 'checked' : '' ?>
                                            >

                                            <div>

                                                <div class="payment-option-title">
                                                    <i class="bi bi-bank me-1"></i>
                                                    Bank Transfer
                                                </div>

                                                <div class="small text-muted mt-1">
                                                    Official transfer instructions will be
                                                    provided after your request is submitted.
                                                </div>

                                            </div>

                                        </div>

                                    </label>

                                </div>


                                <div class="col-md-6">

                                    <label class="payment-option d-block
                                        <?= $selectedPayment === 'card' ? 'selected' : '' ?>">

                                        <div class="d-flex gap-3">

                                            <input
                                                class="form-check-input payment-radio"
                                                type="radio"
                                                name="payment_method"
                                                value="card"
                                                <?= $selectedPayment === 'card' ? 'checked' : '' ?>
                                            >

                                            <div>

                                                <div class="payment-option-title">
                                                    <i class="bi bi-credit-card me-1"></i>
                                                    Card Payment
                                                </div>

                                                <div class="small text-muted mt-1">
                                                    Card processing can be enabled when
                                                    an official payment gateway is configured.
                                                </div>

                                            </div>

                                        </div>

                                    </label>

                                </div>

                            </div>

                        </div>


                        <!-- =================================================
                             MESSAGE
                             ================================================= -->
                        <div class="mb-4">

                            <label for="message" class="form-label">
                                Message
                            </label>

                            <textarea
                                class="form-control"
                                id="message"
                                name="message"
                                rows="4"
                                maxlength="1000"
                                placeholder="Add an optional message with your donation..."
                            ><?= h($old['message'] ?? '') ?></textarea>

                            <div class="form-text">
                                <span id="messageCount">0</span>/1000 characters
                            </div>

                        </div>


                        <!-- =================================================
                             ANONYMOUS
                             ================================================= -->
                        <div class="form-check mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="anonymous"
                                name="anonymous"
                                value="1"
                                <?= !empty($old['anonymous']) ? 'checked' : '' ?>
                            >

                            <label class="form-check-label" for="anonymous">
                                Make this donation anonymous
                            </label>

                            <div class="form-text">
                                Your name will not be displayed publicly.
                            </div>

                        </div>


                        <!-- =================================================
                             SUMMARY
                             ================================================= -->
                        <div class="donation-summary mb-4">

                            <h5 class="fw-bold mb-3">
                                Donation Summary
                            </h5>

                            <div class="summary-row">

                                <span class="text-muted">
                                    Amount
                                </span>

                                <strong id="summaryAmount">
                                    ₦0
                                </strong>

                            </div>


                            <div class="summary-row">

                                <span class="text-muted">
                                    Purpose
                                </span>

                                <strong id="summaryPurpose">
                                    General Support
                                </strong>

                            </div>


                            <?php if (!empty($programs)): ?>

                                <div class="summary-row">

                                    <span class="text-muted">
                                        Program
                                    </span>

                                    <strong id="summaryProgram">
                                        General Foundation Support
                                    </strong>

                                </div>

                            <?php endif; ?>


                            <div class="summary-row">

                                <span class="text-muted">
                                    Payment
                                </span>

                                <strong id="summaryPayment">
                                    Bank Transfer
                                </strong>

                            </div>


                            <div class="summary-row pt-3 mt-2">

                                <span class="fw-semibold">
                                    Total Donation
                                </span>

                                <span
                                    class="summary-total text-primary"
                                    id="summaryTotal"
                                >
                                    ₦0
                                </span>

                            </div>

                        </div>


                        <!-- =================================================
                             AGREEMENT
                             ================================================= -->
                        <div class="form-check mb-4">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="donationAgreement"
                                name="agreement"
                                value="1"
                                required
                            >

                            <label
                                class="form-check-label"
                                for="donationAgreement"
                            >
                                I confirm that the information provided is correct.
                                <span class="required-star">*</span>
                            </label>

                        </div>


                        <!-- =================================================
                             SUBMIT
                             ================================================= -->
                        <button
                            type="submit"
                            id="donateButton"
                            class="btn btn-gof-primary btn-lg w-100"
                        >
                            <i class="bi bi-heart-fill me-2"></i>
                            Proceed With Donation
                        </button>


                        <p class="donation-note text-center mt-3 mb-0">
                            Your donation will be recorded as pending until it is verified.
                        </p>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     PAYMENT INFORMATION
     ========================================================= -->
<section class="py-5" id="paymentInformation">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8">

                <div class="payment-info-card">

                    <div class="d-flex align-items-start gap-3">

                        <div class="text-primary fs-3">
                            <i class="bi bi-shield-check"></i>
                        </div>

                        <div>

                            <h4 class="mb-2">
                                Secure Donation Process
                            </h4>

                            <p class="text-muted mb-2">
                                Submit your donation information through the official
                                GOF system.
                            </p>

                            <p class="text-muted mb-0">
                                Your donation is recorded as pending and can then be
                                verified by an authorized foundation administrator.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     OTHER WAYS TO GIVE
     ========================================================= -->
<section class="py-5 bg-light">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Other Ways to Give
            </h2>

            <p class="text-muted">
                There are several ways you can support our mission.
            </p>

        </div>


        <div class="row g-4">

            <div class="col-md-4">

                <div class="give-card text-center">

                    <div class="give-icon bg-primary bg-opacity-10 mx-auto">
                        <i class="bi bi-calendar-check text-primary fs-2"></i>
                    </div>

                    <h4>
                        Regular Giving
                    </h4>

                    <p class="give-description">
                        Provide continued support for our programs through
                        regular donations.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="give-card text-center">

                    <div class="give-icon bg-success bg-opacity-10 mx-auto">
                        <i class="bi bi-gift text-success fs-2"></i>
                    </div>

                    <h4>
                        In-Kind Donations
                    </h4>

                    <p class="give-description">
                        Support the foundation with useful goods,
                        materials or services.
                    </p>

                </div>

            </div>


            <div class="col-md-4">

                <div class="give-card text-center">

                    <div class="give-icon bg-warning bg-opacity-10 mx-auto">
                        <i class="bi bi-building text-warning fs-2"></i>
                    </div>

                    <h4>
                        Corporate Partnership
                    </h4>

                    <p class="give-description">
                        Organizations can work with GOF to support
                        sustainable community programs.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     TRUST
     ========================================================= -->
<section class="py-5">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Why Support GOF?
            </h2>

        </div>


        <div class="row g-4">

            <div class="col-lg-3 col-md-6">

                <div class="trust-card text-center">

                    <i class="bi bi-shield-check text-primary fs-2 mb-3"></i>

                    <h5>
                        Responsible
                    </h5>

                    <p class="text-muted small mb-0">
                        Donations are recorded and managed through
                        the foundation's system.
                    </p>

                </div>

            </div>


            <div class="col-lg-3 col-md-6">

                <div class="trust-card text-center">

                    <i class="bi bi-bar-chart text-success fs-2 mb-3"></i>

                    <h5>
                        Impact-Focused
                    </h5>

                    <p class="text-muted small mb-0">
                        Contributions support programs intended to
                        benefit vulnerable children.
                    </p>

                </div>

            </div>


            <div class="col-lg-3 col-md-6">

                <div class="trust-card text-center">

                    <i class="bi bi-people text-warning fs-2 mb-3"></i>

                    <h5>
                        Community Driven
                    </h5>

                    <p class="text-muted small mb-0">
                        Programs are designed around the needs
                        of the communities served.
                    </p>

                </div>

            </div>


            <div class="col-lg-3 col-md-6">

                <div class="trust-card text-center">

                    <i class="bi bi-heart text-danger fs-2 mb-3"></i>

                    <h5>
                        Purpose Driven
                    </h5>

                    <p class="text-muted small mb-0">
                        Your support contributes toward
                        the foundation's mission.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const amountInput = document.getElementById('amount');
    const amountButtons = document.querySelectorAll('.amount-btn');

    const purposeInput = document.getElementById('purpose');
    const paymentInputs = document.querySelectorAll('.payment-radio');

    const programInput = document.getElementById('program_id');

    const summaryAmount = document.getElementById('summaryAmount');
    const summaryTotal = document.getElementById('summaryTotal');
    const summaryPurpose = document.getElementById('summaryPurpose');
    const summaryPayment = document.getElementById('summaryPayment');
    const summaryProgram = document.getElementById('summaryProgram');

    const messageInput = document.getElementById('message');
    const messageCount = document.getElementById('messageCount');

    const form = document.getElementById('donationForm');
    const donateButton = document.getElementById('donateButton');


    function formatMoney(value) {

        const number = Number(value || 0);

        return '₦' + number.toLocaleString('en-NG', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }


    function updateSummary() {

        const amount = Number(amountInput.value || 0);

        summaryAmount.textContent = formatMoney(amount);
        summaryTotal.textContent = formatMoney(amount);


        const purposeText =
            purposeInput.options[purposeInput.selectedIndex]?.text ||
            'General Support';

        summaryPurpose.textContent = purposeText;


        const selectedPayment =
            document.querySelector(
                'input[name="payment_method"]:checked'
            );

        if (selectedPayment) {

            summaryPayment.textContent =
                selectedPayment.value === 'bank'
                    ? 'Bank Transfer'
                    : 'Card Payment';
        }


        if (programInput && summaryProgram) {

            const selectedOption =
                programInput.options[programInput.selectedIndex];

            summaryProgram.textContent =
                selectedOption?.text ||
                'General Foundation Support';
        }
    }


    function updateAmountButtons() {

        const currentAmount =
            String(amountInput.value || '');

        amountButtons.forEach(function (button) {

            const buttonAmount =
                String(button.dataset.amount || '');

            button.classList.toggle(
                'active',
                buttonAmount === currentAmount
            );

        });
    }


    amountButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            amountInput.value =
                this.dataset.amount;

            updateAmountButtons();
            updateSummary();

            amountInput.focus();
        });

    });


    amountInput.addEventListener('input', function () {

        updateAmountButtons();
        updateSummary();

    });


    purposeInput.addEventListener(
        'change',
        updateSummary
    );


    if (programInput) {

        programInput.addEventListener(
            'change',
            updateSummary
        );
    }


    paymentInputs.forEach(function (input) {

        input.addEventListener('change', function () {

            document
                .querySelectorAll('.payment-option')
                .forEach(function (option) {
                    option.classList.remove('selected');
                });


            const parent =
                this.closest('.payment-option');

            if (parent) {
                parent.classList.add('selected');
            }

            updateSummary();
        });

    });


    if (messageInput && messageCount) {

        function updateMessageCount() {

            messageCount.textContent =
                messageInput.value.length;
        }

        messageInput.addEventListener(
            'input',
            updateMessageCount
        );

        updateMessageCount();
    }


    form.addEventListener('submit', function (event) {

        const amount =
            Number(amountInput.value || 0);

        if (!amount || amount < 1000) {

            event.preventDefault();

            amountInput.focus();

            alert(
                'Please enter a donation amount of at least ₦1,000.'
            );

            return;
        }


        const name =
            document.getElementById('name')
                .value
                .trim();

        const email =
            document.getElementById('email')
                .value
                .trim();

        const agreement =
            document.getElementById('donationAgreement')
                .checked;


        if (!name) {

            event.preventDefault();

            document.getElementById('name').focus();

            alert(
                'Please enter your full name.'
            );

            return;
        }


        if (!email) {

            event.preventDefault();

            document.getElementById('email').focus();

            alert(
                'Please enter your email address.'
            );

            return;
        }


        if (!agreement) {

            event.preventDefault();

            alert(
                'Please confirm that the information provided is correct.'
            );

            return;
        }


        donateButton.disabled = true;

        donateButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>' +
            'Submitting Donation...';

    });


    updateAmountButtons();
    updateSummary();

});
</script>