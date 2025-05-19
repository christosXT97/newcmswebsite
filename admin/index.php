<?php
// admin/index.php - Admin interface with direct database connection
session_start();

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$db_host = 'localhost';
$db_name = 'newcmswebsite'; // Changed from cms_database to match your database name
$db_user = 'root';
$db_pass = '';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Create database connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get articles
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY id DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get settings
    $stmt = $pdo->query("SELECT * FROM settings");
    $settings = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    // Get social links
    $stmt = $pdo->query("SELECT * FROM links WHERE type='social' ORDER BY sort_order");
    $socialLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Database connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; }
        header { background: #fff; padding: 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        header h1 { margin: 0; }
        header a { text-decoration: none; color: #dc3545; font-weight: bold; }
        .container { max-width: 1000px; margin: auto; padding: 30px; }
        .section { background: white; padding: 20px; border-radius: 6px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        form, .article { background: white; padding: 20px; border-radius: 6px; margin-bottom: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; box-sizing: border-box; }
        button { padding: 10px 20px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        .save-btn { background-color: #28a745; color: white; }
        .delete-btn { background-color: #dc3545; color: white; margin-left: 10px; }
        .view-btn { background-color: #007bff; color: white; margin-left: 10px; text-decoration: none; display: inline-block; }
        .edit-btn { background-color: #ffc107; color: #212529; }
        img { max-width: 300px; max-height: 200px; width: auto; height: auto; margin-top: 10px; border-radius: 6px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .tabs { display: flex; margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; border: 1px solid #ddd; background: #f8f9fa; margin-right: 5px; border-radius: 4px 4px 0 0; }
        .tab.active { background: white; border-bottom: 1px solid white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .error { background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .success { background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <a href="logout.php">Logout</a>
</header>

<div class="container">
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <div class="tabs">
        <div class="tab active" onclick="openTab('company')">Company Info</div>
        <div class="tab" onclick="openTab('articles')">Articles</div>
        <div class="tab" onclick="openTab('social')">Social Links</div>
    </div>

    <!-- Company Info Tab -->
    <div id="company" class="tab-content active section">
        <h2>Company Information</h2>
        <form action="update_settings.php" method="POST">
            <label for="logo">Logo Text:</label>
            <input type="text" id="logo" name="logo" value="<?= htmlspecialchars($settings['logo_text'] ?? 'LOGO') ?>" required>
            
            <label for="company_name">Company Name:</label>
            <input type="text" id="company_name" name="company_name" value="<?= htmlspecialchars($settings['company_name'] ?? 'Your Company') ?>" required>
            
            <label for="company_desc">Company Description:</label>
            <textarea id="company_desc" name="company_desc" rows="4" required><?= htmlspecialchars($settings['company_desc'] ?? 'Company description goes here') ?></textarea>
            
            <button type="submit" class="save-btn">Save Company Info</button>
        </form>
    </div>

    <!-- Articles Tab -->
    <div id="articles" class="tab-content">
        <!-- Add new article -->
        <form action="save_article.php" method="POST" enctype="multipart/form-data" class="section">
            <h2>Add New Article</h2>
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="content1" placeholder="Paragraph 1" rows="4" required></textarea>
            <textarea name="content2" placeholder="Paragraph 2 (optional)" rows="4"></textarea>
            <input type="file" name="image">
            <button type="submit" class="save-btn">Add Article</button>
        </form>

        <h2>Existing Articles</h2>
        <?php if (!empty($articles)): ?>
            <?php foreach ($articles as $a): ?>
                <div class="article" id="article-<?= $a['id'] ?>">
                    <form action="update_article.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <input type="text" name="title" value="<?= htmlspecialchars($a['title']) ?>" required>
                        <textarea name="content1" rows="4" required><?= htmlspecialchars($a['content1']) ?></textarea>
                        <textarea name="content2" rows="4"><?= htmlspecialchars($a['content2'] ?? '') ?></textarea>

                        <?php if (!empty($a['image_url'])): ?>
                            <img src="<?= htmlspecialchars($a['image_url']) ?>" alt="Article Image">
                        <?php endif; ?>

                        <input type="file" name="image">
                        <div style="margin-top: 10px;">
                            <button type="submit" class="save-btn">Update</button>
                            <a href="../index.php#article-<?= $a['id'] ?>" class="view-btn" target="_blank">View</a>
                            <button type="button" class="delete-btn" onclick="deleteArticle(<?= $a['id'] ?>)">Delete</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No articles found. Add your first article above.</p>
        <?php endif; ?>
    </div>

    <!-- Social Links Tab -->
    <div id="social" class="tab-content section">
        <h2>Social Links</h2>
        
        <form action="save_social.php" method="POST">
            <?php if (!empty($socialLinks)): ?>
                <?php foreach ($socialLinks as $link): ?>
                    <div style="display: flex; margin-bottom: 10px; align-items: center;">
                        <input type="hidden" name="link_id[]" value="<?= $link['id'] ?>">
                        <input type="text" name="link_text[]" value="<?= htmlspecialchars($link['text']) ?>" style="flex: 1; margin-right: 10px;" required>
                        <input type="url" name="link_url[]" value="<?= htmlspecialchars($link['url']) ?>" style="flex: 2; margin-right: 10px;" required>
                        <button type="button" class="delete-btn" onclick="this.parentElement.remove()">Remove</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <div id="socialLinksContainer"></div>
            
            <button type="button" class="edit-btn" onclick="addSocialLink()" style="margin-bottom: 15px;">+ Add Social Link</button>
            <button type="submit" class="save-btn">Save All Social Links</button>
        </form>
    </div>
</div>

<script>
    // Tab functionality
    function openTab(tabName) {
        const tabs = document.querySelectorAll('.tab');
        const tabContents = document.querySelectorAll('.tab-content');
        
        // Hide all tab contents and deactivate tabs
        tabContents.forEach(content => content.classList.remove('active'));
        tabs.forEach(tab => tab.classList.remove('active'));
        
        // Activate selected tab and content
        document.getElementById(tabName).classList.add('active');
        event.currentTarget.classList.add('active');
    }
    
    // Delete article confirmation
    function deleteArticle(id) {
        if (confirm('Are you sure you want to delete this article?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete_article.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id';
            input.value = id;
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    }
    
    // Add social link field
    function addSocialLink() {
        const container = document.getElementById('socialLinksContainer');
        const div = document.createElement('div');
        div.style.display = 'flex';
        div.style.marginBottom = '10px';
        div.style.alignItems = 'center';
        
        div.innerHTML = `
            <input type="hidden" name="link_id[]" value="new">
            <input type="text" name="link_text[]" placeholder="Name (e.g. Facebook)" style="flex: 1; margin-right: 10px;" required>
            <input type="url" name="link_url[]" placeholder="URL (e.g. https://facebook.com)" style="flex: 2; margin-right: 10px;" required>
            <button type="button" class="delete-btn" onclick="this.parentElement.remove()">Remove</button>
        `;
        
        container.appendChild(div);
    }
</script>
</body>
</html>