<?php
/**
 * GOF - Public Events Page
 *
 * Events are loaded from:
 * PublicController::events()
 *
 * Only events where is_public = 1 are displayed.
 */

$events = isset($events) && is_array($events) ? $events : [];

/* ---------------------------------------------------------
   Helpers
   --------------------------------------------------------- */

if (!function_exists('event_h')) {
    function event_h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('event_date')) {
    function event_date($date): string
    {
        if (empty($date)) {
            return 'Date to be announced';
        }

        try {
            return date('F j, Y', strtotime($date));
        } catch (\Throwable $e) {
            return event_h($date);
        }
    }
}

if (!function_exists('event_month')) {
    function event_month($date): string
    {
        if (empty($date)) {
            return 'TBA';
        }

        try {
            return strtoupper(date('M', strtotime($date)));
        } catch (\Throwable $e) {
            return 'TBA';
        }
    }
}

if (!function_exists('event_day')) {
    function event_day($date): string
    {
        if (empty($date)) {
            return '--';
        }

        try {
            return date('d', strtotime($date));
        } catch (\Throwable $e) {
            return '--';
        }
    }
}

if (!function_exists('event_time')) {
    function event_time($time): string
    {
        if (empty($time)) {
            return 'Time to be announced';
        }

        try {
            return date('g:i A', strtotime($time));
        } catch (\Throwable $e) {
            return event_h($time);
        }
    }
}

if (!function_exists('event_image_url')) {
    function event_image_url($image): ?string
    {
        if (empty($image)) {
            return null;
        }

        $image = trim((string)$image);

        /*
         * Already a complete URL.
         */
        if (
            str_starts_with($image, 'http://')
            || str_starts_with($image, 'https://')
            || str_starts_with($image, '//')
        ) {
            return $image;
        }

        /*
         * Normalize database paths.
         */
        $image = ltrim(str_replace('\\', '/', $image), '/');

        /*
         * EventController normally stores paths such as:
         * uploads/events/filename.jpg
         */
        if (str_starts_with($image, 'public/')) {
            $image = substr($image, 7);
        }

        if (!str_starts_with($image, 'uploads/')) {
            $image = 'uploads/events/' . $image;
        }

        return url('/' . $image);
    }
}

if (!function_exists('event_status_class')) {
    function event_status_class($status): string
    {
        return match (strtolower((string)$status)) {
            'ongoing'   => 'bg-success',
            'completed' => 'bg-secondary',
            'cancelled' => 'bg-danger',
            default     => 'bg-primary'
        };
    }
}

if (!function_exists('event_status_label')) {
    function event_status_label($status): string
    {
        return ucfirst(strtolower((string)($status ?: 'upcoming')));
    }
}


/* ---------------------------------------------------------
   Separate upcoming/past events
   --------------------------------------------------------- */

$upcomingEvents = [];
$pastEvents = [];

foreach ($events as $event) {

    $status = strtolower(trim((string)($event['status'] ?? 'upcoming')));

    if (
        $status === 'completed'
        || $status === 'cancelled'
        || (
            !empty($event['event_date'])
            && strtotime($event['event_date']) < strtotime(date('Y-m-d'))
            && $status !== 'ongoing'
        )
    ) {
        $pastEvents[] = $event;
    } else {
        $upcomingEvents[] = $event;
    }
}

?>

<style>
/* =========================================================
   EVENTS PAGE HERO
   ========================================================= */

.events-page-hero {
    background: linear-gradient(135deg, #c9a900 0%, #b79500 100%);
    padding: 42px 20px 38px !important;
    min-height: 0 !important;
    height: auto !important;
    display: block !important;
}

.events-page-hero .page-title {
    color: #ffffff !important;
    font-size: clamp(2rem, 4vw, 3rem);
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 8px;
}

.events-page-hero .page-subtitle {
    color: rgba(255,255,255,.92) !important;
    font-size: 1rem;
    margin: 0 0 14px;
}

.events-page-hero .breadcrumb {
    margin: 0;
    padding: 0;
}

.events-page-hero .breadcrumb-item,
.events-page-hero .breadcrumb-item.active {
    color: #ffffff !important;
}

.events-page-hero .breadcrumb-item a {
    color: rgba(255,255,255,.88) !important;
    text-decoration: none;
}

.events-page-hero .breadcrumb-item a:hover {
    color: #ffffff !important;
}

.events-page-hero .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,.75) !important;
}


/* =========================================================
   EVENTS SECTIONS
   ========================================================= */

.events-main-section,
.events-past-section,
.events-involved-section {
    padding: 65px 0 !important;
}


/* =========================================================
   EVENT CARD
   ========================================================= */

.event-card {
    height: 100%;
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 5px 22px rgba(0,0,0,.07);
    transition: transform .25s ease, box-shadow .25s ease;
}

.event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 30px rgba(0,0,0,.11);
}

