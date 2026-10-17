<?php
if(session_status()===PHP_SESSION_NONE) session_start();
$currentUser = $_SESSION['user'] ?? null;
if(!$currentUser){ header('Location: /login'); exit; }
// get unread notification count
$_unreadNotif = 0;
try {
    $db = GOF\Helpers\Database::get();
    $ns = $db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=:uid AND is_read=0');
    $ns->execute([':uid'=>$currentUser['id']]);
    $_unreadNotif = (int)$ns->fetchColumn();
} catch(\Throwable $e){}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — GOF</title>
  <!-- Bootstrap 5.3.2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts: Inter -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- GOF CSS -->
  <link rel="stylesheet" href="<?= asset('/assets/css/gof.css') ?>">
  <?= $extraHead ?? '' ?>
</head>
<body class="gof-admin-body">

<!-- SIDEBAR -->
<aside class="gof-sidebar" id="gofSidebar">
  <div class="gof-sidebar-logo">
    <div class="gof-logo-icon">G</div>
    <div class="gof-logo-text">
      <span class="gof-logo-main">GOF</span>
      <span class="gof-logo-sub">Admin Panel</span>
    </div>
  </div>

  <nav class="gof-sidebar-nav">
    <div class="gof-nav-group">
      <span class="gof-nav-label">Main</span>
      <a href="<?= url('/admin') ?>" class="gof-nav-link <?= ($_SERVER['REQUEST_URI']==='/admin')?'active':'' ?>">
        <i class="bi bi-grid-1x2"></i> Dashboard
      </a>
    </div>

    <div class="gof-nav-group">
      <span class="gof-nav-label">People</span>
      <a href="<?= url('/admin/beneficiaries') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/beneficiaries')===0)?'active':'' ?>">
        <i class="bi bi-people"></i> Beneficiaries
      </a>
      <a href="<?= url('/admin/donors') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/donors')===0)?'active':'' ?>">
        <i class="bi bi-heart"></i> Donors
      </a>
      <a href="<?= url('/admin/staff') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/staff')===0)?'active':'' ?>">
        <i class="bi bi-person-badge"></i> Staff
      </a>
    </div>

    <div class="gof-nav-group">
      <span class="gof-nav-label">Finance</span>
      <a href="<?= url('/admin/donations') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/donations')===0)?'active':'' ?>">
        <i class="bi bi-cash-coin"></i> Donations
      </a>
    </div>

    <div class="gof-nav-group">
      <span class="gof-nav-label">Programs</span>
      <a href="<?= url('/admin/programs') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/programs')===0)?'active':'' ?>">
        <i class="bi bi-kanban"></i> Programs
      </a>
      <a href="<?= url('/admin/events') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/events')===0)?'active':'' ?>">
        <i class="bi bi-calendar-event"></i> Events
      </a>
    </div>

    <div class="gof-nav-group">
      <span class="gof-nav-label">Identity</span>
      <a href="<?= url('/admin/idcards') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/idcards')===0)?'active':'' ?>">
        <i class="bi bi-credit-card-2-front"></i> ID Cards
      </a>
    </div>

    <div class="gof-nav-group">
      <span class="gof-nav-label">Content</span>
      <a href="<?= url('/admin/news') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/news')===0)?'active':'' ?>">
        <i class="bi bi-newspaper"></i> News
      </a>
      <a href="<?= url('/admin/gallery') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/gallery')===0)?'active':'' ?>">
        <i class="bi bi-images"></i> Gallery
      </a>
    </div>

    <div class="gof-nav-group">
      <span class="gof-nav-label">Admin</span>
      <a href="<?= url('/admin/users') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/users')===0)?'active':'' ?>">
        <i class="bi bi-person-gear"></i> Users
      </a>
      <a href="<?= url('/admin/reports') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/reports')===0)?'active':'' ?>">
        <i class="bi bi-bar-chart"></i> Reports
      </a>
      <a href="<?= url('/admin/contacts') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/contacts')===0)?'active':'' ?>">
        <i class="bi bi-envelope"></i> Messages
      </a>
      <a href="<?= url('/admin/volunteers') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/volunteers')===0)?'active':'' ?>">
        <i class="bi bi-hand-thumbs-up"></i> Volunteers
      </a>
      <a href="<?= url('/admin/audit') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/audit')===0)?'active':'' ?>">
        <i class="bi bi-shield-check"></i> Audit Log
      </a>
      <a href="<?= url('/admin/settings') ?>" class="gof-nav-link <?= (strpos($_SERVER['REQUEST_URI'],'/admin/settings')===0)?'active':'' ?>">
        <i class="bi bi-gear"></i> Settings
      </a>
    </div>
  </nav>
</aside>

<!-- TOPBAR -->
<header class="gof-topbar">
  <div class="gof-topbar-left">
    <button class="gof-sidebar-toggle d-lg-none" id="sidebarToggle">
      <i class="bi bi-list fs-4"></i>
    </button>
    <nav aria-label="breadcrumb" class="d-none d-md-flex">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url('/admin') ?>">GOF</a></li>
        <?= $breadcrumb ?? '' ?>
      </ol>
    </nav>
  </div>
  <div class="gof-topbar-right">
    <!-- Notifications -->
    <div class="dropdown me-3">
      <button class="gof-notif-btn" data-bs-toggle="dropdown" id="notifBtn">
        <i class="bi bi-bell"></i>
        <?php if($_unreadNotif > 0): ?>
          <span class="gof-notif-badge"><?= min($_unreadNotif,99) ?></span>
        <?php endif; ?>
      </button>
      <div class="dropdown-menu dropdown-menu-end gof-notif-dropdown" style="min-width:340px">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
          <h6 class="mb-0 fw-600">Notifications</h6>
          <a href="<?= url('/admin/notifications/mark-all-read') ?>" class="text-muted small">Mark all read</a>
        </div>
        <div id="notifList" class="gof-notif-list">
          <div class="text-center py-4 text-muted"><i class="bi bi-bell-slash fs-3 d-block mb-2"></i>No new notifications</div>
        </div>
        <div class="p-2 border-top text-center">
          <a href="<?= url('/admin/notifications') ?>" class="text-muted small">View all</a>
        </div>
      </div>
    </div>
    <!-- User Menu -->
    <div class="dropdown">
      <button class="gof-user-btn dropdown-toggle" data-bs-toggle="dropdown">
        <div class="gof-avatar gof-avatar-sm"><?= strtoupper(substr($currentUser['first_name']??'U',0,1)) ?></div>
        <span class="d-none d-md-inline ms-2"><?= htmlspecialchars(($currentUser['first_name']??'').' '.($currentUser['last_name']??'')) ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="<?= url('/admin/profile') ?>"><i class="bi bi-person me-2"></i>My Profile</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-danger" href="<?= url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
      </ul>
    </div>
  </div>
</header>

<!-- MAIN CONTENT -->
<main class="gof-content" id="gofMain">
  <?php if(!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible gof-flash fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if(!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible gof-flash fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>
  <?php if(!empty($_SESSION['flash_warning'])): ?>
    <div class="alert alert-warning alert-dismissible gof-flash fade show" role="alert">
      <i class="bi bi-exclamation-circle me-2"></i><?= htmlspecialchars($_SESSION['flash_warning']); unset($_SESSION['flash_warning']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?= $content ?? '' ?>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js (optional, loaded per page) -->
<?= $extraScripts ?? '' ?>
<!-- GOF JS -->
<script src="<?= asset('/assets/js/gof.js') ?>"></script>
</body>
</html>
