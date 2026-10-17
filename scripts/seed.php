<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

try {
    $dbConfig = $config['db'];
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    echo "Connected to database successfully.\n";

    // ---------------------------------------------------------------
    // Run all .sql files in seeds/ ordered by filename
    // ---------------------------------------------------------------
    $seedsDir = __DIR__ . '/../database/seeds';
    $files = glob($seedsDir . '/*.sql');
    sort($files);

    foreach ($files as $file) {
        $filename = basename($file);
        echo "Running seed: {$filename}...\n";
        $sql = file_get_contents($file);
        if (trim($sql) === '') { echo " -> Skipped (empty)\n"; continue; }
        try {
            $pdo->exec($sql);
            echo " -> OK\n";
        } catch (PDOException $e) {
            echo " -> Warning: " . $e->getMessage() . "\n";
        }
    }

    // ---------------------------------------------------------------
    // Create Super Admin programmatically (so password is hashed)
    // ---------------------------------------------------------------
    echo "\nCreating / verifying Super Admin user...\n";

    $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $check->execute([':email' => 'admin@gof.org.ng']);
    $existing = $check->fetchColumn();

    if (!$existing) {
        $uuid     = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $pwHash   = password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12]);

        $ins = $pdo->prepare('
            INSERT INTO users (uuid, email, password_hash, first_name, last_name, account_type, status, created_at, updated_at)
            VALUES (:uuid, :email, :pw, :fn, :ln, :type, :status, NOW(), NOW())
        ');
        $ins->execute([
            ':uuid'   => $uuid,
            ':email'  => 'admin@gof.org.ng',
            ':pw'     => $pwHash,
            ':fn'     => 'Super',
            ':ln'     => 'Admin',
            ':type'   => 'super_admin',
            ':status' => 'active',
        ]);
        $adminId = (int)$pdo->lastInsertId();
        echo " -> Super Admin created (ID: {$adminId})\n";

        // Assign super_admin role
        $roleStmt = $pdo->prepare('SELECT id FROM roles WHERE slug = :slug LIMIT 1');
        $roleStmt->execute([':slug' => 'super_admin']);
        $roleId = $roleStmt->fetchColumn();
        if ($roleId) {
            $pdo->prepare('INSERT IGNORE INTO user_roles (user_id, role_id, assigned_by) VALUES (:u,:r,:u)')
                ->execute([':u' => $adminId, ':r' => $roleId]);
            echo " -> Role 'super_admin' assigned\n";
        }
    } else {
        echo " -> Super Admin already exists (ID: {$existing}) — skipping.\n";
    }

    // ---------------------------------------------------------------
    // Ensure sequences table has base rows
    // ---------------------------------------------------------------
    $seqTypes = ['beneficiary', 'staff', 'donor', 'volunteer'];
    foreach ($seqTypes as $type) {
        try {
            $pdo->prepare('INSERT IGNORE INTO sequences (name, last_val) VALUES (:n, 0)')
                ->execute([':n' => $type]);
        } catch (\Throwable $e) { /* table may not use sequences */ }
    }

    echo "\nSeeding complete.\n";
    echo "\n=== CREDENTIALS ===\n";
    echo "URL:      http://localhost/GOF3-light/public\n";
    echo "Email:    admin@gof.org.ng\n";
    echo "Password: Admin@1234\n";
    echo "IMPORTANT: Change this password immediately after first login!\n\n";

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
} catch (Throwable $e) {
    die("Error: " . $e->getMessage() . "\n");
}
