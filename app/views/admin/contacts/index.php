<?php
/**
 * GOF3-light
 * Admin Contact / Support Messages
 */

if (!function_exists('h')) {
    function h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$messages = $messages ?? [];
$status   = $status ?? '';
$page     = max(1, (int)($page ?? 1));
$totalPages = max(1, (int)($totalPages ?? 1));

/*
 * Search is performed client-side because the current
 * ContactsController only supports status filtering.
 */
$unreadCount = 0;
$readCount   = 0;

foreach ($messages as $message) {
    $messageStatus = strtolower((string)($message['status'] ?? ''));

    if ($messageStatus === 'unread') {
        $unreadCount++;
    } elseif ($messageStatus === 'read') {
        $readCount++;
    }
}

$statusBadge = function ($messageStatus): string {
    $status = strtolower((string)$messageStatus);

    if ($status === 'unread') {
        return '<span class="contact-status unread">
                    <span class="status-dot"></span> Unread
                </span>';
    }

    if ($status === 'read') {
        return '<span class="contact-status read">
                    <span class="status-dot"></span> Read
                </span>';
    }

    return '<span class="contact-status">
                <span class="status-dot"></span> ' . h(ucfirst($status ?: 'Unknown')) . '
            </span>';
};
?>

<style>
    .contacts-page {
        max-width: 1500px;
        margin: 0 auto;
    }

    .contacts-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .contacts-title {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .contacts-title-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(13, 110, 253, .10);
        color: #0d6efd;
        font-size: 21px;
    }

    .contacts-title h1 {
        margin: 0;
        font-size: 25px;
        font-weight: 700;
        color: #212529;
    }

    .contacts-title p {
        margin: 4px 0 0;
        color: #6c757d;
        font-size: 14px;
    }

    .contact-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(150px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .contact-stat {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        padding: 18px;
        display: flex;
        align-items: center;
        gap: 13px;
        box-shadow: 0 3px 12px rgba(0,0,0,.035);
    }

    .contact-stat-icon {
        width: 42px;
        height: 42px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #495057;
    }

    .contact-stat strong {
        display: block;
        font-size: 21px;
        line-height: 1.2;
        color: #212529;
    }

    .contact-stat span {
        display: block;
        margin-top: 3px;
        color: #6c757d;
        font-size: 12px;
    }

    .contact-toolbar {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        padding: 15px;
        margin-bottom: 18px;
        box-shadow: 0 3px 12px rgba(0,0,0,.035);
    }

    .contact-toolbar-inner {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .contact-search {
        flex: 1;
        min-width: 240px;
        position: relative;
    }

    .contact-search i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #adb5bd;
    }

    .contact-search input {
        width: 100%;
        height: 42px;
        border: 1px solid #dee2e6;
        border-radius: 9px;
        padding: 0 14px 0 38px;
        outline: none;
        font-size: 14px;
    }

    .contact-search input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 3px rgba(13,110,253,.08);
    }

    .contact-filter {
        height: 42px;
        min-width: 150px;
        border: 1px solid #dee2e6;
        border-radius: 9px;
        padding: 0 12px;
        background: #fff;
        font-size: 14px;
        color: #343a40;
    }

    .messages-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 3px 12px rgba(0,0,0,.035);
    }

    .messages-card-header {
        padding: 17px 20px;
        border-bottom: 1px solid #edf0f2;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .messages-card-header h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: #212529;
    }

    .message-count {
        font-size: 12px;
        color: #6c757d;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .contacts-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 850px;
    }

    .contacts-table th {
        background: #f8f9fa;
        color: #6c757d;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
        padding: 13px 16px;
        border-bottom: 1px solid #e9ecef;
        white-space: nowrap;
    }

    .contacts-table td {
        padding: 15px 16px;
        border-bottom: 1px solid #f0f1f2;
        vertical-align: middle;
        font-size: 13px;
        color: #343a40;
    }

    .contacts-table tbody tr {
        transition: background .15s ease;
    }

    .contacts-table tbody tr:hover {
        background: #fafbfc;
    }

    .contacts-table tbody tr.unread-row {
        background: rgba(13,110,253,.025);
    }

    .sender {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .sender-avatar {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e9f2ff;
        color: #0d6efd;
        font-weight: 700;
        font-size: 13px;
    }

    .sender-name {
        font-weight: 650;
        color: #212529;
        margin-bottom: 2px;
    }

    .sender-email {
        color: #6c757d;
        font-size: 11px;
        word-break: break-word;
    }

    .subject-cell {
        max-width: 230px;
    }

    .subject {
        font-weight: 600;
        color: #212529;
    }

    .message-preview {
        max-width: 300px;
        color: #6c757d;
        font-size: 12px;
        line-height: 1.5;
    }

    .date-cell {
        white-space: nowrap;
        color: #6c757d;
        font-size: 12px;
    }

    .contact-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 9px;
        border-radius: 20px;
        background: #f1f3f5;
        color: #495057;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .contact-status.unread {
        background: #fff3cd;
        color: #856404;
    }

    .contact-status.read {
        background: #d1e7dd;
        color: #0f5132;
    }

    .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .contact-actions {
        display: flex;
        align-items: center;
        gap: 7px;
        white-space: nowrap;
    }

    .btn-contact {
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        border-radius: 8px;
        height: 34px;
        padding: 0 10px;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-contact:hover {
        background: #f8f9fa;
        color: #212529;
    }

    .btn-read {
        color: #0d6efd;
        border-color: #cfe2ff;
        background: #f5f9ff;
    }

    .btn-read:hover {
        background: #e7f1ff;
        color: #0a58ca;
    }

    .empty-state {
        text-align: center;
        padding: 65px 20px;
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #adb5bd;
        font-size: 25px;
    }

    .empty-state h3 {
        margin: 0 0 7px;
        font-size: 17px;
        color: #343a40;
    }

    .empty-state p {
        margin: 0;
        color: #6c757d;
        font-size: 13px;
    }

    .pagination-wrap {
        padding: 15px 18px;
        border-top: 1px solid #edf0f2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 12px;
    }

    .pagination {
        display: flex;
        gap: 5px;
        margin: 0;
    }

    .pagination a {
        min-width: 34px;
        height: 34px;
        padding: 0 9px;
        border: 1px solid #dee2e6;
        border-radius: 7px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #495057;
        font-size: 12px;
        background: #fff;
    }

    .pagination a:hover {
        background: #f8f9fa;
    }

    .pagination a.active {
        background: #0d6efd;
        color: #fff;
        border-color: #0d6efd;
    }

    .pagination a.disabled {
        opacity: .45;
        pointer-events: none;
    }

    .message-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 9999;
        padding: 25px;
        overflow-y: auto;
    }

    .message-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .message-modal-box {
        width: 100%;
        max-width: 650px;
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
    }

    .modal-header-custom {
        padding: 18px 20px;
        border-bottom: 1px solid #edf0f2;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header-custom h3 {
        margin: 0;
        font-size: 17px;
    }

    .modal-close {
        border: 0;
        background: transparent;
        font-size: 23px;
        color: #6c757d;
        cursor: pointer;
    }

    .modal-body-custom {
        padding: 22px;
    }

    .modal-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .modal-meta-item small {
        display: block;
        color: #6c757d;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .modal-meta-item strong {
        color: #212529;
        font-size: 13px;
        word-break: break-word;
    }

    .full-message {
        padding: 16px;
        background: #f8f9fa;
        border-radius: 10px;
        color: #343a40;
        line-height: 1.7;
        font-size: 13px;
        white-space: pre-wrap;
        word-break: break-word;
    }

    @media (max-width: 768px) {
        .contact-stats {
            grid-template-columns: 1fr;
        }

        .contacts-title h1 {
            font-size: 21px;
        }

        .modal-meta {
            grid-template-columns: 1fr;
        }

        .message-modal {
            padding: 12px;
        }
    }

    @media print {
        .contacts-header,
        .contact-stats,
        .contact-toolbar,
        .contact-actions,
        .pagination-wrap {
            display: none !important;
        }

        .messages-card {
            border: 0;
            box-shadow: none;
        }

        .contacts-table {
            min-width: 0;
        }

        .contacts-table th,
        .contacts-table td {
            padding: 8px;
        }
    }
