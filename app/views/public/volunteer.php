<?php
$old = $old ?? [];
$volunteerSuccess = $volunteerSuccess ?? null;

$oldValue = function (string $key, string $fallback = '') use ($old): string {
    return htmlspecialchars(
        (string)($old[$key] ?? $fallback),
        ENT_QUOTES,
        'UTF-8'
    );
};

$selected = function (string $key, string $value) use ($old): string {
    return (($old[$key] ?? '') === $value) ? 'selected' : '';
};

$successReference = '';

if (is_array($volunteerSuccess)) {
    $successReference = (string)($volunteerSuccess['application_ref'] ?? '');
}
?>

<style>
    .volunteer-page-hero {
        background:
            linear-gradient(135deg, rgba(8, 70, 55, .96), rgba(15, 115, 86, .90)),
            url("<?= url('/assets/images/volunteer-bg.jpg') ?>");
        background-size: cover;
        background-position: center;
    }

    .volunteer-benefit-card,
    .opportunity-card,
    .volunteer-form-card,
    .step-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid rgba(0,0,0,.06);
        box-shadow: 0 8px 30px rgba(0,0,0,.06);
    }

    .volunteer-benefit-card {
        padding: 30px;
        height: 100%;
        text-align: center;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .volunteer-benefit-card:hover,
    .opportunity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 14px 35px rgba(0,0,0,.10);
    }

    .volunteer-benefit-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .volunteer-benefit-title {
        font-weight: 700;
        margin-bottom: 12px;
    }

    .volunteer-benefit-description {
        color: #6c757d;
        line-height: 1.7;
        margin-bottom: 0;
    }

    .opportunity-card {
        height: 100%;
        overflow: hidden;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .opportunity-header {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 24px 24px 15px;
    }

    .opportunity-icon {
        width: 52px;
        height: 52px;
        min-width: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
    }

    .opportunity-title {
        font-weight: 700;
        margin: 0 0 7px;
    }

    .opportunity-body {
        padding: 0 24px 24px;
    }

    .opportunity-description {
        color: #6c757d;
        line-height: 1.7;
    }

    .opportunity-details {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        color: #6c757d;
        font-size: .9rem;
    }

    .opportunity-detail {
        display: inline-flex;
        align-items: center;
    }

    .volunteer-form-card {
        padding: 35px;
    }

    .form-section-title {
        font-weight: 700;
        color: #173b32;
        margin-bottom: 20px;
    }

    .form-label {
        color: #263a35;
    }

    .form-control,
    .form-select {
        min-height: 48px;
        border-radius: 10px;
        border-color: #dfe5e2;
    }

    textarea.form-control {
        min-height: auto;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #198754;
        box-shadow: 0 0 0 .2rem rgba(25,135,84,.12);
    }

    .application-note {
        border-radius: 12px;
        padding: 15px 18px;
        background: rgba(25,135,84,.07);
        border: 1px solid rgba(25,135,84,.12);
        color: #31594c;
    }

    .submit-btn {
        min-height: 54px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1rem;
    }

    .step-card {
        padding: 28px 20px;
        height: 100%;
    }

    .step-number {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #198754;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: 700;
        margin: 0 auto 15px;
    }

    .step-title {
        font-weight: 700;
    }

    .step-description {
        color: #6c757d;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .required-star {
        color: #dc3545;
    }

    .character-count {
        font-size: .78rem;
        color: #6c757d;
        text-align: right;
        margin-top: 4px;
    }

    /* =========================================================
       SUCCESS PANEL
    ========================================================= */

    .volunteer-success-wrapper {
        max-width: 900px;
        margin: 45px auto 0;
    }

    .volunteer-success-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid rgba(25,135,84,.15);
        box-shadow: 0 12px 40px rgba(0,0,0,.09);
        overflow: hidden;
    }

    .volunteer-success-top {
        background: linear-gradient(
            135deg,
            #087443,
            #198754
        );
        color: #fff;
        padding: 32px;
        text-align: center;
    }

    .volunteer-success-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: rgba(255,255,255,.18);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 2rem;
    }

    .volunteer-success-top h3 {
        font-weight: 700;
        margin-bottom: 8px;
    }

    .volunteer-success-top p {
        margin: 0;
        color: rgba(255,255,255,.9);
    }

    .volunteer-success-body {
        padding: 32px;
    }

    .reference-box {
        background: #f8f7e9;
        border: 2px dashed #c9a900;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        margin: 25px 0;
    }

    .reference-label {
        display: block;
        color: #766900;
        font-size: .85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .7px;
        margin-bottom: 8px;
    }

    .reference-number {
        display: block;
        color: #665800;
        font-size: 1.65rem;
        font-weight: 800;
        letter-spacing: 1px;
        word-break: break-word;
    }

    .reference-warning {
        margin-top: 10px;
        margin-bottom: 0;
        color: #766900;
        font-size: .9rem;
    }

    .success-instruction {
        background: #f8f9fa;
        border-radius: 13px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .success-instruction-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
    }

    .success-instruction-item:last-child {
        margin-bottom: 0;
    }

    .success-instruction-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 50%;
        background: rgba(25,135,84,.1);
        color: #198754;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .success-actions {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 25px;
    }

    .success-actions .btn {
        min-height: 48px;
        border-radius: 10px;
        font-weight: 600;
        padding: 10px 20px;
    }

    .status-link-note {
        text-align: center;
        color: #6c757d;
        font-size: .88rem;
        margin-top: 15px;
    }

    @media (max-width: 767.98px) {
        .volunteer-form-card {
            padding: 22px;
        }

        .opportunity-header {
            padding: 20px 18px 12px;
        }

        .opportunity-body {
            padding: 0 18px 20px;
        }

        .volunteer-benefit-card {
            padding: 24px 20px;
        }

        .volunteer-success-top,
        .volunteer-success-body {
            padding: 24px 20px;
        }

        .reference-number {
            font-size: 1.35rem;
        }

        .success-actions {
            flex-direction: column;
        }

        .success-actions .btn {
            width: 100%;
        }
    }
