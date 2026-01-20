<?php
/**
 * Central API Endpoint
 * Accepts form submissions from external websites
 */

// Start output buffering to prevent "headers already sent" errors
ob_start();

// Set CORS headers FIRST, before anything else
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Max-Age: 3600");
header("Content-Type: application/json; charset=UTF-8");

// Handle preflight OPTIONS request immediately
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    ob_end_flush();
    exit;
}

// Now load config
require_once __DIR__ . '/../config.php';

/* ===============================
   ONLY POST REQUESTS
================================ */
if (!isPostRequest()) {
    jsonResponse(false, 'Only POST requests are allowed');
}

/* ===============================
   READ JSON INPUT
================================ */
$input = getJSONInput();

if (!$input) {
    jsonResponse(false, 'Invalid JSON data');
}

/* ===============================
   BASIC VALIDATION
================================ */
if (empty($input['type']) || empty($input['website_key'])) {
    jsonResponse(false, 'Missing required fields: type and website_key');
}

$type = sanitizeInput($input['type']);
$websiteKey = sanitizeInput($input['website_key']);

if (!in_array($type, ['subscribe', 'contact'])) {
    jsonResponse(false, 'Invalid type. Must be "subscribe" or "contact"');
}

/* ===============================
   VALIDATE WEBSITE KEY
================================ */
$websites = readJSON(WEBSITES_FILE);
$websiteData = null;

foreach ($websites as $website) {
    if ($website['website_key'] === $websiteKey) {
        $websiteData = $website;
        break;
    }
}

if (!$websiteData) {
    jsonResponse(false, 'Invalid website key');
}

/* ===============================
   SUBSCRIBE FLOW
================================ */
if ($type === 'subscribe') {

    if (empty($input['email'])) {
        jsonResponse(false, 'Email is required for subscription');
    }

    $email = sanitizeInput($input['email']);

    if (!validateEmail($email)) {
        jsonResponse(false, 'Invalid email address');
    }

    $country = isset($input['country']) ? sanitizeInput($input['country']) : '';

    $subscribers = readJSON(SUBSCRIBERS_FILE);

    $newSubscriber = [
        'id'           => getNextId($subscribers),
        'email'        => $email,
        'country'      => $country,
        'website_name' => $websiteData['website_name'],
        'website_type' => $websiteData['website_type'],
        'created_at'   => getCurrentTimestamp()
    ];

    $subscribers[] = $newSubscriber;

    if (writeJSON(SUBSCRIBERS_FILE, $subscribers)) {
        jsonResponse(true, 'Subscription successful', [
            'id' => $newSubscriber['id']
        ]);
    } else {
        jsonResponse(false, 'Failed to save subscription');
    }
}

/* ===============================
   CONTACT FLOW
================================ */
if ($type === 'contact') {

    if (empty($input['name']) || empty($input['email']) || empty($input['message'])) {
        jsonResponse(false, 'Name, email, and message are required for contact');
    }

    $name    = sanitizeInput($input['name']);
    $email   = sanitizeInput($input['email']);
    $message = sanitizeInput($input['message']);

    if (!validateEmail($email)) {
        jsonResponse(false, 'Invalid email address');
    }

$country = isset($input['country']) ? sanitizeInput($input['country']) : '';

    $contacts = readJSON(CONTACTS_FILE);

    $newContact = [
        'id'           => getNextId($contacts),
        'name'         => $name,
        'email'        => $email,
        'message'      => $message,
        'country'      => $country,
        'website_name' => $websiteData['website_name'],
        'website_type' => $websiteData['website_type'],
        'created_at'   => getCurrentTimestamp()
    ];

    $contacts[] = $newContact;

    if (writeJSON(CONTACTS_FILE, $contacts)) {
        jsonResponse(true, 'Contact message sent successfully', [
            'id' => $newContact['id']
        ]);
    } else {
        jsonResponse(false, 'Failed to save contact message');
    }
}
