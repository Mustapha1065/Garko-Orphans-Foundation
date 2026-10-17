<style>
/* =========================================================
   PROGRAMS PAGE HERO
   ========================================================= */

.programs-page-hero {
    background: linear-gradient(135deg, #c9a900 0%, #b79500 100%);
    padding: 42px 20px 38px !important;
    min-height: 0 !important;
    height: auto !important;
    display: block !important;
}

.programs-page-hero .page-title {
    color: #ffffff !important;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 8px;
}

.programs-page-hero .page-subtitle {
    color: rgba(255,255,255,.92) !important;
    font-size: 1rem;
    margin: 0 0 14px;
}

.programs-page-hero .breadcrumb {
    margin: 0;
    padding: 0;
}

.programs-page-hero .breadcrumb-item,
.programs-page-hero .breadcrumb-item.active {
    color: #ffffff !important;
}

.programs-page-hero .breadcrumb-item a {
    color: rgba(255,255,255,.85) !important;
    text-decoration: none;
}

.programs-page-hero .breadcrumb-item a:hover {
    color: #ffffff !important;
}

.programs-page-hero .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,.7) !important;
}


/* =========================================================
   PROGRAM SECTIONS
   ========================================================= */

.programs-main-section {
    padding: 65px 0 !important;
}

.programs-impact-section {
    padding: 65px 0 !important;
}

.programs-help-section {
    padding: 65px 0 !important;
}


/* =========================================================
   PROGRAM CARDS
   ========================================================= */

.program-card-lg {
    height: 100%;
    overflow: hidden;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 5px 22px rgba(0,0,0,.07);
    transition: transform .25s ease, box-shadow .25s ease;
}

.program-card-lg:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,.11);
}

.program-header {
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 18px;
}

.program-icon-lg {
    width: 60px;
    height: 60px;
    min-width: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.program-title-lg {
    margin: 0;
    font-size: 1.35rem;
    font-weight: 700;
}

.program-body {
    padding: 25px;
}

.program-body ul {
    margin-bottom: 0;
}

.program-body li {
    line-height: 1.5;
}


/* =========================================================
   IMPACT CARDS
   ========================================================= */

.impact-card {
    height: 100%;
    padding: 30px 20px;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 5px 20px rgba(0,0,0,.06);
}

.impact-icon {
    font-size: 2.3rem;
    margin-bottom: 12px;
}

.impact-number {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 5px;
}

.impact-label {
    color: #6c757d;
    margin: 0;
}


/* =========================================================
   HELP CARDS
   ========================================================= */

.help-card {
    height: 100%;
    padding: 30px 24px;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 5px 20px rgba(0,0,0,.06);
    transition: transform .25s ease, box-shadow .25s ease;
}

.help-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,.10);
}

.help-icon {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
}

.help-title {
    font-weight: 700;
    margin-bottom: 10px;
}

.help-description {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 20px;
}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    .programs-page-hero {
        padding: 32px 15px 28px !important;
    }

    .programs-page-hero .page-title {
        font-size: 2rem;
    }

    .programs-page-hero .page-subtitle {
        font-size: .95rem;
    }

    .programs-main-section,
    .programs-impact-section,
    .programs-help-section {
        padding: 50px 0 !important;
    }

    .program-header {
        padding: 20px;
    }

    .program-body {
        padding: 20px;
    }
}
</style>


<!-- =========================================================
     HERO SECTION
     ========================================================= -->

<section class="page-hero programs-page-hero">

    <div class="container">

        <div class="row">

            <div class="col-12 text-center">

                <h1 class="page-title">
                    Our Programs
                </h1>

                <p class="page-subtitle">
                    Comprehensive support programs to empower and transform lives
                </p>

                <nav aria-label="breadcrumb">

                    <ol class="breadcrumb justify-content-center">

                        <li class="breadcrumb-item">
                            <a href="<?= url('/') ?>">
                                Home
                            </a>
                        </li>

                        <li class="breadcrumb-item active"
                            aria-current="page">
                            Programs
                        </li>

                    </ol>

                </nav>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     PROGRAMS OVERVIEW
     ========================================================= -->

