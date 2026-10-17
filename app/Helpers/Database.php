<?php
namespace GOF\Helpers;

use PDO;
use PDOException;
use RuntimeException;

class Database {
    private static ?PDO $instance = null;

    public static function get(): PDO {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/config.php';
            $dbConfig = $config['db'];
            
            $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
            ];

            try {
                self::$instance = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
            } catch (PDOException $e) {
                throw new RuntimeException("Database connection failed: " . $e->getMessage());
            }
        }
        
        return self::$instance;
    }
}
