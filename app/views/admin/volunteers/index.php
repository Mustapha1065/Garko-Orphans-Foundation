<?php
/**
 * GOF3-light
 * Admin Volunteer Applications
 */

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

use GOF\Helpers\Csrf;

$applications = $applications ?? [];
$status = $status ?? '';
$page = max(1, (int)($page ?? 1));
$totalPages = max(1, (int)($totalPages ?? 1));
$summary = $summary ?? [];

$totalApplications = array_sum(array_map('intval', $summary));

$statuses = [
    'reviewed'  => ['label' => 'Reviewed',  'icon' => 'bi-eye'],
    'approved' => ['label' => 'Approved', 'icon' => 'bi-check-circle'],
    'rejected' => ['label' => 'Rejected', 'icon' => 'bi-x-circle'],
    'contacted' => ['label' => 'Contacted', 'icon' => 'bi-telephone'],
];

$statusBadge = function ($value): string {
    $value = strtolower((string)$value);

    $config = [
        'reviewed' => [
            'label' => 'Reviewed',
            'class' => 'reviewed',
            'icon'  => 'bi-eye'
        ],
        'approved' => [
            'label' => 'Approved',
            'class' => 'approved',
            'icon'  => 'bi-check-circle'
        ],
        'rejected' => [
            'label' => 'Rejected',
            'class' => 'rejected',
            'icon'  => 'bi-x-circle'
        ],
        'contacted' => [
            'label' => 'Contacted',
            'class' => 'contacted',
            'icon'  => 'bi-telephone'
        ],
    ];

    if (!isset($config[$value])) {
        return '<span class="volunteer-status unknown">
                    <span class="status-dot"></span>
                    ' . h(ucfirst($value ?: 'Pending')) . '
                </span>';
    }

    $item = $config[$value];

    return '<span class="volunteer-status ' . h($item['class']) . '">
                <span class="status-dot"></span>
                <i class="bi ' . h($item['icon']) . '"></i>
                ' . h($item['label']) . '
            </span>';
};

$initials = function ($name): string {
    $name = trim((string)$name);

    if ($name === '') {
        return '?';
    }

    $parts = preg_split('/\s+/', $name);

    if (count($parts) === 1) {
        return strtoupper(substr($parts[0], 0, 1));
    }

    return strtoupper(
        substr($parts[0], 0, 1) .
        substr($parts[count($parts) - 1], 0, 1)
    );
};

$statusUrl = function ($value): string {
    if ($value === '') {
        return url('/admin/volunteers');
    }

    return url('/admin/volunteers?status=' . urlencode($value));
};
?>

<style>
.volunteers-page {
    max-width: 1500px;
    margin: 0 auto;
}

.volunteers-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
    margin-bottom:24px;
    flex-wrap:wrap;
}

.volunteers-title {
    display:flex;
    align-items:center;
    gap:14px;
}

.volunteers-title-icon {
    width:50px;
    height:50px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(13,110,253,.10);
    color:#0d6efd;
    font-size:22px;
}

.volunteers-title h1 {
    margin:0;
    font-size:25px;
    font-weight:700;
    color:#212529;
}

.volunteers-title p {
    margin:4px 0 0;
    color:#6c757d;
    font-size:14px;
}

.btn-volunteer {
    height:38px;
    padding:0 13px;
    border-radius:8px;
    border:1px solid #dee2e6;
    background:#fff;
    color:#495057;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    font-size:12px;
    text-decoration:none;
    cursor:pointer;
    transition:.15s ease;
}

.btn-volunteer:hover {
    background:#f8f9fa;
    color:#212529;
}

.volunteer-stats {
    display:grid;
    grid-template-columns:repeat(5,minmax(130px,1fr));
    gap:14px;
    margin-bottom:22px;
}

.volunteer-stat {
    background:#fff;
    border:1px solid #e9ecef;
    border-radius:14px;
    padding:17px;
    display:flex;
    align-items:center;
    gap:12px;
    box-shadow:0 3px 12px rgba(0,0,0,.035);
    text-decoration:none;
    color:inherit;
    transition:.15s ease;
}

.volunteer-stat:hover {
    transform:translateY(-1px);
    box-shadow:0 6px 18px rgba(0,0,0,.06);
}

.volunteer-stat-icon {
    width:40px;
    height:40px;
    min-width:40px;
    border-radius:11px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:#f8f9fa;
    color:#495057;
}

.volunteer-stat strong {
    display:block;
    font-size:20px;
    line-height:1.2;
    color:#212529;
}

