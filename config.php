<?php
// config.php - Simplified version

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$db_host = 'localhost';
$db_name = 'newcmswebsite';
$db_user = 'root'; // Change to your actual MySQL username (usually 'root' for local development)
$db_pass = '';     // Change if your MySQL has a password

// Create the PDO connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Site configuration
$config = [
    'site_name' => 'My CMS Website',
    'site_url' => 'http://localhost/newcmswebsite/newcmswebsite',
    'upload_dir' => 'uploads/',
    'data_dir' => 'data/'
];

// Create required directories if they don't exist
if (!file_exists($config['upload_dir'])) {
    mkdir($config['upload_dir'], 0755, true);
}

if (!file_exists($config['data_dir'])) {
    mkdir($config['data_dir'], 0755, true);
}
?>