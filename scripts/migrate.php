<?php

require_once __DIR__ . '/../vendor/autoload.php';
$config = require __DIR__ . '/../config/config.php';

try {
    $dbConfig = $config['db'];
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "Connected to database successfully.\n";
    
    $migrationsDir = __DIR__ . '/../database/migrations';
    $files = glob($migrationsDir . '/*.sql');
    sort($files);
    
    foreach ($files as $file) {
        // Only run files that match the new numbered pattern to avoid legacy files
        if (preg_match('/\/(\d{3}_.*\.sql)$/', $file, $matches)) {
            $filename = $matches[1];
            echo "Running migration: {$filename}...\n";
            $sql = file_get_contents($file);
            
            if (trim($sql) !== '' && trim($sql) !== '-- deleted') {
                try {
                    $pdo->exec($sql);
                    echo " -> Success\n";
                } catch (PDOException $e) {
                    echo " -> Failed: " . $e->getMessage() . "\n";
                }
            } else {
                echo " -> Skipped (Empty)\n";
            }
        }
    }
    
    echo "Migrations complete.\n";

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "\n");
}
