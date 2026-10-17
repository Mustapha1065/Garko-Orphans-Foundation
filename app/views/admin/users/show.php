<?php
/**
 * GOF3-light
 * Admin User Profile
 */

if (!function_exists('user_h')) {
    function user_h($value): string
    {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

$user = $user ?? [];

$firstName  = $user['first_name'] ?? '';
$lastName   = $user['last_name'] ?? '';
$email      = $user['email'] ?? '';
$phone      = $user['phone'] ?? '';
$status     = $user['status'] ?? '';
$role       = $user['role'] ?? ($user['role_name'] ?? '');
$createdAt  = $user['created_at'] ?? '';
$updatedAt  = $user['updated_at'] ?? '';

$fullName = trim($firstName . ' ' . $lastName);

if ($fullName === '') {
    $fullName = $user['name'] ?? 'User';
}

if ($status === '') {
    $status = 'Unknown';
}

$statusClass = match (strtolower((string)$status)) {
    'active'   => 'success',
    'inactive' => 'secondary',
    'blocked'  => 'danger',
    'locked'   => 'danger',
    default    => 'secondary'
};
?>

<style>
.user-profile-page {
    padding-bottom: 40px;
}

.user-profile-header {
    background: linear-gradient(135deg, #c9a900 0%, #b79500 100%);
    border-radius: 16px;
    padding: 30px;
    color: #fff;
    margin-bottom: 25px;
}

.user-profile-avatar {
    width: 82px;
    height: 82px;
    border-radius: 50%;
    background: rgba(255,255,255,.18);
    border: 3px solid rgba(255,255,255,.65);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: 700;
}

.user-profile-name {
    margin: 0 0 5px;
    font-weight: 700;
}

.user-profile-email {
    margin: 0;
    opacity: .9;
}

.profile-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 5px 22px rgba(0,0,0,.07);
    overflow: hidden;
}

.profile-card-header {
    padding: 18px 22px;
    border-bottom: 1px solid #eee;
    font-weight: 700;
}

.profile-card-body {
    padding: 22px;
}

.profile-row {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 14px 0;
    border-bottom: 1px solid #f0f0f0;
}

.profile-row:last-child {
    border-bottom: 0;
}

.profile-label {
    color: #6c757d;
    font-weight: 600;
}

.profile-value {
    text-align: right;
    color: #212529;
}

.profile-empty {
    color: #adb5bd;
    font-style: italic;
}

@media (max-width: 575.98px) {
    .user-profile-header {
        padding: 22px;
    }

    .profile-row {
        flex-direction: column;
        gap: 5px;
    }

    .profile-value {
        text-align: left;
    }
}
</style>


<div class="container-fluid user-profile-page">

    <!-- =====================================================
         HEADER
         ===================================================== -->

    <div class="user-profile-header">

        <div class="d-flex align-items-center gap-3 flex-wrap">

            <div class="user-profile-avatar">

                <?= user_h(
                    strtoupper(
                        substr($firstName ?: $fullName, 0, 1)
                    )
                ) ?>

            </div>

            <div>

                <h2 class="user-profile-name">
                    <?= user_h($fullName) ?>
                </h2>

                <p class="user-profile-email">
                    <?= user_h($email) ?>
                </p>

            </div>

        </div>

    </div>


    <!-- =====================================================
         PROFILE INFORMATION
         ===================================================== -->

    <div class="profile-card">

        <div class="profile-card-header">
            <i class="bi bi-person-circle me-2"></i>
            User Profile
        </div>

        <div class="profile-card-body">

            <div class="profile-row">

                <div class="profile-label">
                    Name
                </div>

                <div class="profile-value">
                    <?= user_h($fullName) ?>
                </div>

            </div>


            <div class="profile-row">

                <div class="profile-label">
                    Email
                </div>

                <div class="profile-value">
                    <?php if ($email !== ''): ?>

                        <?= user_h($email) ?>

                    <?php else: ?>

                        <span class="profile-empty">
                            Not provided
                        </span>

                    <?php endif; ?>
                </div>

            </div>


            <div class="profile-row">

                <div class="profile-label">
                    Phone
                </div>

                <div class="profile-value">

                    <?php if ($phone !== ''): ?>

                        <?= user_h($phone) ?>

                    <?php else: ?>

                        <span class="profile-empty">
                            Not provided
                        </span>

                    <?php endif; ?>

                </div>

            </div>


            <div class="profile-row">

                <div class="profile-label">
                    Role
                </div>

                <div class="profile-value">

                    <?php if ($role !== ''): ?>

                        <?= user_h($role) ?>

                    <?php else: ?>

                        <span class="profile-empty">
                            Not assigned
                        </span>

                    <?php endif; ?>

                </div>

            </div>


            <div class="profile-row">

                <div class="profile-label">
                    Status
                </div>

                <div class="profile-value">

                    <span class="badge bg-<?= user_h($statusClass) ?>">
                        <?= user_h(ucfirst((string)$status)) ?>
                    </span>

                </div>

            </div>


            <?php if ($createdAt !== ''): ?>

                <div class="profile-row">

                    <div class="profile-label">
                        Created
                    </div>

                    <div class="profile-value">
                        <?= user_h($createdAt) ?>
                    </div>

                </div>

            <?php endif; ?>


            <?php if ($updatedAt !== ''): ?>

                <div class="profile-row">

                    <div class="profile-label">
                        Last Updated
                    </div>

                    <div class="profile-value">
                        <?= user_h($updatedAt) ?>
                    </div>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- =====================================================
         ACTIONS
         ===================================================== -->

    <div class="mt-4 d-flex gap-2 flex-wrap">

        <a
            href="<?= url('/admin/users') ?>"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Back to Users
        </a>

        <?php if (!empty($user['id'])): ?>

            <a
                href="<?= url('/admin/users/' . (int)$user['id'] . '/edit') ?>"
                class="btn btn-primary"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit User
            </a>

        <?php endif; ?>

    </div>

</div>