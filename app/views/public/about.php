<style>
/* =========================================================
   ABOUT PAGE - HERO FIX
   ========================================================= */

.about-page-hero {
    background: linear-gradient(135deg, #c9a900 0%, #b79500 100%);
    padding: 42px 20px 38px !important;
    min-height: 0 !important;
    height: auto !important;
    display: block !important;
}

.about-page-hero .page-title {
    color: #ffffff !important;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 700;
    margin: 0 0 8px;
    line-height: 1.2;
}

.about-page-hero .page-subtitle {
    color: rgba(255,255,255,.92) !important;
    font-size: 1rem;
    margin: 0 0 14px;
}

.about-page-hero .breadcrumb {
    margin: 0;
    padding: 0;
}

.about-page-hero .breadcrumb-item,
.about-page-hero .breadcrumb-item.active {
    color: #ffffff !important;
}

.about-page-hero .breadcrumb-item a {
    color: rgba(255,255,255,.85) !important;
    text-decoration: none;
}

.about-page-hero .breadcrumb-item a:hover {
    color: #ffffff !important;
}

.about-page-hero .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,.7) !important;
}


/* =========================================================
   ABOUT CONTENT
   ========================================================= */

.about-section {
    padding: 70px 0 !important;
}

.about-section.bg-light {
    padding: 70px 0 !important;
}

.about-image {
    overflow: hidden;
}

.objective-card {
    height: 100%;
    padding: 28px 24px;
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 5px 20px rgba(0,0,0,.06);
    transition: transform .25s ease, box-shadow .25s ease;
}

.objective-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,.10);
}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    .about-page-hero {
        padding: 32px 15px 28px !important;
    }

    .about-page-hero .page-title {
        font-size: 2rem;
    }

    .about-page-hero .page-subtitle {
        font-size: .95rem;
    }

    .about-section {
        padding: 50px 0 !important;
    }

    .about-image {
        height: 300px !important;
    }
}
</style>


<!-- =========================================================
     HERO SECTION
     ========================================================= -->

<section class="page-hero about-page-hero">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">

                <h1 class="page-title">
                    About Us
                </h1>

                <p class="page-subtitle">
                    Learn about our mission, vision, and the work we do
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
                            About
                        </li>
                    </ol>
                </nav>

            </div>
        </div>
    </div>
</section>


<!-- =========================================================
     MISSION SECTION
     ========================================================= -->

<section class="about-section">
    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6 mb-4 mb-lg-0">

                <div
                    class="about-image bg-light d-flex align-items-center justify-content-center"
                    style="
                        height: 400px;
                        border-radius: 14px;
                    "
                >
                    <div
                        class="gof-logo-icon"
                        style="
                            width: 150px;
                            height: 150px;
                            font-size: 60px;
                        "
                    >
                        G
                    </div>
                </div>

            </div>

            <div class="col-lg-6">

                <h2 class="section-title">
                    Our Mission
                </h2>

                <p class="text-muted mb-4">
                    To provide comprehensive care, education, and support
                    to orphans and vulnerable children, empowering them to
                    become self-reliant and contributing members of society.
                </p>

                <p class="text-muted">
                    We believe that every child deserves love, care, and
                    opportunities to succeed regardless of their circumstances.
                    Through our programs, we strive to break the cycle of
                    poverty and create lasting change in the lives of the
                    children we serve.
                </p>

            </div>

        </div>

    </div>
</section>


<!-- =========================================================
     VISION SECTION
     ========================================================= -->

<section class="about-section bg-light">
    <div class="container">

        <div class="row align-items-center">

            <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">

                <div
                    class="about-image bg-gof-secondary d-flex align-items-center justify-content-center"
                    style="
                        height: 400px;
                        border-radius: 14px;
                    "
                >
                    <i
                        class="bi bi-eye-fill text-white"
                        style="font-size: 100px;"
                    ></i>
                </div>

            </div>

            <div class="col-lg-6 order-lg-1">

                <h2 class="section-title">
                    Our Vision
                </h2>

                <p class="text-muted mb-4">
                    A society where every orphan and vulnerable child has
                    access to quality education, healthcare, and the support
                    they need to reach their full potential.
                </p>

                <p class="text-muted">
                    We envision a future where no child is left behind due
                    to lack of resources or support. Through sustainable
                    programs and community partnerships, we aim to create
                    an environment where every child can thrive.
                </p>

            </div>

        </div>

    </div>
</section>


<!-- =========================================================
     OBJECTIVES
     ========================================================= -->

