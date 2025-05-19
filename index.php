<?php
// index.php - Frontend website with direct connection

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$db_host = 'localhost';
$db_name = 'newcmswebsite';
$db_user = 'root';
$db_pass = '';

// Site configuration
$config = [
    'site_name' => 'My CMS Website',
    'site_url' => 'http://localhost/newcmswebsite/newcmswebsite',
    'upload_dir' => 'uploads/',
    'data_dir' => 'data/'
];

// Create the PDO connection directly
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get settings
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    // Get articles
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY id DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get navigation links
    $stmt = $pdo->query("SELECT * FROM links WHERE type='nav' ORDER BY sort_order");
    $navLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get social links
    $stmt = $pdo->query("SELECT * FROM links WHERE type='social' ORDER BY sort_order");
    $socialLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($settings['company_name'] ?? 'My Website') ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 20px 0;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            border: 1px solid #000;
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
        }
        
        .nav-links {
            display: flex;
        }
        
        .nav-links a {
            margin-left: 20px;
            text-decoration: none;
            color: #0d6efd;
        }
        
        .content {
            padding: 40px 0;
        }
        
        .article {
            margin-bottom: 40px;
            padding-bottom: 40px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .article:last-child {
            border-bottom: none;
        }
        
        .article-header {
            margin-bottom: 20px;
        }
        
        .article-title {
            font-size: 28px;
            margin-bottom: 10px;
            color: #212529;
        }
        
        .article-meta {
            color: #6c757d;
            font-size: 14px;
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
        
        footer {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 40px 0;
            margin-top: 40px;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }
        
        .footer-info {
            flex: 2;
            min-width: 300px;
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .footer-links {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 150px;
        }
        
        .footer-links h3 {
            margin-top: 0;
            font-size: 18px;
        }
        
        .footer-links a {
            margin-bottom: 10px;
            text-decoration: none;
            color: #0d6efd;
        }
        
        .copyright {
            margin-top: 20px;
            color: #6c757d;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .article-content {
                flex-direction: column;
            }
            
            .article-image {
                width: 100%;
                margin-top: 20px;
            }
            
            .footer-content {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php if (isset($error)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px;">
            <?= $error ?>
        </div>
    <?php endif; ?>
    
    <header>
        <div class="container">
            <div class="navbar">
                <a href="index.php" class="logo"><?= htmlspecialchars($settings['logo_text'] ?? 'LOGO') ?></a>
                <div class="nav-links">
                    <?php if (!empty($navLinks)): ?>
                        <?php foreach ($navLinks as $link): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['text']) ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#">Home</a>
                        <a href="#">About</a>
                        <a href="#">Blog</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <div class="content">
        <div class="container">
            <?php if (empty($articles)): ?>
                <p>No articles found.</p>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <div class="article" id="article-<?= $article['id'] ?>">
                        <div class="article-header">
                            <h2 class="article-title"><?= htmlspecialchars($article['title']) ?></h2>
                        </div>
                        <div class="article-content">
                            <div class="article-text">
                                <p><?= nl2br(htmlspecialchars($article['content1'] ?? '')) ?></p>
                                <?php if (!empty($article['content2'])): ?>
                                    <p><?= nl2br(htmlspecialchars($article['content2'])) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($article['image_url'])): ?>
                                <div class="article-image" style="background-image: url('<?= htmlspecialchars($article['image_url']) ?>')"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <div class="company-name"><?= htmlspecialchars($settings['company_name'] ?? 'My Company') ?></div>
                    <p><?= nl2br(htmlspecialchars($settings['company_desc'] ?? '')) ?></p>
                    <div class="copyright">© <?= date('Y') ?> <?= htmlspecialchars($settings['company_name'] ?? 'My Company') ?>. All rights reserved.</div>
                </div>
                
                <div class="footer-links">
                    <h3>Navigation</h3>
                    <?php if (!empty($navLinks)): ?>
                        <?php foreach ($navLinks as $link): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>"><?= htmlspecialchars($link['text']) ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#">Home</a>
                        <a href="#">About</a>
                        <a href="#">Blog</a>
                    <?php endif; ?>
                </div>
                
                <div class="footer-links">
                    <h3>Connect</h3>
                    <?php if (!empty($socialLinks)): ?>
                        <?php foreach ($socialLinks as $link): ?>
                            <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank"><?= htmlspecialchars($link['text']) ?></a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="https://facebook.com" target="_blank">Facebook</a>
                        <a href="https://twitter.com" target="_blank">Twitter</a>
                        <a href="https://instagram.com" target="_blank">Instagram</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>