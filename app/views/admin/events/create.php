<?php
/**
 * GOF3-light
 * Admin - Create Event
 */

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('eventUrl')) {
    function eventUrl(string $path = ''): string
    {
        if (function_exists('url')) {
            return url($path);
        }

        return '/GOF3-light/public' . $path;
    }
}

$csrf = '';

if (class_exists('\GOF\Helpers\Csrf')) {
    try {
        $csrf = \GOF\Helpers\Csrf::token();
    } catch (\Throwable $e) {
        $csrf = '';
    }
}

if ($csrf === '') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $csrf = $_SESSION['_csrf']
        ?? $_SESSION['csrf_token']
        ?? '';
}
?>

<style>
    .event-form-page {
        padding-bottom: 40px;
    }

    .event-form-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 5px 22px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .event-form-card .card-header {
        background: #fff;
        border-bottom: 1px solid #edf0f2;
        padding: 22px 24px;
    }

    .event-form-card .card-body {
        padding: 28px 24px;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section:last-child {
        margin-bottom: 0;
    }

    .form-section-title {
        font-size: 15px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 18px;
        padding-bottom: 10px;
        border-bottom: 1px solid #edf0f2;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #343a40;
    }

    .required {
        color: #dc3545;
    }

    .form-control,
    .form-select {
        border-radius: 9px;
        min-height: 44px;
        border-color: #dfe3e7;
    }

    textarea.form-control {
        min-height: 130px;
        resize: vertical;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 .2rem rgba(25,135,84,.10);
    }

    .form-help {
        font-size: 12px;
        color: #6c757d;
        margin-top: 5px;
    }

    .image-upload-box {
        border: 2px dashed #dfe3e7;
        border-radius: 14px;
        padding: 28px 20px;
        text-align: center;
        background: #fafbfc;
        transition: border-color .2s ease, background .2s ease;
        cursor: pointer;
    }

    .image-upload-box:hover {
        border-color: #198754;
        background: #f7fcf9;
    }

    .image-upload-icon {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: rgba(25,135,84,.10);
        color: #198754;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        font-size: 24px;
    }

    .image-preview-wrapper {
        display: none;
        margin-top: 18px;
    }

    .image-preview {
        width: 100%;
        max-height: 280px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .remove-preview {
        margin-top: 10px;
    }

    .visibility-box {
        border: 1px solid #e1e5e9;
        border-radius: 12px;
        padding: 15px 16px;
        background: #fafbfc;
    }

    .visibility-box .form-check-input {
        width: 20px;
        height: 20px;
        margin-top: 0;
    }

    .visibility-box .form-check-label {
        margin-left: 7px;
        font-weight: 600;
        font-size: 13px;
    }

    .form-actions {
        padding-top: 22px;
        border-top: 1px solid #edf0f2;
        margin-top: 28px;
    }

    .character-count {
        font-size: 11px;
        color: #6c757d;
        text-align: right;
        margin-top: 4px;
    }

    @media (max-width: 575.98px) {
        .event-form-card .card-body {
            padding: 20px 16px;
        }

        .event-form-card .card-header {
            padding: 18px 16px;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>

<div class="container-fluid event-form-page">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-calendar-plus me-2"></i>
                Create Event
            </h1>

            <p class="text-muted mb-0">
                Add a new event to the Garko Orphans Foundation calendar.
            </p>
        </div>

        <a
            href="<?= h(eventUrl('/admin/events')) ?>"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Events
        </a>

    </div>

    <!-- Form Card -->
    <div class="card event-form-card">

        <div class="card-header">

            <h5 class="mb-1 fw-bold">
                Event Information
            </h5>

            <div class="text-muted small">
                Fields marked with <span class="required">*</span> are required.
            </div>

        </div>

        <div class="card-body">

            <form
                method="POST"
                action="<?= h(eventUrl('/admin/events')) ?>"
                enctype="multipart/form-data"
                id="createEventForm"
            >

                <?php if ($csrf !== ''): ?>

                    <input
                        type="hidden"
                        name="_token"
                        value="<?= h($csrf) ?>"
                    >

                <?php endif; ?>


                <!-- Basic Information -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-info-circle me-2"></i>
                        Basic Information
                    </div>

                    <div class="row g-3">

                        <!-- Title -->
                        <div class="col-12">

                            <label
                                for="title"
                                class="form-label"
                            >
                                Event Title
                                <span class="required">*</span>
                            </label>

                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control"
                                maxlength="200"
                                required
                                placeholder="e.g. Annual Orphans Support Program"
                            >

                            <div class="character-count">
                                <span id="titleCount">0</span>/200
                            </div>

                        </div>


                        <!-- Description -->
                        <div class="col-12">

                            <label
                                for="description"
                                class="form-label"
                            >
                                Description
                            </label>

                            <textarea
                                name="description"
                                id="description"
                                class="form-control"
                                placeholder="Describe the event, its purpose and what participants should expect..."
                            ></textarea>

                            <div class="form-help">
                                Provide enough information for visitors to understand the event.
                            </div>

                        </div>

                    </div>

                </div>


                <!-- Date & Location -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-calendar3 me-2"></i>
                        Date, Time & Location
                    </div>

                    <div class="row g-3">

                        <!-- Event Date -->
                        <div class="col-md-4">

                            <label
                                for="event_date"
                                class="form-label"
                            >
                                Event Date
                                <span class="required">*</span>
                            </label>

                            <input
                                type="date"
                                name="event_date"
                                id="event_date"
                                class="form-control"
                                required
                            >

                        </div>


                        <!-- End Date -->
                        <div class="col-md-4">

                            <label
                                for="end_date"
                                class="form-label"
                            >
                                End Date
                            </label>

                            <input
                                type="date"
                                name="end_date"
                                id="end_date"
                                class="form-control"
                            >

                            <div class="form-help">
                                Leave empty for a one-day event.
                            </div>

                        </div>


                        <!-- Time -->
                        <div class="col-md-4">

                            <label
                                for="event_time"
                                class="form-label"
                            >
                                Event Time
                            </label>

                            <input
                                type="time"
                                name="event_time"
                                id="event_time"
                                class="form-control"
                            >

                        </div>


                        <!-- Location -->
                        <div class="col-12">

                            <label
                                for="location"
                                class="form-label"
                            >
                                Location
                            </label>

                            <div class="input-group">

                                <span class="input-group-text bg-white">
                                    <i class="bi bi-geo-alt"></i>
                                </span>

                                <input
                                    type="text"
                                    name="location"
                                    id="location"
                                    class="form-control"
                                    maxlength="200"
                                    placeholder="e.g. Garko Community Hall, Kano State"
                                >

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Status & Visibility -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-sliders me-2"></i>
                        Status & Visibility
                    </div>

                    <div class="row g-3">

                        <!-- Status -->
                        <div class="col-md-6">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Event Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                            >

                                <option value="upcoming" selected>
                                    Upcoming
                                </option>

                                <option value="ongoing">
                                    Ongoing
                                </option>

                                <option value="completed">
                                    Completed
                                </option>

                                <option value="cancelled">
                                    Cancelled
                                </option>

                            </select>

                        </div>


                        <!-- Visibility -->
                        <div class="col-md-6">

                            <label class="form-label">
                                Website Visibility
                            </label>

                            <div class="visibility-box">

                                <div class="form-check d-flex align-items-center">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="is_public"
                                        id="is_public"
                                        value="1"
                                        checked
                                    >

                                    <label
                                        class="form-check-label"
                                        for="is_public"
                                    >
                                        Publish this event publicly
                                    </label>

                                </div>

                                <div class="form-help ms-4">
                                    Public events will appear on the organization's website.
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Event Image -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-image me-2"></i>
                        Event Image
                    </div>

                    <label
                        for="image"
                        class="image-upload-box w-100"
                        id="uploadBox"
                    >

                        <div class="image-upload-icon">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>

                        <h6 class="fw-bold mb-1">
                            Upload Event Image
                        </h6>

                        <div class="text-muted small">
                            Click to select an image
                        </div>

                        <div class="form-help">
                            JPG, PNG or WebP · Maximum 5 MB
                        </div>

                    </label>

                    <input
                        type="file"
                        name="image"
                        id="image"
                        class="d-none"
                        accept="image/jpeg,image/png,image/webp"
                    >

                    <div
                        class="image-preview-wrapper"
                        id="imagePreviewWrapper"
                    >

                        <img
                            src=""
                            alt="Event preview"
                            class="image-preview"
                            id="imagePreview"
                        >

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-danger remove-preview"
                            id="removeImage"
                        >
                            <i class="bi bi-trash me-1"></i>
                            Remove Image
                        </button>

                    </div>

                </div>


                <!-- Actions -->
                <div class="form-actions">

                    <div class="d-flex flex-wrap justify-content-end gap-2">

                        <a
                            href="<?= h(eventUrl('/admin/events')) ?>"
                            class="btn btn-outline-secondary"
                        >
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn btn-success"
                            id="submitButton"
                        >
                            <i class="bi bi-check2-circle me-1"></i>
                            Create Event
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
(function () {

    const form = document.getElementById('createEventForm');
    const title = document.getElementById('title');
    const titleCount = document.getElementById('titleCount');

    const eventDate = document.getElementById('event_date');
    const endDate = document.getElementById('end_date');

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewWrapper =
        document.getElementById('imagePreviewWrapper');

    const removeImage =
        document.getElementById('removeImage');

    const submitButton =
        document.getElementById('submitButton');


    /* -------------------------------------------------------
       Title character counter
    ------------------------------------------------------- */

    function updateTitleCount() {

        if (!title || !titleCount) {
            return;
        }

        titleCount.textContent =
            title.value.length;
    }

    if (title) {

        title.addEventListener(
            'input',
            updateTitleCount
        );

        updateTitleCount();
    }


    /* -------------------------------------------------------
       Date validation
    ------------------------------------------------------- */

    if (eventDate && endDate) {

        function validateDates() {

            if (
                eventDate.value &&
                endDate.value &&
                endDate.value < eventDate.value
            ) {
                endDate.setCustomValidity(
                    'End date cannot be earlier than event date.'
                );
            } else {

                endDate.setCustomValidity('');
            }
        }

        eventDate.addEventListener(
            'change',
            validateDates
        );

        endDate.addEventListener(
            'change',
            validateDates
        );
    }


    /* -------------------------------------------------------
       Image preview
    ------------------------------------------------------- */

    if (imageInput) {

        imageInput.addEventListener(
            'change',
            function () {

                const file =
                    this.files && this.files[0];

                if (!file) {
                    return;
                }

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    alert(
                        'Please select a JPG, PNG or WebP image.'
                    );

                    this.value = '';

                    return;
                }

                if (
                    file.size >
                    5 * 1024 * 1024
                ) {

                    alert(
                        'The selected image is larger than 5 MB.'
                    );

                    this.value = '';

                    return;
                }

                const reader =
                    new FileReader();

                reader.onload =
                    function (event) {

                        imagePreview.src =
                            event.target.result;

                        imagePreviewWrapper.style.display =
                            'block';
                    };

                reader.readAsDataURL(file);
            }
        );
    }


    /* -------------------------------------------------------
       Remove image
    ------------------------------------------------------- */

    if (removeImage) {

        removeImage.addEventListener(
            'click',
            function () {

                imageInput.value = '';

                imagePreview.src = '';

                imagePreviewWrapper.style.display =
                    'none';
            }
        );
    }


    /* -------------------------------------------------------
       Form submission
    ------------------------------------------------------- */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                if (!form.checkValidity()) {

                    event.preventDefault();

                    form.reportValidity();

                    return;
                }

                submitButton.disabled = true;

                submitButton.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span> Creating Event...';
            }
        );
    }

})();
</script>