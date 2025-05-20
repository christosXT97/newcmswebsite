<?php
// Include the database connection
require_once(__DIR__ . '/../../db_connect.php');

// Check if user is logged in
if (!is_logged_in()) {
    redirect('../login.php');
}

// Process form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $title = $_POST['title'] ?? '';
    $content1 = $_POST['content1'] ?? '';
    $content2 = $_POST['content2'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    
    // Basic validation
    if (empty($id) || empty($title) || empty($content1)) {
        $_SESSION['message'] = 'ID, title, and content are required';
        $_SESSION['message_type'] = 'danger';
        redirect('../index.php?page=articles&action=edit&id=' . $id);
    }
    
    try {
        // Get existing article
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            throw new Exception('Article not found');
        }
        
        // Handle image upload
        $image_url = $article['image_url']; // Keep existing image by default
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../uploads/';
            
            // Create directory if not exists
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $filename = time() . '_' . basename($_FILES['image']['name']);
            $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '', $filename); // Sanitize filename
            $target_file = $upload_dir . $filename;
            
            // Move uploaded file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Delete old image if exists
                if (!empty($article['image_url']) && file_exists('../../' . $article['image_url'])) {
                    unlink('../../' . $article['image_url']);
                }
                
                $image_url = 'uploads/' . $filename;
            } else {
                throw new Exception('Failed to upload image');
            }
        }
        
        // Combine content for the 'content' field
        $content = $content1;
        if (!empty($content2)) {
            $content .= "\n\n" . $content2;
        }
        
        // Update article in database - support both field structures
        $stmt = $pdo->prepare('UPDATE articles SET title = ?, content = ?, content1 = ?, content2 = ?, status = ?, image_url = ?, featured_image = ? WHERE id = ?');
        $stmt->execute([$title, $content, $content1, $content2, $status, $image_url, $image_url, $id]);
        
        // Set success message
        $_SESSION['message'] = 'Article updated successfully';
        $_SESSION['message_type'] = 'success';
        
        // Redirect back to articles page
        redirect('../index.php?page=articles');
        
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        redirect('../index.php?page=articles&action=edit&id=' . $id);
    }
} else {
    // Not a POST request
    redirect('../index.php?page=articles');
}