.volunteer-stat span {
    display:block;
    margin-top:3px;
    font-size:11px;
    color:#6c757d;
}

.volunteer-toolbar {
    background:#fff;
    border:1px solid #e9ecef;
    border-radius:14px;
    padding:15px;
    margin-bottom:18px;
    box-shadow:0 3px 12px rgba(0,0,0,.035);
}

.volunteer-toolbar-inner {
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}

.volunteer-search {
    flex:1;
    min-width:250px;
    position:relative;
}

.volunteer-search i {
    position:absolute;
    left:13px;
    top:50%;
    transform:translateY(-50%);
    color:#adb5bd;
}

.volunteer-search input {
    width:100%;
    height:42px;
    border:1px solid #dee2e6;
    border-radius:9px;
    padding:0 14px 0 38px;
    outline:none;
    font-size:13px;
}

.volunteer-search input:focus {
    border-color:#86b7fe;
    box-shadow:0 0 0 3px rgba(13,110,253,.08);
}

.volunteer-filter {
    height:42px;
    min-width:155px;
    border:1px solid #dee2e6;
    border-radius:9px;
    padding:0 12px;
    background:#fff;
    color:#343a40;
    font-size:13px;
    outline:none;
}

.volunteer-table-card {
    background:#fff;
    border:1px solid #e9ecef;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 3px 12px rgba(0,0,0,.035);
}

.volunteer-card-header {
    padding:17px 20px;
    border-bottom:1px solid #edf0f2;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
}

.volunteer-card-header h2 {
    margin:0;
    font-size:17px;
    font-weight:700;
    color:#212529;
}

.volunteer-card-header small {
    color:#6c757d;
    font-size:12px;
}

.table-responsive {
    overflow-x:auto;
}

.volunteer-table {
    width:100%;
    min-width:1050px;
    border-collapse:collapse;
}

.volunteer-table th {
    background:#f8f9fa;
    color:#6c757d;
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:.04em;
    font-weight:700;
    padding:13px 15px;
    border-bottom:1px solid #e9ecef;
    white-space:nowrap;
}

.volunteer-table td {
    padding:14px 15px;
    border-bottom:1px solid #f0f1f2;
    vertical-align:middle;
    font-size:13px;
    color:#343a40;
}

.volunteer-table tbody tr {
    transition:background .15s ease;
}

.volunteer-table tbody tr:hover {
    background:#fafbfc;
}

.volunteer-table tbody tr:last-child td {
    border-bottom:0;
}

.applicant {
    display:flex;
    align-items:center;
    gap:10px;
}

.applicant-avatar {
    width:38px;
    height:38px;
    min-width:38px;
    border-radius:50%;
    background:#e9f2ff;
    color:#0d6efd;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    font-weight:700;
}

.applicant-name {
    font-weight:650;
    color:#212529;
    margin-bottom:2px;
}

.applicant-email {
    font-size:11px;
    color:#6c757d;
    word-break:break-word;
}

.contact-info {
    line-height:1.6;
}

.contact-info div {
    white-space:nowrap;
}

.contact-info i {
    width:15px;
    color:#6c757d;
}

.volunteer-status {
    display:inline-flex;
    align-items:center;
    gap:5px;
    padding:5px 9px;
    border-radius:20px;
    font-size:10px;
    font-weight:650;
    white-space:nowrap;
}

.volunteer-status.reviewed {
    background:#cff4fc;
    color:#055160;
}

.volunteer-status.approved {
    background:#d1e7dd;
    color:#0f5132;
}

.volunteer-status.rejected {
    background:#f8d7da;
    color:#842029;
}

.volunteer-status.contacted {
    background:#fff3cd;
    color:#664d03;
}

.volunteer-status.unknown {
    background:#f1f3f5;
    color:#495057;
}

.status-dot {
    width:5px;
    height:5px;
    border-radius:50%;
    background:currentColor;
}

.application-date {
    color:#6c757d;
    white-space:nowrap;
    font-size:11px;
}

.action-group {
    display:flex;
    gap:6px;
    align-items:center;
    flex-wrap:wrap;
}

.action-button {
    height:32px;
    padding:0 9px;
    border-radius:7px;
    border:1px solid #dee2e6;
    background:#fff;
    color:#495057;
    display:inline-flex;
    align-items:center;
    gap:5px;
    font-size:11px;
    cursor:pointer;
    transition:.15s ease;
}

.action-button:hover {
    background:#f8f9fa;
}

