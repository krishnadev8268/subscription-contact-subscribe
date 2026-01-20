<?php
/**
 * AJAX Endpoint: Delete website
 */

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Check if POST request
if (!isPostRequest()) {
    jsonResponse(false, 'Invalid request method');
}

// Get JSON input
$input = getJSONInput();

// Validate ID
if (empty($input['id'])) {
    jsonResponse(false, 'Website ID is required');
}

$websiteId = (int)$input['id'];

// Read existing websites
$websites = readJSON(WEBSITES_FILE);

// Find and remove website
$found = false;
$filteredWebsites = [];

foreach ($websites as $website) {
    if ($website['id'] === $websiteId) {
        $found = true;
        continue; // Skip this website (delete it)
    }
    $filteredWebsites[] = $website;
}

if (!$found) {
    jsonResponse(false, 'Website not found');
}

// Save updated array
if (writeJSON(WEBSITES_FILE, $filteredWebsites)) {
    jsonResponse(true, 'Website deleted successfully');
} else {
    jsonResponse(false, 'Failed to delete website');
}
