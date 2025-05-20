<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($settings['site_name'] ?? 'Enhanced CMS') ?></title>
    
    <!-- Meta tags -->
    <meta name="description" content="<?= h($settings['site_description'] ?? 'A modern content management system') ?>">
    <meta name="keywords" content="CMS, content management system, website">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= SITE_URL ?>/assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    
    <style>
        /* Basic styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .hero {
            background-color: #f8f9fa;
            padding: 5rem 0;
            margin-bottom: 3rem;
        }
        
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }
        
        .hero p {
            font-size: 1.25rem;
            opacity: 0.8;
            margin-bottom: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .card-img-top {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            height: 200px;
            object-fit: cover;
        }
        
        .article-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .article-meta {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }
        
        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 3rem 0;
        }
        
        .footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
        }
        
        .footer a:hover {
            color: #fff;
        }
        
        .footer-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .social-links {
            margin-top: 1.5rem;
        }
        
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            margin-right: 0.5rem;
            transition: background-color 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        /* Article styles */
        .article-header {
            margin-bottom: 2rem;
        }
        
        .article-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .article-featured-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 0.5rem;
            margin-bottom: 2rem;
        }
        
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        
        /* Pagination */
        .pagination .page-link {
            color: #333;
            border: none;
            padding: 0.5rem 1rem;
        }
        
        .pagination .page-item.active .page-link {
            background-color: #343a40;
            color: #fff;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .hero {
                padding: 3rem 0;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .article-header h1 {
                font-size: 2rem;
            }
            
            .article-featured-image {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="<?= SITE_URL ?>/index.php">
                <?= h($settings['site_name'] ?? 'Enhanced CMS') ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (empty($menu_items)): ?>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'home' ? 'active' : '' ?>" href="<?= SITE_URL ?>/index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'about' ? 'active' : '' ?>" href="<?= SITE_URL ?>/index.php?page=about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'blog' ? 'active' : '' ?>" href="<?= SITE_URL ?>/index.php?page=blog">Blog</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $page === 'contact' ? 'active' : '' ?>" href="<?= SITE_URL ?>/index.php?page=contact">Contact</a>
                        </li>
                    <?php else: ?>
                        <?php foreach ($menu_items as $item): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= h($item['url']) ?>" <?= $item['target'] !== '_self' ? 'target="' . h($item['target']) . '"' : '' ?>>
                                    <?php if (!empty($item['icon'])): ?>
                                        <i class="bi bi-<?= h($item['icon']) ?> me-1"></i>
                                    <?php endif; ?>
                                    <?= h($item['title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-outline-primary btn-sm" href="<?= SITE_URL ?>/admin/login.php">
                            <i class="bi bi-lock-fill me-1"></i> Admin Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>