<?php
/**
 * AJAX Endpoint: Fetch all websites
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$websites = readJSON(WEBSITES_FILE);

echo json_encode([
    'success' => true,
    'data' => $websites
]);
