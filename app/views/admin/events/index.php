<?php
/**
 * GOF3-light
 * Admin Events Management
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

$events = $events ?? [];
$search = $search ?? '';
$status = $status ?? '';
$visibility = $visibility ?? '';

$summary = $summary ?? [];

$totalEvents = (int)($summary['total'] ?? 0);
$upcomingEvents = (int)($summary['upcoming'] ?? 0);
$ongoingEvents = (int)($summary['ongoing'] ?? 0);
$completedEvents = (int)($summary['completed'] ?? 0);
$cancelledEvents = (int)($summary['cancelled'] ?? 0);
$publicEvents = (int)($summary['public_events'] ?? 0);

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

    $csrf = $_SESSION['_csrf'] ?? $_SESSION['csrf_token'] ?? '';
}
?>

<style>
    .events-page {
        padding-bottom: 30px;
    }

    .event-stat-card {
        border: 0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
    }

    .event-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,.09);
    }

    .event-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        background: rgba(25,135,84,.10);
        color: #198754;
        flex-shrink: 0;
    }

    .event-stat-number {
        font-size: 26px;
        font-weight: 700;
        line-height: 1;
        color: #212529;
    }

    .event-stat-label {
        font-size: 13px;
        color: #6c757d;
        margin-top: 6px;
    }

    .events-main-card {
        border: 0;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        overflow: hidden;
    }

    .events-main-card .card-header {
        background: #fff;
        border-bottom: 1px solid #edf0f2;
        padding: 20px;
    }

    .events-main-card .card-body {
        padding: 0;
    }

    .events-toolbar {
        padding: 18px 20px;
        background: #fafbfc;
        border-bottom: 1px solid #edf0f2;
    }

    .event-table {
        margin: 0;
    }

    .event-table th {
        background: #f8f9fa;
        color: #495057;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .03em;
        white-space: nowrap;
        border-bottom: 1px solid #e9ecef;
        padding: 14px 16px;
    }

    .event-table td {
        padding: 15px 16px;
        vertical-align: middle;
        border-color: #f0f1f3;
    }

    .event-table tbody tr {
        transition: background .15s ease;
    }

    .event-table tbody tr:hover {
        background: #fafcff;
    }

    .event-image {
        width: 72px;
        height: 52px;
        object-fit: cover;
        border-radius: 9px;
        background: #f1f3f5;
    }

    .event-image-placeholder {
        width: 72px;
        height: 52px;
        border-radius: 9px;
        background: #f1f3f5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #adb5bd;
        font-size: 18px;
    }

    .event-title {
        font-weight: 700;
        color: #212529;
        margin-bottom: 3px;
    }

    .event-description {
        max-width: 280px;
        font-size: 12px;
        color: #6c757d;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .event-meta {
        font-size: 13px;
        color: #495057;
        line-height: 1.6;
    }

    .event-meta i {
        width: 18px;
        color: #6c757d;
    }

    .event-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-upcoming {
        background: rgba(13,110,253,.10);
        color: #0d6efd;
    }

    .status-ongoing {
        background: rgba(25,135,84,.12);
        color: #198754;
    }

    .status-completed {
        background: rgba(108,117,125,.12);
        color: #6c757d;
    }

    .status-cancelled {
        background: rgba(220,53,69,.10);
        color: #dc3545;
    }

    .visibility-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 600;
    }

    .visibility-public {
        color: #198754;
    }

    .visibility-private {
        color: #6c757d;
    }

    .event-actions {
        display: flex;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .event-actions .btn {
        width: 34px;
        height: 34px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }

    .empty-events {
        padding: 70px 20px;
        text-align: center;
    }

    .empty-events-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #f1f3f5;
        color: #adb5bd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto 18px;
    }

    .filter-label {
        font-size: 12px;
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }

    .results-count {
        font-size: 13px;
        color: #6c757d;
    }

    @media (max-width: 767.98px) {
        .events-toolbar .row > div {
            margin-bottom: 10px;
        }

        .event-table {
            min-width: 950px;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>

<div class="container-fluid events-page">

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 mb-1 fw-bold">
                <i class="bi bi-calendar-event me-2"></i>
                Events Management
            </h1>

            <p class="text-muted mb-0">
                Create, manage and publish Garko Orphans Foundation events.
            </p>
        </div>

        <a href="<?= h(eventUrl('/admin/events/create')) ?>"
           class="btn btn-success">
            <i class="bi bi-plus-lg me-1"></i>
            Create Event
        </a>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">

        <div class="col-6 col-xl-2">
            <div class="card event-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="event-stat-icon">
                            <i class="bi bi-calendar3"></i>
                        </div>

                        <div>
                            <div class="event-stat-number">
                                <?= $totalEvents ?>
                            </div>

                            <div class="event-stat-label">
                                Total Events
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card event-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="event-stat-icon"
                             style="background:rgba(13,110,253,.10);color:#0d6efd;">
                            <i class="bi bi-calendar-check"></i>
                        </div>

                        <div>
                            <div class="event-stat-number">
                                <?= $upcomingEvents ?>
                            </div>

                            <div class="event-stat-label">
                                Upcoming
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card event-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="event-stat-icon"
                             style="background:rgba(25,135,84,.10);color:#198754;">
                            <i class="bi bi-broadcast"></i>
                        </div>

                        <div>
                            <div class="event-stat-number">
                                <?= $ongoingEvents ?>
                            </div>

                            <div class="event-stat-label">
                                Ongoing
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card event-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="event-stat-icon"
                             style="background:rgba(108,117,125,.10);color:#6c757d;">
                            <i class="bi bi-check2-circle"></i>
                        </div>

                        <div>
                            <div class="event-stat-number">
                                <?= $completedEvents ?>
                            </div>

                            <div class="event-stat-label">
                                Completed
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card event-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="event-stat-icon"
                             style="background:rgba(220,53,69,.10);color:#dc3545;">
                            <i class="bi bi-x-circle"></i>
                        </div>

                        <div>
                            <div class="event-stat-number">
                                <?= $cancelledEvents ?>
                            </div>

                            <div class="event-stat-label">
                                Cancelled
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-2">
            <div class="card event-stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">
                        <div class="event-stat-icon"
                             style="background:rgba(111,66,193,.10);color:#6f42c1;">
                            <i class="bi bi-globe2"></i>
                        </div>

                        <div>
                            <div class="event-stat-number">
                                <?= $publicEvents ?>
                            </div>

                            <div class="event-stat-label">
                                Public
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Card -->
    <div class="card events-main-card">

        <div class="card-header">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-1 fw-bold">
                        All Events
                    </h5>

                    <div class="results-count">
                        <?= count($events) ?> event(s) displayed
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="events-toolbar">

            <form method="GET"
                  action="<?= h(eventUrl('/admin/events')) ?>">

                <div class="row g-3 align-items-end">

                    <div class="col-lg-5">
                        <label class="filter-label">
                            Search
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="Search title, description or location..."
                                value="<?= h($search) ?>"
                            >
                        </div>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="filter-label">
                            Status
                        </label>

                        <select name="status"
                                class="form-select">

                            <option value="">All statuses</option>

                            <option value="upcoming"
                                <?= $status === 'upcoming' ? 'selected' : '' ?>>
                                Upcoming
                            </option>

                            <option value="ongoing"
                                <?= $status === 'ongoing' ? 'selected' : '' ?>>
                                Ongoing
                            </option>

                            <option value="completed"
                                <?= $status === 'completed' ? 'selected' : '' ?>>
                                Completed
                            </option>

                            <option value="cancelled"
                                <?= $status === 'cancelled' ? 'selected' : '' ?>>
                                Cancelled
                            </option>

                        </select>
                    </div>

                    <div class="col-md-4 col-lg-2">
                        <label class="filter-label">
                            Visibility
                        </label>

                        <select name="visibility"
                                class="form-select">

                            <option value="">All visibility</option>

                            <option value="public"
                                <?= $visibility === 'public' ? 'selected' : '' ?>>
                                Public
                            </option>

                            <option value="private"
                                <?= $visibility === 'private' ? 'selected' : '' ?>>
                                Private
                            </option>

                        </select>
                    </div>

                    <div class="col-md-4 col-lg-3">
                        <div class="d-flex gap-2">

                            <button type="submit"
                                    class="btn btn-success flex-grow-1">
                                <i class="bi bi-funnel me-1"></i>
                                Filter
                            </button>

                            <a href="<?= h(eventUrl('/admin/events')) ?>"
                               class="btn btn-outline-secondary"
                               title="Clear filters">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>

                        </div>
                    </div>

                </div>

            </form>

        </div>

        <!-- Table -->
        <div class="card-body">

            <?php if (!empty($events)): ?>

                <div class="table-responsive">

                    <table class="table event-table align-middle">

                        <thead>
                        <tr>
                            <th>Event</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Visibility</th>
                            <th class="text-end">Actions</th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($events as $event): ?>

                            <?php
                            $eventId = (int)($event['id'] ?? 0);

                            $title = $event['title'] ?? 'Untitled Event';

                            $description =
                                trim((string)($event['description'] ?? ''));

                            $eventDate =
                                $event['event_date'] ?? null;

                            $endDate =
                                $event['end_date'] ?? null;

                            $eventTime =
                                $event['event_time'] ?? null;

                            $location =
                                trim((string)($event['location'] ?? ''));

                            $eventStatus =
                                strtolower((string)($event['status'] ?? 'upcoming'));

                            $isPublic =
                                (int)($event['is_public'] ?? 0) === 1;

                            $image =
                                trim((string)($event['image'] ?? ''));

                            $imageUrl = '';

                            if ($image !== '') {
                                $imageUrl = eventUrl('/' . ltrim($image, '/'));
                            }

                            $statusClass = match ($eventStatus) {
                                'ongoing' => 'status-ongoing',
                                'completed' => 'status-completed',
                                'cancelled' => 'status-cancelled',
                                default => 'status-upcoming'
                            };

                            $statusIcon = match ($eventStatus) {
                                'ongoing' => 'bi-broadcast',
                                'completed' => 'bi-check-circle',
                                'cancelled' => 'bi-x-circle',
                                default => 'bi-clock'
                            };

                            $formattedDate = '—';

                            if ($eventDate) {
                                try {
                                    $formattedDate =
                                        (new DateTime($eventDate))->format('d M Y');
                                } catch (\Throwable $e) {
                                    $formattedDate = h($eventDate);
                                }
                            }

                            $formattedEndDate = '';

                            if ($endDate) {
                                try {
                                    $formattedEndDate =
                                        (new DateTime($endDate))->format('d M Y');
                                } catch (\Throwable $e) {
                                    $formattedEndDate = h($endDate);
                                }
                            }

                            $formattedTime = '';

                            if ($eventTime) {
                                try {
                                    $formattedTime =
                                        (new DateTime($eventTime))->format('g:i A');
                                } catch (\Throwable $e) {
                                    $formattedTime = h($eventTime);
                                }
                            }
                            ?>

                            <tr>

                                <!-- Event -->
                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        <?php if ($imageUrl !== ''): ?>

                                            <img
                                                src="<?= h($imageUrl) ?>"
                                                alt="<?= h($title) ?>"
                                                class="event-image"
                                            >

                                        <?php else: ?>

                                            <div class="event-image-placeholder">
                                                <i class="bi bi-calendar-event"></i>
                                            </div>

                                        <?php endif; ?>

                                        <div style="min-width:0;">

                                            <div class="event-title">
                                                <?= h($title) ?>
                                            </div>

                                            <?php if ($description !== ''): ?>

                                                <div class="event-description">
                                                    <?= h($description) ?>
                                                </div>

                                            <?php else: ?>

                                                <div class="event-description">
                                                    No description provided
                                                </div>

                                            <?php endif; ?>

                                        </div>

                                    </div>

                                </td>

                                <!-- Date -->
                                <td>

                                    <div class="event-meta">

                                        <div>
                                            <i class="bi bi-calendar3"></i>
                                            <?= h($formattedDate) ?>
                                        </div>

                                        <?php if ($formattedEndDate !== ''): ?>

                                            <div>
                                                <i class="bi bi-calendar-range"></i>
                                                Until <?= h($formattedEndDate) ?>
                                            </div>

                                        <?php endif; ?>

                                        <?php if ($formattedTime !== ''): ?>

                                            <div>
                                                <i class="bi bi-clock"></i>
                                                <?= h($formattedTime) ?>
                                            </div>

                                        <?php endif; ?>

                                    </div>

                                </td>

                                <!-- Location -->
                                <td>

                                    <?php if ($location !== ''): ?>

                                        <div class="event-meta">
                                            <i class="bi bi-geo-alt"></i>
                                            <?= h($location) ?>
                                        </div>

                                    <?php else: ?>

                                        <span class="text-muted">
                                            Not specified
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <!-- Status -->
                                <td>

                                    <span class="event-status <?= h($statusClass) ?>">
                                        <i class="bi <?= h($statusIcon) ?>"></i>
                                        <?= h(ucfirst($eventStatus)) ?>
                                    </span>

                                </td>

                                <!-- Visibility -->
                                <td>

                                    <?php if ($isPublic): ?>

                                        <span class="visibility-badge visibility-public">
                                            <i class="bi bi-globe2"></i>
                                            Public
                                        </span>

                                    <?php else: ?>

                                        <span class="visibility-badge visibility-private">
                                            <i class="bi bi-lock"></i>
                                            Private
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <!-- Actions -->
                                <td>

                                    <div class="event-actions justify-content-end">

                                        <a
                                            href="<?= h(eventUrl('/admin/events/' . $eventId . '/edit')) ?>"
                                            class="btn btn-outline-primary"
                                            title="Edit event"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form
                                            method="POST"
                                            action="<?= h(eventUrl('/admin/events/' . $eventId . '/delete')) ?>"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');"
                                        >

                                            <?php if ($csrf !== ''): ?>

                                                <input
                                                    type="hidden"
                                                    name="_token"
                                                    value="<?= h($csrf) ?>"
                                                >

                                            <?php endif; ?>

                                            <button
                                                type="submit"
                                                class="btn btn-outline-danger"
                                                title="Delete event"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="empty-events">

                    <div class="empty-events-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>

                    <h5 class="fw-bold">
                        No events found
                    </h5>

                    <p class="text-muted mb-4">
                        There are no events matching your current filters.
                    </p>

                    <a
                        href="<?= h(eventUrl('/admin/events/create')) ?>"
                        class="btn btn-success"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Create Your First Event
                    </a>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>