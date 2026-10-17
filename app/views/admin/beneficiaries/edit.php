<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$beneficiaryId = (int)($beneficiary['id'] ?? 0);
$currentPhoto = trim((string)($beneficiary['photo'] ?? ''));

$currentPhotoUrl = '';

if ($currentPhoto !== '') {
    $currentPhotoUrl = url('/' . ltrim($currentPhoto, '/'));
}

$fullName = $beneficiary['full_name'] ?? '';
$dob = $beneficiary['date_of_birth'] ?? '';
$gender = $beneficiary['gender'] ?? '';
$phone = $beneficiary['phone'] ?? '';
$address = $beneficiary['address'] ?? '';
$state = $beneficiary['state'] ?? '';
$lga = $beneficiary['lga'] ?? '';
$notes = $beneficiary['notes'] ?? '';
$status = $beneficiary['status'] ?? 'pending';

$guardianName = $beneficiary['guardian_name'] ?? '';
$guardianRelationship = $beneficiary['guardian_relationship'] ?? '';
$guardianPhone = $beneficiary['guardian_phone'] ?? '';
$guardianEmergency = !empty($beneficiary['guardian_is_emergency']);
?>

<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h1 class="h3 mb-1 text-gray-800">
                Edit Beneficiary
            </h1>

            <?php if (!empty($beneficiary['gof_id'])): ?>
                <div class="text-muted small">
                    GOF ID:
                    <strong><?= h($beneficiary['gof_id']) ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <a
            href="<?= url('/admin/beneficiaries/' . $beneficiaryId) ?>"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>

    </div>


    <div class="card shadow-sm max-w-800 mx-auto">

        <div class="card-body">

            <form
                method="POST"
                action="<?= url('/admin/beneficiaries/' . $beneficiaryId . '/update') ?>"
                enctype="multipart/form-data"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= h($_SESSION['csrf_token'] ?? '') ?>"
                >


                <!-- =====================================================
                     SECTION 1: PERSONAL INFORMATION
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-person me-2"></i>
                    Section 1 - Personal Information
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-12">

                        <label class="form-label">
                            Full Name *
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            class="form-control"
                            value="<?= h($fullName) ?>"
                            required
                            maxlength="150"
                            autocomplete="name"
                        >

                    </div>


                    <!-- =================================================
                         CURRENT PHOTO / NEW PHOTO
                    ================================================== -->

                    <div class="col-md-12">

                        <label class="form-label">
                            <i class="bi bi-camera me-1"></i>
                            Beneficiary Photo
                        </label>


                        <div class="row g-3 align-items-start">

                            <!-- CURRENT PHOTO -->

                            <div class="col-md-4">

                                <div class="small text-muted mb-2">
                                    Current Photo
                                </div>

                                <?php if ($currentPhotoUrl): ?>

                                    <div
                                        class="border rounded p-2"
                                        style="width:170px;"
                                    >

                                        <img
                                            src="<?= h($currentPhotoUrl) ?>"
                                            alt="Current beneficiary photo"
                                            class="img-fluid rounded"
                                            style="
                                                width:150px;
                                                height:170px;
                                                object-fit:cover;
                                            "
                                        >

                                    </div>

                                <?php else: ?>

                                    <div
                                        class="border rounded d-flex align-items-center justify-content-center text-muted"
                                        style="
                                            width:170px;
                                            height:190px;
                                        "
                                    >
                                        <div class="text-center">
                                            <i
                                                class="bi bi-person-circle"
                                                style="font-size:60px;"
                                            ></i>

                                            <div class="small mt-2">
                                                No photo
                                            </div>
                                        </div>
                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- NEW PHOTO -->

                            <div class="col-md-8">

                                <label
                                    for="beneficiary_photo"
                                    class="form-label"
                                >
                                    Replace Photo
                                </label>

                                <input
                                    type="file"
                                    name="photo"
                                    id="beneficiary_photo"
                                    class="form-control"
                                    accept="image/jpeg,image/png,image/webp"
                                >

                                <div class="form-text">
                                    Leave empty to keep the current photo.
                                    JPG, PNG or WebP. Maximum 5 MB.
                                </div>


                                <!-- NEW PHOTO PREVIEW -->

                                <div
                                    id="photoPreviewContainer"
                                    class="mt-3"
                                    style="display:none;"
                                >

                                    <div class="small text-muted mb-2">
                                        New Photo Preview
                                    </div>

                                    <div
                                        class="border rounded p-2"
                                        style="width:170px;"
                                    >

                                        <img
                                            id="photoPreview"
                                            src=""
                                            alt="New photo preview"
                                            class="img-fluid rounded"
                                            style="
                                                width:150px;
                                                height:170px;
                                                object-fit:cover;
                                            "
                                        >

                                    </div>

                                    <button
                                        type="button"
                                        id="removePhoto"
                                        class="btn btn-sm btn-outline-danger mt-2"
                                    >
                                        <i class="bi bi-x-circle me-1"></i>
                                        Cancel New Photo
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Date of Birth
                        </label>

                        <input
                            type="date"
                            name="dob"
                            class="form-control"
                            value="<?= h($dob) ?>"
                        >

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Gender
                        </label>

                        <select
                            name="gender"
                            class="form-select"
                        >

                            <option value="">
                                Select...
                            </option>

                            <option
                                value="Male"
                                <?= $gender === 'Male' ? 'selected' : '' ?>
                            >
                                Male
                            </option>

                            <option
                                value="Female"
                                <?= $gender === 'Female' ? 'selected' : '' ?>
                            >
                                Female
                            </option>

                            <option
                                value="Other"
                                <?= $gender === 'Other' ? 'selected' : '' ?>
                            >
                                Other
                            </option>

                        </select>

                    </div>


                    <div class="col-md-4">

                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="<?= h($phone) ?>"
                            maxlength="30"
                            autocomplete="tel"
                        >

                    </div>

                </div>


                <!-- =====================================================
                     SECTION 2: ADDRESS
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-geo-alt me-2"></i>
                    Section 2 - Address
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-12">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="2"
                        ><?= h($address) ?></textarea>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            State
                        </label>

                        <input
                            type="text"
                            name="state"
                            class="form-control"
                            value="<?= h($state) ?>"
                            maxlength="100"
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            LGA
                        </label>

                        <input
                            type="text"
                            name="lga"
                            class="form-control"
                            value="<?= h($lga) ?>"
                            maxlength="100"
                        >

                    </div>

                </div>


                <!-- =====================================================
                     SECTION 3: GUARDIAN INFORMATION
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-people me-2"></i>
                    Section 3 - Guardian Information
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label class="form-label">
                            Guardian Name
                        </label>

                        <input
                            type="text"
                            name="guardian_name"
                            class="form-control"
                            value="<?= h($guardianName) ?>"
                            maxlength="150"
                            autocomplete="name"
                        >

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Relationship
                        </label>

                        <select
                            name="guardian_relationship"
                            class="form-select"
                        >

                            <option value="">
                                Select...
                            </option>

                            <option
                                value="Father"
                                <?= $guardianRelationship === 'Father' ? 'selected' : '' ?>
                            >
                                Father
                            </option>

                            <option
                                value="Mother"
                                <?= $guardianRelationship === 'Mother' ? 'selected' : '' ?>
                            >
                                Mother
                            </option>

                            <option
                                value="Uncle"
                                <?= $guardianRelationship === 'Uncle' ? 'selected' : '' ?>
                            >
                                Uncle
                            </option>

                            <option
                                value="Aunt"
                                <?= $guardianRelationship === 'Aunt' ? 'selected' : '' ?>
                            >
                                Aunt
                            </option>

                            <option
                                value="Sibling"
                                <?= $guardianRelationship === 'Sibling' ? 'selected' : '' ?>
                            >
                                Sibling
                            </option>

                            <option
                                value="Other"
                                <?= $guardianRelationship === 'Other' ? 'selected' : '' ?>
                            >
                                Other
                            </option>

                        </select>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Guardian Phone
                        </label>

                        <input
                            type="text"
                            name="guardian_phone"
                            class="form-control"
                            value="<?= h($guardianPhone) ?>"
                            maxlength="30"
                            autocomplete="tel"
                        >

                    </div>


                    <div class="col-md-6 d-flex align-items-end">

                        <div class="form-check mb-2">

                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="guardian_is_emergency"
                                id="g_em"
                                value="1"
                                <?= $guardianEmergency ? 'checked' : '' ?>
                            >

                            <label
                                class="form-check-label"
                                for="g_em"
                            >
                                Is Emergency Contact?
                            </label>

                        </div>

                    </div>

                </div>


                <!-- =====================================================
                     SECTION 4: STATUS
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    Section 4 - Status
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option
                                value="pending"
                                <?= $status === 'pending' ? 'selected' : '' ?>
                            >
                                Pending
                            </option>

                            <option
                                value="active"
                                <?= $status === 'active' ? 'selected' : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="rejected"
                                <?= $status === 'rejected' ? 'selected' : '' ?>
                            >
                                Rejected
                            </option>

                            <option
                                value="archived"
                                <?= $status === 'archived' ? 'selected' : '' ?>
                            >
                                Archived
                            </option>

                        </select>

                    </div>

                </div>


                <!-- =====================================================
                     SECTION 5: NOTES
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-journal-text me-2"></i>
                    Section 5 - Notes
                </h5>

                <div class="mb-4">

                    <label class="form-label">
                        Internal Admin Notes
                    </label>

                    <textarea
                        name="notes"
                        class="form-control"
                        rows="3"
                    ><?= h($notes) ?></textarea>

                </div>


                <!-- =====================================================
                     ACTION BUTTONS
                ====================================================== -->

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="<?= url('/admin/beneficiaries/' . $beneficiaryId) ?>"
                        class="btn btn-light border"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-save me-1"></i>
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =============================================================
     PHOTO PREVIEW SCRIPT
