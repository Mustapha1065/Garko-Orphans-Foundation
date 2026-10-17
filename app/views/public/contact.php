<?php
/**
 * GOF - Premium Public Contact Page
 *
 * Functional submission:
 * POST /contact
 * handled by PublicController@contactSubmit
 */

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$success = $_SESSION['contact_success'] ?? null;
$error   = $_SESSION['contact_error'] ?? null;

unset($_SESSION['contact_success'], $_SESSION['contact_error']);

$csrfToken = '';

try {
    if (class_exists('\GOF\Helpers\Csrf')) {
        $csrfToken = \GOF\Helpers\Csrf::token();
    }
} catch (\Throwable $e) {
    $csrfToken = $_SESSION['csrf_token']
        ?? $_SESSION['_csrf']
        ?? $_SESSION['csrf']
        ?? '';
}
?>

<style>
/* =========================================================
   GOF PREMIUM CONTACT PAGE
   ========================================================= */

.gof-contact-page {
    --gof-gold: #c9a227;
    --gof-gold-light: #e5c968;
    --gof-navy: #0d1829;
    --gof-navy-2: #172840;
    --gof-ink: #172033;
    --gof-muted: #6d7686;
    --gof-soft: #f5f7fa;
    --gof-border: #e5e9ef;
    background: #fff;
}

/* =========================================================
   PREMIUM HERO
   ========================================================= */

.gof-contact-hero {
    position: relative;
    overflow: hidden;
    min-height: 440px;
    display: flex;
    align-items: center;
    color: #fff;

    background:
        radial-gradient(
            circle at 85% 25%,
            rgba(229,201,104,.22),
            transparent 30%
        ),
        radial-gradient(
            circle at 8% 90%,
            rgba(201,162,39,.13),
            transparent 30%
        ),
        linear-gradient(
            135deg,
            #0b1627 0%,
            #13243a 55%,
            #1d3049 100%
        );
}

.gof-contact-hero::before {
    content: "";
    position: absolute;
    width: 480px;
    height: 480px;
    right: -170px;
    top: -220px;
    border: 1px solid rgba(229,201,104,.22);
    border-radius: 50%;
}

.gof-contact-hero::after {
    content: "";
    position: absolute;
    width: 330px;
    height: 330px;
    right: 35px;
    bottom: -220px;
    border: 1px solid rgba(255,255,255,.08);
    border-radius: 50%;
}

.gof-contact-hero-inner {
    position: relative;
    z-index: 2;
}

.gof-contact-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 8px 15px;
    border: 1px solid rgba(229,201,104,.38);
    border-radius: 999px;
    background: rgba(255,255,255,.055);
    color: #f0da8a;
    font-size: .76rem;
    font-weight: 800;
    letter-spacing: .13em;
    text-transform: uppercase;
}

.gof-contact-eyebrow::before {
    content: "";
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--gof-gold-light);
    box-shadow: 0 0 0 5px rgba(229,201,104,.10);
}

.gof-contact-hero h1 {
    max-width: 750px;
    margin: 20px 0 16px;
    font-size: clamp(2.7rem, 6vw, 5.1rem);
    line-height: .98;
    letter-spacing: -.05em;
    font-weight: 850;
}

.gof-contact-hero h1 span {
    color: var(--gof-gold-light);
}

.gof-contact-accent {
    width: 76px;
    height: 4px;
    border-radius: 100px;
    background: linear-gradient(
        90deg,
        var(--gof-gold-light),
        transparent
    );
}

.gof-contact-hero-copy {
    max-width: 650px;
    margin-top: 25px;
    color: rgba(255,255,255,.73);
    font-size: 1.08rem;
    line-height: 1.8;
}

.gof-contact-breadcrumb {
    margin-top: 28px;
}

.gof-contact-breadcrumb a,
.gof-contact-breadcrumb span {
    color: rgba(255,255,255,.58);
    font-size: .9rem;
    text-decoration: none;
}

.gof-contact-breadcrumb .active {
    color: #fff;
}

