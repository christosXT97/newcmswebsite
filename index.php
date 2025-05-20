<?php
// Include database connection
require_once('db_connect.php');

// Fetch articles from database
try {
    $stmt = $pdo->query("SELECT * FROM articles WHERE status = 'published' ORDER BY created_at DESC");
    $articles = $stmt->fetchAll();
} catch (Exception $e) {
    // Log error
    error_log('Error fetching articles: ' . $e->getMessage());
    $articles = [];
}

// Fetch featured articles
$featured_articles = [];
try {
    $stmt = $pdo->query("SELECT * FROM articles WHERE status = 'published' AND is_featured = 1 ORDER BY created_at DESC LIMIT 3");
    $featured_articles = $stmt->fetchAll();
} catch (Exception $e) {
    // Silently fail
}

// Fetch site settings
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .hero {
            background-color: #f8f9fa;
            padding: 5rem 0;
            margin-bottom: 3rem;
        }
        
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
        }
        
        .featured-image {
            height: 250px;
            object-fit: cover;
            border-radius: 8px 8px 0 0;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .footer {
            margin-top: 4rem;
            padding: 2rem 0;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?= htmlspecialchars($site_name) ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="mb-3"><?= htmlspecialchars($site_name) ?></h1>
                    <p class="lead mb-4"><?= htmlspecialchars($site_description) ?></p>
                    <a href="#articles" class="btn btn-primary btn-lg">Read Our Articles</a>
                </div>
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x400" alt="Hero Image" class="img-fluid rounded">
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Articles -->
    <?php if (!empty($featured_articles)): ?>
        <section class="container mb-5">
            <h2 class="mb-4">Featured Articles</h2>
            <div class="row">
                <?php foreach ($featured_articles as $article): ?>
                    <?php
                    // Get image URL
                    $image_url = !empty($article['image_url']) ? $article['image_url'] : 
                                (!empty($article['featured_image']) ? $article['featured_image'] : 'https://via.placeholder.com/800x450');
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($image_url) ?>" class="featured-image" alt="<?= htmlspecialchars($article['title']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                                <p class="card-text">
                                    <?php
                                    if (!empty($article['excerpt'])) {
                                        echo htmlspecialchars(substr($article['excerpt'], 0, 100)) . '...';
                                    } elseif (!empty($article['content1'])) {
                                        echo htmlspecialchars(substr($article['content1'], 0, 100)) . '...';
                                    } elseif (!empty($article['content'])) {
                                        echo htmlspecialchars(substr($article['content'], 0, 100)) . '...';
                                    }
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?= date('F j, Y', strtotime($article['created_at'])) ?></small>
                                    <a href="article.php?slug=<?= htmlspecialchars($article['slug']) ?>" class="btn btn-sm btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- All Articles -->
    <section id="articles" class="container">
        <h2 class="mb-4">Latest Articles</h2>
        <div class="row">
            <?php if (empty($articles)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No articles found.</div>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <?php
                    // Get image URL
                    $image_url = !empty($article['image_url']) ? $article['image_url'] : 
                                (!empty($article['featured_image']) ? $article['featured_image'] : 'https://via.placeholder.com/800x450');
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($image_url) ?>" class="featured-image" alt="<?= htmlspecialchars($article['title']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                                <p class="card-text">
                                    <?php
                                    if (!empty($article['excerpt'])) {
                                        echo htmlspecialchars(substr($article['excerpt'], 0, 100)) . '...';
                                    } elseif (!empty($article['content1'])) {
                                        echo htmlspecialchars(substr($article['content1'], 0, 100)) . '...';
                                    } elseif (!empty($article['content'])) {
                                        echo htmlspecialchars(substr($article['content'], 0, 100)) . '...';
                                    }
                                    ?>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted"><?= date('F j, Y', strtotime($article['created_at'])) ?></small>
                                    <a href="article.php?slug=<?= htmlspecialchars($article['slug']) ?>" class="btn btn-sm btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3><?= htmlspecialchars($site_name) ?></h3>
                    <p><?= htmlspecialchars($site_description) ?></p>
                </div>
                <div class="col-md-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-dark fs-5"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-dark fs-5"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-dark fs-5"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-dark fs-5"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="mb-0">&copy; <?= date('Y') ?> <?= htmlspecialchars($site_name) ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>