<section class="programs-main-section">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Our Core Programs
            </h2>

            <p class="text-muted">
                Holistic programs designed to address the comprehensive
                needs of our beneficiaries
            </p>

        </div>


        <div class="row">


            <!-- EDUCATION -->

            <div class="col-lg-6 mb-4">

                <div class="program-card-lg">

                    <div class="program-header bg-primary bg-opacity-10">

                        <div class="program-icon-lg bg-primary">
                            <i class="bi bi-mortarboard-fill text-white fs-2"></i>
                        </div>

                        <h3 class="program-title-lg">
                            Education Support
                        </h3>

                    </div>

                    <div class="program-body">

                        <p class="text-muted mb-3">
                            We believe education is the key to breaking
                            the cycle of poverty. Our education program provides:
                        </p>

                        <ul class="list-unstyled text-muted">

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                School enrollment and tuition support
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                Learning materials and supplies
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                After-school tutoring programs
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                Scholarship opportunities
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                Career guidance and counseling
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


            <!-- HEALTHCARE -->

            <div class="col-lg-6 mb-4">

                <div class="program-card-lg">

                    <div class="program-header bg-success bg-opacity-10">

                        <div class="program-icon-lg bg-success">
                            <i class="bi bi-heart-pulse-fill text-white fs-2"></i>
                        </div>

                        <h3 class="program-title-lg">
                            Healthcare Services
                        </h3>

                    </div>

                    <div class="program-body">

                        <p class="text-muted mb-3">
                            Good health is fundamental to a child's development.
                            Our healthcare program includes:
                        </p>

                        <ul class="list-unstyled text-muted">

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Regular health check-ups
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Medical treatment and care
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Nutrition and wellness programs
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Mental health support
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                Health education workshops
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


            <!-- SHELTER -->

            <div class="col-lg-6 mb-4">

                <div class="program-card-lg">

                    <div class="program-header bg-warning bg-opacity-10">

                        <div class="program-icon-lg bg-warning">

                            <i class="bi bi-house-heart-fill text-white fs-2"></i>

                        </div>

                        <h3 class="program-title-lg">
                            Shelter & Care
                        </h3>

                    </div>

                    <div class="program-body">

                        <p class="text-muted mb-3">
                            Every child deserves a safe and loving home.
                            Our shelter program provides:
                        </p>

                        <ul class="list-unstyled text-muted">

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                Safe and comfortable housing
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                Nutritious meals daily
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                Clothing and personal care items
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                Loving and supportive caregivers
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-warning me-2"></i>
                                Recreational activities
                            </li>

                        </ul>

                    </div>

                </div>

            </div>


            <!-- SKILL DEVELOPMENT -->

            <div class="col-lg-6 mb-4">

                <div class="program-card-lg">

                    <div class="program-header bg-info bg-opacity-10">

                        <div class="program-icon-lg bg-info">

                            <i class="bi bi-tools-fill text-white fs-2"></i>

                        </div>

                        <h3 class="program-title-lg">
                            Skill Development
                        </h3>

                    </div>

                    <div class="program-body">

                        <p class="text-muted mb-3">
                            Practical skills for self-reliance and future
                            employment. Our skill program offers:
                        </p>

                        <ul class="list-unstyled text-muted">

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-info me-2"></i>
                                Vocational training workshops
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-info me-2"></i>
                                Computer literacy classes
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-info me-2"></i>
                                Entrepreneurship training
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-info me-2"></i>
                                Apprenticeship opportunities
                            </li>

                            <li class="mb-2">
                                <i class="bi bi-check-circle-fill text-info me-2"></i>
                                Business startup support
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     PROGRAM IMPACT
     ========================================================= -->