.gof-contact-hero-card {
    width: 195px;
    height: 195px;
    margin-left: auto;
    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 44px;
    transform: rotate(5deg);

    background:
        linear-gradient(
            145deg,
            rgba(255,255,255,.14),
            rgba(255,255,255,.035)
        );

    border: 1px solid rgba(255,255,255,.16);

    box-shadow:
        0 35px 75px rgba(0,0,0,.25);

    backdrop-filter: blur(14px);
}

.gof-contact-hero-card i {
    font-size: 5.6rem;
    color: var(--gof-gold-light);
    transform: rotate(-5deg);
}

@media (max-width: 991.98px) {
    .gof-contact-hero {
        min-height: 390px;
    }

    .gof-contact-hero-card {
        display: none;
    }
}

/* =========================================================
   MAIN SECTION
   ========================================================= */

.gof-contact-main {
    padding: 85px 0;
    background: #fff;
}

.gof-section-label {
    margin-bottom: 10px;
    color: #a17e13;
    font-size: .76rem;
    font-weight: 800;
    letter-spacing: .14em;
    text-transform: uppercase;
}

.gof-section-title {
    color: var(--gof-ink);
    font-size: clamp(2rem, 4vw, 3rem);
    line-height: 1.08;
    font-weight: 850;
    letter-spacing: -.04em;
}

.gof-section-description {
    max-width: 670px;
    color: var(--gof-muted);
    line-height: 1.8;
}

/* =========================================================
   CONTACT INFORMATION CARDS
   ========================================================= */

.gof-contact-info-card {
    height: 100%;
    padding: 30px;

    border: 1px solid var(--gof-border);
    border-radius: 23px;

    background: #fff;

    box-shadow:
        0 12px 35px rgba(20,32,52,.055);

    transition:
        transform .25s ease,
        box-shadow .25s ease,
        border-color .25s ease;
}

.gof-contact-info-card:hover {
    transform: translateY(-6px);

    border-color: rgba(201,162,39,.35);

    box-shadow:
        0 22px 50px rgba(20,32,52,.11);
}

.gof-contact-icon {
    width: 56px;
    height: 56px;

    display: flex;
    align-items: center;
    justify-content: center;

    margin-bottom: 20px;

    border-radius: 17px;

    background: #fbf7e8;
    color: #a17e13;
}

.gof-contact-icon i {
    font-size: 1.5rem;
}

.gof-contact-info-card h4 {
    margin-bottom: 9px;

    color: var(--gof-ink);

    font-size: 1.08rem;
    font-weight: 800;
}

.gof-contact-info-card p {
    margin: 0;

    color: var(--gof-muted);
    line-height: 1.75;
}

/* =========================================================
   FORM
   ========================================================= */

.gof-form-panel {
    margin-top: 52px;
    padding: clamp(25px, 4vw, 45px);

    border: 1px solid var(--gof-border);
    border-radius: 28px;

    background: #fff;

    box-shadow:
        0 20px 60px rgba(20,32,52,.08);
}

.gof-form-panel h3 {
    color: var(--gof-ink);
    font-weight: 850;
    letter-spacing: -.025em;
}

.gof-form-label {
    display: block;

    margin-bottom: 8px;

    color: #30394a;

    font-size: .88rem;
    font-weight: 750;
}

.gof-form-control {
    min-height: 53px;

    padding: 13px 15px;

    border: 1px solid #dfe4eb !important;
    border-radius: 13px !important;

    box-shadow: none !important;

    transition:
        border-color .2s ease,
        box-shadow .2s ease;
}

textarea.gof-form-control {
    min-height: 165px;
    resize: vertical;
}

.gof-form-control:focus {
    border-color: var(--gof-gold) !important;

    box-shadow:
        0 0 0 4px rgba(201,162,39,.11) !important;
}

