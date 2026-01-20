<?php
/**
 * AJAX Endpoint: Fetch all subscribers
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$subscribers = readJSON(SUBSCRIBERS_FILE);

echo json_encode([
    'success' => true,
    'data' => $subscribers
]);
