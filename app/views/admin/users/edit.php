<?php
/**
 * GOF3-light
 * Admin - Edit User
 *
 * Drop-in replacement.
 * Safely handles NULL database values.
 */

if (!function_exists('user_edit_h')) {
    function user_edit_h($value): string
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

$userId       = (int)($user['id'] ?? 0);
$firstName    = $user['first_name'] ?? '';
$lastName     = $user['last_name'] ?? '';
$email        = $user['email'] ?? '';
$phone        = $user['phone'] ?? '';
$accountType  = $user['account_type'] ?? 'staff';
$roleId       = $user['role_id'] ?? '';
?>

<div class="container-fluid py-3">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Edit User</h2>
            <p class="text-muted mb-0">
                Update the user's account information.
            </p>
        </div>

        <a href="<?= user_edit_h(url('/admin/users')) ?>"
           class="btn btn-outline-secondary">
            ← Back to Users
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form
                action="<?= user_edit_h(url('/admin/users/' . $userId . '/update')) ?>"
                method="POST"
                autocomplete="off"
            >

                <!-- CSRF -->
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= user_edit_h($_SESSION['csrf_token'] ?? '') ?>"
                >

                <!-- Basic Information -->
                <h5 class="mb-3">Basic Information</h5>

                <div class="row mb-3">

                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="first_name" class="form-label">
                            First Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            class="form-control"
                            value="<?= user_edit_h($firstName) ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="last_name" class="form-label">
                            Last Name <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            class="form-control"
                            value="<?= user_edit_h($lastName) ?>"
                            required
                            maxlength="100"
                        >
                    </div>

                </div>

                <div class="row mb-4">

                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="<?= user_edit_h($email) ?>"
                            required
                            maxlength="150"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            class="form-control"
                            value="<?= user_edit_h($phone) ?>"
                            maxlength="30"
                            placeholder="Optional"
                        >
                    </div>

                </div>

                <hr class="my-4">

                <!-- Account Settings -->
                <h5 class="mb-3">Account Settings</h5>

                <div class="row mb-3">

                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="password" class="form-label">
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            placeholder="Leave blank to keep current"
                        >

                        <small class="text-muted">
                            Leave blank if you do not want to change the password.
                        </small>
                    </div>

                    <div class="col-md-6">
                        <label for="account_type" class="form-label">
                            Account Type
                        </label>

                        <select
                            id="account_type"
                            name="account_type"
                            class="form-select"
                            required
                        >
                            <option value="staff"
                                <?= $accountType === 'staff' ? 'selected' : '' ?>>
                                Staff
                            </option>

                            <option value="super_admin"
                                <?= $accountType === 'super_admin' ? 'selected' : '' ?>>
                                Super Admin
                            </option>

                            <option value="beneficiary"
                                <?= $accountType === 'beneficiary' ? 'selected' : '' ?>>
                                Beneficiary
                            </option>

                            <option value="donor"
                                <?= $accountType === 'donor' ? 'selected' : '' ?>>
                                Donor
                            </option>
                        </select>
                    </div>

                </div>

                <div class="row mb-4">

                    <div class="col-md-6">
                        <label for="role_id" class="form-label">
                            Role
                        </label>

                        <select
                            id="role_id"
                            name="role_id"
                            class="form-select"
                        >
                            <option value="">None</option>

                            <?php foreach (($roles ?? []) as $r): ?>

                                <?php
                                $roleOptionId = $r['id'] ?? '';
                                $roleName = $r['name'] ?? 'Unnamed Role';
                                ?>

                                <option
                                    value="<?= user_edit_h($roleOptionId) ?>"
                                    <?= ((string)$roleId === (string)$roleOptionId)
                                        ? 'selected'
                                        : '' ?>
                                >
                                    <?= user_edit_h($roleName) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>
                    </div>

                </div>

                <hr class="my-4">

                <!-- Actions -->
                <div class="d-flex gap-2 flex-wrap">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update User
                    </button>

                    <a
                        href="<?= user_edit_h(url('/admin/users')) ?>"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>