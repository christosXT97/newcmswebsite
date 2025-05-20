<?php
// Database connection parameters for school server
$db_host = 'localhost'; // Most school servers use localhost for MySQL
$db_name = 'cms_database'; // Your existing database
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

// Helper functions
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function set_flash_message($type, $message) {
    $_SESSION['flash_messages'][$type][] = $message;
}

function get_flash_messages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}