============================================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const photoInput =
        document.getElementById('beneficiary_photo');

    const previewContainer =
        document.getElementById('photoPreviewContainer');

    const preview =
        document.getElementById('photoPreview');

    const removeButton =
        document.getElementById('removePhoto');

    if (!photoInput) {
        return;
    }

    photoInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            previewContainer.style.display = 'none';
            preview.src = '';
            return;
        }

        /*
         * Maximum 5 MB.
         */
        if (file.size > 5 * 1024 * 1024) {

            alert('Photo must not exceed 5 MB.');

            this.value = '';

            previewContainer.style.display = 'none';
            preview.src = '';

            return;
        }

        /*
         * Allowed image types.
         */
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {

            alert(
                'Only JPG, PNG and WebP images are allowed.'
            );

            this.value = '';

            previewContainer.style.display = 'none';
            preview.src = '';

            return;
        }

        /*
         * Generate preview.
         */
        const reader = new FileReader();

        reader.onload = function (event) {

            preview.src =
                event.target.result;

            previewContainer.style.display =
                'block';
        };

        reader.readAsDataURL(file);
    });


    /*
     * Cancel replacement photo.
     */
    if (removeButton) {

        removeButton.addEventListener(
            'click',
            function () {

                photoInput.value = '';

                preview.src = '';

                previewContainer.style.display =
                    'none';
            }
        );
    }

});
</script>