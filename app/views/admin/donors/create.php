<?php
function h($v)
{
    return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Register New Donor</h2>

        <a href="<?= url('/admin/donors') ?>"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Back
        </a>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form method="POST"
                  action="<?= url('/admin/donors') ?>"
                  enctype="multipart/form-data">

                <input type="hidden"
                       name="csrf_token"
                       value="<?= h($_SESSION['csrf_token'] ?? '') ?>">


                <!-- ==================================================
                     SECTION 1 - DONOR INFORMATION
                =================================================== -->

                <h5 class="mb-3 border-bottom pb-2 text-primary">
                    <i class="bi bi-person me-2"></i>
                    Section 1 - Donor Information
                </h5>

                <div class="row mb-4">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Name *
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               required>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Organization
                        </label>

                        <input type="text"
                               name="organization"
                               class="form-control">

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Donor Type
                        </label>

                        <select name="donor_type"
                                class="form-select">

                            <option value="individual">
                                Individual
                            </option>

                            <option value="organization">
                                Organization
                            </option>

                            <option value="government">
                                Government
                            </option>

                            <option value="foundation">
                                Foundation
                            </option>

                            <option value="ngo">
                                NGO
                            </option>

                            <option value="other">
                                Other
                            </option>

                        </select>

                    </div>

                </div>


                <!-- ==================================================
                     PHOTO
                =================================================== -->

                <h5 class="mb-3 border-bottom pb-2 text-primary">
                    <i class="bi bi-camera me-2"></i>
                    Donor Photo
                </h5>

                <div class="row mb-4">

                    <div class="col-md-8">

                        <div class="d-flex align-items-start gap-4">

                            <!-- Preview -->
                            <div>

                                <div id="photoPreviewContainer"
                                     style="display:none;">

                                    <img id="photoPreview"
                                         src=""
                                         alt="Donor Photo Preview"
                                         style="
                                            width:130px;
                                            height:130px;
                                            object-fit:cover;
                                            border-radius:50%;
                                            border:4px solid #0d6efd;
                                         ">

                                </div>


                                <!-- Default placeholder -->

                                <div id="photoPlaceholder"
                                     class="d-flex align-items-center justify-content-center bg-light text-secondary"
                                     style="
                                        width:130px;
                                        height:130px;
                                        border-radius:50%;
                                        border:4px solid #e9ecef;
                                        font-size:42px;
                                     ">

                                    <i class="bi bi-person"></i>

                                </div>

                            </div>


                            <div class="flex-grow-1">

                                <label for="donorPhoto"
                                       class="form-label fw-semibold">

                                    Upload Donor Photo

                                </label>

                                <input type="file"
                                       name="photo"
                                       id="donorPhoto"
                                       class="form-control"
                                       accept="image/jpeg,image/png,image/webp">

                                <small class="text-muted d-block mt-2">

                                    JPG, PNG or WebP.
                                    Maximum size: 5 MB.

                                </small>


                                <div id="photoError"
                                     class="text-danger small mt-2"
                                     style="display:none;">
                                </div>


                                <button type="button"
                                        id="removePhoto"
                                        class="btn btn-sm btn-outline-danger mt-2"
                                        style="display:none;">

                                    <i class="bi bi-x-circle me-1"></i>
                                    Remove Photo

                                </button>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ==================================================
                     SECTION 2 - CONTACT
                =================================================== -->

                <h5 class="mb-3 border-bottom pb-2 text-primary">

                    <i class="bi bi-telephone me-2"></i>
                    Section 2 - Contact

                </h5>

                <div class="row mb-4">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input type="email"
                               name="email"
                               class="form-control">

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Phone
                        </label>

                        <input type="text"
                               name="phone"
                               class="form-control">

                    </div>


                    <div class="col-md-8 mb-3">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea name="address"
                                  class="form-control"
                                  rows="2"></textarea>

                    </div>


                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Country
                        </label>

                        <input type="text"
                               name="country"
                               class="form-control"
                               value="Nigeria">

                    </div>

                </div>


                <!-- ==================================================
                     SECTION 3 - NOTES
                =================================================== -->

                <h5 class="mb-3 border-bottom pb-2 text-primary">

                    <i class="bi bi-journal-text me-2"></i>
                    Section 3 - Notes

                </h5>

                <div class="mb-4">

                    <label class="form-label">
                        Internal Notes
                    </label>

                    <textarea name="notes"
                              class="form-control"
                              rows="3"></textarea>

                </div>


                <!-- ==================================================
                     ACTIONS
                =================================================== -->

                <div class="d-flex justify-content-end gap-2">

                    <a href="<?= url('/admin/donors') ?>"
                       class="btn btn-light border">

                        Cancel

                    </a>

                    <button type="submit"
                            class="btn btn-primary px-4">

                        <i class="bi bi-person-plus me-1"></i>
                        Register Donor

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- ==========================================================
     PHOTO PREVIEW
=========================================================== -->

<script>
document.addEventListener('DOMContentLoaded', function () {

    const input =
        document.getElementById('donorPhoto');

    const preview =
        document.getElementById('photoPreview');

    const previewContainer =
        document.getElementById('photoPreviewContainer');

    const placeholder =
        document.getElementById('photoPlaceholder');

    const removeButton =
        document.getElementById('removePhoto');

    const errorBox =
        document.getElementById('photoError');


    if (!input) {
        return;
    }


    input.addEventListener('change', function () {

        errorBox.style.display = 'none';
        errorBox.textContent = '';

        const file = this.files[0];

        if (!file) {
            return;
        }


        /*
         * Maximum 5 MB.
         */
        const maxSize =
            5 * 1024 * 1024;


        /*
         * Allowed image types.
         */
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


        /*
         * Show preview.
         */
        const reader =
            new FileReader();


        reader.onload = function (event) {

            preview.src =
                event.target.result;

            previewContainer.style.display =
                'block';

            placeholder.style.display =
                'none';

            removeButton.style.display =
                'inline-block';
        };


        reader.readAsDataURL(file);
    });


    /*
     * Remove selected photo.
     */
    removeButton.addEventListener(
        'click',
        function () {

            input.value = '';

            preview.src = '';

            previewContainer.style.display =
                'none';

            placeholder.style.display =
                'flex';

            removeButton.style.display =
                'none';

            errorBox.style.display =
                'none';

            errorBox.textContent = '';
        }
    );

});
</script>