.gof-submit-btn {
    min-height: 55px;

    padding: 0 28px;

    border: 0;
    border-radius: 13px;

    color: #fff;

    background:
        linear-gradient(
            135deg,
            #ae8615,
            #d4af3f
        );

    font-weight: 800;

    box-shadow:
        0 12px 27px rgba(174,134,21,.23);

    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.gof-submit-btn:hover {
    color: #fff;

    transform: translateY(-2px);

    box-shadow:
        0 17px 32px rgba(174,134,21,.30);
}

.gof-submit-btn:disabled {
    opacity: .7;
    transform: none;
}

/* =========================================================
   SIDEBAR
   ========================================================= */

.gof-side-panel {
    margin-top: 52px;
    padding: 32px;

    border-radius: 27px;

    color: #fff;

    background:
        linear-gradient(
            145deg,
            #0e192b,
            #1b2d46
        );

    box-shadow:
        0 22px 55px rgba(14,25,43,.18);
}

.gof-side-panel h3 {
    font-weight: 850;
}

.gof-side-panel p {
    color: rgba(255,255,255,.68);
    line-height: 1.75;
}

.gof-hours-row {
    display: flex;
    justify-content: space-between;
    gap: 20px;

    padding: 15px 0;

    border-bottom: 1px solid rgba(255,255,255,.09);
}

.gof-hours-row:last-child {
    border-bottom: 0;
}

.gof-hours-row span:first-child {
    color: rgba(255,255,255,.70);
}

.gof-hours-row span:last-child {
    color: #f0d878;
    font-weight: 750;
    text-align: right;
}

.gof-quick-link {
    display: flex;
    align-items: center;
    gap: 12px;

    padding: 13px 0;

    border-bottom: 1px solid rgba(255,255,255,.08);

    color: rgba(255,255,255,.78);
    text-decoration: none;

    transition: color .2s ease;
}

.gof-quick-link:hover {
    color: #f0d878;
}

.gof-quick-link i {
    color: #e2c45e;
}

/* =========================================================
   ALERTS
   ========================================================= */

.gof-alert {
    border: 0;
    border-radius: 16px;

    padding: 18px 20px;

    box-shadow:
        0 10px 30px rgba(0,0,0,.055);
}

/* =========================================================
   LOCATION
   ========================================================= */

.gof-location {
    padding: 80px 0;
    background: var(--gof-soft);
}

.gof-location-card {
    overflow: hidden;

    border: 1px solid var(--gof-border);
    border-radius: 28px;

    background: #fff;

    box-shadow:
        0 15px 45px rgba(20,32,52,.07);
}

.gof-location-visual {
    min-height: 320px;

    display: flex;
    align-items: center;
    justify-content: center;

    background:
        radial-gradient(
            circle at 50% 35%,
            rgba(201,162,39,.18),
            transparent 27%
        ),
        linear-gradient(
            145deg,
            #101b2d,
            #1c3049
        );
}

.gof-location-visual i {
    font-size: 6rem;
    color: #e1c25b;
}

.gof-location-content {
    padding: 42px;
}

.gof-location-content h3 {
    color: var(--gof-ink);
    font-weight: 850;
}

.gof-location-content p {
    color: var(--gof-muted);
    line-height: 1.8;
}

/* =========================================================
   FINAL CTA
   ========================================================= */

.gof-contact-cta {
    padding: 75px 0;
    background: #fff;
}

.gof-contact-cta-box {
    position: relative;
    overflow: hidden;

    padding: 52px;

    border-radius: 29px;

    color: #fff;

    background:
        linear-gradient(
            135deg,
            #101b2d,
            #20344f
        );
}

.gof-contact-cta-box::after {
    content: "";

    position: absolute;

    width: 260px;
    height: 260px;

    right: -90px;
    top: -110px;

    border: 1px solid rgba(229,201,104,.18);
    border-radius: 50%;
}

.gof-contact-cta-box h2 {
    position: relative;
    z-index: 2;

    font-weight: 850;
    letter-spacing: -.035em;
}

.gof-contact-cta-box p {
    position: relative;
    z-index: 2;

    color: rgba(255,255,255,.70);
}

.gof-cta-btn {
    position: relative;
    z-index: 2;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    min-height: 51px;

    padding: 0 23px;

    border-radius: 12px;

    color: #182337;
    background: #e1c25b;

    font-weight: 800;
    text-decoration: none;
}

.gof-cta-btn:hover {
    color: #182337;
    background: #f0d878;
}

/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    .gof-contact-main,
    .gof-location,
    .gof-contact-cta {
        padding: 55px 0;
    }

    .gof-form-panel,
    .gof-side-panel {
        margin-top: 32px;
    }

    .gof-contact-cta-box {
        padding: 33px 25px;
    }

    .gof-contact-hero h1 {
        font-size: 2.75rem;
    }

    .gof-contact-hero-copy {
        font-size: 1rem;
    }

    .gof-location-content {
        padding: 30px;
    }
}
</style>


