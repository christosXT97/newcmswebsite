<?php
/**
 * Articles Controller
 * Handles all article-related operations in the admin panel
 *
 * File path: /admin/controllers/articles.php
 */

// Process form submissions for articles
if (!isset($_POST['action'])) {
    redirect('index.php?page=articles');
}

$action = $_POST['action'];

switch ($action) {
    case 'create':
        handleCreateArticle();
        break;
    case 'update':
        handleUpdateArticle();
        break;
    case 'delete':
        handleDeleteArticle();
        break;
    default:
        set_flash_message('error', 'Invalid action');
        redirect('index.php?page=articles');
}

/**
 * Handle article creation
 */
function handleCreateArticle() {
    global $pdo;
    
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $categories = $_POST['categories'] ?? [];
    $tags = $_POST['tags'] ?? '';
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
    
    // Generate slug from title
    $slug = createSlug($title);
    
    // Validate inputs
    if (empty($title)) {
        set_flash_message('error', 'Title is required');
        $_SESSION['form_data'] = $_POST;
        redirect('index.php?page=articles&action=new');
    }
    
    // Check if slug already exists
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetchColumn() > 0) {
            $slug = $slug . '-' . time();
        }

        // Begin transaction
        $pdo->beginTransaction();
        
        // Handle image upload
        $featured_image = null;
        if (!empty($_FILES['featured_image']['name'])) {
            $featured_image = handleImageUpload($_FILES['featured_image']);
            if ($featured_image === false) {
                // Image upload failed
                throw new Exception('Error uploading image. Please try again.');
            }
        }
        
        // Insert article
        $stmt = $pdo->prepare("INSERT INTO articles (title, slug, content, excerpt, featured_image, author_id, 
                               status, meta_description, meta_keywords, is_featured, published_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $title,
            $slug,
            $content,
            $excerpt,
            $featured_image,
            $_SESSION['admin_id'],
            $status,
            $meta_description,
            $meta_keywords,
            $is_featured,
            $published_at
        ]);
        
        $article_id = $pdo->lastInsertId();
        
        // Process categories
        if (!empty($categories)) {
            $stmt = $pdo->prepare("INSERT INTO article_category (article_id, category_id) VALUES (?, ?)");
            foreach ($categories as $category_id) {
                $stmt->execute([$article_id, $category_id]);
            }
        }
        
        // Process tags
        if (!empty($tags)) {
            $tag_names = array_map('trim', explode(',', $tags));
            foreach ($tag_names as $tag_name) {
                if (empty($tag_name)) continue;
                
                // Create slug for tag
                $tag_slug = createSlug($tag_name);
                
                // Check if tag exists
                $stmt = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
                $stmt->execute([$tag_slug]);
                $tag = $stmt->fetch();
                
                if ($tag) {
                    $tag_id = $tag['id'];
                } else {
                    // Create new tag
                    $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
                    $stmt->execute([$tag_name, $tag_slug]);
                    $tag_id = $pdo->lastInsertId();
                }
                
                // Associate tag with article
                $stmt = $pdo->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$article_id, $tag_id]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        set_flash_message('success', 'Article created successfully');
        redirect('index.php?page=articles');
        
    } catch (Exception $e) {
        // Rollback on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        set_flash_message('error', 'Error creating article: ' . $e->getMessage());
        $_SESSION['form_data'] = $_POST;
        redirect('index.php?page=articles&action=new');
    }
}

/**
 * Handle article update
 */