.event-image {
    overflow: hidden;
    height: 220px;
    background: #f5f5f5;
}

.event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.event-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.event-placeholder i {
    font-size: 60px;
    color: #c9a900;
}

.event-body {
    padding: 25px;
}

.event-title {
    font-weight: 700;
    margin-bottom: 12px;
    color: #212529;
}

.event-description {
    color: #6c757d;
    line-height: 1.6;
    margin-bottom: 18px;
}

.event-date-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    z-index: 5;
    width: 64px;
    height: 64px;
    border-radius: 12px;
    background: #c9a900;
    color: #ffffff;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(0,0,0,.18);
}

.event-month {
    font-size: .75rem;
    font-weight: 700;
    line-height: 1;
}

.event-day {
    font-size: 1.45rem;
    font-weight: 800;
    line-height: 1.1;
}

.event-meta {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.event-meta-item {
    color: #6c757d;
    font-size: .92rem;
}

.event-meta-item i {
    color: #c9a900;
}

.event-status {
    margin-bottom: 12px;
}


/* =========================================================
   PAST EVENTS
   ========================================================= */

.past-event-card {
    height: 100%;
    background: #ffffff;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,.06);
    transition: transform .25s ease, box-shadow .25s ease;
}

.past-event-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,.10);
}

.past-event-image {
    overflow: hidden;
    border-radius: 10px;
    height: 150px;
    background: #eeeeee;
}

.past-event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.past-event-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.past-event-placeholder i {
    font-size: 42px;
    color: #ffffff;
}

.past-event-title {
    font-weight: 700;
    margin-bottom: 8px;
}

.past-event-date {
    font-size: .9rem;
    margin-bottom: 8px;
}

.past-event-description {
    line-height: 1.55;
    margin-bottom: 0;
}


/* =========================================================
   EMPTY STATE
   ========================================================= */

.events-empty {
    background: #ffffff;
    border-radius: 16px;
    padding: 55px 25px;
    text-align: center;
    box-shadow: 0 5px 20px rgba(0,0,0,.06);
}

.events-empty-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: rgba(201,169,0,.12);
    display: flex;
    align-items: center;
    justify-content: center;
}

.events-empty-icon i {
    font-size: 38px;
    color: #c9a900;
}


/* =========================================================
   GET INVOLVED
   ========================================================= */

.events-involved-section .section-title {
    margin-bottom: 12px;
}

.events-involved-section .text-muted {
    line-height: 1.7;
}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 767.98px) {

    .events-page-hero {
        padding: 32px 15px 28px !important;
    }

    .events-page-hero .page-title {
        font-size: 2rem;
    }

    .events-page-hero .page-subtitle {
        font-size: .95rem;
    }

    .events-main-section,
    .events-past-section,
    .events-involved-section {
        padding: 50px 0 !important;
    }

    .event-body {
        padding: 20px;
    }

    .event-image {
        height: 200px;
    }

    .past-event-card {
        padding: 15px;
    }

    .past-event-image {
        margin-bottom: 15px;
    }

    .events-involved-section .d-flex {
        flex-direction: column;
        align-items: stretch;
    }

    .events-involved-section .btn {
        width: 100%;
    }
}
</style>


<!-- =========================================================
     HERO SECTION
     ========================================================= -->

<section class="page-hero events-page-hero">

    <div class="container">

        <div class="row">

            <div class="col-12 text-center">

                <h1 class="page-title">
                    Events & Activities
                </h1>

                <p class="page-subtitle">
                    Join us for upcoming events and see what we've been doing
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
                            Events
                        </li>

                    </ol>

                </nav>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     UPCOMING EVENTS
     ========================================================= -->

