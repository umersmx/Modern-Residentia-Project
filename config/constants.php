<?php
/**
 * Modern Residentia — Application Constants
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'modern_residentia');

// Application
define('SITE_NAME', 'Modern Residentia');
// Dynamic SITE_URL helper to support local wifi testing (e.g. on mobile devices)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
define('SITE_URL', $protocol . $host . '/WEB%20Project');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);

// Allowed image types
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Property types
define('PROPERTY_TYPES', [
    'apartment' => 'Apartment',
    'house'     => 'House',
    'room'      => 'Room',
    'hostel'    => 'Hostel',
    'commercial'=> 'Commercial'
]);

// Property statuses
define('PROPERTY_STATUSES', [
    'pending'  => 'Pending Review',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'rented'   => 'Rented Out'
]);
