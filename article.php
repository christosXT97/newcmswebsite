<?php
// Include database connection
require_once('db_connect.php');

// Get article slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (empty($slug) && isset($_GET['article'])) {
    $slug = $_GET['article']; // Alternative parameter name
}

// If no slug provided, redirect to home page
if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Fetch article from database
try {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    $article = $stmt->fetch();
    
    // If article not found, redirect to home page
    if (!$article) {
        header('Location: index.php');
        exit;
    }
    
    // Update view count
    $stmt = $pdo->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?");
    $stmt->execute([$article['id']]);
    
} catch (Exception $e) {
    // Log error
    error_log('Error fetching article: ' . $e->getMessage());
    header('Location: index.php');
    exit;
}

// Get image URL (check both columns)
$image_url = null;
if (!empty($article['image_url'])) {
    $image_url = $article['image_url'];
} elseif (!empty($article['featured_image'])) {
    $image_url = $article['featured_image']; 
}

// Set page title
$page_title = $article['title'];

// Get article content
$content1 = !empty($article['content1']) ? $article['content1'] : '';
$content2 = !empty($article['content2']) ? $article['content2'] : '';
if (empty($content1) && !empty($article['content'])) {
    // If content1 is empty but we have main content, split it
    $parts = explode("\n\n", $article['content'], 2);
    $content1 = $parts[0];
    $content2 = isset($parts[1]) ? $parts[1] : '';
}

// Fetch site settings for header/footer
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Silently fail
}

$site_name = $settings['site_name'] ?? 'My CMS';
$site_description = $settings['site_description'] ?? 'A modern content management system';
$logo_text = $settings['logo_text'] ?? 'LOGO';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> - <?= htmlspecialchars($site_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: white;
            border-bottom: 1px solid #dee2e6;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
        }
        
        .article-container {
            max-width: 1200px;
            margin: 40px auto;
        }
        
        .article-card {
            background-color: white;
            border: 1px solid #dee2e6;
            border-radius: 0;
            padding: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            position: relative;
        }
        
        .article-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #212529;
        }
        
        .article-meta {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        .article-content {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .article-text {
            flex: 1;
            min-width: 300px;
        }
        
        .article-image {
            flex: 0 0 300px;
            height: 200px;
            background-size: cover;
            background-position: center;
            border-radius: 5px;
        }
        
        .article-text p {
            margin-bottom: 20px;
        }
        
        .edit-button {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: #0d6efd;
            color: white;
            font-size: 16px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
        }
        
        .edit-button:hover {
            background-color: #0b5ed7;
            color: white;
        }
        
        .delete-button {
            position: absolute;
            top: 15px;
            right: 55px;
            background-color: #dc3545;
            color: white;
            font-size: 16px;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
        }
        
        .delete-button:hover {
            background-color: #bb2d3b;
            color: white;
        }
        
        .footer {
            background-color: white;
            border-top: 1px solid #dee2e6;
            padding: 40px 0;
            margin-top: 40px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .article-content {
                flex-direction: column;
            }
            
            .article-image {
                width: 100%;
                margin-top: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <header>
        <div class="container">
            <div class="navbar py-3">
                <a href="index.php" class="logo"><?= htmlspecialchars($logo_text) ?></a>
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="index.php?page=about">About</a>
                    <a href="index.php?page=blog">Blog</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <div class="article-container">
        <div class="article-card">
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                <a href="admin/index.php?page=articles&action=edit&id=<?= $article['id'] ?>" class="edit-button">✏️</a>
                <button onclick="confirmDelete(<?= $article['id'] ?>)" class="delete-button">🗑️</button>
            <?php endif; ?>
            
            <h1 class="article-title"><?= htmlspecialchars($article['title']) ?></h1>
            
            <div class="article-meta">
                <span><?= date('F j, Y', strtotime($article['created_at'])) ?></span>
            </div>
            
            <div class="article-content">
                <div class="article-text">
                    <?php if (!empty($content1)): ?>
                        <p><?= nl2br(htmlspecialchars($content1)) ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($content2)): ?>
                        <p><?= nl2br(htmlspecialchars($content2)) ?></p>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($image_url)): ?>
                    <div class="article-image" style="background-image: url('<?= htmlspecialchars($image_url) ?>')"></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- More Articles Section -->
        <?php
        try {
            $stmt = $pdo->prepare("SELECT * FROM articles WHERE id != ? AND status = 'published' ORDER BY created_at DESC LIMIT 2");
            $stmt->execute([$article['id']]);
            $more_articles = $stmt->fetchAll();
            
            if (!empty($more_articles)):
        ?>
            <?php foreach ($more_articles as $more_article): ?>
                <?php
                // Get image URL for this article
                $more_image_url = null;
                if (!empty($more_article['image_url'])) {
                    $more_image_url = $more_article['image_url'];
                } elseif (!empty($more_article['featured_image'])) {
                    $more_image_url = $more_article['featured_image']; 
                }
                
                // Get content
                $more_content = !empty($more_article['content1']) ? $more_article['content1'] : $more_article['content'];
                $more_content = substr($more_content, 0, 300) . '...';
                ?>
                
                <div class="article-card">
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                        <a href="admin/index.php?page=articles&action=edit&id=<?= $more_article['id'] ?>" class="edit-button">✏️</a>
                        <button onclick="confirmDelete(<?= $more_article['id'] ?>)" class="delete-button">🗑️</button>
                    <?php endif; ?>
                    
                    <h2 class="article-title">
                        <a href="article.php?slug=<?= htmlspecialchars($more_article['slug']) ?>" style="color: inherit; text-decoration: none;">
                            <?= htmlspecialchars($more_article['title']) ?>
                        </a>
                    </h2>
                    
                    <div class="article-meta">
                        <span><?= date('F j, Y', strtotime($more_article['created_at'])) ?></span>
                    </div>
                    
                    <div class="article-content">
                        <div class="article-text">
                            <p><?= nl2br(htmlspecialchars($more_content)) ?></p>
                            <a href="article.php?slug=<?= htmlspecialchars($more_article['slug']) ?>" class="btn btn-primary">Read More</a>
                        </div>
                        
                        <?php if (!empty($more_image_url)): ?>
                            <div class="article-image" style="background-image: url('<?= htmlspecialchars($more_image_url) ?>')"></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php
            endif;
        } catch (Exception $e) {
            // Silently fail
        }
        ?>
    </div>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="company-name"><?= htmlspecialchars($settings['company_name'] ?? 'My Company') ?></div>
                    <p><?= htmlspecialchars($settings['company_desc'] ?? '') ?></p>
                    <div class="copyright">© <?= date('Y') ?> <?= htmlspecialchars($settings['company_name'] ?? 'My Company') ?>. All rights reserved.</div>
                </div>
                
                <div class="footer-links">
                    <h3>Navigation</h3>
                    <a href="index.php">Home</a>
                    <a href="index.php?page=about">About</a>
                    <a href="index.php?page=blog">Blog</a>
                </div>
                
                <div class="footer-links">
                    <h3>Connect</h3>
                    <a href="#">Facebook</a>
                    <a href="#">Twitter</a>
                    <a href="#">Instagram</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Delete Confirmation Modal -->
    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this article?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <form action="admin/controllers/delete_article.php" method="post">
                        <input type="hidden" name="article_id" id="deleteArticleId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            document.getElementById('deleteArticleId').value = id;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
    <?php endif; ?>
</body>
</html>