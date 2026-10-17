<?php
declare(strict_types=1);

use GOF\Helpers\Router;

// Check if this is a direct access to index.php or a rewritten URL
$scriptName = $_SERVER['SCRIPT_NAME'];
$requestUri = $_SERVER['REQUEST_URI'];

// If accessing /index.php directly, show home page
if (strpos($requestUri, '/index.php') !== false && strpos($requestUri, '?') === false) {
    // Direct access to index.php - serve homepage
    $homeRedirect = true;
} else {
    $homeRedirect = false;
}

try {
    // Autoload
    require __DIR__ . '/../vendor/autoload.php';
    
    // Bootstrap
    require __DIR__ . '/../config/bootstrap.php';

    // Load routes
    $routes = require __DIR__ . '/../routes/web.php';
    
    // If direct index.php access, show home
    if ($homeRedirect) {
        $controller = new \GOF\Controllers\PublicController();
        $controller->home();
        exit;
    }
    
    // Otherwise dispatch normally
    Router::dispatch($routes);
    
} catch (\Exception $e) {
    http_response_code(500);
    echo "<h1>Application Error</h1>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . htmlspecialchars($e->getFile()) . " (Line: " . $e->getLine() . ")</p>";
    error_log("GOF2 Error: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
}
