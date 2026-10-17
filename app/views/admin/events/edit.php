<?php
/**
 * GOF3-light
 * Admin - Edit Event
 */

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
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

/*
 * Generate CSRF token using the same mechanism used elsewhere
 * in GOF3-light.
 */
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

/*
 * Existing event values.
 */
$title       = $event['title'] ?? '';
$description = $event['description'] ?? '';
$eventDate   = $event['event_date'] ?? '';
$endDate     = $event['end_date'] ?? '';
$eventTime   = $event['event_time'] ?? '';
$location    = $event['location'] ?? '';
$status      = $event['status'] ?? 'upcoming';
$isPublic    = (int)($event['is_public'] ?? 0);
$image       = $event['image'] ?? '';

/*
 * Format dates safely for HTML date inputs.
 */
if ($eventDate !== '') {
    $eventDate = date('Y-m-d', strtotime($eventDate));
}

if ($endDate !== '') {
    $endDate = date('Y-m-d', strtotime($endDate));
}

/*
 * Format time safely for HTML time input.
 */
if ($eventTime !== '') {
    $eventTime = date('H:i', strtotime($eventTime));
}
?>

<style>
    .event-edit-card {
        border: 0;
        border-radius: 18px;
        box-shadow: 0 5px 22px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .event-edit-card .card-header {
        background: #fff;
        border-bottom: 1px solid #edf0f2;
        padding: 22px 24px;
    }

    .event-edit-card .card-body {
        padding: 28px 24px;
    }

    .form-section {
        margin-bottom: 30px;
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
        min-height: 44px;
        border-radius: 9px;
        border-color: #dfe3e7;
    }

    textarea.form-control {
        min-height: 140px;
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

    .current-image-box {
        border: 1px solid #e5e8eb;
        border-radius: 14px;
        padding: 14px;
        background: #fafbfc;
    }

    .current-event-image {
        width: 100%;
        max-height: 280px;
        object-fit: cover;
        border-radius: 10px;
        display: block;
    }

    .no-image {
        height: 180px;
        border-radius: 10px;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        flex-direction: column;
        gap: 8px;
    }

    .no-image i {
        font-size: 35px;
    }

    .image-upload-box {
        border: 2px dashed #dfe3e7;
        border-radius: 14px;
        padding: 25px 20px;
        text-align: center;
        background: #fafbfc;
        cursor: pointer;
        transition: .2s ease;
    }

    .image-upload-box:hover {
        border-color: #198754;
        background: #f7fcf9;
    }

    .image-upload-icon {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: rgba(25,135,84,.10);
        color: #198754;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-size: 23px;
    }

    .new-image-preview-wrapper {
        display: none;
        margin-top: 15px;
    }

    .new-image-preview {
        width: 100%;
        max-height: 260px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e5e8eb;
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

    .danger-zone {
        border: 1px solid #f1c2c2;
        background: #fff8f8;
        border-radius: 12px;
        padding: 15px;
    }

    .form-actions {
        border-top: 1px solid #edf0f2;
        padding-top: 22px;
        margin-top: 28px;
    }

    @media (max-width: 575.98px) {
        .event-edit-card .card-header {
            padding: 18px 16px;
        }

        .event-edit-card .card-body {
            padding: 20px 16px;
        }

        .form-actions .btn {
            width: 100%;
        }
    }
</style>


<div class="container-fluid">

    <!-- Page Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">

        <div>
            <h1 class="h3 fw-bold mb-1">
                <i class="bi bi-calendar-event me-2"></i>
                Edit Event
            </h1>

            <p class="text-muted mb-0">
                Update the event information, schedule, visibility or image.
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


    <!-- Main Card -->
    <div class="card event-edit-card">

        <div class="card-header">

            <h5 class="fw-bold mb-1">
                <?= h($title) ?>
            </h5>

            <div class="text-muted small">
                Update the details below and save your changes.
            </div>

        </div>


        <div class="card-body">

            <form
                method="POST"
                action="<?= h(eventUrl('/admin/events/' . (int)$event['id'] . '/update')) ?>"
                enctype="multipart/form-data"
                id="editEventForm"
            >

                <!-- IMPORTANT:
                     Send both common token names so the existing
                     GOF3-light CSRF validator can recognize it.
                -->
                <?php if ($csrf !== ''): ?>

                    <input
                        type="hidden"
                        name="_token"
                        value="<?= h($csrf) ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
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

                        <div class="col-lg-8">

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
                                value="<?= h($title) ?>"
                                required
                            >

                        </div>


                        <div class="col-lg-4">

                            <label
                                for="status"
                                class="form-label"
                            >
                                Status
                            </label>

                            <select
                                name="status"
                                id="status"
                                class="form-select"
                            >

                                <option
                                    value="upcoming"
                                    <?= $status === 'upcoming' ? 'selected' : '' ?>
                                >
                                    Upcoming
                                </option>

                                <option
                                    value="ongoing"
                                    <?= $status === 'ongoing' ? 'selected' : '' ?>
                                >
                                    Ongoing
                                </option>

                                <option
                                    value="completed"
                                    <?= $status === 'completed' ? 'selected' : '' ?>
                                >
                                    Completed
                                </option>

                                <option
                                    value="cancelled"
                                    <?= $status === 'cancelled' ? 'selected' : '' ?>
                                >
                                    Cancelled
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <!-- Schedule -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-calendar3 me-2"></i>
                        Date, Time & Location
                    </div>

                    <div class="row g-3">

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
                                value="<?= h($eventDate) ?>"
                                required
                            >

                        </div>


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
                                value="<?= h($endDate) ?>"
                            >

                            <div class="form-help">
                                Leave empty if this is a one-day event.
                            </div>

                        </div>


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
                                value="<?= h($eventTime) ?>"
                            >

                        </div>


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
                                    value="<?= h($location) ?>"
                                    placeholder="Event location"
                                >

                            </div>

                        </div>

                    </div>

                </div>


                <!-- Description -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-file-text me-2"></i>
                        Description
                    </div>

                    <textarea
                        name="description"
                        id="description"
                        class="form-control"
                        rows="6"
                        placeholder="Describe the event..."
                    ><?= h($description) ?></textarea>

                </div>


                <!-- Visibility -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-eye me-2"></i>
                        Website Visibility
                    </div>

                    <div class="visibility-box">

                        <div class="form-check d-flex align-items-center">

                            <input
                                type="checkbox"
                                class="form-check-input"
                                id="is_public"
                                name="is_public"
                                value="1"
                                <?= $isPublic ? 'checked' : '' ?>
                            >

                            <label
                                class="form-check-label"
                                for="is_public"
                            >
                                Make this event public on the website
                            </label>

                        </div>

                        <div class="form-help ms-4">
                            Uncheck this option to keep the event hidden from the public website.
                        </div>

                    </div>

                </div>


                <!-- Image -->
                <div class="form-section">

                    <div class="form-section-title">
                        <i class="bi bi-image me-2"></i>
                        Event Image
                    </div>


                    <?php if ($image !== ''): ?>

                        <div class="current-image-box mb-3">

                            <div class="small fw-semibold mb-2">
                                Current Image
                            </div>

                            <img
                                src="<?= h(eventUrl('/' . ltrim($image, '/'))) ?>"
                                alt="<?= h($title) ?>"
                                class="current-event-image"
                            >

                        </div>


                        <div class="danger-zone mb-3">

                            <div class="form-check">

                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    name="remove_image"
                                    id="remove_image"
                                    value="1"
                                >

                                <label
                                    class="form-check-label fw-semibold"
                                    for="remove_image"
                                >
                                    Remove current image
                                </label>

                            </div>

                            <div class="small text-muted mt-1">
                                The image will be removed when you save the event.
                            </div>

                        </div>

                    <?php else: ?>

                        <div class="current-image-box mb-3">

                            <div class="no-image">

                                <i class="bi bi-image"></i>

                                <span>
                                    No event image uploaded
                                </span>

                            </div>

                        </div>

                    <?php endif; ?>


                    <label
                        for="image"
                        class="image-upload-box w-100"
                    >

                        <div class="image-upload-icon">
                            <i class="bi bi-cloud-arrow-up"></i>
                        </div>

                        <h6 class="fw-bold mb-1">
                            <?= $image !== '' ? 'Replace Event Image' : 'Upload Event Image' ?>
                        </h6>

                        <div class="text-muted small">
                            Click to select a new image
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
                        class="new-image-preview-wrapper"
                        id="newImagePreviewWrapper"
                    >

                        <div class="small fw-semibold mb-2">
                            New Image Preview
                        </div>

                        <img
                            src=""
                            alt="New event image preview"
                            class="new-image-preview"
                            id="newImagePreview"
                        >

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
                            class="btn btn-success px-4"
                            id="updateButton"
                        >
                            <i class="bi bi-check2-circle me-1"></i>
                            Save Changes
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>


<script>
(function () {

    const form =
        document.getElementById('editEventForm');

    const eventDate =
        document.getElementById('event_date');

    const endDate =
        document.getElementById('end_date');

    const imageInput =
        document.getElementById('image');

    const previewWrapper =
        document.getElementById('newImagePreviewWrapper');

    const preview =
        document.getElementById('newImagePreview');

    const removeImage =
        document.getElementById('remove_image');

    const updateButton =
        document.getElementById('updateButton');


    /*
     * Date validation
     */
    function validateDates() {

        if (
            eventDate &&
            endDate &&
            eventDate.value &&
            endDate.value &&
            endDate.value < eventDate.value
        ) {

            endDate.setCustomValidity(
                'End date cannot be earlier than event date.'
            );

        } else if (endDate) {

            endDate.setCustomValidity('');
        }
    }


    if (eventDate) {
        eventDate.addEventListener(
            'change',
            validateDates
        );
    }

    if (endDate) {
        endDate.addEventListener(
            'change',
            validateDates
        );
    }


    /*
     * New image preview
     */
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
                    function (e) {

                        preview.src =
                            e.target.result;

                        previewWrapper.style.display =
                            'block';
                    };

                reader.readAsDataURL(file);
            }
        );
    }


    /*
     * If user chooses a new image,
     * don't accidentally remove it.
     */
    if (imageInput && removeImage) {

        imageInput.addEventListener(
            'change',
            function () {

                if (this.files.length > 0) {
                    removeImage.checked = false;
                }

            }
        );
    }


    /*
     * Prevent double submission
     */
    if (form) {

        form.addEventListener(
            'submit',
            function (e) {

                validateDates();

                if (!form.checkValidity()) {

                    e.preventDefault();

                    form.reportValidity();

                    return;
                }

                if (updateButton) {

                    updateButton.disabled = true;

                    updateButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
                }

            }
        );
    }

})();
</script>