<section class="programs-impact-section bg-light">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Our Impact
            </h2>

            <p class="text-muted">
                Making a measurable difference in our community
            </p>

        </div>


        <div class="row text-center">


            <div class="col-md-3 mb-4">

                <div class="impact-card">

                    <i class="bi bi-mortarboard-fill impact-icon text-primary"></i>

                    <h3 class="impact-number">
                        200+
                    </h3>

                    <p class="impact-label">
                        Children in School
                    </p>

                </div>

            </div>


            <div class="col-md-3 mb-4">

                <div class="impact-card">

                    <i class="bi bi-heart-pulse-fill impact-icon text-success"></i>

                    <h3 class="impact-number">
                        500+
                    </h3>

                    <p class="impact-label">
                        Health Check-ups
                    </p>

                </div>

            </div>


            <div class="col-md-3 mb-4">

                <div class="impact-card">

                    <i class="bi bi-tools-fill impact-icon text-info"></i>

                    <h3 class="impact-number">
                        150+
                    </h3>

                    <p class="impact-label">
                        Skills Trained
                    </p>

                </div>

            </div>


            <div class="col-md-3 mb-4">

                <div class="impact-card">

                    <i class="bi bi-trophy-fill impact-icon text-warning"></i>

                    <h3 class="impact-number">
                        50+
                    </h3>

                    <p class="impact-label">
                        Graduates
                    </p>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =========================================================
     HOW YOU CAN HELP
     ========================================================= -->

<section class="programs-help-section">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                How You Can Help
            </h2>

            <p class="text-muted">
                Support our programs and make a difference today
            </p>

        </div>


        <div class="row">


            <!-- DONATE -->

            <div class="col-md-4 mb-4">

                <div class="help-card text-center">

                    <div class="help-icon bg-primary bg-opacity-10 mx-auto">

                        <i class="bi bi-cash-stack text-primary fs-2"></i>

                    </div>

                    <h4 class="help-title">
                        Donate
                    </h4>

                    <p class="help-description">
                        Your financial support helps us sustain and expand
                        our programs to reach more children in need.
                    </p>

                    <a
                        href="<?= url('/donate') ?>"
                        class="btn btn-primary"
                    >
                        Donate Now
                    </a>

                </div>

            </div>


            <!-- VOLUNTEER -->

            <div class="col-md-4 mb-4">

                <div class="help-card text-center">

                    <div class="help-icon bg-success bg-opacity-10 mx-auto">

                        <i class="bi bi-hand-thumbs-up text-success fs-2"></i>

                    </div>

                    <h4 class="help-title">
                        Volunteer
                    </h4>

                    <p class="help-description">
                        Share your time and skills to help us run our
                        programs and support our beneficiaries directly.
                    </p>

                    <a
                        href="<?= url('/volunteer') ?>"
                        class="btn btn-success"
                    >
                        Become a Volunteer
                    </a>

                </div>

            </div>


            <!-- SHARE -->

            <div class="col-md-4 mb-4">

                <div class="help-card text-center">

                    <div class="help-icon bg-info bg-opacity-10 mx-auto">

                        <i class="bi bi-share text-info fs-2"></i>

                    </div>

                    <h4 class="help-title">
                        Spread the Word
                    </h4>

                    <p class="help-description">
                        Help us raise awareness about our work by sharing
                        our mission with your network.
                    </p>

                    <button
                        type="button"
                        class="btn btn-info"
                        onclick="sharePage()"
                    >
                        Share
                    </button>

                </div>

            </div>


        </div>

    </div>

</section>


<!-- =========================================================
     CALL TO ACTION
     ========================================================= -->

<section class="cta-section py-5">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8 text-center">

                <h2 class="cta-title">
                    Support Our Programs
                </h2>

                <p class="cta-subtitle">
                    Your contribution can help us continue providing
                    life-changing programs to orphans and vulnerable children.
                </p>

                <a
                    href="<?= url('/donate') ?>"
                    class="btn btn-gof-primary btn-lg"
                >
                    Make a Donation
                </a>

            </div>

        </div>

    </div>

</section>


<script>
function sharePage() {

    if (navigator.share) {

        navigator.share({
            title: 'GOF Programs',
            text: 'Learn about the programs at Garko Orphans Foundation',
            url: window.location.href
        });

    } else {

        alert(
            'Copy this link to share: ' +
            window.location.href
        );

    }
}
</script>