<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Register Beneficiary</h1>

        <a href="<?= url('/admin/beneficiaries') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm max-w-800 mx-auto">
        <div class="card-body">

            <form method="POST"
                  action="<?= url('/admin/beneficiaries') ?>"
                  enctype="multipart/form-data">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

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
                            required
                            maxlength="150"
                            autocomplete="name"
                        >
                    </div>

                    <!-- PHOTO UPLOAD -->
                    <div class="col-md-12">
                        <label for="beneficiary_photo" class="form-label">
                            <i class="bi bi-camera me-1"></i>
                            Beneficiary Photo
                        </label>

                        <input
                            type="file"
                            name="photo"
                            id="beneficiary_photo"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <div class="form-text">
                            JPG, PNG or WebP only. Maximum size: 5 MB.
                        </div>

                        <!-- PHOTO PREVIEW -->
                        <div id="photoPreviewContainer"
                             class="mt-3"
                             style="display:none;">

                            <div class="border rounded p-2"
                                 style="width:160px;">

                                <img
                                    id="photoPreview"
                                    src=""
                                    alt="Photo Preview"
                                    class="img-fluid rounded"
                                    style="width:140px;height:160px;object-fit:cover;"
                                >

                            </div>

                            <button
                                type="button"
                                id="removePhoto"
                                class="btn btn-sm btn-outline-danger mt-2"
                            >
                                <i class="bi bi-x-circle me-1"></i>
                                Remove Photo
                            </button>

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
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Gender
                        </label>

                        <select name="gender" class="form-select">
                            <option value="">Select...</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
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
                        ></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            State
                        </label>

                        <input
                            type="text"
                            name="state"
                            class="form-control"
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
                            <option value="">Select...</option>
                            <option value="Father">Father</option>
                            <option value="Mother">Mother</option>
                            <option value="Uncle">Uncle</option>
                            <option value="Aunt">Aunt</option>
                            <option value="Sibling">Sibling</option>
                            <option value="Other">Other</option>
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
                     SECTION 4: NOTES
                ====================================================== -->
                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-journal-text me-2"></i>
                    Section 4 - Notes
                </h5>

                <div class="mb-4">

                    <label class="form-label">
                        Internal Admin Notes
                    </label>

                    <textarea
                        name="notes"
                        class="form-control"
                        rows="3"
                    ></textarea>

                </div>


                <!-- =====================================================
                     ACTION BUTTONS
                ====================================================== -->
                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="<?= url('/admin/beneficiaries') ?>"
                        class="btn btn-light border"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-person-plus me-1"></i>
                        Submit Registration
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
         * Client-side size check.
         */
        if (file.size > 5 * 1024 * 1024) {

            alert('Photo must not exceed 5 MB.');

            this.value = '';
            previewContainer.style.display = 'none';
            preview.src = '';

            return;
        }

        /*
         * Client-side type check.
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
         * Display preview.
         */
        const reader = new FileReader();

        reader.onload = function (event) {

            preview.src = event.target.result;

            previewContainer.style.display = 'block';
        };

        reader.readAsDataURL(file);
    });


    /*
     * Remove selected photo.
     */
    if (removeButton) {

        removeButton.addEventListener('click', function () {

            photoInput.value = '';

            preview.src = '';

            previewContainer.style.display = 'none';
        });
    }

});
</script>