<?php
// delete_article.php - Handle deleting an article
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
    $id = $_POST['id'] ?? 0;
    
    try {
        // Get the image URL before deleting the article
        $stmt = $pdo->prepare("SELECT image_url FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($article) {
            // Delete the article
            $deleteStmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $deleteStmt->execute([$id]);
            
            // Delete the associated image file if exists
            if (!empty($article['image_url']) && file_exists('../' . $article['image_url'])) {
                unlink('../' . $article['image_url']);
            }
            
            $_SESSION['success'] = "Article deleted successfully!";
        } else {
            $_SESSION['error'] = "Article not found.";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error deleting article: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit;
}

// Redirect if accessed directly without POST
header("Location: index.php");
exit;
?>