<?php
/**
 * 404 Not Found Page Template
 * Displayed when a requested page is not found
 *
 * File path: /public/templates/pages/404.php
 */
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="error-template">
                <h1 class="display-1">404</h1>
                <h2 class="mb-4">Page Not Found</h2>
                <div class="error-details mb-4">
                    Sorry, we couldn't find the page you were looking for.
                </div>
                <div class="error-actions">
                    <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary me-2">
                        <i class="bi bi-house-fill"></i> Back to Home
                    </a>
                    <a href="<?= SITE_URL ?>/index.php?page=contact" class="btn btn-outline-secondary">
                        <i class="bi bi-envelope-fill"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">You might be interested in:</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Recent Articles</h4>
                            <ul class="list-unstyled">
                                <?php foreach (array_slice($recent_articles, 0, 3) as $recent): ?>
                                    <li class="mb-2">
                                        <a href="<?= SITE_URL ?>/index.php?page=article&slug=<?= h($recent['slug']) ?>" class="text-decoration-none">
                                            <?= h($recent['title']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h4>Popular Pages</h4>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <a href="<?= SITE_URL ?>/index.php" class="text-decoration-none">Home</a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= SITE_URL ?>/index.php?page=blog" class="text-decoration-none">Blog</a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= SITE_URL ?>/index.php?page=about" class="text-decoration-none">About Us</a>
                                </li>
                                <li class="mb-2">
                                    <a href="<?= SITE_URL ?>/index.php?page=contact" class="text-decoration-none">Contact Us</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>