.action-button.review {
    color:#055160;
    border-color:#b6effb;
    background:#f3fdff;
}

.action-button.approve {
    color:#0f5132;
    border-color:#badbcc;
    background:#f3fbf6;
}

.action-button.reject {
    color:#842029;
    border-color:#f1aeb5;
    background:#fff6f7;
}

.action-button.contact {
    color:#664d03;
    border-color:#ffe69c;
    background:#fffdf2;
}

.empty-state {
    text-align:center;
    padding:65px 20px;
}

.empty-icon {
    width:65px;
    height:65px;
    border-radius:18px;
    margin:0 auto 16px;
    background:#f8f9fa;
    color:#adb5bd;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:26px;
}

.empty-state h3 {
    margin:0 0 7px;
    font-size:17px;
    color:#343a40;
}

.empty-state p {
    margin:0;
    font-size:13px;
    color:#6c757d;
}

.pagination-wrap {
    padding:15px 18px;
    border-top:1px solid #edf0f2;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    flex-wrap:wrap;
}

.pagination-info {
    color:#6c757d;
    font-size:12px;
}

.pagination {
    display:flex;
    gap:5px;
}

.pagination a {
    min-width:34px;
    height:34px;
    padding:0 9px;
    border:1px solid #dee2e6;
    border-radius:7px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#495057;
    font-size:12px;
    background:#fff;
}

.pagination a:hover {
    background:#f8f9fa;
}

.pagination a.active {
    background:#0d6efd;
    color:#fff;
    border-color:#0d6efd;
}

.pagination a.disabled {
    opacity:.45;
    pointer-events:none;
}

.action-form {
    display:inline;
    margin:0;
}

@media (max-width:1200px) {
    .volunteer-stats {
        grid-template-columns:repeat(3,1fr);
    }
}

@media (max-width:768px) {
    .volunteer-stats {
        grid-template-columns:repeat(2,1fr);
    }

    .volunteers-title h1 {
        font-size:21px;
    }
}

@media (max-width:520px) {
    .volunteer-stats {
        grid-template-columns:1fr;
    }

    .volunteer-search {
        min-width:100%;
    }

    .volunteer-filter {
        width:100%;
    }
}

@media print {
    .volunteers-header button,
    .volunteer-stats,
    .volunteer-toolbar,
    .action-group,
    .pagination-wrap {
        display:none !important;
    }

    .volunteer-table-card {
        border:0;
        box-shadow:none;
    }
}
</style>

