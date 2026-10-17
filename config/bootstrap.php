<?php

if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $k => $v) {
        putenv("$k=$v");
    }
}

$config = require __DIR__ . '/config.php';

if ($config['app']['env'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

ini_set('session.name', $config['session']['name']);
ini_set('session.gc_maxlifetime', (string)$config['session']['lifetime']);
ini_set('session.cookie_lifetime', '0');
ini_set('session.cookie_samesite', $config['session']['cookie_samesite']);
ini_set('session.cookie_httponly', $config['session']['cookie_httponly'] ? '1' : '0');
ini_set('session.cookie_secure', $config['session']['cookie_secure'] ? '1' : '0');
ini_set('session.use_strict_mode', '1');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

set_exception_handler(function (\Throwable $e) use ($config) {
    if ($config['app']['env'] === 'development') {
        echo "<h1>Uncaught Exception</h1>";
        echo "<p><strong>" . get_class($e) . "</strong>: " . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        if (file_exists(__DIR__ . '/../app/views/errors/500.php')) {
            include __DIR__ . '/../app/views/errors/500.php';
        } else {
            echo "Internal Server Error";
        }
    }
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
});

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new \ErrorException($message, 0, $severity, $file, $line);
});

ini_set('error_log', __DIR__ . '/../storage/logs/app.log');

// Load helper functions
require __DIR__ . '/../app/Helpers/UrlHelper.php';

// Set security headers
\GOF\Helpers\SecurityHeaders::set();

// Ensure a CSRF token always exists for the current session so that
// forms rendered by any view can rely on $_SESSION['csrf_token'] being set.
\GOF\Helpers\Csrf::token();