function handleUpdateArticle() {
    global $pdo;
    
    $id = $_POST['id'] ?? 0;
    $title = trim($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = trim($_POST['excerpt'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $categories = $_POST['categories'] ?? [];
    $tags = $_POST['tags'] ?? '';
    $meta_description = trim($_POST['meta_description'] ?? '');
    $meta_keywords = trim($_POST['meta_keywords'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
    
    // Validate inputs
    if (empty($title) || empty($id)) {
        set_flash_message('error', 'Title and ID are required');
        $_SESSION['form_data'] = $_POST;
        redirect('index.php?page=articles&action=edit&id=' . $id);
    }
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Check if the article exists
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            throw new Exception('Article not found');
        }
        
        // Generate slug from title if title changed
        $slug = $article['slug'];
        if ($title !== $article['title']) {
            $slug = createSlug($title);
            
            // Check if slug already exists (excluding current article)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            if ($stmt->fetchColumn() > 0) {
                $slug = $slug . '-' . time();
            }
        }
        
        // Handle image upload
        $featured_image = $article['featured_image'];
        if (!empty($_FILES['featured_image']['name'])) {
            $new_image = handleImageUpload($_FILES['featured_image']);
            if ($new_image === false) {
                // Image upload failed
                throw new Exception('Error uploading image. Please try again.');
            }
            
            // Delete old image if exists
            if (!empty($featured_image) && file_exists(BASE_PATH . '/' . $featured_image)) {
                unlink(BASE_PATH . '/' . $featured_image);
            }
            
            $featured_image = $new_image;
        }
        
        // Update article
        $stmt = $pdo->prepare("UPDATE articles SET 
                               title = ?, 
                               slug = ?, 
                               content = ?, 
                               excerpt = ?, 
                               featured_image = ?, 
                               status = ?, 
                               meta_description = ?, 
                               meta_keywords = ?, 
                               is_featured = ?, 
                               published_at = ? 
                               WHERE id = ?");
        
        $stmt->execute([
            $title,
            $slug,
            $content,
            $excerpt,
            $featured_image,
            $status,
            $meta_description,
            $meta_keywords,
            $is_featured,
            $published_at,
            $id
        ]);
        
        // Update categories (delete and re-insert)
        $stmt = $pdo->prepare("DELETE FROM article_category WHERE article_id = ?");
        $stmt->execute([$id]);
        
        if (!empty($categories)) {
            $stmt = $pdo->prepare("INSERT INTO article_category (article_id, category_id) VALUES (?, ?)");
            foreach ($categories as $category_id) {
                $stmt->execute([$id, $category_id]);
            }
        }
        
        // Update tags (delete and re-insert)
        $stmt = $pdo->prepare("DELETE FROM article_tag WHERE article_id = ?");
        $stmt->execute([$id]);
        
        if (!empty($tags)) {
            $tag_names = array_map('trim', explode(',', $tags));
            foreach ($tag_names as $tag_name) {
                if (empty($tag_name)) continue;
                
                // Create slug for tag
                $tag_slug = createSlug($tag_name);
                
                // Check if tag exists
                $stmt = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
                $stmt->execute([$tag_slug]);
                $tag = $stmt->fetch();
                
                if ($tag) {
                    $tag_id = $tag['id'];
                } else {
                    // Create new tag
                    $stmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
                    $stmt->execute([$tag_name, $tag_slug]);
                    $tag_id = $pdo->lastInsertId();
                }
                
                // Associate tag with article
                $stmt = $pdo->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)");
                $stmt->execute([$id, $tag_id]);
            }
        }
        
        // Commit transaction
        $pdo->commit();
        
        set_flash_message('success', 'Article updated successfully');
        redirect('index.php?page=articles');
        
    } catch (Exception $e) {
        // Rollback on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        set_flash_message('error', 'Error updating article: ' . $e->getMessage());
        $_SESSION['form_data'] = $_POST;
        redirect('index.php?page=articles&action=edit&id=' . $id);
    }
}

/**
 * Handle article deletion
 */
function handleDeleteArticle() {
    global $pdo;
    
    $id = $_POST['id'] ?? 0;
    
    if (empty($id)) {
        set_flash_message('error', 'Article ID is required');
        redirect('index.php?page=articles');
    }
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Get article details to delete associated image
        $stmt = $pdo->prepare("SELECT featured_image FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();
        
        if (!$article) {
            throw new Exception('Article not found');
        }
        
        // Delete associated records from article_category table
        $stmt = $pdo->prepare("DELETE FROM article_category WHERE article_id = ?");
        $stmt->execute([$id]);
        
        // Delete associated records from article_tag table
        $stmt = $pdo->prepare("DELETE FROM article_tag WHERE article_id = ?");
        $stmt->execute([$id]);
        
        // Delete the article
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete the image file if exists
        if (!empty($article['featured_image']) && file_exists(BASE_PATH . '/' . $article['featured_image'])) {
            unlink(BASE_PATH . '/' . $article['featured_image']);
        }
        
        // Commit transaction
        $pdo->commit();
        
        set_flash_message('success', 'Article deleted successfully');
        
    } catch (Exception $e) {
        // Rollback on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        set_flash_message('error', 'Error deleting article: ' . $e->getMessage());
    }
    
    redirect('index.php?page=articles');
}

/**
 * Handle image upload
 * 
 * @param array $file The uploaded file data from $_FILES
 * @return string|false The relative path to the uploaded file or false on failure
 */
function handleImageUpload($file) {
    // Validate file
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = getenv('UPLOAD_MAX_SIZE') ?: 5242880; // 5MB default
    
    if ($file['size'] > $max_size) {
        set_flash_message('error', 'File size exceeds the maximum limit (5MB)');
        return false;
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        set_flash_message('error', 'Only JPG, PNG, GIF, and WEBP images are allowed');
        return false;
    }
    
    // Generate safe filename
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', basename($file['name']));
    $upload_dir = 'uploads/articles/';
    $upload_path = BASE_PATH . '/' . $upload_dir;
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    $target_file = $upload_path . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $upload_dir . $filename;
    } else {
        return false;
    }
}

/**
 * Create a URL-friendly slug from a string
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