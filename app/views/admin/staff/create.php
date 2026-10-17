<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Staff Member</h1>

        <a href="<?= url('/admin/staff') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm max-w-800 mx-auto">
        <div class="card-body">

            <form
                method="POST"
                action="<?= url('/admin/staff') ?>"
                enctype="multipart/form-data"
                id="staffCreateForm"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= h($_SESSION['csrf_token'] ?? '') ?>"
                >

                <!-- =====================================================
                     PERSONAL INFORMATION
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-person me-2"></i>
                    Personal Information
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">
                            First Name *
                        </label>

                        <input
                            type="text"
                            name="first_name"
                            class="form-control"
                            required
                            maxlength="100"
                            autocomplete="given-name"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Last Name *
                        </label>

                        <input
                            type="text"
                            name="last_name"
                            class="form-control"
                            required
                            maxlength="100"
                            autocomplete="family-name"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Email *
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            required
                            maxlength="150"
                            autocomplete="email"
                        >
                    </div>

                    <div class="col-md-6">
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

                    <!-- =================================================
                         STAFF PHOTO
                    ================================================== -->

                    <div class="col-12">
                        <label class="form-label">
                            Staff Photo
                        </label>

                        <div class="border rounded p-3 bg-light">

                            <div class="row align-items-center g-3">

                                <div class="col-md-8">

                                    <input
                                        type="file"
                                        name="photo"
                                        id="staffPhoto"
                                        class="form-control"
                                        accept="image/jpeg,image/png,image/webp"
                                    >

                                    <div class="form-text">
                                        JPG, PNG or WebP. Maximum size: 5 MB.
                                    </div>

                                </div>

                                <div class="col-md-4 text-center">

                                    <div
                                        id="staffPhotoPreviewContainer"
                                        class="d-none"
                                    >

                                        <img
                                            id="staffPhotoPreview"
                                            src=""
                                            alt="Staff photo preview"
                                            class="rounded border"
                                            style="
                                                width:120px;
                                                height:120px;
                                                object-fit:cover;
                                            "
                                        >

                                        <div class="mt-2">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                id="removeStaffPhoto"
                                            >
                                                <i class="bi bi-x-circle me-1"></i>
                                                Remove
                                            </button>
                                        </div>

                                    </div>

                                    <div
                                        id="staffPhotoPlaceholder"
                                        class="text-muted"
                                    >
                                        <i
                                            class="bi bi-person-circle"
                                            style="font-size:70px;"
                                        ></i>

                                        <div>
                                            Photo Preview
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">
                            Password *
                        </label>

                        <input
                            type="password"
                            name="password"
                            id="staffPassword"
                            class="form-control"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Confirm Password *
                        </label>

                        <input
                            type="password"
                            name="password_confirm"
                            id="staffPasswordConfirm"
                            class="form-control"
                            required
                            minlength="8"
                            autocomplete="new-password"
                        >

                        <div
                            id="passwordMatchMessage"
                            class="small mt-1"
                        ></div>
                    </div>

                </div>


                <!-- =====================================================
                     EMPLOYMENT DETAILS
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-briefcase me-2"></i>
                    Employment Details
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">
                            Department
                        </label>

                        <input
                            type="text"
                            name="department"
                            class="form-control"
                            maxlength="150"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Position
                        </label>

                        <input
                            type="text"
                            name="position"
                            class="form-control"
                            maxlength="150"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Employment Type
                        </label>

                        <select
                            name="employment_type"
                            class="form-select"
                        >
                            <option value="">Select Type...</option>
                            <option value="full_time">Full Time</option>
                            <option value="part_time">Part Time</option>
                            <option value="contract">Contract</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Employment Date
                        </label>

                        <input
                            type="date"
                            name="employment_date"
                            class="form-control"
                        >
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            System Role
                        </label>

                        <select
                            name="role_id"
                            class="form-select"
                        >
                            <option value="">No Access</option>

                            <?php foreach ($roles ?? [] as $r): ?>

                                <option value="<?= h($r['id']) ?>">
                                    <?= h($r['name']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>


                <!-- =====================================================
                     EMERGENCY CONTACT
                ====================================================== -->

                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-telephone-plus me-2"></i>
                    Emergency Contact
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">
                            Emergency Contact Name
                        </label>

                        <input
                            type="text"
                            name="emergency_contact_name"
                            class="form-control"
                            maxlength="150"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Emergency Contact Phone
                        </label>

                        <input
                            type="text"
                            name="emergency_contact_phone"
                            class="form-control"
                            maxlength="30"
                        >
                    </div>

                </div>


                <!-- =====================================================
                     ACTIONS
                ====================================================== -->

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="<?= url('/admin/staff') ?>"
                        class="btn btn-light border"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-person-plus me-1"></i>
                        Create Staff Member
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const photoInput = document.getElementById('staffPhoto');
    const preview = document.getElementById('staffPhotoPreview');
    const previewContainer = document.getElementById('staffPhotoPreviewContainer');
    const placeholder = document.getElementById('staffPhotoPlaceholder');
    const removeButton = document.getElementById('removeStaffPhoto');

    const form = document.getElementById('staffCreateForm');

    const password = document.getElementById('staffPassword');
    const passwordConfirm = document.getElementById('staffPasswordConfirm');
    const passwordMessage = document.getElementById('passwordMatchMessage');


    /*
    |--------------------------------------------------------------------------
    | Photo Preview
    |--------------------------------------------------------------------------
    */
    photoInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            return;
        }


        /*
        | Maximum 5 MB
        */
        if (file.size > 5 * 1024 * 1024) {

            alert('Photo is too large. Maximum allowed size is 5 MB.');

            this.value = '';

            previewContainer.classList.add('d-none');
            placeholder.classList.remove('d-none');

            return;
        }


        /*
        | Check image type
        */
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {

            alert('Please select a JPG, PNG, or WebP image.');

            this.value = '';

            previewContainer.classList.add('d-none');
            placeholder.classList.remove('d-none');

            return;
        }


        /*
        | Preview
        */
        const reader = new FileReader();

        reader.onload = function (event) {

            preview.src = event.target.result;

            previewContainer.classList.remove('d-none');
            placeholder.classList.add('d-none');
        };

        reader.readAsDataURL(file);
    });


    /*
    |--------------------------------------------------------------------------
    | Remove Photo
    |--------------------------------------------------------------------------
    */
    removeButton.addEventListener('click', function () {

        photoInput.value = '';

        preview.src = '';

        previewContainer.classList.add('d-none');

        placeholder.classList.remove('d-none');
    });


    /*
    |--------------------------------------------------------------------------
    | Password Confirmation
    |--------------------------------------------------------------------------
    */
    function checkPasswords() {

        if (!passwordConfirm.value) {

            passwordMessage.textContent = '';

            passwordConfirm.classList.remove(
                'is-valid',
                'is-invalid'
            );

            return;
        }


        if (password.value === passwordConfirm.value) {

            passwordMessage.textContent =
                'Passwords match.';

            passwordMessage.className =
                'small mt-1 text-success';

            passwordConfirm.classList.remove(
                'is-invalid'
            );

            passwordConfirm.classList.add(
                'is-valid'
            );

        } else {

            passwordMessage.textContent =
                'Passwords do not match.';

            passwordMessage.className =
                'small mt-1 text-danger';

            passwordConfirm.classList.remove(
                'is-valid'
            );

            passwordConfirm.classList.add(
                'is-invalid'
            );
        }
    }


    password.addEventListener(
        'input',
        checkPasswords
    );

    passwordConfirm.addEventListener(
        'input',
        checkPasswords
    );


    /*
    |--------------------------------------------------------------------------
    | Final Form Validation
    |--------------------------------------------------------------------------
    */
    form.addEventListener('submit', function (event) {

        if (
            password.value !==
            passwordConfirm.value
        ) {

            event.preventDefault();

            alert(
                'Password and Confirm Password must match.'
            );

            passwordConfirm.focus();

            return false;
        }


        const file = photoInput.files[0];

        if (file && file.size > 5 * 1024 * 1024) {

            event.preventDefault();

            alert(
                'Photo is too large. Maximum allowed size is 5 MB.'
            );

            return false;
        }
    });

});
</script>