<section class="events-main-section">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Upcoming Events
            </h2>

            <p class="text-muted">
                Mark your calendar for these exciting events
            </p>

        </div>


        <?php if (!empty($upcomingEvents)): ?>

            <div class="row">

                <?php foreach ($upcomingEvents as $event): ?>

                    <?php
                    $imageUrl = event_image_url($event['image'] ?? null);
                    $status = strtolower((string)($event['status'] ?? 'upcoming'));
                    ?>

                    <div class="col-lg-4 col-md-6 mb-4">

                        <div class="event-card">

                            <!-- DATE BADGE -->

                            <div class="event-date-badge">

                                <span class="event-month">
                                    <?= event_h(event_month($event['event_date'] ?? null)) ?>
                                </span>

                                <span class="event-day">
                                    <?= event_h(event_day($event['event_date'] ?? null)) ?>
                                </span>

                            </div>


                            <!-- IMAGE -->

                            <div class="event-image">

                                <?php if ($imageUrl): ?>

                                    <img
                                        src="<?= event_h($imageUrl) ?>"
                                        alt="<?= event_h($event['title'] ?? 'Event') ?>"
                                        loading="lazy"
                                    >

                                <?php else: ?>

                                    <div class="event-placeholder">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>

                                <?php endif; ?>

                            </div>


                            <!-- BODY -->

                            <div class="event-body">

                                <div class="event-status">

                                    <span class="badge <?= event_status_class($status) ?>">
                                        <?= event_h(event_status_label($status)) ?>
                                    </span>

                                </div>


                                <h4 class="event-title">
                                    <?= event_h($event['title'] ?? 'Untitled Event') ?>
                                </h4>


                                <div class="event-meta mb-3">

                                    <?php if (!empty($event['event_time'])): ?>

                                        <span class="event-meta-item">

                                            <i class="bi bi-clock me-1"></i>

                                            <?= event_h(event_time($event['event_time'])) ?>

                                        </span>

                                    <?php endif; ?>


                                    <?php if (!empty($event['event_date'])): ?>

                                        <span class="event-meta-item">

                                            <i class="bi bi-calendar3 me-1"></i>

                                            <?= event_h(event_date($event['event_date'])) ?>

                                        </span>

                                    <?php endif; ?>


                                    <?php if (!empty($event['location'])): ?>

                                        <span class="event-meta-item">

                                            <i class="bi bi-geo-alt me-1"></i>

                                            <?= event_h($event['location']) ?>

                                        </span>

                                    <?php endif; ?>

                                </div>


                                <?php if (!empty($event['description'])): ?>

                                    <p class="event-description">

                                        <?= nl2br(event_h($event['description'])) ?>

                                    </p>

                                <?php else: ?>

                                    <p class="event-description">

                                        Join us for this special Garko Orphans
                                        Foundation event.

                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="events-empty">

                <div class="events-empty-icon">
                    <i class="bi bi-calendar-x"></i>
                </div>

                <h3>
                    No Upcoming Events
                </h3>

                <p class="text-muted mb-0">
                    There are currently no public upcoming events.
                    Please check back soon.
                </p>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- =========================================================
     RECENT / PAST EVENTS
     ========================================================= -->

<?php if (!empty($pastEvents)): ?>

<section class="events-past-section bg-light">

    <div class="container">

        <div class="text-center mb-5">

            <h2 class="section-title">
                Recent Events
            </h2>

            <p class="text-muted">
                Highlights from our recent activities
            </p>

        </div>


        <div class="row">

            <?php foreach ($pastEvents as $event): ?>

                <?php
                $imageUrl = event_image_url($event['image'] ?? null);
                ?>

                <div class="col-lg-6 mb-4">

                    <div class="past-event-card">

                        <div class="row align-items-center">

                            <div class="col-md-4">

                                <div class="past-event-image">

                                    <?php if ($imageUrl): ?>

                                        <img
                                            src="<?= event_h($imageUrl) ?>"
                                            alt="<?= event_h($event['title'] ?? 'Event') ?>"
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <div class="past-event-placeholder bg-secondary">

                                            <i class="bi bi-calendar-check"></i>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>


                            <div class="col-md-8">

                                <span class="badge bg-secondary mb-2">
                                    <?= event_h(event_status_label($event['status'] ?? 'completed')) ?>
                                </span>


                                <h4 class="past-event-title">

                                    <?= event_h($event['title'] ?? 'Untitled Event') ?>

                                </h4>


                                <?php if (!empty($event['event_date'])): ?>

                                    <p class="past-event-date text-muted">

                                        <i class="bi bi-calendar me-1"></i>

                                        <?= event_h(event_date($event['event_date'])) ?>

                                    </p>

                                <?php endif; ?>


                                <?php if (!empty($event['location'])): ?>

                                    <p class="past-event-date text-muted">

                                        <i class="bi bi-geo-alt me-1"></i>

                                        <?= event_h($event['location']) ?>

                                    </p>

                                <?php endif; ?>


                                <?php if (!empty($event['description'])): ?>

                                    <p class="past-event-description text-muted">

                                        <?= nl2br(event_h($event['description'])) ?>

                                    </p>

                                <?php endif; ?>

                            </div>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</section>

<?php endif; ?>


<!-- =========================================================
     GET INVOLVED
     ========================================================= -->

<section class="events-involved-section">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-lg-8 text-center">

                <h2 class="section-title">
                    Get Involved
                </h2>

                <p class="text-muted mb-4">

                    You can participate in our events as a volunteer,
                    sponsor, or attendee. Your support helps us create
                    meaningful experiences for our beneficiaries.

                </p>


                <div class="d-flex justify-content-center gap-3">

                    <a
                        href="<?= url('/volunteer') ?>"
                        class="btn btn-gof-primary"
                    >
                        Become a Volunteer
                    </a>


                    <a
                        href="<?= url('/contact') ?>"
                        class="btn btn-outline-primary"
                    >
                        Contact Us
                    </a>

                </div>

            </div>

        </div>

    </div>

</section>