</style>

<div class="contacts-page">

    <!-- Header -->
    <div class="contacts-header">
        <div class="contacts-title">
            <div class="contacts-title-icon">
                <i class="bi bi-headset"></i>
            </div>

            <div>
                <h1>Support & Contact Messages</h1>
                <p>Manage messages and enquiries submitted to Garko Orphans Foundation.</p>
            </div>
        </div>

        <button type="button"
                class="btn-contact"
                onclick="window.print()">
            <i class="bi bi-printer"></i>
            Print
        </button>
    </div>

    <!-- Statistics -->
    <div class="contact-stats">

        <div class="contact-stat">
            <div class="contact-stat-icon">
                <i class="bi bi-envelope"></i>
            </div>
            <div>
                <strong><?= count($messages) ?></strong>
                <span>Messages on this page</span>
            </div>
        </div>

        <div class="contact-stat">
            <div class="contact-stat-icon">
                <i class="bi bi-envelope-exclamation"></i>
            </div>
            <div>
                <strong><?= $unreadCount ?></strong>
                <span>Unread on this page</span>
            </div>
        </div>

        <div class="contact-stat">
            <div class="contact-stat-icon">
                <i class="bi bi-envelope-check"></i>
            </div>
            <div>
                <strong><?= $readCount ?></strong>
                <span>Read on this page</span>
            </div>
        </div>

    </div>

    <!-- Filters -->
    <div class="contact-toolbar">
        <form method="GET" action="<?= h(url('/admin/contacts')) ?>">
            <div class="contact-toolbar-inner">

                <div class="contact-search">
                    <i class="bi bi-search"></i>
                    <input
                        type="search"
                        id="messageSearch"
                        placeholder="Search sender, email, subject or message..."
                        autocomplete="off"
                    >
                </div>

                <select
                    name="status"
                    class="contact-filter"
                    onchange="this.form.submit()"
                >
                    <option value="">All messages</option>
                    <option value="unread" <?= $status === 'unread' ? 'selected' : '' ?>>
                        Unread
                    </option>
                    <option value="read" <?= $status === 'read' ? 'selected' : '' ?>>
                        Read
                    </option>
                </select>

                <?php if (!empty($status)): ?>
                    <a
                        href="<?= h(url('/admin/contacts')) ?>"
                        class="btn-contact"
                    >
                        <i class="bi bi-x-circle"></i>
                        Clear Filter
                    </a>
                <?php endif; ?>

            </div>
        </form>
    </div>

    <!-- Messages -->
    <div class="messages-card">

        <div class="messages-card-header">
            <h2>
                <i class="bi bi-chat-left-text me-2"></i>
                Incoming Messages
            </h2>

            <span class="message-count">
                Page <?= $page ?> of <?= $totalPages ?>
            </span>
        </div>

        <?php if (!empty($messages)): ?>

            <div class="table-responsive">

                <table class="contacts-table" id="contactsTable">

                    <thead>
                        <tr>
                            <th>Sender</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php foreach ($messages as $message): ?>

                        <?php
                        $messageId = (int)($message['id'] ?? 0);

                        $name = trim(
                            (string)($message['name']
                            ?? $message['full_name']
                            ?? $message['sender_name']
                            ?? 'Unknown')
                        );

                        $email = trim(
                            (string)($message['email']
                            ?? $message['sender_email']
                            ?? '')
                        );

                        $phone = trim(
                            (string)($message['phone']
                            ?? $message['phone_number']
                            ?? '')
                        );

                        $subject = trim(
                            (string)($message['subject']
                            ?? $message['title']
                            ?? 'No subject')
                        );

                        $body = trim(
                            (string)($message['message']
                            ?? $message['content']
                            ?? '')
                        );

                        $messageStatus = strtolower(
                            (string)($message['status'] ?? '')
                        );

                        $createdAt = $message['created_at']
                            ?? $message['submitted_at']
                            ?? null;

                        $initial = strtoupper(
                            substr(
                                preg_replace('/[^A-Za-z0-9]/', '', $name),
                                0,
                                1
                            )
                        );

                        if ($initial === '') {
                            $initial = '?';
                        }

                        $searchText = strtolower(
                            $name . ' ' .
                            $email . ' ' .
                            $phone . ' ' .
                            $subject . ' ' .
                            $body
                        );

                        $shortMessage = mb_strlen($body) > 95
                            ? mb_substr($body, 0, 95) . '...'
                            : $body;
                        ?>

                        <tr
                            class="<?= $messageStatus === 'unread' ? 'unread-row' : '' ?>"
                            data-search="<?= h($searchText) ?>"
                        >

                            <!-- Sender -->
                            <td>
                                <div class="sender">

                                    <div class="sender-avatar">
                                        <?= h($initial) ?>
                                    </div>

                                    <div>
                                        <div class="sender-name">
                                            <?= h($name) ?>
                                        </div>

                                        <?php if ($email): ?>
                                            <div class="sender-email">
                                                <?= h($email) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </td>

                            <!-- Subject -->
                            <td class="subject-cell">
                                <div class="subject">
                                    <?= h($subject) ?>
                                </div>
                            </td>

                            <!-- Message -->
                            <td>
                                <div class="message-preview">
                                    <?= h($shortMessage ?: 'No message content.') ?>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td>
                                <?php if ($phone): ?>
                                    <div>
                                        <i class="bi bi-telephone me-1"></i>
                                        <?= h($phone) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- Date -->
                            <td class="date-cell">
                                <?php if ($createdAt): ?>
                                    <?= h(date('d M Y', strtotime($createdAt))) ?>
                                    <br>
                                    <small>
                                        <?= h(date('h:i A', strtotime($createdAt))) ?>
                                    </small>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <!-- Status -->
                            <td>
                                <?= $statusBadge($messageStatus) ?>
                            </td>

                            <!-- Actions -->
                            <td>

                                <div class="contact-actions">

                                    <button
                                        type="button"
                                        class="btn-contact"
                                        onclick="openMessage(<?= $messageId ?>)"
                                    >
                                        <i class="bi bi-eye"></i>
                                        View
                                    </button>

                                    <?php if ($messageStatus === 'unread'): ?>

                                        <form
                                            method="POST"
                                            action="<?= h(url('/admin/contacts/' . $messageId . '/mark-read')) ?>"
                                            style="display:inline;"
                                        >
                                            <input
                                                type="hidden"
                                                name="_token"
                                                value="<?= h($_SESSION['csrf_token'] ?? '') ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn-contact btn-read"
                                                title="Mark as read"
                                            >
                                                <i class="bi bi-check2"></i>
                                                Read
                                            </button>
                                        </form>

                                    <?php endif; ?>

                                </div>

                                <!-- Hidden message data for modal -->
                                <div
                                    id="message-data-<?= $messageId ?>"
                                    style="display:none;"
                                    data-name="<?= h($name) ?>"
                                    data-email="<?= h($email) ?>"
                                    data-phone="<?= h($phone) ?>"
                                    data-subject="<?= h($subject) ?>"
                                    data-message="<?= h($body) ?>"
                                    data-date="<?= h($createdAt ? date('d M Y, h:i A', strtotime($createdAt)) : '—') ?>"
                                ></div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>

                <div class="pagination-wrap">

                    <div class="pagination-info">
                        Showing page <?= $page ?> of <?= $totalPages ?>
                    </div>

                    <div class="pagination">

                        <?php
                        $previousPage = $page - 1;
                        $nextPage = $page + 1;
                        ?>

                        <a
                            href="<?= $page > 1
                                ? h(url('/admin/contacts?page=' . $previousPage . (!empty($status) ? '&status=' . urlencode($status) : '')))
                                : '#'
                            ?>"
                            class="<?= $page <= 1 ? 'disabled' : '' ?>"
                        >
                            <i class="bi bi-chevron-left"></i>
                        </a>

                        <?php
                        $startPage = max(1, $page - 2);
                        $endPage   = min($totalPages, $page + 2);

                        for ($p = $startPage; $p <= $endPage; $p++):
                        ?>

                            <a
                                href="<?= h(
                                    url(
                                        '/admin/contacts?page=' . $p .
                                        (!empty($status)
                                            ? '&status=' . urlencode($status)
                                            : '')
                                    )
                                ) ?>"
                                class="<?= $p === $page ? 'active' : '' ?>"
                            >
                                <?= $p ?>
                            </a>

                        <?php endfor; ?>

                        <a
                            href="<?= $page < $totalPages
                                ? h(url('/admin/contacts?page=' . $nextPage . (!empty($status) ? '&status=' . urlencode($status) : '')))
                                : '#'
                            ?>"
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
                    <i class="bi bi-inbox"></i>
                </div>

                <h3>No contact messages found</h3>

                <?php if (!empty($status)): ?>
                    <p>
                        There are no <?= h($status) ?> messages at the moment.
                    </p>
                <?php else: ?>
                    <p>
                        Messages submitted through the contact/support system
                        will appear here.
                    </p>
                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</div>