</style>


<!-- =========================================================
     HERO
========================================================= -->
<section class="page-hero volunteer-page-hero">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center py-5">

                <h1 class="page-title text-white">
                    Become a Volunteer
                </h1>

                <p class="page-subtitle text-white-50">
                    Join us and help create meaningful change in the lives
                    of children and families.
                </p>

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb justify-content-center mb-0">

                        <li class="breadcrumb-item">
                            <a
                                href="<?= url('/') ?>"
                                class="text-white-50"
                            >
                                Home
                            </a>
                        </li>

                        <li
                            class="breadcrumb-item active text-white"
                            aria-current="page"
                        >
                            Volunteer
                        </li>

                    </ol>

                </nav>

            </div>
        </div>
    </div>
</section>


<!-- =========================================================
     SUCCESS MESSAGE
========================================================= -->
<?php if ($volunteerSuccess && $successReference !== ''): ?>

<section class="py-4">

    <div class="container">

        <div class="volunteer-success-wrapper">

            <div class="volunteer-success-card">

                <div class="volunteer-success-top">

                    <div class="volunteer-success-icon">
                        <i class="bi bi-check-lg"></i>
                    </div>

                    <h3>
                        Application Submitted Successfully
                    </h3>

                    <p>
                        Thank you for your interest in volunteering
                        with Garko Orphans Foundation.
                    </p>

                </div>


                <div class="volunteer-success-body">

                    <div class="text-center">

                        <p class="mb-0">
                            Your application has been received and is now
                            waiting for review by our team.
                        </p>

                    </div>


                    <!-- APPLICATION REFERENCE -->
                    <div class="reference-box">

                        <span class="reference-label">
                            Your Application Reference
                        </span>

                        <span
                            class="reference-number"
                            id="applicationReference"
                        >
                            <?= htmlspecialchars(
                                $successReference,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </span>

                        <p class="reference-warning">
                            <i class="bi bi-exclamation-circle me-1"></i>

                            Please save this reference number.
                            You will need it to check your application status.
                        </p>

                    </div>


                    <!-- WHAT HAPPENS NEXT -->
                    <div class="success-instruction">

                        <div class="success-instruction-item">

                            <div class="success-instruction-icon">
                                <i class="bi bi-1-circle"></i>
                            </div>

                            <div>
                                <strong>
                                    Keep your reference
                                </strong>

                                <div class="text-muted small mt-1">
                                    Save
                                    <strong>
                                        <?= htmlspecialchars(
                                            $successReference,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </strong>
                                    somewhere safe.
                                </div>
                            </div>

                        </div>


                        <div class="success-instruction-item">

                            <div class="success-instruction-icon">
                                <i class="bi bi-2-circle"></i>
                            </div>

                            <div>
                                <strong>
                                    Wait for review
                                </strong>

                                <div class="text-muted small mt-1">
                                    The GOF team will review your application
                                    and make a decision.
                                </div>
                            </div>

                        </div>


                        <div class="success-instruction-item">

                            <div class="success-instruction-icon">
                                <i class="bi bi-3-circle"></i>
                            </div>

                            <div>
                                <strong>
                                    Check your application
                                </strong>

                                <div class="text-muted small mt-1">
                                    Use your reference number and application
                                    email to check the latest decision.
                                </div>
                            </div>

                        </div>

                    </div>


                    <!-- ACTIONS -->
                    <div class="success-actions">

                        <a
                            href="<?= url('/volunteer/status') ?>"
                            class="btn btn-success"
                        >
                            <i class="bi bi-search me-2"></i>
                            Check Application Status
                        </a>


                        <button
                            type="button"
                            class="btn btn-outline-success"
                            id="copyReferenceBtn"
                        >
                            <i class="bi bi-copy me-2"></i>
                            Copy Reference
                        </button>


                        <a
                            href="<?= url('/') ?>"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-house me-2"></i>
                            Return Home
                        </a>

                    </div>


                    <div class="status-link-note">

                        You can return to the status page anytime to check
                        whether your application has been reviewed.

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<?php endif; ?>


<!-- =========================================================
     WHY VOLUNTEER
========================================================= -->
<section class="py-5">

    <div class="container">

        <div class="text-center mb-5">

            <span class="badge bg-success-subtle text-success px-3 py-2 mb-3">
                MAKE A DIFFERENCE
            </span>

            <h2 class="section-title">
                Why Volunteer with GOF?
            </h2>

            <p class="text-muted">
                Your time, knowledge and skills can create lasting
                positive change.
            </p>

        </div>


        <div class="row">

            <div class="col-lg-4 mb-4">

                <div class="volunteer-benefit-card">

                    <div class="volunteer-benefit-icon bg-primary bg-opacity-10">
                        <i class="bi bi-heart text-primary fs-2"></i>
                    </div>

                    <h4 class="volunteer-benefit-title">
                        Make an Impact
                    </h4>

                    <p class="volunteer-benefit-description">
                        Directly contribute to improving the lives of
                        orphans, vulnerable children and families in
                        our community.
                    </p>

                </div>

            </div>


            <div class="col-lg-4 mb-4">

                <div class="volunteer-benefit-card">

                    <div class="volunteer-benefit-icon bg-success bg-opacity-10">
                        <i class="bi bi-trophy text-success fs-2"></i>
                    </div>

                    <h4 class="volunteer-benefit-title">
                        Gain Experience
                    </h4>

                    <p class="volunteer-benefit-description">
                        Develop practical experience in education,
                        community service, healthcare, media and
                        nonprofit activities.
                    </p>

                </div>

            </div>


            <div class="col-lg-4 mb-4">

                <div class="volunteer-benefit-card">

                    <div class="volunteer-benefit-icon bg-warning bg-opacity-10">
                        <i class="bi bi-people text-warning fs-2"></i>
                    </div>

                    <h4 class="volunteer-benefit-title">
                        Join a Community
                    </h4>

                    <p class="volunteer-benefit-description">
                        Connect with people who share your interest
                        in serving others and strengthening the community.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     OPPORTUNITIES
========================================================= -->
<section class="py-5 bg-light">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Volunteer Opportunities
            </h2>

            <p class="text-muted">
                Choose an area that matches your skills and interests.
            </p>

        </div>


        <div class="row">


            <!-- Education -->
            <div class="col-lg-6 mb-4">

                <div class="opportunity-card">

                    <div class="opportunity-header">

                        <div class="opportunity-icon bg-primary">
                            <i class="bi bi-mortarboard text-white"></i>
                        </div>

                        <div>

                            <h4 class="opportunity-title">
                                Education Support
                            </h4>

                            <span class="badge bg-primary">
                                Teaching
                            </span>

                        </div>

                    </div>

                    <div class="opportunity-body">

                        <p class="opportunity-description">
                            Support tutoring, homework assistance and
                            educational activities for children.
                        </p>

                        <div class="opportunity-details">

                            <span class="opportunity-detail">
                                <i class="bi bi-clock me-1"></i>
                                Flexible
                            </span>

                            <span class="opportunity-detail">
                                <i class="bi bi-geo-alt me-1"></i>
                                On-site
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Healthcare -->
            <div class="col-lg-6 mb-4">

                <div class="opportunity-card">

                    <div class="opportunity-header">

                        <div class="opportunity-icon bg-success">
                            <i class="bi bi-heart-pulse text-white"></i>
                        </div>

                        <div>

                            <h4 class="opportunity-title">
                                Healthcare Assistance
                            </h4>

                            <span class="badge bg-success">
                                Medical
                            </span>

                        </div>

                    </div>

                    <div class="opportunity-body">

                        <p class="opportunity-description">
                            Support health education, wellness activities
                            and community health initiatives.
                        </p>

                        <div class="opportunity-details">

                            <span class="opportunity-detail">
                                <i class="bi bi-clock me-1"></i>
                                Weekdays
                            </span>

                            <span class="opportunity-detail">
                                <i class="bi bi-geo-alt me-1"></i>
                                On-site
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Skills -->
            <div class="col-lg-6 mb-4">

                <div class="opportunity-card">

                    <div class="opportunity-header">

                        <div class="opportunity-icon bg-warning">
                            <i class="bi bi-tools text-white"></i>
                        </div>

                        <div>

                            <h4 class="opportunity-title">
                                Skills Training
                            </h4>

                            <span class="badge bg-warning">
                                Vocational
                            </span>

                        </div>

                    </div>

                    <div class="opportunity-body">

                        <p class="opportunity-description">
                            Help teach practical skills such as tailoring,
                            computer literacy, crafts and other useful skills.
                        </p>

                        <div class="opportunity-details">

                            <span class="opportunity-detail">
                                <i class="bi bi-clock me-1"></i>
                                Weekends
                            </span>

                            <span class="opportunity-detail">
                                <i class="bi bi-geo-alt me-1"></i>
                                On-site
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Events -->
            <div class="col-lg-6 mb-4">

                <div class="opportunity-card">

                    <div class="opportunity-header">

                        <div class="opportunity-icon bg-info">
                            <i class="bi bi-calendar-event text-white"></i>
                        </div>

                        <div>

                            <h4 class="opportunity-title">
                                Event Support
                            </h4>

                            <span class="badge bg-info">
                                Events
                            </span>

                        </div>

                    </div>

                    <div class="opportunity-body">

                        <p class="opportunity-description">
                            Assist with events, fundraising activities
                            and community outreach programs.
                        </p>

                        <div class="opportunity-details">

                            <span class="opportunity-detail">
                                <i class="bi bi-clock me-1"></i>
                                As needed
                            </span>

                            <span class="opportunity-detail">
                                <i class="bi bi-geo-alt me-1"></i>
                                Various
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Media -->
            <div class="col-lg-6 mb-4">

                <div class="opportunity-card">

                    <div class="opportunity-header">

                        <div class="opportunity-icon bg-danger">
                            <i class="bi bi-camera text-white"></i>
                        </div>

                        <div>

                            <h4 class="opportunity-title">
                                Media & Communications
                            </h4>

                            <span class="badge bg-danger">
                                Creative
                            </span>

                        </div>

                    </div>

                    <div class="opportunity-body">

                        <p class="opportunity-description">
                            Help with photography, content creation,
                            social media and communication activities.
                        </p>

                        <div class="opportunity-details">

                            <span class="opportunity-detail">
                                <i class="bi bi-clock me-1"></i>
                                Flexible
                            </span>

                            <span class="opportunity-detail">
                                <i class="bi bi-geo-alt me-1"></i>
                                Remote / On-site
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Administration -->
            <div class="col-lg-6 mb-4">

                <div class="opportunity-card">

                    <div class="opportunity-header">

                        <div class="opportunity-icon bg-purple">
                            <i class="bi bi-house text-white"></i>
                        </div>

                        <div>

                            <h4 class="opportunity-title">
                                Administrative Support
                            </h4>

                            <span class="badge bg-purple text-white">
                                Office
                            </span>

                        </div>

                    </div>

                    <div class="opportunity-body">

                        <p class="opportunity-description">
                            Assist with administrative work, records,
                            data entry, filing and general office activities.
                        </p>

                        <div class="opportunity-details">

                            <span class="opportunity-detail">
                                <i class="bi bi-clock me-1"></i>
                                Weekdays
                            </span>

                            <span class="opportunity-detail">
                                <i class="bi bi-geo-alt me-1"></i>
                                On-site
                            </span>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     APPLICATION FORM
========================================================= -->
<section class="py-5" id="volunteer-application">

    <div class="container">

        <div class="text-center mb-5">

            <span class="badge bg-success-subtle text-success px-3 py-2 mb-3">
                VOLUNTEER APPLICATION
            </span>

            <h2 class="section-title">
                Apply to Volunteer
            </h2>

            <p class="text-muted">
                Tell us about yourself and how you would like to contribute.
            </p>

        </div>


        <div class="row">

            <div class="col-lg-9 mx-auto">

                <div class="volunteer-form-card">

                    <div class="application-note mb-4">

                        <div class="d-flex gap-3 align-items-start">

                            <i class="bi bi-info-circle-fill fs-5"></i>

                            <div>

                                <strong>
                                    Before you submit
                                </strong>

                                <div class="small mt-1">
                                    Please provide accurate information.
                                    Your application will be reviewed by
                                    the GOF team before any volunteer placement.
                                </div>

                            </div>

                        </div>

                    </div>


                    <form
                        id="volunteerForm"
                        method="POST"
                        action="<?= url('/volunteer') ?>"
                        novalidate
                    >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars(
                                \GOF\Helpers\Csrf::token(),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                        >


                        <!-- Personal Information -->
                        <h4 class="form-section-title">
                            <i class="bi bi-person me-2"></i>
                            Personal Information
                        </h4>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="firstName"
                                    class="form-label fw-medium"
                                >
                                    First Name
                                    <span class="required-star">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="firstName"
                                    name="firstName"
                                    value="<?= $oldValue('firstName') ?>"
                                    maxlength="100"
                                    autocomplete="given-name"
                                    required
                                >

                            </div>


                            <div class="col-md-6 mb-3">

                                <label
                                    for="lastName"
                                    class="form-label fw-medium"
                                >
                                    Last Name
                                    <span class="required-star">*</span>
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="lastName"
                                    name="lastName"
                                    value="<?= $oldValue('lastName') ?>"
                                    maxlength="100"
                                    autocomplete="family-name"
                                    required
                                >

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="email"
                                    class="form-label fw-medium"
                                >
                                    Email Address
                                    <span class="required-star">*</span>
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?= $oldValue('email') ?>"
                                    maxlength="150"
                                    autocomplete="email"
                                    required
                                >

                            </div>


                            <div class="col-md-6 mb-3">

                                <label
                                    for="phone"
                                    class="form-label fw-medium"
                                >
                                    Phone Number
                                    <span class="required-star">*</span>
                                </label>

                                <input
                                    type="tel"
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                    value="<?= $oldValue('phone') ?>"
                                    maxlength="20"
                                    autocomplete="tel"
                                    required
                                >

                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="age"
                                    class="form-label fw-medium"
                                >
                                    Age
                                </label>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="age"
                                    name="age"
                                    value="<?= $oldValue('age') ?>"
                                    min="13"
                                    max="100"
                                >

                                <div class="form-text">
                                    Optional
                                </div>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label
                                    for="occupation"
                                    class="form-label fw-medium"
                                >
                                    Occupation
                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="occupation"
                                    name="occupation"
                                    value="<?= $oldValue('occupation') ?>"
                                    maxlength="100"
                                    autocomplete="organization-title"
                                    placeholder="e.g. Student, Teacher, Designer"
                                >

                            </div>

                        </div>


                        <div class="mb-4">

                            <label
                                for="address"
                                class="form-label fw-medium"
                            >
                                Address
                            </label>

                            <textarea
                                class="form-control"
                                id="address"
                                name="address"
                                rows="3"
                                maxlength="1000"
                                autocomplete="street-address"
                                placeholder="Enter your residential address"
                            ><?= $oldValue('address') ?></textarea>

                        </div>


                        <!-- Skills -->
                        <h4 class="form-section-title">
                            <i class="bi bi-stars me-2"></i>
                            Skills & Interests
                        </h4>


                        <div class="mb-3">

                            <label
                                for="skills"
                                class="form-label fw-medium"
                            >
                                Skills & Expertise
                            </label>

                            <textarea
                                class="form-control"
                                id="skills"
                                name="skills"
                                rows="3"
                                maxlength="1000"
                                placeholder="e.g. Teaching, Computer Skills, Photography, First Aid, Tailoring..."
                            ><?= $oldValue('skills') ?></textarea>

                            <div class="character-count">
                                <span id="skillsCount">0</span>/1000
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-md-6 mb-3">

                                <label
                                    for="interest"
                                    class="form-label fw-medium"
                                >
                                    Area of Interest
                                    <span class="required-star">*</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="interest"
                                    name="interest"
                                    required
                                >

                                    <option value="">
                                        Select an area
                                    </option>

                                    <option
                                        value="education"
                                        <?= $selected('interest', 'education') ?>
                                    >
                                        Education Support
                                    </option>

                                    <option
                                        value="healthcare"
                                        <?= $selected('interest', 'healthcare') ?>
                                    >
                                        Healthcare Assistance
                                    </option>

                                    <option
                                        value="skills"
                                        <?= $selected('interest', 'skills') ?>
                                    >
                                        Skills Training
                                    </option>

                                    <option
                                        value="events"
                                        <?= $selected('interest', 'events') ?>
                                    >
                                        Event Support
                                    </option>

                                    <option
                                        value="media"
                                        <?= $selected('interest', 'media') ?>
                                    >
                                        Media & Communications
                                    </option>

                                    <option
                                        value="admin"
                                        <?= $selected('interest', 'admin') ?>
                                    >
                                        Administrative Support
                                    </option>

                                    <option
                                        value="other"
                                        <?= $selected('interest', 'other') ?>
                                    >
                                        Other
                                    </option>

                                </select>

                            </div>


                            <div class="col-md-6 mb-3">

                                <label
                                    for="availability"
                                    class="form-label fw-medium"
                                >
                                    Availability
                                    <span class="required-star">*</span>
                                </label>

                                <select
                                    class="form-select"
                                    id="availability"
                                    name="availability"
                                    required
                                >

                                    <option value="">
                                        Select availability
                                    </option>

                                    <option
                                        value="weekdays"
                                        <?= $selected('availability', 'weekdays') ?>
                                    >
                                        Weekdays
                                    </option>

                                    <option
                                        value="weekends"
                                        <?= $selected('availability', 'weekends') ?>
                                    >
                                        Weekends
                                    </option>

                                    <option
                                        value="evenings"
                                        <?= $selected('availability', 'evenings') ?>
                                    >
                                        Evenings
                                    </option>

                                    <option
                                        value="flexible"
                                        <?= $selected('availability', 'flexible') ?>
                                    >
                                        Flexible
                                    </option>

                                </select>

                            </div>

                        </div>


                        <!-- Experience -->
                        <h4 class="form-section-title mt-4">
                            <i class="bi bi-briefcase me-2"></i>
                            Experience
                        </h4>


                        <div class="mb-4">

                            <label
                                for="experience"
                                class="form-label fw-medium"
                            >
                                Previous Volunteer Experience
                            </label>

                            <textarea
                                class="form-control"
                                id="experience"
                                name="experience"
                                rows="4"
                                maxlength="2000"
                                placeholder="Tell us about previous volunteer work, community service or relevant experience."
                            ><?= $oldValue('experience') ?></textarea>

                        </div>


                        <!-- Motivation -->
                        <div class="mb-4">

                            <label
                                for="motivation"
                                class="form-label fw-medium"
                            >
                                Why do you want to volunteer?
                                <span class="required-star">*</span>
                            </label>

                            <textarea
                                class="form-control"
                                id="motivation"
                                name="motivation"
                                rows="5"
                                maxlength="3000"
                                placeholder="Tell us what motivates you to volunteer with Garko Orphans Foundation..."
                                required
                            ><?= $oldValue('motivation') ?></textarea>

                            <div class="character-count">
                                <span id="motivationCount">0</span>/3000
                            </div>

                        </div>


                        <!-- Agreement -->
                        <div class="mb-4">

                            <div class="form-check">

                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="agreement"
                                    required
                                >

                                <label
                                    class="form-check-label"
                                    for="agreement"
                                >
                                    I confirm that the information provided
                                    is accurate and I understand that my
                                    information may be used by GOF for
                                    volunteer coordination and application
                                    review.
                                    <span class="required-star">*</span>
                                </label>

                            </div>

                        </div>


                        <!-- Submit -->
                        <button
                            type="submit"
                            id="submitVolunteer"
                            class="btn btn-success btn-lg w-100 submit-btn"
                        >
                            <i class="bi bi-send me-2"></i>
                            Submit Volunteer Application
                        </button>


                        <div class="text-center mt-3">

                            <small class="text-muted">
                                <i class="bi bi-shield-check me-1"></i>
                                Your application will be securely submitted
                                for review.
                            </small>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     WHAT HAPPENS NEXT
========================================================= -->
<section class="py-5 bg-light">

    <div class="container">

        <div class="text-center mb-5">

            <span class="badge bg-success-subtle text-success px-3 py-2 mb-3">
                APPLICATION PROCESS
            </span>

            <h2 class="section-title">
                What Happens Next?
            </h2>

        </div>


        <div class="row">

            <div class="col-md-3 mb-4">

                <div class="step-card text-center">

                    <div class="step-number">
                        1
                    </div>

                    <h5 class="step-title">
                        Submit Application
                    </h5>

                    <p class="step-description">
                        Complete and submit your volunteer application.
                    </p>

                </div>

            </div>


            <div class="col-md-3 mb-4">

                <div class="step-card text-center">

                    <div class="step-number">
                        2
                    </div>

                    <h5 class="step-title">
                        Application Review
                    </h5>

                    <p class="step-description">
                        The GOF team reviews your application and interests.
                    </p>

                </div>

            </div>


            <div class="col-md-3 mb-4">

                <div class="step-card text-center">

                    <div class="step-number">
                        3
                    </div>

                    <h5 class="step-title">
                        Contact
                    </h5>

                    <p class="step-description">
                        Our team may contact you to discuss your application.
                    </p>

                </div>

            </div>


            <div class="col-md-3 mb-4">

                <div class="step-card text-center">

                    <div class="step-number">
                        4
                    </div>

                    <h5 class="step-title">
                        Orientation
                    </h5>

                    <p class="step-description">
                        Approved volunteers receive further information
                        about participation and orientation.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('volunteerForm');
    const submitButton = document.getElementById('submitVolunteer');

    const skills = document.getElementById('skills');
    const motivation = document.getElementById('motivation');

    const skillsCount = document.getElementById('skillsCount');
    const motivationCount = document.getElementById('motivationCount');


    function updateCounter(field, counter) {

        if (!field || !counter) {
            return;
        }

        counter.textContent = field.value.length;
    }


    updateCounter(skills, skillsCount);
    updateCounter(motivation, motivationCount);


    skills?.addEventListener('input', function () {
        updateCounter(skills, skillsCount);
    });


    motivation?.addEventListener('input', function () {
        updateCounter(motivation, motivationCount);
    });


    form?.addEventListener('submit', function (event) {

        if (!form.checkValidity()) {

            event.preventDefault();
            event.stopPropagation();

            form.classList.add('was-validated');

            const firstInvalid =
                form.querySelector(':invalid');

            if (firstInvalid) {
                firstInvalid.focus();
            }

            return;
        }


        if (submitButton) {

            submitButton.disabled = true;

            submitButton.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2" ' +
                'role="status" aria-hidden="true"></span>' +
                'Submitting Application...';

        }

    });


    /*
     * Copy application reference
     */
    const copyButton =
        document.getElementById('copyReferenceBtn');

    const referenceElement =
        document.getElementById('applicationReference');


    copyButton?.addEventListener('click', async function () {

        if (!referenceElement) {
            return;
        }

        const reference =
            referenceElement.textContent.trim();

        if (!reference) {
            return;
        }


        try {

            await navigator.clipboard.writeText(reference);

            const original =
                copyButton.innerHTML;

            copyButton.innerHTML =
                '<i class="bi bi-check-lg me-2"></i>' +
                'Copied!';

            copyButton.classList.remove(
                'btn-outline-success'
            );

            copyButton.classList.add(
                'btn-success'
            );


            setTimeout(function () {

                copyButton.innerHTML =
                    original;

                copyButton.classList.remove(
                    'btn-success'
                );

                copyButton.classList.add(
                    'btn-outline-success'
                );

            }, 1800);


        } catch (error) {

            /*
             * Fallback for browsers that block
             * navigator.clipboard.
             */
            const textArea =
                document.createElement('textarea');

            textArea.value = reference;

            textArea.style.position = 'fixed';
            textArea.style.opacity = '0';

            document.body.appendChild(textArea);

            textArea.select();

            try {
                document.execCommand('copy');
            } catch (e) {
                // Clipboard unavailable.
            }

            document.body.removeChild(textArea);

        }

    });

});
</script>