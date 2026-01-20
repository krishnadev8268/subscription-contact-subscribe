<?php
/**
 * AJAX Endpoint: Fetch all contacts
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

$contacts = readJSON(CONTACTS_FILE);

echo json_encode([
    'success' => true,
    'data' => $contacts
]);
