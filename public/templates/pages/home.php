<?php
/**
 * Home Page Template
 * Main landing page for the public website
 *
 * File path: /public/templates/pages/home.php
 */
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1><?= h($settings['site_name'] ?? 'Enhanced CMS') ?></h1>
                <p class="lead"><?= h($settings['site_description'] ?? 'A modern content management system') ?></p>
                <a href="<?= SITE_URL ?>/index.php?page=blog" class="btn btn-primary btn-lg">Read Our Blog</a>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <img src="<?= SITE_URL ?>/assets/img/hero-image.svg" alt="Hero Image" class="img-fluid">
            </div>
        </div>
    </div>
</section>

<!-- Featured Articles Section -->
<?php if (!empty($featured_articles)): ?>
    <section class="featured-articles py-5">
        <div class="container">
            <h2 class="text-center mb-5">Featured Articles</h2>
            
            <div class="row">
                <?php foreach ($featured_articles as $article): ?>
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <?php if (!empty($article['featured_image'])): ?>
                                <img src="<?= h($article['featured_image']) ?>" class="card-img-top" alt="<?= h($article['title']) ?>">
                            <?php else: ?>
                                <div class="bg-light text-center py-5 card-img-top">
                                    <i class="bi bi-image text-secondary" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body">
                                <h3 class="article-title">
                                    <a href="<?= SITE_URL ?>/index.php?page=article&slug=<?= h($article['slug']) ?>" class="text-decoration-none text-reset">
                                        <?= h($article['title']) ?>
                                    </a>
                                </h3>
                                
                                <div class="article-meta">
                                    <span><i class="bi bi-person"></i> <?= h($article['author_name'] ?? 'Admin') ?></span>
                                    <span class="mx-2">|</span>
                                    <span><i class="bi bi-calendar3"></i> <?= date('M j, Y', strtotime($article['published_at'])) ?></span>
                                </div>
                                
                                <p class="card-text">
                                    <?= !empty($article['excerpt']) ? h($article['excerpt']) : substr(strip_tags($article['content']), 0, 150) . '...' ?>
                                </p>
                                
                                <a href="<?= SITE_URL ?>/index.php?page=article&slug=<?= h($article['slug']) ?>" class="btn btn-outline-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- About Section -->
<section class="about py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h2>About Us</h2>
                <p>Welcome to <?= h($settings['site_name'] ?? 'our website') ?>! We are dedicated to providing high-quality content and services to our visitors.</p>
                <p>Our mission is to deliver valuable information, insights, and resources that help our readers stay informed and engaged.</p>
                <a href="<?= SITE_URL ?>/index.php?page=about" class="btn btn-outline-dark">Learn More</a>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Why Choose Us?</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> High-quality, curated content</li>
                            <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> Regular updates and fresh perspectives</li>
                            <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> Expert insights and analysis</li>
                            <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> User-friendly experience</li>
                            <li class="list-group-item bg-transparent"><i class="bi bi-check-circle-fill text-success me-2"></i> Responsive customer support</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h2>Ready to Get Started?</h2>
                <p class="lead mb-4">Join our community today and stay updated with the latest articles, news, and resources.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a href="<?= SITE_URL ?>/index.php?page=contact" class="btn btn-primary btn-lg px-4 gap-3">Contact Us</a>
                    <a href="<?= SITE_URL ?>/index.php?page=blog" class="btn btn-outline-secondary btn-lg px-4">Explore Blog</a>
                </div>
            </div>
        </div>
    </div>
</section>