<?php
use GOF\Helpers\Database;

/*
|--------------------------------------------------------------------------
| Resolve Person Photo
|--------------------------------------------------------------------------
*/

$personType = strtolower((string)($card['person_type'] ?? ''));
$personId   = (int)($card['person_id'] ?? 0);

$personPhoto = null;

if ($personId > 0) {

    try {

        $db = Database::get();

        if ($personType === 'beneficiary') {

            $stmt = $db->prepare("
                SELECT photo
                FROM beneficiaries
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$personId]);

            $personPhoto = $stmt->fetchColumn();

        } elseif ($personType === 'staff') {

            $stmt = $db->prepare("
                SELECT photo
                FROM staff
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$personId]);

            $personPhoto = $stmt->fetchColumn();

        } elseif ($personType === 'donor') {

            $stmt = $db->prepare("
                SELECT photo
                FROM donors
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$personId]);

            $personPhoto = $stmt->fetchColumn();
        }

    } catch (\Throwable $e) {

        $personPhoto = null;
    }
}


/*
|--------------------------------------------------------------------------
| Build Photo URL
|--------------------------------------------------------------------------
*/

$photoUrl = null;

if (!empty($personPhoto)) {

    $personPhoto = ltrim((string)$personPhoto, '/');

    $photoUrl = url('/' . $personPhoto);
}


/*
|--------------------------------------------------------------------------
| Basic Card Values
|--------------------------------------------------------------------------
*/

$personName = $card['person_name'] ?? 'UNKNOWN';
$gofId      = $card['gof_id'] ?? '';
$personType = $card['person_type'] ?? '';
$issueDate  = $card['issue_date'] ?? '';
$expiryDate = $card['expiry_date'] ?? '';
$cardNo     = $card['card_no'] ?? '';
$qrToken    = $card['qr_token'] ?? '';

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        ID Card - <?= htmlspecialchars($personName) ?>
    </title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 20px;
            margin: 0;
        }

        .id-card {
            width: 85.6mm;
            height: 54mm;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
            background: white;
        }

        .front {
            background: linear-gradient(
                135deg,
                #1B5E3B,
                #0F2E1E
            );

            color: white;
            padding: 10px;
        }

        .back {
            background: #fff;
            color: #333;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #FFD700;
            margin-bottom: 5px;
        }


        /*
        |--------------------------------------------------------------------------
        | Photo
        |--------------------------------------------------------------------------
        */

        .photo-container {

            width: 70px;
            height: 70px;

            margin: 0 auto 5px;

            border-radius: 50%;

            overflow: hidden;

            background: #ccc;

            border: 2px solid white;

            display: flex;

            align-items: center;

            justify-content: center;

            box-sizing: border-box;
        }

        .photo-container img {

            width: 100%;

            height: 100%;

            object-fit: cover;

            display: block;
        }

        .photo-placeholder {

            width: 100%;

            height: 100%;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #666;

            font-size: 11px;

            font-weight: bold;

            background: #ddd;
        }


        .details {
            text-align: center;
        }

        .name {
            font-size: 16px;
            font-weight: bold;
            margin: 5px 0 2px;
        }

        .gof-id {
            font-family: monospace;
            color: #FFD700;
            font-size: 14px;
        }

        .category {
            font-size: 12px;
            color: #aaa;
            text-transform: uppercase;
        }

        .footer {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 10px;
            margin-top: 10px;
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
        }

        .qr-code {
            width: 40px;
            height: 40px;
            background: white;
            padding: 2px;
        }

        .back-header {
            color: #1B5E3B;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .back-text {
            font-size: 10px;
            margin-bottom: 5px;
        }

        .back-verify {
            font-size: 11px;
            font-weight: bold;
            margin: 10px 0;
            color: #1B5E3B;
        }

        .back-terms {
            font-size: 9px;
            color: #666;
            margin-top: 15px;
            padding-top: 5px;
            border-top: 1px solid #eee;
        }

        @media print {

            body {
                background: none;
                padding: 0;
            }

            .id-card {
                box-shadow: none;
                break-inside: avoid;
            }
        }

    </style>

</head>

<body>


<!-- ========================================================= -->
<!-- FRONT OF CARD -->
<!-- ========================================================= -->

<div class="id-card front">

    <div class="header">
        GARKO ORPHANS FOUNDATION
    </div>


    <!-- PHOTO -->

    <div class="photo-container">

        <?php if ($photoUrl): ?>

            <img
                src="<?= htmlspecialchars($photoUrl) ?>"
                alt="<?= htmlspecialchars($personName) ?>"
            >

        <?php else: ?>

            <div class="photo-placeholder">
                PHOTO
            </div>

        <?php endif; ?>

    </div>


    <!-- DETAILS -->

    <div class="details">

        <div class="name">
            <?= htmlspecialchars($personName) ?>
        </div>

        <div class="gof-id">
            <?= htmlspecialchars($gofId) ?>
        </div>

        <div class="category">
            <?= htmlspecialchars($personType) ?>
        </div>

    </div>


    <!-- FOOTER -->

    <div class="footer">

        <div>

            <div>
                Issued:
                <?= htmlspecialchars($issueDate) ?>
            </div>

            <div>
                Expires:
                <?= htmlspecialchars($expiryDate) ?>
            </div>

            <div>
                No:
                <?= htmlspecialchars($cardNo) ?>
            </div>

        </div>


        <?php
        $qrUrl = url('/uploads/qrcodes/' . $qrToken . '.png');
        ?>

        <img
            src="<?= htmlspecialchars($qrUrl) ?>"
            class="qr-code"
            alt="QR Code"
        >

    </div>

</div>


<!-- ========================================================= -->
<!-- BACK OF CARD -->
<!-- ========================================================= -->

<div class="id-card back">

    <div class="back-header">
        GARKO ORPHANS FOUNDATION
    </div>

    <div class="back-text">
        Address: Garko, Kano State, Nigeria
    </div>

    <div class="back-text">
        Email: info@gof.org.ng
    </div>

    <div class="back-text">
        Phone: +234 800 000 0000
    </div>

    <div class="back-verify">
        Scan QR code or visit: gof.org.ng/verify
    </div>

    <div class="back-terms">

        This card is the property of GOF.<br>

        If found, please return to the nearest GOF office.

    </div>

    <div
        style="
            font-size: 10px;
            color: #999;
            margin-top: 10px;
            font-family: monospace;
        "
    >
        <?= htmlspecialchars($cardNo) ?>
    </div>

</div>


<script>

    /*
    |--------------------------------------------------------------------------
    | Auto Print
    |--------------------------------------------------------------------------
    */

    if (
        window.location.search.includes('print=1')
    ) {
        window.print();
    }

</script>

</body>

</html>