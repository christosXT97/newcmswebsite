<?php
// update_article.php - Handle updating an existing article
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
    $title = $_POST['title'] ?? '';
    $content1 = $_POST['content1'] ?? '';
    $content2 = $_POST['content2'] ?? '';
    
    try {
        // Check if article exists
        $checkStmt = $pdo->prepare("SELECT image_url FROM articles WHERE id = ?");
        $checkStmt->execute([$id]);
        $article = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$article) {
            $_SESSION['error'] = "Article not found.";
            header("Location: index.php");
            exit;
        }
        
        $imageUrl = $article['image_url'];
        
        // Process image upload if present
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Generate safe filename
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $fileName = preg_replace("/[^a-zA-Z0-9_.-]/", "", $fileName); // sanitize filename
            $targetFile = $uploadDir . $fileName;
            
            // Check if file is an actual image
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {
                // Move the uploaded file
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                    // Delete old image if exists and is not the default
                    if (!empty($imageUrl) && file_exists('../' . $imageUrl)) {
                        unlink('../' . $imageUrl);
                    }
                    
                    $imageUrl = 'uploads/' . $fileName;
                } else {
                    $_SESSION['error'] = "Error uploading file.";
                    header("Location: index.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "File is not an image.";
                header("Location: index.php");
                exit;
            }
        }
        
        // Update the article
        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content1 = ?, content2 = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$title, $content1, $content2, $imageUrl, $id]);
        
        $_SESSION['success'] = "Article updated successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error updating article: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit;
}

// Redirect if accessed directly without POST
header("Location: index.php");
exit;
?>