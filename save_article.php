<?php
// save_article.php - Handle saving a new article
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
    $title = $_POST['title'] ?? '';
    $content1 = $_POST['content1'] ?? '';
    $content2 = $_POST['content2'] ?? '';
    $imageUrl = '';
    
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
    
    try {
        // Insert the new article
        $stmt = $pdo->prepare("INSERT INTO articles (title, content1, content2, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content1, $content2, $imageUrl]);
        
        $_SESSION['success'] = "Article added successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error adding article: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit;
}

// Redirect if accessed directly without POST
header("Location: index.php");
exit;
?>