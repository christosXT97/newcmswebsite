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
    $title = $_POST['title'] ?? '';
    $content1 = $_POST['content1'] ?? '';
    $content2 = $_POST['content2'] ?? '';
    $status = $_POST['status'] ?? 'draft';
    
    // Basic validation
    if (empty($title) || empty($content1)) {
        $_SESSION['message'] = 'Title and content are required';
        $_SESSION['message_type'] = 'danger';
        redirect('../index.php?page=articles&action=new');
    }
    
    try {
        // Generate a slug for the article
        $slug = createSlug($title);
        
        // Check if slug already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() > 0) {
            // Add timestamp to make slug unique
            $slug = $slug . '-' . time();
        }
        
        // Handle image upload
        $image_url = '';
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
        
        // Insert article into database - support both field structures
        $stmt = $pdo->prepare('INSERT INTO articles (title, slug, content, content1, content2, status, image_url, featured_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$title, $slug, $content, $content1, $content2, $status, $image_url, $image_url]);
        
        // Set success message
        $_SESSION['message'] = 'Article created successfully';
        $_SESSION['message_type'] = 'success';
        
        // Redirect back to articles page
        redirect('../index.php?page=articles');
        
    } catch (Exception $e) {
        $_SESSION['message'] = 'Error: ' . $e->getMessage();
        $_SESSION['message_type'] = 'danger';
        redirect('../index.php?page=articles&action=new');
    }
} else {
    // Not a POST request
    redirect('../index.php?page=articles');
}

/**
 * Create a slug from a string
 * 
 * @param string $string The string to convert
 * @return string The slug
 */
function createSlug($string) {
    // Convert to lowercase
    $string = strtolower($string);
    
    // Remove special characters
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    
    // Replace spaces with hyphens
    $string = preg_replace('/\s+/', '-', $string);
    
    // Remove duplicate hyphens
    $string = preg_replace('/-+/', '-', $string);
    
    // Trim hyphens from beginning and end
    return trim($string, '-');
}