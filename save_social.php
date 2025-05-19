<?php
// save_social.php - Handle saving social links
session_start();

// Database configuration
$db_host = 'localhost';
$db_name = 'newcmswebsite'; // Changed from cms_database to match your database name
$db_user = 'root';
$db_pass = '';

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Create database connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $_SESSION['error'] = "Database connection failed: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $link_ids = $_POST['link_id'] ?? [];
    $link_texts = $_POST['link_text'] ?? [];
    $link_urls = $_POST['link_url'] ?? [];
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // First, delete all social links
        $pdo->exec("DELETE FROM links WHERE type = 'social'");
        
        // Then, insert the updated social links
        $stmt = $pdo->prepare("INSERT INTO links (text, url, type, sort_order) VALUES (?, ?, 'social', ?)");
        
        foreach ($link_texts as $i => $text) {
            if (empty($text) || empty($link_urls[$i])) continue;
            
            $url = $link_urls[$i];
            
            // Make sure URL has a protocol
            if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
                $url = 'https://' . $url;
            }
            
            $stmt->execute([$text, $url, $i + 1]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success'] = "Social links updated successfully!";
    } catch(PDOException $e) {
        // Rollback on error
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating social links: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit;
}

// Redirect if accessed directly without POST
header("Location: index.php");
exit;
?>