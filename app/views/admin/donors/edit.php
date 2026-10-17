<?php
function h($v) {
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}

$currentPhoto = trim((string)($donor['photo'] ?? ''));
$currentPhotoUrl = '';

if ($currentPhoto !== '') {
    $currentPhotoUrl = url('/' . ltrim($currentPhoto, '/'));
}
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Donor: <?= h($donor['name']) ?></h2>

        <a href="<?= url('/admin/donors/' . $donor['id']) ?>"
           class="btn btn-secondary">
            Back
        </a>
    </div>

    <div class="card">
        <div class="card-body">

            <form method="POST"
                  action="<?= url('/admin/donors/' . $donor['id'] . '/update') ?>"
                  enctype="multipart/form-data">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= h(\GOF\Helpers\Csrf::token()) ?>">


                <!-- ============================= -->
                <!-- DONOR PHOTO -->
                <!-- ============================= -->

                <h5 class="mb-3 border-bottom pb-2">
                    Donor Photo
                </h5>

                <div class="row mb-4">

                    <!-- Current Photo -->
                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Current Photo
                        </label>

                        <div class="border rounded p-3 text-center bg-light">

                            <?php if ($currentPhotoUrl): ?>

                                <img
                                    src="<?= h($currentPhotoUrl) ?>"
                                    alt="<?= h($donor['name']) ?>"
                                    class="img-fluid rounded"
                                    style="
                                        width:180px;
                                        height:200px;
                                        object-fit:cover;
                                    "
                                    onerror="
                                        this.style.display='none';
                                        document.getElementById('currentPhotoFallback').style.display='flex';
                                    "
                                >

                                <div
                                    id="currentPhotoFallback"
                                    class="rounded bg-secondary text-white mx-auto align-items-center justify-content-center"
                                    style="
                                        width:180px;
                                        height:200px;
                                        display:none;
                                        font-size:50px;
                                        font-weight:bold;
                                    "
                                >
                                    <?= h(strtoupper(substr($donor['name'] ?? 'D', 0, 1))) ?>
                                </div>

                            <?php else: ?>

                                <div
                                    class="rounded bg-secondary text-white mx-auto d-flex align-items-center justify-content-center"
                                    style="
                                        width:180px;
                                        height:200px;
                                        font-size:50px;
                                        font-weight:bold;
                                    "
                                >
                                    <?= h(strtoupper(substr($donor['name'] ?? 'D', 0, 1))) ?>
                                </div>

                                <div class="small text-muted mt-2">
                                    No photo uploaded
                                </div>

                            <?php endif; ?>

                        </div>

                    </div>


                    <!-- New Photo -->
                    <div class="col-md-8 mb-3">

                        <label class="form-label">
                            Upload New Photo
                        </label>

                        <input
                            type="file"
                            name="photo"
                            id="donorPhoto"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <div class="form-text">
                            JPG, PNG or WebP. Maximum size: 5 MB.
                            Leave empty to keep the current photo.
                        </div>


                        <!-- New Photo Preview -->

                        <div
                            id="photoPreviewContainer"
                            class="mt-3"
                            style="display:none;"
                        >

                            <div class="d-flex align-items-start gap-3">

                                <div>

                                    <div class="small text-muted mb-2">
                                        New Photo Preview
                                    </div>

                                    <img
                                        id="photoPreview"
                                        src=""
                                        alt="Photo preview"
                                        class="rounded border"
                                        style="
                                            width:180px;
                                            height:200px;
                                            object-fit:cover;
                                        "
                                    >

                                </div>

                                <div class="pt-4">

                                    <button
                                        type="button"
                                        id="removePhoto"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        Remove New Photo
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ============================= -->
                <!-- DONOR INFORMATION -->
                <!-- ============================= -->

                <h5 class="mb-3 border-bottom pb-2">
                    Section 1 - Donor Information
                </h5>

                <div class="row mb-4">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Name *
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="<?= h($donor['name']) ?>"
                            required
                        >

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Organization
                        </label>

                        <input
                            type="text"
                            name="organization"
                            class="form-control"
                            value="<?= h($donor['organization_name']) ?>"
                        >

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Donor Type
                        </label>

                        <select name="donor_type" class="form-select">

                            <?php foreach (
                                ['individual', 'organization', 'government', 'foundation', 'ngo', 'other']
                                as $opt
                            ): ?>

                                <option
                                    value="<?= h($opt) ?>"
                                    <?= ($donor['donor_type'] ?? '') === $opt ? 'selected' : '' ?>
                                >
                                    <?= h(ucfirst($opt)) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>


                <!-- ============================= -->
                <!-- CONTACT -->
                <!-- ============================= -->

                <h5 class="mb-3 border-bottom pb-2">
                    Section 2 - Contact
                </h5>

                <div class="row mb-4">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="<?= h($donor['email']) ?>"
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="<?= h($donor['phone']) ?>"
                        >

                    </div>


                    <div class="col-md-8 mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="2"
                        ><?= h($donor['address']) ?></textarea>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Country
                        </label>

                        <input
                            type="text"
                            name="country"
                            class="form-control"
                            value="<?= h($donor['country']) ?>"
                        >

                    </div>

                </div>


                <!-- ============================= -->
                <!-- META & NOTES -->
                <!-- ============================= -->

                <h5 class="mb-3 border-bottom pb-2">
                    Section 3 - Meta & Notes
                </h5>

                <div class="row mb-4">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option
                                value="active"
                                <?= ($donor['status'] ?? 'active') === 'active' ? 'selected' : '' ?>
                            >
                                Active
                            </option>

                            <option
                                value="inactive"
                                <?= ($donor['status'] ?? 'active') === 'inactive' ? 'selected' : '' ?>
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="col-md-12 mb-3">

                        <label class="form-label">
                            Internal Notes
                        </label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="3"
                        ><?= h($donor['notes']) ?></textarea>

                    </div>

                </div>


                <!-- ============================= -->
                <!-- BUTTONS -->
                <!-- ============================= -->

                <div class="d-flex justify-content-end gap-2">

                    <a
                        href="<?= url('/admin/donors/' . $donor['id']) ?>"
                        class="btn btn-light border"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary px-4"
                    >
                        Update Donor
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const photoInput = document.getElementById('donorPhoto');
    const previewContainer = document.getElementById('photoPreviewContainer');
    const preview = document.getElementById('photoPreview');
    const removeButton = document.getElementById('removePhoto');

    if (!photoInput) {
        return;
    }

    photoInput.addEventListener('change', function () {

        const file = this.files[0];

        if (!file) {
            previewContainer.style.display = 'none';
            return;
        }

        // Maximum 5 MB
        const maxSize = 5 * 1024 * 1024;

        if (file.size > maxSize) {

            alert('Photo is too large. Maximum allowed size is 5 MB.');

            this.value = '';
            previewContainer.style.display = 'none';

            return;
        }

        // Allowed MIME types
        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {

            alert('Invalid photo format. Please select JPG, PNG or WebP.');

            this.value = '';
            previewContainer.style.display = 'none';

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

        photoInput.value = '';
        preview.src = '';
        previewContainer.style.display = 'none';

    });

});
</script>