<div class="volunteers-page">

    <div class="volunteers-header">
        <div class="volunteers-title">
            <div class="volunteers-title-icon">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <h1>Volunteer Management</h1>
                <p>
                    Review and manage volunteer applications submitted to
                    Garko Orphans Foundation.
                </p>
            </div>
        </div>

        <button
            type="button"
            class="btn-volunteer"
            onclick="window.print()"
        >
            <i class="bi bi-printer"></i>
            Print
        </button>
    </div>

    <div class="volunteer-stats">

        <a href="<?= h($statusUrl('')) ?>" class="volunteer-stat">
            <div class="volunteer-stat-icon">
                <i class="bi bi-people"></i>
            </div>

            <div>
                <strong><?= (int)$totalApplications ?></strong>
                <span>Total Applications</span>
            </div>
        </a>

        <?php foreach ($statuses as $key => $item): ?>

            <a
                href="<?= h($statusUrl($key)) ?>"
                class="volunteer-stat"
            >
                <div class="volunteer-stat-icon">
                    <i class="bi <?= h($item['icon']) ?>"></i>
                </div>

                <div>
                    <strong><?= (int)($summary[$key] ?? 0) ?></strong>
                    <span><?= h($item['label']) ?></span>
                </div>
            </a>

        <?php endforeach; ?>

    </div>

    <div class="volunteer-toolbar">

        <div class="volunteer-toolbar-inner">

            <div class="volunteer-search">
                <i class="bi bi-search"></i>

                <input
                    type="search"
                    id="volunteerSearch"
                    placeholder="Search applicant, email, phone, skills..."
                    autocomplete="off"
                >
            </div>

            <form
                method="GET"
                action="<?= h(url('/admin/volunteers')) ?>"
                style="display:flex;gap:10px;"
            >
                <select
                    name="status"
                    class="volunteer-filter"
                    onchange="this.form.submit()"
                >
                    <option value="">All statuses</option>

                    <?php foreach ($statuses as $key => $item): ?>
                        <option
                            value="<?= h($key) ?>"
                            <?= $status === $key ? 'selected' : '' ?>
                        >
                            <?= h($item['label']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </form>

            <?php if ($status !== ''): ?>

                <a
                    href="<?= h(url('/admin/volunteers')) ?>"
                    class="btn-volunteer"
                >
                    <i class="bi bi-x-circle"></i>
                    Clear Filter
                </a>

            <?php endif; ?>

        </div>

    </div>

    <div class="volunteer-table-card">

        <div class="volunteer-card-header">
            <h2>
                <i class="bi bi-person-lines-fill me-2"></i>
                Volunteer Applications
            </h2>

            <small>
                Page <?= $page ?> of <?= $totalPages ?>
            </small>
        </div>

        <?php if (!empty($applications)): ?>

            <div class="table-responsive">

                <table
                    class="volunteer-table"
                    id="volunteerTable"
                >

                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Contact</th>
                            <th>Skills / Interest</th>
                            <th>Application Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($applications as $application): ?>

                        <?php
                        $id = (int)($application['id'] ?? 0);

                        $firstName = trim(
                            (string)($application['first_name'] ?? '')
                        );

                        $lastName = trim(
                            (string)($application['last_name'] ?? '')
                        );

                        $name = trim($firstName . ' ' . $lastName);

                        if ($name === '') {
                            $name =
                                $application['name']
                                ?? $application['full_name']
                                ?? $application['applicant_name']
                                ?? 'Unknown Applicant';
                        }

                        $email = trim(
                            (string)($application['email'] ?? '')
                        );

                        $phone = trim(
                            (string)(
                                $application['phone']
                                ?? $application['phone_number']
                                ?? ''
                            )
                        );

                        $skills = trim(
                            (string)(
                                $application['skills']
                                ?? $application['interests']
                                ?? $application['area_of_interest']
                                ?? $application['motivation']
                                ?? '—'
                            )
                        );

                        $createdAt =
                            $application['created_at']
                            ?? $application['submitted_at']
                            ?? null;

                        $applicationStatus = strtolower(
                            (string)(
                                $application['status']
                                ?? 'pending'
                            )
                        );

                        $searchText = strtolower(
                            $name . ' ' .
                            $email . ' ' .
                            $phone . ' ' .
                            $skills . ' ' .
                            $applicationStatus
                        );

                        $shortSkills =
                            mb_strlen($skills) > 70
                                ? mb_substr($skills, 0, 70) . '...'
                                : $skills;
                        ?>

                        <tr data-search="<?= h($searchText) ?>">

                            <td>
                                <div class="applicant">

                                    <div class="applicant-avatar">
                                        <?= h($initials($name)) ?>
                                    </div>

                                    <div>

                                        <div class="applicant-name">
                                            <?= h($name) ?>
                                        </div>

                                        <?php if ($email): ?>
                                            <div class="applicant-email">
                                                <?= h($email) ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>

                                </div>
                            </td>

                            <td>

                                <div class="contact-info">

                                    <?php if ($email): ?>
                                        <div>
                                            <i class="bi bi-envelope"></i>
                                            <?= h($email) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($phone): ?>
                                        <div>
                                            <i class="bi bi-telephone"></i>
                                            <?= h($phone) ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!$email && !$phone): ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>

                                </div>

                            </td>

                            <td>
                                <div
                                    title="<?= h($skills) ?>"
                                    style="max-width:250px;line-height:1.5;"
                                >
                                    <?= h($shortSkills) ?>
                                </div>
                            </td>

                            <td>

                                <div class="application-date">

                                    <?php if ($createdAt): ?>

                                        <?= h(
                                            date(
                                                'd M Y',
                                                strtotime($createdAt)
                                            )
                                        ) ?>

                                        <br>

                                        <?= h(
                                            date(
                                                'h:i A',
                                                strtotime($createdAt)
                                            )
                                        ) ?>

                                    <?php else: ?>
                                        —
                                    <?php endif; ?>

                                </div>

                            </td>

                            <td>
                                <?= $statusBadge($applicationStatus) ?>
                            </td>

                            <td>

                                <div class="action-group">

                                    <?php
                                    $actions = [
                                        'reviewed' => [
                                            'label' => 'Review',
                                            'icon' => 'bi-eye',
                                            'class' => 'review'
                                        ],
                                        'approved' => [
                                            'label' => 'Approve',
                                            'icon' => 'bi-check-lg',
                                            'class' => 'approve'
                                        ],
                                        'rejected' => [
                                            'label' => 'Reject',
                                            'icon' => 'bi-x-lg',
                                            'class' => 'reject'
                                        ],
                                        'contacted' => [
                                            'label' => 'Contacted',
                                            'icon' => 'bi-telephone',
                                            'class' => 'contact'
                                        ],
                                    ];
                                    ?>

                                    <?php foreach ($actions as $action => $button): ?>

                                        <?php if ($applicationStatus !== $action): ?>

                                            <form
                                                method="POST"
                                                action="<?= h(
                                                    url(
                                                        '/admin/volunteers/' .
                                                        $id .
                                                        '/review'
                                                    )
                                                ) ?>"
                                                class="action-form"
                                                onsubmit="return confirmVolunteerAction('<?= h($action) ?>');"
                                            >

                                                <!-- FIXED CSRF TOKEN -->
                                                <input
                                                    type="hidden"
                                                    name="_csrf"
                                                    value="<?= h(Csrf::token()) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="action"
                                                    value="<?= h($action) ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="action-button <?= h($button['class']) ?>"
                                                    title="<?= h($button['label']) ?>"
                                                >
                                                    <i class="bi <?= h($button['icon']) ?>"></i>
                                                    <?= h($button['label']) ?>
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    <?php endforeach; ?>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <?php if ($totalPages > 1): ?>

                <div class="pagination-wrap">

                    <div class="pagination-info">
                        Showing page <?= $page ?> of <?= $totalPages ?>
                    </div>

                    <div class="pagination">

                        <?php
                        $previousPage = $page - 1;
                        $nextPage = $page + 1;

                        $previousUrl = $page > 1
                            ? url(
                                '/admin/volunteers?page=' .
                                $previousPage .
                                ($status !== ''
                                    ? '&status=' . urlencode($status)
                                    : '')
                            )
                            : '#';

                        $nextUrl = $page < $totalPages
                            ? url(
                                '/admin/volunteers?page=' .
                                $nextPage .
                                ($status !== ''
                                    ? '&status=' . urlencode($status)
                                    : '')
                            )
                            : '#';
                        ?>

                        <a
                            href="<?= h($previousUrl) ?>"
                            class="<?= $page <= 1 ? 'disabled' : '' ?>"
                        >
                            <i class="bi bi-chevron-left"></i>
                        </a>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);

                        for (
                            $p = $startPage;
                            $p <= $endPage;
                            $p++
                        ):
                        ?>

                            <a
                                href="<?= h(
                                    url(
                                        '/admin/volunteers?page=' .
                                        $p .
                                        ($status !== ''
                                            ? '&status=' .
                                            urlencode($status)
                                            : '')
                                    )
                                ) ?>"
                                class="<?= $p === $page ? 'active' : '' ?>"
                            >
                                <?= $p ?>
                            </a>

                        <?php endfor; ?>

                        <a
                            href="<?= h($nextUrl) ?>"
                            class="<?= $page >= $totalPages ? 'disabled' : '' ?>"
                        >
                            <i class="bi bi-chevron-right"></i>
                        </a>

                    </div>

                </div>

            <?php endif; ?>

        <?php else: ?>

            <div class="empty-state">

                <div class="empty-icon">
                    <i class="bi bi-people"></i>
                </div>

                <h3>No volunteer applications found</h3>

                <?php if ($status !== ''): ?>

                    <p>
                        There are currently no
                        <?= h($status) ?>
                        volunteer applications.
                    </p>

                <?php else: ?>

                    <p>
                        Volunteer applications submitted through the
                        public system will appear here.
                    </p>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</div>

<script>
(function () {

    const searchInput =
        document.getElementById('volunteerSearch');

    if (!searchInput) {
        return;
    }

    searchInput.addEventListener('input', function () {

        const query =
            this.value.toLowerCase().trim();

        const rows =
            document.querySelectorAll(
                '#volunteerTable tbody tr'
            );

        rows.forEach(function (row) {

            const searchable =
                (
                    row.getAttribute('data-search')
                    || ''
                ).toLowerCase();

            row.style.display =
                !query ||
                searchable.includes(query)
                    ? ''
                    : 'none';

        });

    });

})();

function confirmVolunteerAction(action) {

    const labels = {
        reviewed: 'mark this application as reviewed',
        approved: 'approve this volunteer application',
        rejected: 'reject this volunteer application',
        contacted: 'mark this applicant as contacted'
    };

    const message =
        labels[action]
        || 'update this volunteer application';

    return confirm(
        'Are you sure you want to ' +
        message +
        '?'
    );
}
</script>