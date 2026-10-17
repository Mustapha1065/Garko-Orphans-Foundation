<?php
function h($value): string
{
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$receiptNo = $donation['donation_ref'] ?? 'N/A';
$donorName = $donation['donor_name'] ?? 'N/A';
$currency = $donation['currency'] ?? 'NGN';
$amount = (float)($donation['amount'] ?? 0);
$paymentMethod = ucfirst(str_replace('_', ' ', $donation['payment_method'] ?? 'N/A'));
$purpose = !empty($donation['purpose']) ? $donation['purpose'] : 'General Support';

$donationDate = !empty($donation['donation_date'])
    ? date('F j, Y', strtotime($donation['donation_date']))
    : 'N/A';

$confirmedDate = !empty($donation['confirmed_at'])
    ? date('F j, Y \a\t g:i A', strtotime($donation['confirmed_at']))
    : null;

$programName = $donation['program_name'] ?? null;
$transactionRef = $donation['transaction_ref'] ?? null;
$confirmedBy = $donation['confirmed_by_name'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        Donation Receipt - <?= h($receiptNo) ?>
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f1f5f9;
            color: #1f2937;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
        }

        .no-print {
            display: block;
        }

        .print-actions {
            max-width: 850px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 11px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-print {
            background: #198754;
            color: #fff;
        }

        .btn-print:hover {
            background: #157347;
        }

        .btn-back {
            background: #6c757d;
            color: #fff;
        }

        .btn-back:hover {
            background: #5c636a;
        }

        .receipt {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            padding: 45px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
        }

        /* Header */
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 30px;
            padding-bottom: 25px;
            border-bottom: 3px solid #198754;
        }

        .organization {
            flex: 1;
        }

        .organization-name {
            margin: 0;
            color: #198754;
            font-size: 30px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .organization-subtitle {
            margin: 4px 0 12px;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .organization-contact {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            line-height: 1.7;
        }

        .receipt-heading {
            min-width: 230px;
            text-align: right;
        }

        .receipt-heading h2 {
            margin: 0 0 8px;
            color: #334155;
            font-size: 25px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .receipt-number {
            margin: 0;
            font-size: 13px;
            color: #64748b;
        }

        .receipt-number strong {
            color: #111827;
        }

        /* Status */
        .status {
            display: inline-block;
            margin-top: 10px;
            padding: 5px 12px;
            border-radius: 20px;
            background: #d1e7dd;
            color: #0f5132;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        /* Amount */
        .amount-box {
            margin: 30px 0;
            padding: 25px;
            text-align: center;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
        }

        .amount-label {
            margin: 0 0 5px;
            color: #64748b;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .amount {
            margin: 0;
            color: #198754;
            font-size: 34px;
            font-weight: 800;
        }

        /* Sections */
        .section {
            margin-top: 28px;
        }

        .section-title {
            margin: 0 0 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e5e7eb;
            color: #334155;
            font-size: 16px;
            font-weight: 700;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #eef2f7;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table th,
        .details-table td {
            padding: 12px 10px;
            vertical-align: top;
            text-align: left;
            font-size: 14px;
        }

        .details-table th {
            width: 35%;
            color: #64748b;
            font-weight: 600;
        }

        .details-table td {
            color: #1f2937;
            font-weight: 500;
        }

        /* Thank you */
        .thank-you {
            margin-top: 35px;
            padding: 22px;
            text-align: center;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .thank-you-title {
            margin: 0 0 5px;
            color: #198754;
            font-size: 19px;
            font-weight: 700;
        }

        .thank-you-text {
            margin: 0;
            color: #64748b;
            font-size: 13px;
        }

        /* Footer */
        .receipt-footer {
            margin-top: 45px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 30px;
        }

        .footer-note {
            max-width: 55%;
            color: #94a3b8;
            font-size: 11px;
        }

        .signature {
            width: 220px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #334155;
            margin-bottom: 7px;
        }

        .signature-text {
            margin: 0;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
        }

        /* Print */
        @page {
            size: A4;
            margin: 15mm;
        }

        @media print {

            body {
                padding: 0;
                margin: 0;
                background: #fff;
            }

            .no-print {
                display: none !important;
            }

            .receipt {
                max-width: none;
                width: 100%;
                margin: 0;
                padding: 20px;
                border: none;
                box-shadow: none;
            }

            .amount-box {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .status {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .thank-you {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

        @media (max-width: 700px) {

            body {
                padding: 10px;
            }

            .receipt {
                padding: 25px 20px;
            }

            .receipt-header {
                flex-direction: column;
            }

            .receipt-heading {
                text-align: left;
                min-width: auto;
            }

            .receipt-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .footer-note {
                max-width: 100%;
            }

            .signature {
                width: 100%;
                max-width: 220px;
                margin-left: auto;
            }

            .amount {
                font-size: 28px;
            }

            .details-table th,
            .details-table td {
                display: block;
                width: 100%;
                padding: 7px 5px;
            }

            .details-table th {
                padding-bottom: 0;
            }

            .details-table td {
                padding-top: 2px;
                padding-bottom: 12px;
            }
        }
    </style>
</head>

<body>

    <!-- Print / navigation controls -->
    <div class="print-actions no-print">

        <a href="<?= function_exists('url') ? url('/admin/donations/' . (int)$donation['id']) : '/admin/donations/' . (int)$donation['id'] ?>"
           class="btn btn-back">
            ← Back
        </a>

        <button type="button"
                class="btn btn-print"
                onclick="window.print()">
            🖨 Print Receipt
        </button>

    </div>


    <!-- Receipt -->
    <div class="receipt">

        <!-- Header -->
        <header class="receipt-header">

            <div class="organization">

                <h1 class="organization-name">
                    Garko Orphans Foundation
                </h1>

                <p class="organization-subtitle">
                    Donation Receipt
                </p>

                <p class="organization-contact">
                    Supporting vulnerable children and communities through
                    care, education and sustainable development.<br>

                    Nigeria
                </p>

            </div>


            <div class="receipt-heading">

                <h2>Official Receipt</h2>

                <p class="receipt-number">
                    Receipt No:
                    <strong><?= h($receiptNo) ?></strong>
                </p>

                <p class="receipt-number">
                    Donation Date:
                    <strong><?= h($donationDate) ?></strong>
                </p>

                <span class="status">
                    Payment Confirmed
                </span>

            </div>

        </header>


        <!-- Amount -->
        <div class="amount-box">

            <p class="amount-label">
                Amount Received
            </p>

            <p class="amount">
                <?= h($currency) ?>
                <?= number_format($amount, 2) ?>
            </p>

        </div>


        <!-- Donor information -->
        <section class="section">

            <h3 class="section-title">
                Donor Information
            </h3>

            <table class="details-table">

                <tr>
                    <th>Received From</th>

                    <td>
                        <strong><?= h($donorName) ?></strong>
                    </td>
                </tr>

                <?php if (!empty($donation['donor_email'])): ?>
                    <tr>
                        <th>Email</th>
                        <td><?= h($donation['donor_email']) ?></td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($donation['donor_phone'])): ?>
                    <tr>
                        <th>Phone</th>
                        <td><?= h($donation['donor_phone']) ?></td>
                    </tr>
                <?php endif; ?>

                <?php if (!empty($donation['donor_goff_id'])): ?>
                    <tr>
                        <th>GOF ID</th>
                        <td><?= h($donation['donor_goff_id']) ?></td>
                    </tr>
                <?php endif; ?>

            </table>

        </section>


        <!-- Donation information -->
        <section class="section">

            <h3 class="section-title">
                Donation Information
            </h3>

            <table class="details-table">

                <tr>
                    <th>Payment Method</th>

                    <td>
                        <?= h($paymentMethod) ?>
                    </td>
                </tr>

                <?php if (!empty($transactionRef)): ?>

                    <tr>
                        <th>Transaction Reference</th>

                        <td>
                            <?= h($transactionRef) ?>
                        </td>
                    </tr>

                <?php endif; ?>


                <tr>
                    <th>Purpose</th>

                    <td>
                        <?= h($purpose) ?>
                    </td>
                </tr>


                <?php if (!empty($programName)): ?>

                    <tr>
                        <th>Program</th>

                        <td>
                            <?= h($programName) ?>
                        </td>
                    </tr>

                <?php endif; ?>

            </table>

        </section>


        <!-- Confirmation -->
        <section class="section">

            <h3 class="section-title">
                Confirmation Details
            </h3>

            <table class="details-table">

                <?php if ($confirmedDate): ?>

                    <tr>
                        <th>Confirmed On</th>

                        <td>
                            <?= h($confirmedDate) ?>
                        </td>
                    </tr>

                <?php endif; ?>


                <?php if (!empty($confirmedBy)): ?>

                    <tr>
                        <th>Confirmed By</th>

                        <td>
                            <?= h($confirmedBy) ?>
                        </td>
                    </tr>

                <?php endif; ?>


                <tr>
                    <th>Receipt Status</th>

                    <td>
                        <strong style="color:#198754;">
                            Confirmed
                        </strong>
                    </td>
                </tr>

            </table>

        </section>


        <!-- Thank you -->
        <div class="thank-you">

            <p class="thank-you-title">
                Thank You for Your Generous Support
            </p>

            <p class="thank-you-text">
                Your contribution helps Garko Orphans Foundation
                support vulnerable children and communities.
            </p>

        </div>


        <!-- Footer -->
        <footer class="receipt-footer">

            <div class="footer-note">

                <strong>Important:</strong><br>

                This receipt confirms that the donation recorded above
                has been received and confirmed by Garko Orphans Foundation.
                Please retain this receipt for your records.

            </div>


            <div class="signature">

                <div class="signature-line"></div>

                <p class="signature-text">
                    Authorized Signature
                </p>

            </div>

        </footer>

    </div>


    <script>
        // Automatically focus the page when opened.
        window.addEventListener('load', function () {
            document.body.focus();
        });
    </script>

</body>
</html>