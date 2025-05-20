<?php
// Include database connection
require_once(__DIR__ . '/db_connect.php');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/error.log');

// Define constants
define('SITE_URL', 'http://194.197.245.5/~23p_3351/newcmswebsite');
define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads');

// Create directories if they don't exist
$directories = [
    __DIR__ . '/uploads',
    __DIR__ . '/uploads/articles',
    __DIR__ . '/uploads/media',
    __DIR__ . '/logs'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Global settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    if ($stmt) {
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
} catch (PDOException $e) {
    // Silently fail for now
    error_log("Error loading settings: " . $e->getMessage());
}