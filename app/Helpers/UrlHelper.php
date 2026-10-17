<?php
/**
 * URL Helper Functions
 */

/**
 * Generate a URL with the proper base path
 * @param string $path The path (e.g., '/login', 'admin/dashboard')
 * @return string Full URL path
 */
function url($path = '') {
    static $baseUrl = null;
    
    if ($baseUrl === null) {
        // Get base URL from config - default to localhost path
        $baseUrl = 'http://localhost/GOF3-light/public';
        
        // Try to load from config if available
        $configPath = __DIR__ . '/../../config/config.php';
        if (file_exists($configPath)) {
            $config = require $configPath;
            $baseUrl = $config['app']['url'] ?? $baseUrl;
        }
        
        $baseUrl = rtrim($baseUrl, '/');
    }
    
    // If path starts with http, return as-is (external URL)
    if (strpos($path, 'http') === 0) {
        return $path;
    }
    
    // Remove leading slash from path for consistent joining
    $path = ltrim($path, '/');
    
    if (empty($path)) {
        return $baseUrl;
    }
    
    return $baseUrl . '/' . $path;
}

/**
 * Generate an asset URL (CSS, JS, images)
 * @param string $path The asset path (e.g., '/assets/css/style.css')
 * @return string Full asset URL
 */
function asset($path) {
    // Try to load from config if available
    $configPath = __DIR__ . '/../../config/config.php';
    if (file_exists($configPath)) {
        $baseUrl = require $configPath;
        $appUrl = $baseUrl['app']['url'] ?? 'http://localhost/GOF3-light/public';
    } else {
        $appUrl = 'http://localhost/GOF3-light/public';
    }
    
    $appUrl = rtrim($appUrl, '/');
    
    // Remove leading slash from path
    $path = ltrim($path, '/');
    
    return $appUrl . '/' . $path;
}
