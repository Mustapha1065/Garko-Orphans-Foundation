<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="hero-title">Empowering Orphans, Transforming Lives</h1>
                <p class="hero-subtitle">Garko Orphans Foundation is dedicated to providing education, healthcare, and support to orphans in need, ensuring a brighter future for the next generation.</p>
                <div class="hero-buttons">
                    <a href="<?= url('/donate') ?>" class="btn btn-gof-primary btn-lg me-3">Donate Now</a>
                    <a href="<?= url('/about') ?>" class="btn btn-outline-light btn-lg">Learn More</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image">
                    <div class="gof-logo-icon mx-auto" style="width: 200px; height: 200px; font-size: 80px;">G</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="stat-card">
                    <i class="bi bi-people-fill stat-icon"></i>
                    <h3 class="stat-number">500+</h3>
                    <p class="stat-label">Beneficiaries</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="stat-card">
                    <i class="bi bi-heart-fill stat-icon"></i>
                    <h3 class="stat-number">200+</h3>
                    <p class="stat-label">Donors</p>
                </div>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <div class="stat-card">
                    <i class="bi bi-book-fill stat-icon"></i>
                    <h3 class="stat-number">50+</h3>
                    <p class="stat-label">Education Programs</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <i class="bi bi-calendar-event-fill stat-icon"></i>
                    <h3 class="stat-number">100+</h3>
                    <p class="stat-label">Events</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Preview -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-preview-image bg-gof-primary d-flex align-items-center justify-content-center" style="height: 300px; border-radius: 10px;">
                    <div class="gof-logo-icon text-white" style="width: 100px; height: 100px; font-size: 40px;">G</div>
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="section-title">About Our Foundation</h2>
                <p class="text-muted mb-4">Founded with a vision to make a difference in the lives of orphans, Garko Orphans Foundation has been working tirelessly since its inception. We believe every child deserves love, care, and opportunities to succeed.</p>
                <ul class="list-unstyled mb-4">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Quality Education</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Healthcare Support</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Nutritional Programs</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Skill Development</li>
                </ul>
                <a href="<?= url('/about') ?>" class="btn btn-gof-secondary">Read More</a>
            </div>
        </div>
    </div>
</section>

<!-- Programs Preview -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Programs</h2>
            <p class="text-muted">Making a lasting impact through comprehensive support programs</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="program-card">
                    <div class="program-icon bg-primary bg-opacity-10">
                        <i class="bi bi-mortarboard-fill text-primary fs-1"></i>
                    </div>
                    <h4 class="program-title">Education</h4>
                    <p class="program-description">Providing quality education and learning materials to ensure every child has access to knowledge.</p>
                    <a href="<?= url('/programs') ?>" class="btn btn-outline-primary btn-sm">Learn More</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="program-card">
                    <div class="program-icon bg-success bg-opacity-10">
                        <i class="bi bi-heart-pulse-fill text-success fs-1"></i>
                    </div>
                    <h4 class="program-title">Healthcare</h4>
                    <p class="program-description">Ensuring access to medical care, regular check-ups, and health education for all beneficiaries.</p>
                    <a href="<?= url('/programs') ?>" class="btn btn-outline-success btn-sm">Learn More</a>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="program-card">
                    <div class="program-icon bg-warning bg-opacity-10">
                        <i class="bi bi-house-heart-fill text-warning fs-1"></i>
                    </div>
                    <h4 class="program-title">Shelter & Care</h4>
                    <p class="program-description">Providing safe housing, nutritious meals, and loving care to create a nurturing environment.</p>
                    <a href="<?= url('/programs') ?>" class="btn btn-outline-warning btn-sm">Learn More</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2 class="cta-title">Make a Difference Today</h2>
                <p class="cta-subtitle">Your contribution can change a child's life forever. Join us in our mission to empower orphans and build a brighter future.</p>
                <div class="cta-buttons">
                    <a href="<?= url('/donate') ?>" class="btn btn-gof-primary btn-lg me-3">Donate Now</a>
                    <a href="<?= url('/volunteer') ?>" class="btn btn-outline-light btn-lg">Become a Volunteer</a>
                </div>
            </div>
        </div>
    </div>
</section>
