<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$selectedDonorId = $_GET['donor_id'] ?? '';
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Record New Donation</h1>
            <p class="text-muted mb-0">
                Record a donation received by Garko Orphans Foundation.
            </p>
        </div>

        <a href="<?= url('/admin/donations') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
            Back to Donations
        </a>
    </div>

    <!-- Information -->
    <div class="alert alert-info d-flex align-items-start gap-2">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
            <strong>Donation confirmation:</strong>
            All newly recorded donations are initially marked as
            <strong>Pending</strong>. A finance officer or authorized administrator
            must confirm the donation before an official receipt can be issued.
        </div>
    </div>

    <form method="POST" action="<?= url('/admin/donations') ?>" id="donationForm">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h($_SESSION['csrf_token'] ?? '') ?>"
        >

        <div class="row">

            <!-- Main Donation Information -->
            <div class="col-lg-8">

                <div class="card shadow-sm mb-4">

                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold">
                            <i class="bi bi-cash-coin"></i>
                            Donation Information
                        </h6>
                    </div>

                    <div class="card-body">

                        <!-- Donor -->
                        <div class="mb-4">

                            <label for="donor_id" class="form-label fw-semibold">
                                Donor <span class="text-danger">*</span>
                            </label>

                            <select
                                name="donor_id"
                                id="donor_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    -- Select Donor --
                                </option>

                                <?php foreach ($donors as $donor): ?>

                                    <option
                                        value="<?= (int)$donor['id'] ?>"
                                        <?= ((string)$selectedDonorId === (string)$donor['id']) ? 'selected' : '' ?>
                                    >
                                        <?= h($donor['name']) ?>

                                        <?php if (!empty($donor['gof_id'])): ?>
                                            (<?= h($donor['gof_id']) ?>)
                                        <?php endif; ?>

                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <?php if (empty($donors)): ?>

                                <div class="alert alert-warning mt-2 mb-0">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    No active donors are currently available.
                                    <a href="<?= url('/admin/donors/create') ?>">
                                        Register a donor first.
                                    </a>
                                </div>

                            <?php endif; ?>

                        </div>

                        <!-- Amount / Currency -->
                        <div class="row mb-4">

                            <div class="col-md-7">

                                <label for="amount" class="form-label fw-semibold">
                                    Donation Amount
                                    <span class="text-danger">*</span>
                                </label>

                                <div class="input-group">
                                    <span class="input-group-text">₦</span>

                                    <input
                                        type="number"
                                        name="amount"
                                        id="amount"
                                        class="form-control"
                                        min="0.01"
                                        step="0.01"
                                        placeholder="0.00"
                                        required
                                    >
                                </div>

                                <small class="text-muted">
                                    Enter the actual amount received.
                                </small>

                            </div>

                            <div class="col-md-5">

                                <label for="currency" class="form-label fw-semibold">
                                    Currency
                                </label>

                                <select
                                    name="currency"
                                    id="currency"
                                    class="form-select"
                                >
                                    <option value="NGN" selected>
                                        NGN — Nigerian Naira
                                    </option>

                                    <option value="USD">
                                        USD — US Dollar
                                    </option>

                                    <option value="EUR">
                                        EUR — Euro
                                    </option>

                                    <option value="GBP">
                                        GBP — British Pound
                                    </option>
                                </select>

                            </div>

                        </div>

                        <!-- Date / Payment Method -->
                        <div class="row mb-4">

                            <div class="col-md-6">

                                <label for="donation_date" class="form-label fw-semibold">
                                    Donation Date
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="donation_date"
                                    id="donation_date"
                                    class="form-control"
                                    value="<?= h(date('Y-m-d')) ?>"
                                    required
                                >

                            </div>

                            <div class="col-md-6">

                                <label for="payment_method" class="form-label fw-semibold">
                                    Payment Method
                                </label>

                                <select
                                    name="payment_method"
                                    id="payment_method"
                                    class="form-select"
                                >

                                    <option value="bank_transfer" selected>
                                        Bank Transfer
                                    </option>

                                    <option value="cash">
                                        Cash
                                    </option>

                                    <option value="cheque">
                                        Cheque
                                    </option>

                                    <option value="online">
                                        Online Payment
                                    </option>

                                    <option value="mobile_money">
                                        Mobile Money
                                    </option>

                                    <option value="other">
                                        Other
                                    </option>

                                </select>

                            </div>

                        </div>

                        <!-- Transaction Reference -->
                        <div class="mb-4">

                            <label for="transaction_ref" class="form-label fw-semibold">
                                Transaction Reference
                            </label>

                            <input
                                type="text"
                                name="transaction_ref"
                                id="transaction_ref"
                                class="form-control"
                                maxlength="150"
                                placeholder="e.g. Transfer reference, teller number or cheque number"
                            >

                            <small class="text-muted">
                                Recommended for bank transfers, cheques and online payments.
                            </small>

                        </div>

                        <!-- Purpose -->
                        <div class="mb-4">

                            <label for="purpose" class="form-label fw-semibold">
                                Donation Purpose
                            </label>

                            <input
                                type="text"
                                name="purpose"
                                id="purpose"
                                class="form-control"
                                maxlength="255"
                                placeholder="e.g. Education support, food assistance, general donation"
                            >

                        </div>

                        <!-- Program -->
                        <div class="mb-4">

                            <label for="program_id" class="form-label fw-semibold">
                                Linked Program
                            </label>

                            <select
                                name="program_id"
                                id="program_id"
                                class="form-select"
                            >

                                <option value="">
                                    -- No Specific Program --
                                </option>

                                <?php foreach ($programs as $program): ?>

                                    <option value="<?= (int)$program['id'] ?>">
                                        <?= h($program['name']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <small class="text-muted">
                                Optional. Select the program this donation is intended to support.
                            </small>

                        </div>

                        <!-- Notes -->
                        <div class="mb-2">

                            <label for="notes" class="form-label fw-semibold">
                                Notes
                            </label>

                            <textarea
                                name="notes"
                                id="notes"
                                class="form-control"
                                rows="4"
                                maxlength="1000"
                                placeholder="Additional information about this donation..."
                            ></textarea>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Summary / Actions -->
            <div class="col-lg-4">

                <div class="card shadow-sm mb-4">

                    <div class="card-header py-3">
                        <h6 class="m-0 fw-bold">
                            <i class="bi bi-receipt"></i>
                            Donation Summary
                        </h6>
                    </div>

                    <div class="card-body">

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                Status
                            </small>

                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock"></i>
                                Pending
                            </span>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                Donor
                            </small>

                            <span id="summaryDonor">
                                Not selected
                            </span>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                Amount
                            </small>

                            <strong id="summaryAmount">
                                ₦0.00
                            </strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">
                                Donation Date
                            </small>

                            <span id="summaryDate">
                                <?= h(date('Y-m-d')) ?>
                            </span>
                        </div>

                        <hr>

                        <div class="alert alert-light border mb-0">
                            <small>
                                <i class="bi bi-shield-check"></i>
                                After submission, the donation will receive a unique
                                donation reference and remain pending until confirmed.
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card shadow-sm">

                    <div class="card-body">

                        <button
                            type="submit"
                            class="btn btn-primary w-100 mb-2"
                            id="submitDonation"
                            <?= empty($donors) ? 'disabled' : '' ?>
                        >
                            <i class="bi bi-check-circle"></i>
                            Record Donation
                        </button>

                        <a
                            href="<?= url('/admin/donations') ?>"
                            class="btn btn-outline-secondary w-100"
                        >
                            Cancel
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const donorSelect = document.getElementById('donor_id');
    const amountInput = document.getElementById('amount');
    const dateInput = document.getElementById('donation_date');
    const form = document.getElementById('donationForm');
    const submitButton = document.getElementById('submitDonation');

    const summaryDonor = document.getElementById('summaryDonor');
    const summaryAmount = document.getElementById('summaryAmount');
    const summaryDate = document.getElementById('summaryDate');

    function updateSummary() {

        if (donorSelect) {
            const option = donorSelect.options[donorSelect.selectedIndex];

            if (option && option.value) {
                summaryDonor.textContent = option.text.trim();
            } else {
                summaryDonor.textContent = 'Not selected';
            }
        }

        if (amountInput) {

            const amount = parseFloat(amountInput.value);

            if (!isNaN(amount) && amount > 0) {
                summaryAmount.textContent =
                    '₦' + amount.toLocaleString('en-NG', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
            } else {
                summaryAmount.textContent = '₦0.00';
            }
        }

        if (dateInput && dateInput.value) {
            summaryDate.textContent = dateInput.value;
        }
    }

    donorSelect?.addEventListener('change', updateSummary);
    amountInput?.addEventListener('input', updateSummary);
    dateInput?.addEventListener('change', updateSummary);

    form?.addEventListener('submit', function (event) {

        const donor = donorSelect?.value;
        const amount = parseFloat(amountInput?.value || 0);
        const date = dateInput?.value;

        if (!donor) {
            event.preventDefault();
            alert('Please select a donor.');
            donorSelect?.focus();
            return;
        }

        if (!amount || amount <= 0) {
            event.preventDefault();
            alert('Please enter a valid donation amount.');
            amountInput?.focus();
            return;
        }

        if (!date) {
            event.preventDefault();
            alert('Please select the donation date.');
            dateInput?.focus();
            return;
        }

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1"></span> Recording...';
        }
    });

    updateSummary();
});
</script>