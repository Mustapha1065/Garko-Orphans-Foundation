<?php $pageTitle = 'Profile'; $breadcrumb = '<li class="breadcrumb-item active">Profile</li>'; ?>
<div class="container-fluid py-4">
    <div class="row g-4">
        <!-- Profile Info -->
        <div class="col-lg-4">
            <div class="gof-card p-4 text-center">
                <div class="gof-avatar gof-avatar-xl mb-3 mx-auto" style="width: 100px; height: 100px; font-size: 2.5rem;">
                    <?= strtoupper(substr($profile['first_name'] ?? 'U', 0, 1)) ?>
                </div>
                <h4 class="fw-bold mb-1"><?= htmlspecialchars(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')) ?></h4>
                <p class="text-muted mb-3"><?= htmlspecialchars($profile['email'] ?? '') ?></p>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <span class="badge bg-primary"><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $profile['account_type'] ?? 'user'))) ?></span>
                    <?php if(!empty($profile['role'])): ?>
                    <span class="badge bg-secondary"><?= htmlspecialchars($profile['role']) ?></span>
                    <?php endif; ?>
                </div>
                
                <ul class="list-group list-group-flush text-start">
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Joined</span>
                        <span class="fw-medium"><?= date('M j, Y', strtotime($profile['created_at'] ?? 'now')) ?></span>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted">Last Login</span>
                        <span class="fw-medium"><?= $profile['last_login'] ? date('M j, Y H:i', strtotime($profile['last_login'])) : 'Never' ?></span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Profile Edit Form -->
        <div class="col-lg-8">
            <div class="gof-card p-4">
                <h5 class="fw-bold mb-4">Edit Profile</h5>
                
                <form action="<?= url('/admin/profile/update') ?>" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(\GOF\Helpers\Csrf::token()) ?>">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email Address</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" disabled>
                            <div class="form-text">Email address cannot be changed.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Phone Number</label>
                            <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <h5 class="fw-bold mb-4">Change Password</h5>
                    <p class="text-muted small mb-3">Leave these fields blank if you do not wish to change your password.</p>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-medium">Current Password</label>
                            <input type="password" class="form-control" name="current_password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">New Password</label>
                            <input type="password" class="form-control" name="new_password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password">
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
