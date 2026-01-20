<?php
/**
 * AJAX Endpoint: Add new website
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

// Validate required fields
if (empty($input['website_name']) || empty($input['website_type']) || empty($input['owner_name'])) {
    jsonResponse(false, 'All fields are required');
}

// Sanitize inputs
$websiteName = sanitizeInput($input['website_name']);
$websiteType = sanitizeInput($input['website_type']);
$ownerName = sanitizeInput($input['owner_name']);

// Read existing websites
$websites = readJSON(WEBSITES_FILE);

// Generate new website data
$newWebsite = [
    'id' => getNextId($websites),
    'website_name' => $websiteName,
    'website_type' => $websiteType,
    'owner_name' => $ownerName,
    'website_key' => generateWebsiteKey(),
    'created_at' => getCurrentTimestamp()
];

// Add to array
$websites[] = $newWebsite;

// Save to file
if (writeJSON(WEBSITES_FILE, $websites)) {
    jsonResponse(true, 'Website added successfully', $newWebsite);
} else {
    jsonResponse(false, 'Failed to save website');
}
