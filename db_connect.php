<?php
// Database connection file

// Database connection parameters
$db_host = '194.197.245.5';
$db_name = 'cms_database';
$db_user = '23p_3351';
$db_pass = 'IJ*gNVzjp9zI(oSh';

// Create PDO Connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper function to safely output HTML
if (!function_exists('h')) {
    function h($value) {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// Helper function for redirects
if (!function_exists('redirect')) {
    function redirect($url) {
        header("Location: $url");
        exit;
    }
}

// Check if user is logged in
if (!function_exists('is_logged_in')) {
    function is_logged_in() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
}

// Set flash message
if (!function_exists('set_flash_message')) {
    function set_flash_message($type, $message) {
        $_SESSION['flash_messages'][$type][] = $message;
    }
}

// Get flash messages
if (!function_exists('get_flash_messages')) {
    function get_flash_messages() {
        $messages = $_SESSION['flash_messages'] ?? [];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
}