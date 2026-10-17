<?php
function h($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$beneficiaryId = (int)($beneficiary['id'] ?? 0);
$hasIdCard = !empty($idCard);

/*
|--------------------------------------------------------------------------
| Beneficiary Photo
|--------------------------------------------------------------------------
*/
$photo = trim((string)($beneficiary['photo'] ?? ''));
$photoExists = false;
$photoUrl = '';

if ($photo !== '') {
    $photoPath = __DIR__ . '/../../../public/' . ltrim($photo, '/');

    if (is_file($photoPath)) {
        $photoExists = true;
        $photoUrl = url('/' . ltrim($photo, '/'));
    }
}

/*
|--------------------------------------------------------------------------
| Avatar / Initial
|--------------------------------------------------------------------------
*/
$fullName = trim((string)($beneficiary['full_name'] ?? 'Unknown'));
$initial = strtoupper(substr($fullName, 0, 1));

/*
|--------------------------------------------------------------------------
| Status Color
|--------------------------------------------------------------------------
*/
$scolor = 'secondary';

if (($beneficiary['status'] ?? '') === 'pending') {
    $scolor = 'warning';
} elseif (($beneficiary['status'] ?? '') === 'active') {
    $scolor = 'success';
} elseif (($beneficiary['status'] ?? '') === 'graduated') {
    $scolor = 'primary';
} elseif (($beneficiary['status'] ?? '') === 'suspended') {
    $scolor = 'danger';
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3 mb-0 text-gray-800">
            Beneficiary Profile
        </h1>

        <div>

            <a href="<?= url('/admin/beneficiaries') ?>"
               class="btn btn-outline-secondary">

                <i class="bi bi-arrow-left me-1"></i>
                Back

            </a>

        </div>

    </div>


    <?php if (($beneficiary['status'] ?? '') === 'pending'): ?>

        <div class="card bg-success-subtle border-success shadow-sm mb-4">

            <div class="card-body d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="text-success mb-1">

                        <i class="bi bi-shield-exclamation me-2"></i>

                        Pending Approval

                    </h5>

                    <p class="mb-0 text-muted small">
                        This beneficiary is awaiting approval.
                    </p>

                </div>


                <div class="d-flex gap-2">

                    <form method="POST"
                          action="<?= url('/admin/beneficiaries/' . $beneficiaryId . '/approve') ?>">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                        <button type="submit"
                                class="btn btn-success">

                            <i class="bi bi-check-lg me-1"></i>

                            Approve

                        </button>

                    </form>


                    <button type="button"
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#rejectModal">

                        <i class="bi bi-x-lg me-1"></i>

                        Reject

                    </button>

                </div>

            </div>

        </div>


        <!-- Reject Modal -->

        <div class="modal fade"
             id="rejectModal"
             tabindex="-1">

            <div class="modal-dialog">

                <div class="modal-content">

                    <form method="POST"
                          action="<?= url('/admin/beneficiaries/' . $beneficiaryId . '/reject') ?>">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= h($_SESSION['csrf_token'] ?? '') ?>">


                        <div class="modal-header">

                            <h5 class="modal-title">
                                Reject Beneficiary
                            </h5>

                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="modal">
                            </button>

                        </div>


                        <div class="modal-body">

                            <div class="mb-3">

                                <label class="form-label">
                                    Reason for rejection *
                                </label>

                                <textarea name="rejection_reason"
                                          class="form-control"
                                          required></textarea>

                            </div>

                        </div>


                        <div class="modal-footer">

                            <button type="button"
                                    class="btn btn-light"
                                    data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button type="submit"
                                    class="btn btn-danger">

                                Reject

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    <?php endif; ?>


    <div class="row">


        <!-- =========================================================
             LEFT PROFILE PANEL
        ========================================================== -->

        <div class="col-md-3 mb-4">

            <div class="card shadow-sm text-center border-0 pt-4 mb-3">

                <div class="card-body">


                    <!-- BENEFICIARY PHOTO -->

                    <div class="mb-3">

                        <?php if ($photoExists): ?>

                            <img src="<?= h($photoUrl) ?>"
                                 alt="<?= h($fullName) ?>"
                                 class="rounded-circle shadow-sm"
                                 style="
                                     width:140px;
                                     height:140px;
                                     object-fit:cover;
                                     border:4px solid #fff;
                                     outline:1px solid #dee2e6;
                                 ">

                        <?php else: ?>

                            <div class="gof-avatar gof-avatar-xl mx-auto bg-<?= h($scolor) ?> text-white"
                                 style="
                                     width:140px;
                                     height:140px;
                                     font-size:3.5rem;
                                     display:flex;
                                     align-items:center;
                                     justify-content:center;
                                     border-radius:50%;
                                 ">

                                <?= h($initial) ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- PHOTO STATUS -->

                    <?php if ($photoExists): ?>

                        <div class="small text-success mb-3">

                            <i class="bi bi-camera-fill me-1"></i>

                            Profile photo uploaded

                        </div>

                    <?php else: ?>

                        <div class="small text-muted mb-3">

                            <i class="bi bi-person-circle me-1"></i>

                            No profile photo

                        </div>

                    <?php endif; ?>


                    <h4 class="mb-1">
                        <?= h($fullName) ?>
                    </h4>


                    <div class="mb-2">

                        <span class="badge bg-light text-dark border">

                            <?= h($beneficiary['gof_id'] ?? '') ?>

                        </span>

                    </div>


                    <div class="mb-3">

                        <span class="badge bg-<?= h($scolor) ?>">

                            <?= h(ucfirst($beneficiary['status'] ?? 'unknown')) ?>

                        </span>

                    </div>


                    <div class="small text-muted mb-4">

                        Registered:

                        <?= h(substr($beneficiary['created_at'] ?? '', 0, 10)) ?>

                    </div>


                    <div class="d-grid gap-2">


                        <!-- EDIT -->

                        <a href="<?= url('/admin/beneficiaries/' . $beneficiaryId . '/edit') ?>"
                           class="btn btn-outline-primary">

                            <i class="bi bi-pencil me-1"></i>

                            Edit Profile

                        </a>


                        <!-- GENERATE ID CARD -->

                        <?php if (!$hasIdCard): ?>

                            <form method="POST"
                                  action="<?= url('/admin/idcards/generate') ?>">

                                <input type="hidden"
                                       name="csrf_token"
                                       value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                                <input type="hidden"
                                       name="person_type"
                                       value="beneficiary">

                                <input type="hidden"
                                       name="person_id"
                                       value="<?= $beneficiaryId ?>">

                                <button type="submit"
                                        class="btn btn-outline-dark w-100">

                                    <i class="bi bi-person-badge me-1"></i>

                                    Generate ID Card

                                </button>

                            </form>

                        <?php else: ?>

                            <a href="<?= url('/admin/idcards/' . (int)$idCard['id']) ?>"
                               class="btn btn-outline-success">

                                <i class="bi bi-person-badge me-1"></i>

                                View ID Card

                            </a>

                        <?php endif; ?>


                        <!-- ARCHIVE -->

                        <form method="POST"
                              action="<?= url('/admin/beneficiaries/' . $beneficiaryId . '/archive') ?>">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                            <button type="submit"
                                    class="btn btn-outline-secondary w-100"
                                    onclick="return confirm('Archive this beneficiary?')">

                                <i class="bi bi-archive me-1"></i>

                                Archive

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>


        <!-- =========================================================
             RIGHT CONTENT
        ========================================================== -->

        <div class="col-md-9">

            <div class="card shadow-sm">


                <div class="card-header bg-white">

                    <ul class="nav nav-tabs card-header-tabs"
                        id="profileTabs">

                        <li class="nav-item">

                            <a class="nav-link active"
                               data-bs-toggle="tab"
                               href="#tab-profile">

                                Profile

                            </a>

                        </li>


                        <li class="nav-item">

                            <a class="nav-link"
                               data-bs-toggle="tab"
                               href="#tab-education">

                                Education

                            </a>

                        </li>


                        <li class="nav-item">

                            <a class="nav-link"
                               data-bs-toggle="tab"
                               href="#tab-support">

                                Support History

                            </a>

                        </li>


                        <li class="nav-item">

                            <a class="nav-link"
                               data-bs-toggle="tab"
                               href="#tab-idcard">

                                ID Card

                            </a>

                        </li>


                        <li class="nav-item">

                            <a class="nav-link"
                               data-bs-toggle="tab"
                               href="#tab-documents">

                                Documents

                            </a>

                        </li>

                    </ul>

                </div>


                <div class="card-body">

                    <div class="tab-content">


                        <!-- =================================================
                             PROFILE
                        ================================================== -->

                        <div class="tab-pane fade show active"
                             id="tab-profile">


                            <h6 class="text-primary mb-3">

                                <i class="bi bi-person-lines-fill me-2"></i>

                                Personal Info

                            </h6>


                            <div class="row mb-4">


                                <div class="col-md-6 mb-2">

                                    <strong>Full Name:</strong>

                                    <?= h($beneficiary['full_name'] ?? 'N/A') ?>

                                </div>


                                <div class="col-md-6 mb-2">

                                    <strong>Date of Birth:</strong>

                                    <?= h($beneficiary['date_of_birth'] ?: 'N/A') ?>

                                </div>


                                <div class="col-md-6 mb-2">

                                    <strong>Gender:</strong>

                                    <?= h($beneficiary['gender'] ?: 'N/A') ?>

                                </div>


                                <div class="col-md-6 mb-2">

                                    <strong>Phone:</strong>

                                    <?= h($beneficiary['phone'] ?: 'N/A') ?>

                                </div>

                            </div>


                            <h6 class="text-primary mb-3">

                                <i class="bi bi-geo-alt me-2"></i>

                                Address

                            </h6>


                            <div class="row mb-4">


                                <div class="col-md-12 mb-2">

                                    <strong>Address:</strong>

                                    <?= h($beneficiary['address'] ?: 'N/A') ?>

                                </div>


                                <div class="col-md-6 mb-2">

                                    <strong>State:</strong>

                                    <?= h($beneficiary['state'] ?: 'N/A') ?>

                                </div>


                                <div class="col-md-6 mb-2">

                                    <strong>LGA:</strong>

                                    <?= h($beneficiary['lga'] ?: 'N/A') ?>

                                </div>

                            </div>


                            <h6 class="text-primary mb-3">

                                <i class="bi bi-people me-2"></i>

                                Guardians

                            </h6>


                            <?php if (empty($beneficiary['guardians'])): ?>

                                <p class="text-muted small">
                                    No guardian information.
                                </p>

                            <?php else: ?>

                                <div class="table-responsive mb-4">

                                    <table class="table table-sm">

                                        <thead>

                                            <tr>

                                                <th>Name</th>
                                                <th>Relationship</th>
                                                <th>Phone</th>
                                                <th>Emergency</th>

                                            </tr>

                                        </thead>


                                        <tbody>

                                            <?php foreach ($beneficiary['guardians'] as $g): ?>

                                                <tr>

                                                    <td>
                                                        <?= h($g['full_name']) ?>
                                                    </td>

                                                    <td>
                                                        <?= h($g['relationship']) ?>
                                                    </td>

                                                    <td>
                                                        <?= h($g['phone']) ?>
                                                    </td>

                                                    <td>

                                                        <?php if (!empty($g['is_emergency'])): ?>

                                                            <span class="badge bg-danger">
                                                                Yes
                                                            </span>

                                                        <?php else: ?>

                                                            No

                                                        <?php endif; ?>

                                                    </td>

                                                </tr>

                                            <?php endforeach; ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php endif; ?>


                            <h6 class="text-primary mb-3">

                                <i class="bi bi-journal-text me-2"></i>

                                Notes

                            </h6>


                            <p class="mb-0 text-muted bg-light p-3 rounded">

                                <?= nl2br(h($beneficiary['notes'] ?: 'No notes.')) ?>

                            </p>

                        </div>


                        <!-- =================================================
                             EDUCATION
                        ================================================== -->

                        <div class="tab-pane fade"
                             id="tab-education">


                            <div class="d-flex justify-content-between mb-3">

                                <h6>
                                    Education Records
                                </h6>


                                <a href="<?= url('/admin/education?beneficiary_id=' . $beneficiaryId) ?>"
                                   class="btn btn-sm btn-primary">

                                    Manage Education

                                </a>

                            </div>


                            <?php if (empty($beneficiary['education'])): ?>

                                <p class="text-muted">
                                    No education records.
                                </p>

                            <?php else: ?>

                                <table class="table table-sm">

                                    <thead>

                                        <tr>

                                            <th>Institution</th>
                                            <th>Type</th>
                                            <th>Level</th>
                                            <th>Status</th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <?php foreach ($beneficiary['education'] as $ed): ?>

                                            <tr>

                                                <td>
                                                    <?= h($ed['institution_name']) ?>
                                                </td>

                                                <td>
                                                    <?= h($ed['institution_type']) ?>
                                                </td>

                                                <td>
                                                    <?= h($ed['course_level']) ?>
                                                </td>

                                                <td>
                                                    <?= h($ed['enrollment_status']) ?>
                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>

                                </table>

                            <?php endif; ?>

                        </div>


                        <!-- =================================================
                             SUPPORT
                        ================================================== -->

                        <div class="tab-pane fade"
                             id="tab-support">


                            <div class="d-flex justify-content-between mb-3">

                                <h6>
                                    Support History
                                </h6>


                                <a href="<?= url('/admin/support?beneficiary_id=' . $beneficiaryId) ?>"
                                   class="btn btn-sm btn-primary">

                                    Manage Support

                                </a>

                            </div>


                            <?php if (empty($beneficiary['support'])): ?>

                                <p class="text-muted">

                                    View and manage support records by clicking
                                    the button above.

                                </p>

                            <?php else: ?>

                                <table class="table table-sm">

                                    <thead>

                                        <tr>

                                            <th>Type</th>
                                            <th>Amount</th>
                                            <th>Date</th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <?php foreach ($beneficiary['support'] as $sup): ?>

                                            <tr>

                                                <td>
                                                    <?= h($sup['support_type']) ?>
                                                </td>

                                                <td>
                                                    <?= h($sup['amount'] . ' ' . $sup['currency']) ?>
                                                </td>

                                                <td>
                                                    <?= h($sup['support_date']) ?>
                                                </td>

                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>

                                </table>

                            <?php endif; ?>

                        </div>


                        <!-- =================================================
                             ID CARD
                        ================================================== -->

                        <div class="tab-pane fade"
                             id="tab-idcard">


                            <h6>
                                ID Card Details
                            </h6>


                            <?php if ($idCard): ?>

                                <div class="card bg-light border-0">

                                    <div class="card-body">


                                        <p>

                                            <strong>
                                                Card Number:
                                            </strong>

                                            <?= h($idCard['card_no']) ?>

                                        </p>


                                        <p>

                                            <strong>
                                                Status:
                                            </strong>

                                            <?= h($idCard['status']) ?>

                                        </p>


                                        <div class="mt-3">


                                            <?php if (!empty($idCard['id'])): ?>

                                                <a href="<?= url('/admin/idcards/' . (int)$idCard['id'] . '/pdf') ?>"
                                                   class="btn btn-sm btn-secondary">

                                                    <i class="bi bi-download me-1"></i>

                                                    Download PDF

                                                </a>


                                                <a href="<?= url('/admin/idcards/' . (int)$idCard['id'] . '/print') ?>"
                                                   class="btn btn-sm btn-secondary">

                                                    <i class="bi bi-printer me-1"></i>

                                                    Print

                                                </a>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </div>

                            <?php else: ?>

                                <p class="text-muted">

                                    No ID card generated yet.

                                </p>

                            <?php endif; ?>

                        </div>


                        <!-- =================================================
                             DOCUMENTS
                        ================================================== -->

                        <div class="tab-pane fade"
                             id="tab-documents">


                            <?php

                            $entityType = 'beneficiary';
                            $entityId = $beneficiaryId;

                            ?>


                            <?php include __DIR__ . '/../partials/documents.php'; ?>


                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>