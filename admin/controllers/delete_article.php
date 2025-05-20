<?php
// Include the database connection
require_once(__DIR__ . '/../../db_connect.php');

// Check if user is logged in
if (!is_logged_in()) {
    redirect('../login.php');
}

// Process deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id'])) {
    $article_id = (int)$_POST['article_id'];
    
    try {
        // Get article details to delete image
        $stmt = $pdo->prepare('SELECT image_url, featured_image FROM articles WHERE id = ?');
        $stmt->execute([$article_id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            throw new Exception('Article not found');
        }
        
        // Delete the article
        $stmt = $pdo->prepare('DELETE FROM articles WHERE id = ?');
        $stmt->execute([$article_id]);
        
        // Delete the image file if it exists
        if (!empty($article['image_url'])) {
            $image_path = '../../' . $article['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // Also check featured_image field
        if (!empty($article['featured_image']) && $article['featured_image'] != $article['image_url']) {
            $image_path = '../../' . $article['featured_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $_SESSION['message'] = 'Article deleted successfully';
        $_SESSION['message_type'] = 'success';
        
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
    }
}

// Redirect back to articles page
redirect('../index.php?page=articles');