<section class="about-section">
    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Our Objectives
            </h2>

            <p class="text-muted">
                Key goals that guide our work and programs
            </p>

        </div>


        <div class="row">


            <!-- EDUCATION -->

            <div class="col-md-4 mb-4">

                <div class="objective-card">

                    <div class="objective-icon bg-primary bg-opacity-10">
                        <i class="bi bi-mortarboard-fill text-primary fs-2"></i>
                    </div>

                    <h4 class="objective-title">
                        Education
                    </h4>

                    <p class="objective-description">
                        Provide quality education and learning materials
                        to ensure every child has access to knowledge and
                        skills development.
                    </p>

                </div>

            </div>


            <!-- HEALTHCARE -->

            <div class="col-md-4 mb-4">

                <div class="objective-card">

                    <div class="objective-icon bg-success bg-opacity-10">
                        <i class="bi bi-heart-pulse-fill text-success fs-2"></i>
                    </div>

                    <h4 class="objective-title">
                        Healthcare
                    </h4>

                    <p class="objective-description">
                        Ensure access to medical care, regular health
                        check-ups, and health education for all beneficiaries.
                    </p>

                </div>

            </div>


            <!-- SHELTER -->

            <div class="col-md-4 mb-4">

                <div class="objective-card">

                    <div class="objective-icon bg-warning bg-opacity-10">
                        <i class="bi bi-house-heart-fill text-warning fs-2"></i>
                    </div>

                    <h4 class="objective-title">
                        Shelter & Care
                    </h4>

                    <p class="objective-description">
                        Provide safe housing, nutritious meals, and loving
                        care to create a nurturing environment for growth.
                    </p>

                </div>

            </div>


            <!-- SKILLS -->

            <div class="col-md-4 mb-4">

                <div class="objective-card">

                    <div class="objective-icon bg-info bg-opacity-10">
                        <i class="bi bi-tools-fill text-info fs-2"></i>
                    </div>

                    <h4 class="objective-title">
                        Skill Development
                    </h4>

                    <p class="objective-description">
                        Equip beneficiaries with vocational skills and
                        practical knowledge for self-reliance and future
                        employment.
                    </p>

                </div>

            </div>


            <!-- COMMUNITY -->

            <div class="col-md-4 mb-4">

                <div class="objective-card">

                    <div class="objective-icon bg-danger bg-opacity-10">
                        <i class="bi bi-people-fill text-danger fs-2"></i>
                    </div>

                    <h4 class="objective-title">
                        Community Support
                    </h4>

                    <p class="objective-description">
                        Build strong community relationships and foster
                        a supportive network around our beneficiaries.
                    </p>

                </div>

            </div>


            <!-- SUSTAINABILITY -->

            <div class="col-md-4 mb-4">

                <div class="objective-card">

                    <div class="objective-icon bg-purple bg-opacity-10">
                        <i class="bi bi-gear-fill text-purple fs-2"></i>
                    </div>

                    <h4 class="objective-title">
                        Sustainability
                    </h4>

                    <p class="objective-description">
                        Develop sustainable programs that create long-term
                        impact and reduce dependency on external aid.
                    </p>

                </div>

            </div>

        </div>

    </div>
</section>


<!-- =========================================================
     OUR STORY
     ========================================================= -->

<section class="about-section bg-light">

    <div class="container">

        <div class="row">

            <div class="col-lg-8 mx-auto">

                <div class="text-center mb-5">

                    <h2 class="section-title">
                        Our Story
                    </h2>

                </div>


                <div class="story-timeline">


                    <div class="timeline-item">

                        <div class="timeline-marker"></div>

                        <div class="timeline-content">

                            <h4>
                                Founded
                            </h4>

                            <p class="text-muted">
                                Garko Orphans Foundation was established
                                with a vision to make a difference in the
                                lives of orphans in Garko, Kano State.
                            </p>

                        </div>

                    </div>


                    <div class="timeline-item">

                        <div class="timeline-marker"></div>

                        <div class="timeline-content">

                            <h4>
                                First Beneficiaries
                            </h4>

                            <p class="text-muted">
                                We welcomed our first group of beneficiaries,
                                providing them with shelter, education,
                                and care.
                            </p>

                        </div>

                    </div>


                    <div class="timeline-item">

                        <div class="timeline-marker"></div>

                        <div class="timeline-content">

                            <h4>
                                Program Expansion
                            </h4>

                            <p class="text-muted">
                                Expanded our programs to include healthcare,
                                skill development, and community outreach
                                initiatives.
                            </p>

                        </div>

                    </div>


                    <div class="timeline-item">

                        <div class="timeline-marker"></div>

                        <div class="timeline-content">

                            <h4>
                                Growth & Impact
                            </h4>

                            <p class="text-muted">
                                Today, we support hundreds of beneficiaries
                                through our comprehensive programs, making
                                a lasting impact in their lives.
                            </p>

                        </div>

                    </div>


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
                    Join Our Mission
                </h2>

                <p class="cta-subtitle">
                    Your support can help us continue making a difference
                    in the lives of orphans and vulnerable children.
                </p>

                <div class="cta-buttons">

                    <a
                        href="<?= url('/donate') ?>"
                        class="btn btn-gof-primary btn-lg me-3"
                    >
                        Donate Now
                    </a>

                    <a
                        href="<?= url('/volunteer') ?>"
                        class="btn btn-outline-light btn-lg"
                    >
                        Become a Volunteer
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>