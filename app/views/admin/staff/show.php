<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$staffId = (int)($staff['id'] ?? 0);
$hasIdCard = !empty($idCard);
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Staff Profile</h1>

        <div>
            <a href="<?= url('/admin/staff/' . $staffId . '/edit') ?>"
               class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>

            <a href="<?= url('/admin/staff') ?>"
               class="btn btn-outline-secondary ms-2">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>


    <div class="row">

        <!-- LEFT COLUMN -->
        <div class="col-md-4 mb-4">

            <div class="card shadow-sm text-center border-0 pt-4">

                <div class="card-body">

                    <div class="gof-avatar gof-avatar-xl mx-auto mb-3"
                         style="width:100px;height:100px;font-size:2.5rem;">

                        <?= strtoupper(
                            substr(
                                (string)($staff['first_name'] ?? ''),
                                0,
                                1
                            )
                        ) ?>

                    </div>


                    <h4 class="mb-1">
                        <?= h(
                            trim(
                                ($staff['first_name'] ?? '') . ' ' .
                                ($staff['last_name'] ?? '')
                            )
                        ) ?>
                    </h4>


                    <div class="mb-2">
                        <span class="badge bg-primary fs-6">
                            <?= h($staff['gof_id'] ?? '') ?>
                        </span>
                    </div>


                    <div class="text-muted mb-3">
                        <?= h($staff['position'] ?: 'No position set') ?>
                    </div>


                    <div class="d-flex justify-content-center gap-2 mb-4">

                        <?php if (($staff['user_status'] ?? '') === 'active'): ?>

                            <span class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Active
                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary">
                                <?= h($staff['user_status'] ?? 'Unknown') ?>
                            </span>

                        <?php endif; ?>


                        <?php if (!empty($staff['role_name'])): ?>

                            <span class="badge bg-info text-dark">
                                <i class="bi bi-shield-lock me-1"></i>
                                <?= h($staff['role_name']) ?>
                            </span>

                        <?php endif; ?>

                    </div>


                    <!-- CORRECT ID CARD GENERATION FORM -->
                    <form method="POST"
                          action="<?= url('/admin/idcards/generate') ?>"
                          class="d-grid">

                        <input type="hidden"
                               name="csrf_token"
                               value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                        <!-- IMPORTANT:
                             Use staff.id, NOT users.id -->
                        <input type="hidden"
                               name="person_type"
                               value="staff">

                        <input type="hidden"
                               name="person_id"
                               value="<?= $staffId ?>">

                        <button type="submit"
                                class="btn btn-outline-dark">

                            <i class="bi bi-person-badge me-1"></i>

                            Generate ID Card

                        </button>

                    </form>

                </div>

            </div>


            <!-- EXISTING ID CARD -->
            <?php if ($hasIdCard): ?>

                <div class="card shadow-sm border-0 mt-4">

                    <div class="card-header bg-white">

                        <h6 class="mb-0">
                            <i class="bi bi-credit-card-2-front me-2"></i>
                            ID Card
                        </h6>

                    </div>


                    <div class="card-body">

                        <div class="mb-2">
                            <strong>Card No:</strong>
                            <?= h($idCard['card_no'] ?? '') ?>
                        </div>

                        <div class="mb-3">
                            <strong>Status:</strong>
                            <?= h($idCard['status'] ?? '') ?>
                        </div>


                        <div class="d-grid gap-2">

                            <?php if (!empty($idCard['id'])): ?>

                                <a href="<?= url('/admin/idcards/' . (int)$idCard['id']) ?>"
                                   class="btn btn-outline-primary">

                                    <i class="bi bi-eye me-1"></i>
                                    View ID Card

                                </a>


                                <a href="<?= url('/admin/idcards/' . (int)$idCard['id'] . '/pdf') ?>"
                                   class="btn btn-outline-danger">

                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                    Download PDF

                                </a>


                                <a href="<?= url('/admin/idcards/' . (int)$idCard['id'] . '/print') ?>"
                                   class="btn btn-outline-secondary">

                                    <i class="bi bi-printer me-1"></i>
                                    Print Card

                                </a>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            <?php endif; ?>

        </div>



        <!-- RIGHT COLUMN -->
        <div class="col-md-8">

            <!-- PERSONAL DETAILS -->
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white py-3">

                    <h6 class="mb-0 text-primary">
                        <i class="bi bi-person-lines-fill me-2"></i>
                        Personal Details
                    </h6>

                </div>


                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Email Address
                        </div>

                        <div class="col-sm-8 fw-semibold">

                            <?php if (!empty($staff['email'])): ?>

                                <a href="mailto:<?= h($staff['email']) ?>">
                                    <?= h($staff['email']) ?>
                                </a>

                            <?php else: ?>

                                N/A

                            <?php endif; ?>

                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Phone Number
                        </div>

                        <div class="col-sm-8 fw-semibold">
                            <?= h($staff['phone'] ?: 'N/A') ?>
                        </div>

                    </div>

                </div>

            </div>



            <!-- EMPLOYMENT INFORMATION -->
            <div class="card shadow-sm border-0 mb-4">

                <div class="card-header bg-white py-3">

                    <h6 class="mb-0 text-primary">
                        <i class="bi bi-briefcase me-2"></i>
                        Employment Information
                    </h6>

                </div>


                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Department
                        </div>

                        <div class="col-sm-8 fw-semibold">
                            <?= h($staff['department'] ?: 'N/A') ?>
                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Employment Type
                        </div>

                        <div class="col-sm-8 fw-semibold">

                            <?= h(
                                ucwords(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $staff['employment_type'] ?: 'N/A'
                                    )
                                )
                            ) ?>

                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Employment Date
                        </div>

                        <div class="col-sm-8 fw-semibold">
                            <?= h($staff['employment_date'] ?: 'N/A') ?>
                        </div>

                    </div>

                </div>

            </div>



            <!-- EMERGENCY CONTACT -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white py-3">

                    <h6 class="mb-0 text-primary">
                        <i class="bi bi-telephone-plus me-2"></i>
                        Emergency Contact
                    </h6>

                </div>


                <div class="card-body">

                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Contact Name
                        </div>

                        <div class="col-sm-8 fw-semibold">
                            <?= h(
                                $staff['emergency_contact_name'] ?: 'N/A'
                            ) ?>
                        </div>

                    </div>


                    <div class="row mb-3">

                        <div class="col-sm-4 text-muted">
                            Contact Phone
                        </div>

                        <div class="col-sm-8 fw-semibold">
                            <?= h(
                                $staff['emergency_contact_phone'] ?: 'N/A'
                            ) ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>