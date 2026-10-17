<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$staffId = (int)($staff['id'] ?? 0);
$currentPhoto = trim((string)($staff['photo'] ?? ''));

$staffName = trim(
    ($staff['first_name'] ?? '') . ' ' .
    ($staff['last_name'] ?? '')
);

if ($staffName === '') {
    $staffName = $staff['full_name'] ?? 'Staff Member';
}

$currentPhotoUrl = '';
if ($currentPhoto !== '') {
    $currentPhotoUrl = url('/' . ltrim($currentPhoto, '/'));
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            Edit Staff Member: <?= h($staffName) ?>
        </h1>

        <a href="<?= url('/admin/staff/' . $staffId) ?>"
           class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card shadow-sm max-w-800 mx-auto">
        <div class="card-body">

            <form method="POST"
                  action="<?= url('/admin/staff/' . $staffId . '/update') ?>"
                  enctype="multipart/form-data">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= h($_SESSION['csrf_token'] ?? '') ?>">

                <!-- =========================
                     PHOTO
                ========================== -->
                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-camera me-2"></i>
                    Staff Photo
                </h5>

                <div class="row mb-4">
                    <div class="col-md-8">

                        <div class="d-flex align-items-center gap-4">

                            <!-- Current photo -->
                            <div>
                                <?php if ($currentPhotoUrl): ?>

                                    <img src="<?= h($currentPhotoUrl) ?>"
                                         id="currentStaffPhoto"
                                         alt="Current Staff Photo"
                                         style="
                                            width:120px;
                                            height:120px;
                                            object-fit:cover;
                                            border-radius:50%;
                                            border:4px solid #e9ecef;
                                         ">

                                <?php else: ?>

                                    <div id="currentStaffPhoto"
                                         class="d-flex align-items-center justify-content-center bg-light text-secondary"
                                         style="
                                            width:120px;
                                            height:120px;
                                            border-radius:50%;
                                            border:4px solid #e9ecef;
                                            font-size:40px;
                                         ">
                                        <?= h(strtoupper(substr($staffName, 0, 1))) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                            <div class="flex-grow-1">

                                <label for="staffPhoto" class="form-label fw-semibold">
                                    Upload / Change Photo
                                </label>

                                <input type="file"
                                       name="photo"
                                       id="staffPhoto"
                                       class="form-control"
                                       accept="image/jpeg,image/png,image/webp">

                                <small class="text-muted d-block mt-2">
                                    JPG, PNG or WebP. Maximum size: 5 MB.
                                </small>

                                <div id="photoError"
                                     class="text-danger small mt-2"
                                     style="display:none;">
                                </div>

                                <!-- New photo preview -->
                                <div id="photoPreviewContainer"
                                     class="mt-3"
                                     style="display:none;">

                                    <div class="small text-muted mb-2">
                                        New photo preview:
                                    </div>

                                    <img id="photoPreview"
                                         src=""
                                         alt="New Photo Preview"
                                         style="
                                            width:120px;
                                            height:120px;
                                            object-fit:cover;
                                            border-radius:50%;
                                            border:4px solid #0d6efd;
                                         ">

                                    <div class="mt-2">
                                        <button type="button"
                                                id="removePhoto"
                                                class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Remove New Photo
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- =========================
                     PERSONAL INFORMATION
                ========================== -->
                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-person me-2"></i>
                    Personal Information
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">
                            First Name *
                        </label>

                        <input type="text"
                               name="first_name"
                               class="form-control"
                               value="<?= h($staff['first_name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Last Name *
                        </label>

                        <input type="text"
                               name="last_name"
                               class="form-control"
                               value="<?= h($staff['last_name'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Email *
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               value="<?= h($staff['email'] ?? '') ?>"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Phone
                        </label>

                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="<?= h($staff['phone'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Password
                            <small class="text-muted">
                                (Leave blank to keep current)
                            </small>
                        </label>

                        <input type="password"
                               name="password"
                               class="form-control">
                    </div>

                </div>

                <!-- =========================
                     EMPLOYMENT DETAILS
                ========================== -->
                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-briefcase me-2"></i>
                    Employment Details
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">
                            Department
                        </label>

                        <input type="text"
                               name="department"
                               class="form-control"
                               value="<?= h($staff['department'] ?? '') ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            Position
                        </label>

                        <input type="text"
                               name="position"
                               class="form-control"
                               value="<?= h($staff['position'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Employment Type
                        </label>

                        <select name="employment_type"
                                class="form-select">

                            <option value="">
                                Select Type...
                            </option>

                            <option value="full_time"
                                <?= ($staff['employment_type'] ?? '') === 'full_time'
                                    ? 'selected'
                                    : '' ?>>
                                Full Time
                            </option>

                            <option value="part_time"
                                <?= ($staff['employment_type'] ?? '') === 'part_time'
                                    ? 'selected'
                                    : '' ?>>
                                Part Time
                            </option>

                            <option value="contract"
                                <?= ($staff['employment_type'] ?? '') === 'contract'
                                    ? 'selected'
                                    : '' ?>>
                                Contract
                            </option>

                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            Employment Date
                        </label>

                        <input type="date"
                               name="employment_date"
                               class="form-control"
                               value="<?= h($staff['employment_date'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">
                            System Role
                        </label>

                        <select name="role_id"
                                class="form-select">

                            <option value="">
                                No Access
                            </option>

                            <?php foreach ($roles ?? [] as $r): ?>

                                <option value="<?= h($r['id']) ?>"
                                    <?= ($staff['role_id'] ?? '') == $r['id']
                                        ? 'selected'
                                        : '' ?>>

                                    <?= h($r['name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>

                <!-- =========================
                     EMERGENCY CONTACT
                ========================== -->
                <h5 class="card-title text-primary mb-3">
                    <i class="bi bi-telephone-plus me-2"></i>
                    Emergency Contact
                </h5>

                <div class="row g-3 mb-4">

                    <div class="col-md-6">

                        <label class="form-label">
                            Emergency Contact Name
                        </label>

                        <input type="text"
                               name="emergency_contact_name"
                               class="form-control"
                               value="<?= h($staff['emergency_contact_name'] ?? '') ?>">

                    </div>

                    <div class="col-md-6">

                        <label class="form-label">
                            Emergency Contact Phone
                        </label>

                        <input type="text"
                               name="emergency_contact_phone"
                               class="form-control"
                               value="<?= h($staff['emergency_contact_phone'] ?? '') ?>">

                    </div>

                </div>

                <!-- =========================
                     ACTIONS
                ========================== -->
                <div class="d-flex justify-content-end gap-2">

                    <a href="<?= url('/admin/staff/' . $staffId) ?>"
                       class="btn btn-light border">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        Save Changes
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('staffPhoto');
    const preview = document.getElementById('photoPreview');
    const previewContainer = document.getElementById('photoPreviewContainer');
    const removeButton = document.getElementById('removePhoto');
    const errorBox = document.getElementById('photoError');

    if (!input) {
        return;
    }

    input.addEventListener('change', function () {

        errorBox.style.display = 'none';
        errorBox.textContent = '';

        previewContainer.style.display = 'none';
        preview.src = '';

        const file = this.files[0];

        if (!file) {
            return;
        }

        const maxSize = 5 * 1024 * 1024;

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {

            errorBox.textContent =
                'Invalid image type. Please select JPG, PNG or WebP.';

            errorBox.style.display = 'block';

            this.value = '';

            return;
        }

        if (file.size > maxSize) {

            errorBox.textContent =
                'Photo is too large. Maximum allowed size is 5 MB.';

            errorBox.style.display = 'block';

            this.value = '';

            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {

            preview.src = event.target.result;

            previewContainer.style.display = 'block';
        };

        reader.readAsDataURL(file);
    });

    removeButton.addEventListener('click', function () {

        input.value = '';

        preview.src = '';

        previewContainer.style.display = 'none';

        errorBox.style.display = 'none';
        errorBox.textContent = '';
    });

});
</script>