<!-- Message Details Modal -->
<div
    class="message-modal"
    id="messageModal"
    onclick="closeMessageOutside(event)"
>
    <div
        class="message-modal-box"
        onclick="event.stopPropagation()"
    >

        <div class="modal-header-custom">

            <h3>
                <i class="bi bi-envelope-open me-2"></i>
                Message Details
            </h3>

            <button
                type="button"
                class="modal-close"
                onclick="closeMessage()"
            >
                &times;
            </button>

        </div>

        <div class="modal-body-custom">

            <div class="modal-meta">

                <div class="modal-meta-item">
                    <small>Sender</small>
                    <strong id="modalName">—</strong>
                </div>

                <div class="modal-meta-item">
                    <small>Email</small>
                    <strong id="modalEmail">—</strong>
                </div>

                <div class="modal-meta-item">
                    <small>Phone</small>
                    <strong id="modalPhone">—</strong>
                </div>

                <div class="modal-meta-item">
                    <small>Date</small>
                    <strong id="modalDate">—</strong>
                </div>

            </div>

            <div class="modal-meta-item" style="margin-bottom:15px;">
                <small>Subject</small>
                <strong id="modalSubject">—</strong>
            </div>

            <div class="modal-meta-item">
                <small style="margin-bottom:8px;">Message</small>

                <div
                    class="full-message"
                    id="modalMessage"
                ></div>
            </div>

        </div>

    </div>
