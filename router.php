<?php
/**
 * Router for PHP Built-in Server
 */

// Get the URI path
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Set CORS headers only for API requests
if (strpos($uri, '/api/') === 0) {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
    
    // Handle preflight OPTIONS request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit(0);
    }
}

// For static files, return false to let PHP serve them
if (preg_match('/\.(?:png|jpg|jpeg|gif|css|js|ico|svg|woff|woff2|ttf|eot)$/', $uri)) {
    return false;
}

// Handle directory requests
if (substr($uri, -1) === '/') {
    // Check for admin directory
    if ($uri === '/admin/') {
        chdir(__DIR__ . '/admin');
        require __DIR__ . '/admin/login.php';
        exit;
    }
    // Check for index.php in directory
    $indexPath = __DIR__ . $uri . 'index.php';
    if (file_exists($indexPath)) {
        require $indexPath;
        exit;
    }
}

// For PHP files, let the server handle them
$requestedFile = __DIR__ . $uri;
if (file_exists($requestedFile) && is_file($requestedFile)) {
    return false;
}

// If file doesn't exist, return 404
http_response_code(404);
echo "404 Not Found";
