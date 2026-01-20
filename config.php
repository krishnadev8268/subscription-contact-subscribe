<?php
/**
 * Central Admin Dashboard - Configuration File
 * Core PHP - No Framework
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base paths
define('BASE_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');

// JSON file paths
define('ADMINS_FILE', STORAGE_PATH . '/admins.json');
define('WEBSITES_FILE', STORAGE_PATH . '/websites.json');
define('SUBSCRIBERS_FILE', STORAGE_PATH . '/subscribers.json');
define('CONTACTS_FILE', STORAGE_PATH . '/contacts.json');

// Timezone
date_default_timezone_set('Asia/Kolkata');

/**
 * Read JSON file and return array
 * @param string $filepath Path to JSON file
 * @return array Decoded JSON data
 */
function readJSON($filepath) {
    if (!file_exists($filepath)) {
        return [];
    }
    
    $content = file_get_contents($filepath);
    if ($content === false) {
        return [];
    }
    
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}

/**
 * Write array to JSON file with file locking
 * @param string $filepath Path to JSON file
 * @param array $data Data to write
 * @return bool Success status
 */
function writeJSON($filepath, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    // Use file locking to prevent race conditions
    $fp = fopen($filepath, 'w');
    if ($fp === false) {
        return false;
    }
    
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $json);
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    
    fclose($fp);
    return true;
}

/**
 * Generate next auto-increment ID
 * @param array $data Array of records
 * @return int Next ID
 */
function getNextId($data) {
    if (empty($data)) {
        return 1;
    }
    
    $maxId = 0;
    foreach ($data as $item) {
        if (isset($item['id']) && $item['id'] > $maxId) {
            $maxId = $item['id'];
        }
    }
    
    return $maxId + 1;
}

/**
 * Sanitize input string
 * @param string $input Raw input
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    return $input;
}

/**
 * Validate email address
 * @param string $email Email to validate
 * @return bool Valid status
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate unique website key
 * @return string Random hex string
 */
function generateWebsiteKey() {
    return bin2hex(random_bytes(16));
}

/**
 * Get current timestamp in ISO 8601 format
 * @return string Formatted timestamp
 */
function getCurrentTimestamp() {
    return date('c'); // ISO 8601 format with timezone
}

/**
 * Send JSON response and exit
 * @param bool $success Success status
 * @param string $message Response message
 * @param array $data Additional data
 */
function jsonResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Check if request method is POST
 * @return bool
 */
function isPostRequest() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Get JSON input from request body
 * @return array|null Decoded JSON or null on failure
 */
function getJSONInput() {
    $input = file_get_contents('php://input');
    return json_decode($input, true);
}
function getUserCountry() {
    // Cloudflare header (agar use ho raha ho)
    if (!empty($_SERVER['HTTP_CF_IPCOUNTRY'])) {
        return $_SERVER['HTTP_CF_IPCOUNTRY'];
    }

    // Fallback (local / testing)
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}
