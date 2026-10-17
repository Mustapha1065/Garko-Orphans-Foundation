<?php
declare(strict_types=1);

// GOF3-light one-click local installer.
$root = __DIR__;
$envFile = $root . '/.env';
if (!is_file($envFile)) {
    fwrite(STDERR, "Missing .env file.\n");
    exit(1);
}

$env = parse_ini_file($envFile, false, INI_SCANNER_RAW) ?: [];
foreach ($env as $k => $v) {
    putenv($k . '=' . trim((string)$v, "\\\"'"));
}

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_DATABASE') ?: 'gof3light';
$user = getenv('DB_USERNAME') ?: 'root';
$pass = getenv('DB_PASSWORD') ?: '';

function out(string $message): void { echo $message . PHP_EOL; }
function qi(string $name): string { return '`' . str_replace('`', '``', $name) . '`'; }

try {
    out('Connecting to MySQL...');
    $pdo = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('CREATE DATABASE IF NOT EXISTS ' . qi($db) . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $pdo->exec('USE ' . qi($db));
    out("Database ready: {$db}");

    $files = glob($root . '/database/migrations/[0-9][0-9][0-9]_*.sql') ?: [];
    natsort($files);
    if (!$files) throw new RuntimeException('No migration files were found.');

    $count = 0;
    foreach ($files as $file) {
        $sql = trim((string)file_get_contents($file));
        if ($sql === '' || $sql === '-- deleted') continue;
        $name = basename($file);
        out("  Installing {$name}...");
        $pdo->exec($sql);
        $count++;
    }

    $seeds = [
        '001_seed_roles.sql',
        '002_seed_permissions.sql',
        '003_seed_role_permissions.sql',
        '004_seed_super_admin.sql',
        '005_seed_settings.sql',
    ];
    foreach ($seeds as $seed) {
        $file = $root . '/database/seeds/' . $seed;
        if (!is_file($file)) continue;
        $sql = trim((string)file_get_contents($file));
        if ($sql === '') continue;
        out("  Seeding {$seed}...");
        $pdo->exec($sql);
    }

    // Always guarantee a valid local-development Super Admin password.
    $hash = password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare("UPDATE users
        SET password_hash=:hash, account_type='super_admin', status='active',
            failed_login_attempts=0, locked_until=NULL, updated_at=NOW()
        WHERE email='admin@gof.org.ng'");
    $stmt->execute([':hash' => $hash]);
    if ($stmt->rowCount() === 0) {
        $stmt = $pdo->prepare("INSERT INTO users
            (email,password_hash,account_type,status,first_name,last_name,failed_login_attempts,created_at,updated_at)
            VALUES ('admin@gof.org.ng',:hash,'super_admin','active','Super','Admin',0,NOW(),NOW())");
        $stmt->execute([':hash' => $hash]);
    }

    $pdo->exec("INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by)
                SELECT u.id, r.id, u.id FROM users u JOIN roles r ON r.slug='super_admin'
                WHERE u.email='admin@gof.org.ng'");

    $checks = [
        'users','roles','permissions','user_roles','user_permissions','login_attempts',
        'password_resets','beneficiaries','staff','donors','donations','programs','events',
        'volunteer_applications','contact_messages','id_cards','audit_logs','guardians',
        'education_records','beneficiary_support','news','gallery_albums','gallery_images',
        'notifications','id_card_tokens','id_card_history','documents','blocked_ips',
        'verification_attempts','sequences','settings'
    ];
    $missing = [];
    $q = $pdo->prepare('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=:db AND table_name=:t');
    foreach ($checks as $t) {
        $q->execute([':db'=>$db, ':t'=>$t]);
        if (!(int)$q->fetchColumn()) $missing[] = $t;
    }

    if ($missing) {
        throw new RuntimeException('Installation finished but required tables are missing: ' . implode(', ', $missing));
    }

    out('');
    out('GOF3-light installation completed successfully.');
    out("Database: {$db}");
    out("Migrations installed: {$count}");
    out('Super Admin: admin@gof.org.ng');
    out('Temporary password: Admin@1234');
    out('URL: http://localhost/GOF3-light/public/');
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, "\nGOF3-light installation failed:\n" . $e->getMessage() . "\n");
    exit(1);
}