</div>


<script>
(function () {

    const searchInput = document.getElementById('messageSearch');

    if (searchInput) {

        searchInput.addEventListener('input', function () {

            const query = this.value.toLowerCase().trim();

            document
                .querySelectorAll('#contactsTable tbody tr')
                .forEach(function (row) {

                    const searchable = (
                        row.getAttribute('data-search') || ''
                    ).toLowerCase();

                    row.style.display =
                        !query || searchable.includes(query)
                            ? ''
                            : 'none';

                });

        });

    }

})();


function openMessage(id) {

    const data = document.getElementById('message-data-' + id);

    if (!data) {
        return;
    }

    document.getElementById('modalName').textContent =
        data.dataset.name || '—';

    document.getElementById('modalEmail').textContent =
        data.dataset.email || '—';

    document.getElementById('modalPhone').textContent =
        data.dataset.phone || '—';

    document.getElementById('modalDate').textContent =
        data.dataset.date || '—';

    document.getElementById('modalSubject').textContent =
        data.dataset.subject || 'No subject';

    document.getElementById('modalMessage').textContent =
        data.dataset.message || 'No message content.';

    document.getElementById('messageModal')
        .classList.add('show');

    document.body.style.overflow = 'hidden';
}


function closeMessage() {

    document.getElementById('messageModal')
        .classList.remove('show');

    document.body.style.overflow = '';

}


function closeMessageOutside(event) {

    if (event.target.id === 'messageModal') {
        closeMessage();
    }

}


document.addEventListener('keydown', function (event) {

    if (event.key === 'Escape') {
        closeMessage();
    }

});
</script>