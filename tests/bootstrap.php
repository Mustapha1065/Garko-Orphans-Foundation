<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/bootstrap.php';
// start session for CSRF tests
if (session_status() === PHP_SESSION_NONE) session_start();

// Setup an in-memory SQLite DB for unit tests and inject into Database helper
use GOF\Helpers\Database;

$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Minimal schema for testing IdGenerator and Beneficiary
$pdo->exec("CREATE TABLE sequences (name TEXT PRIMARY KEY, last_val INTEGER NOT NULL DEFAULT 0);");
$pdo->exec("CREATE TABLE beneficiaries (id INTEGER PRIMARY KEY AUTOINCREMENT, gof_id TEXT UNIQUE, user_id INTEGER, full_name TEXT, dob TEXT, gender TEXT, photo TEXT, phone TEXT, address TEXT, state TEXT, lga TEXT, guardian_summary TEXT, registration_date TEXT, status TEXT, created_by INTEGER, created_at TEXT DEFAULT CURRENT_TIMESTAMP);");
$pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT, uuid TEXT UNIQUE, email TEXT UNIQUE, password TEXT, first_name TEXT, last_name TEXT, status TEXT, created_at TEXT);");
$pdo->exec("CREATE TABLE audit_logs (id INTEGER PRIMARY KEY AUTOINCREMENT, user_id INTEGER, action TEXT, module TEXT, target_table TEXT, target_id TEXT, ip_address TEXT, meta TEXT, created_at TEXT DEFAULT CURRENT_TIMESTAMP);");

Database::set($pdo);