<div class="gof-contact-page">

    <!-- =====================================================
         PREMIUM HERO
         ===================================================== -->
    <section class="gof-contact-hero">

        <div class="container">

            <div class="row align-items-center gof-contact-hero-inner">

                <div class="col-lg-8">

                    <div class="gof-contact-eyebrow">
                        Garko Orphans Foundation
                    </div>

                    <h1>
                        Let's make a<br>
                        <span>difference together.</span>
                    </h1>

                    <div class="gof-contact-accent"></div>

                    <p class="gof-contact-hero-copy">
                        Whether you have a question, want to support our work,
                        volunteer your time, or explore a partnership,
                        our team is ready to hear from you.
                    </p>

                    <div class="gof-contact-breadcrumb">
                        <a href="<?= h(url('/')) ?>">
                            Home
                        </a>

                        <span class="mx-2">/</span>

                        <span class="active">
                            Contact Us
                        </span>
                    </div>

                </div>


                <div class="col-lg-4">

                    <div class="gof-contact-hero-card">
                        <i class="bi bi-chat-heart"></i>
                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         MAIN CONTACT AREA
         ===================================================== -->
    <section class="gof-contact-main">

        <div class="container">

            <div class="text-center mb-5">

                <div class="gof-section-label">
                    Get in touch
                </div>

                <h2 class="gof-section-title mb-3">
                    We're here to listen
                </h2>

                <p class="gof-section-description mx-auto">
                    Connect with Garko Orphans Foundation through the channel
                    that works best for you. We value every question,
                    suggestion and opportunity to collaborate.
                </p>

            </div>


            <!-- CONTACT INFORMATION -->
            <div class="row g-4">

                <div class="col-md-4">

                    <div class="gof-contact-info-card">

                        <div class="gof-contact-icon">
                            <i class="bi bi-geo-alt"></i>
                        </div>

                        <h4>
                            Visit Our Office
                        </h4>

                        <p>
                            NO.1 Central Mosque<br>
                            Garko, Kano State<br>
                            Nigeria
                        </p>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="gof-contact-info-card">

                        <div class="gof-contact-icon">
                            <i class="bi bi-telephone"></i>
                        </div>

                        <h4>
                            Call Our Team
                        </h4>

                        <p>
                            +234 123 456 7890<br>
                            +234 098 765 4321<br>
                            Mon-Fri, 9am-5pm
                        </p>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="gof-contact-info-card">

                        <div class="gof-contact-icon">
                            <i class="bi bi-envelope"></i>
                        </div>

                        <h4>
                            Email Us
                        </h4>

                        <p>
                            info@gof.org.ng<br>
                            support@gof.org.ng<br>
                            donations@gof.org.ng
                        </p>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 FORM + SIDEBAR
                 ================================================= -->
            <div class="row g-5">

                <!-- FORM -->
                <div class="col-lg-8">

                    <div class="gof-form-panel">

                        <div class="mb-4">

                            <div class="gof-section-label">
                                Send a message
                            </div>

                            <h3 class="mb-2">
                                How can we help?
                            </h3>

                            <p class="text-muted mb-0">
                                Complete the form below and our team will
                                review your message.
                            </p>

                        </div>


                        <?php if (!empty($success)): ?>

                            <div class="alert alert-success gof-alert mb-4">

                                <div class="d-flex gap-3">

                                    <i class="bi bi-check-circle-fill fs-4"></i>

                                    <div>

                                        <strong>
                                            Message sent successfully.
                                        </strong>

                                        <div class="small mt-1">
                                            <?= h($success) ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if (!empty($error)): ?>

                            <div class="alert alert-danger gof-alert mb-4">

                                <div class="d-flex gap-3">

                                    <i class="bi bi-exclamation-circle-fill fs-4"></i>

                                    <div>

                                        <strong>
                                            We couldn't send your message.
                                        </strong>

                                        <div class="small mt-1">
                                            <?= h($error) ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <!-- REAL BACKEND FORM -->
                        <form id="contactForm"
                              method="POST"
                              action="<?= h(url('/contact')) ?>"
                              novalidate>

                            <input type="hidden"
                                   name="_token"
                                   value="<?= h($csrfToken) ?>">


                            <div class="row">

                                <!-- NAME -->
                                <div class="col-md-6 mb-4">

                                    <label for="name"
                                           class="gof-form-label">
                                        Full Name *
                                    </label>

                                    <input type="text"
                                           class="form-control gof-form-control"
                                           id="name"
                                           name="name"
                                           maxlength="150"
                                           autocomplete="name"
                                           placeholder="Your full name"
                                           required>

                                </div>


                                <!-- EMAIL -->
                                <div class="col-md-6 mb-4">

                                    <label for="email"
                                           class="gof-form-label">
                                        Email Address *
                                    </label>

                                    <input type="email"
                                           class="form-control gof-form-control"
                                           id="email"
                                           name="email"
                                           maxlength="150"
                                           autocomplete="email"
                                           placeholder="you@example.com"
                                           required>

                                </div>


                                <!-- PHONE -->
                                <div class="col-md-6 mb-4">

                                    <label for="phone"
                                           class="gof-form-label">
                                        Phone Number
                                    </label>

                                    <input type="tel"
                                           class="form-control gof-form-control"
                                           id="phone"
                                           name="phone"
                                           maxlength="30"
                                           autocomplete="tel"
                                           placeholder="+234...">

                                </div>


                                <!-- SUBJECT -->
                                <div class="col-md-6 mb-4">

                                    <label for="subject"
                                           class="gof-form-label">
                                        Subject *
                                    </label>

                                    <select class="form-select gof-form-control"
                                            id="subject"
                                            name="subject"
                                            required>

                                        <option value="">
                                            Select a subject
                                        </option>

                                        <option value="general">
                                            General Inquiry
                                        </option>

                                        <option value="donation">
                                            Donation Information
                                        </option>

                                        <option value="volunteer">
                                            Volunteer Opportunities
                                        </option>

                                        <option value="partnership">
                                            Partnership Inquiry
                                        </option>

                                        <option value="beneficiary">
                                            Beneficiary Information
                                        </option>

                                        <option value="other">
                                            Other
                                        </option>

                                    </select>

                                </div>


                                <!-- MESSAGE -->
                                <div class="col-12 mb-4">

                                    <label for="message"
                                           class="gof-form-label">
                                        Message *
                                    </label>

                                    <textarea class="form-control gof-form-control"
                                              id="message"
                                              name="message"
                                              maxlength="5000"
                                              placeholder="Write your message here..."
                                              required></textarea>

                                    <div class="text-end mt-2">

                                        <small class="text-muted">
                                            <span id="messageCount">0</span>/5000
                                        </small>

                                    </div>

                                </div>


                                <!-- SUBMIT -->
                                <div class="col-12">

                                    <button type="submit"
                                            id="contactSubmitBtn"
                                            class="gof-submit-btn">

                                        <i class="bi bi-send me-2"></i>

                                        Send Message

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>


                <!-- SIDEBAR -->
                <div class="col-lg-4">

                    <div class="gof-side-panel">

                        <div class="gof-section-label">
                            Our availability
                        </div>

                        <h3 class="mb-3">
                            We're available to help.
                        </h3>

                        <p>
                            Contact us during our office hours and we'll do
                            our best to respond as soon as possible.
                        </p>


                        <h5 class="mt-4 mb-2">
                            Office Hours
                        </h5>


                        <div class="gof-hours-row">
                            <span>
                                Monday - Friday
                            </span>

                            <span>
                                9:00 AM - 5:00 PM
                            </span>
                        </div>


                        <div class="gof-hours-row">
                            <span>
                                Saturday
                            </span>

                            <span>
                                10:00 AM - 2:00 PM
                            </span>
                        </div>


                        <div class="gof-hours-row">
                            <span>
                                Sunday
                            </span>

                            <span>
                                Closed
                            </span>
                        </div>


                        <h5 class="mt-5 mb-2">
                            Quick Links
                        </h5>


                        <a href="<?= h(url('/about')) ?>"
                           class="gof-quick-link">

                            <i class="bi bi-arrow-right-circle"></i>

                            About Us

                        </a>


                        <a href="<?= h(url('/programs')) ?>"
                           class="gof-quick-link">

                            <i class="bi bi-arrow-right-circle"></i>

                            Our Programs

                        </a>


                        <a href="<?= h(url('/donate')) ?>"
                           class="gof-quick-link">

                            <i class="bi bi-arrow-right-circle"></i>

                            Support Our Work

                        </a>


                        <a href="<?= h(url('/volunteer')) ?>"
                           class="gof-quick-link">

                            <i class="bi bi-arrow-right-circle"></i>

                            Become a Volunteer

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         LOCATION
         ===================================================== -->
    <section class="gof-location">

        <div class="container">

            <div class="text-center mb-5">

                <div class="gof-section-label">
                    Find us
                </div>

                <h2 class="gof-section-title">
                    Our location
                </h2>

            </div>


            <div class="gof-location-card">

                <div class="row g-0 align-items-stretch">

                    <div class="col-lg-6">

                        <div class="gof-location-visual h-100">

                            <i class="bi bi-geo-alt-fill"></i>

                        </div>

                    </div>


                    <div class="col-lg-6">

                        <div class="gof-location-content">

                            <div class="gof-contact-icon">
                                <i class="bi bi-building"></i>
                            </div>

                            <h3>
                                Garko Orphans Foundation
                            </h3>

                            <p>
                                NO.1 Central Mosque<br>
                                Garko, Kano State<br>
                                Nigeria
                            </p>

                            <p class="small">
                                Visit our office during our working hours
                                or contact our team before coming.
                            </p>

                            <a href="https://www.google.com/maps/search/?api=1&query=NO.1+Central+Mosque+Garko+Kano+State+Nigeria"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="btn btn-outline-dark rounded-3 px-4">

                                <i class="bi bi-map me-2"></i>

                                Open in Maps

                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =====================================================
         FINAL CTA
         ===================================================== -->
    <section class="gof-contact-cta">

        <div class="container">

            <div class="gof-contact-cta-box">

                <div class="row align-items-center">

                    <div class="col-lg-8">

                        <div class="gof-section-label">
                            Get involved
                        </div>

                        <h2 class="mb-3">
                            Every conversation can become an opportunity
                            to create impact.
                        </h2>

                        <p class="mb-0">
                            Learn more about our programs, support our work,
                            or join our volunteer community.
                        </p>

                    </div>


                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">

                        <a href="<?= h(url('/volunteer')) ?>"
                           class="gof-cta-btn">

                            Become a Volunteer

                            <i class="bi bi-arrow-right ms-2"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </section>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('contactForm');
    const message = document.getElementById('message');
    const counter = document.getElementById('messageCount');
    const submit = document.getElementById('contactSubmitBtn');

    if (!form) {
        return;
    }


    /* =====================================================
       MESSAGE COUNTER
       ===================================================== */

    if (message && counter) {

        const updateCounter = function () {
            counter.textContent = message.value.length;
        };

        message.addEventListener('input', updateCounter);

        updateCounter();
    }


    /* =====================================================
       REAL FORM SUBMISSION
       ===================================================== */

    form.addEventListener('submit', function (e) {

        const name = document.getElementById('name');
        const email = document.getElementById('email');
        const subject = document.getElementById('subject');

        if (!name.value.trim()) {
            e.preventDefault();
            name.focus();
            return;
        }

        if (!email.value.trim()) {
            e.preventDefault();
            email.focus();
            return;
        }

        if (!subject.value) {
            e.preventDefault();
            subject.focus();
            return;
        }

        if (!message.value.trim()) {
            e.preventDefault();
            message.focus();
            return;
        }


        /*
         * IMPORTANT:
         * Do NOT call preventDefault() here.
         *
         * The browser submits the form to:
         *
         * POST /contact
         *
         * PublicController@contactSubmit then stores
         * the message in contact_messages.
         */

        submit.disabled = true;

        submit.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>' +
            'Sending Message...';

    });

});
</script>