</main>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h4 class="footer-title"><?= h($settings['site_name'] ?? 'Enhanced CMS') ?></h4>
                    <p><?= h($settings['site_description'] ?? 'A modern content management system') ?></p>
                    
                    <div class="social-links">
                        <?php if (empty($social_links)): ?>
                            <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                            <a href="#" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                            <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <?php else: ?>
                            <?php foreach ($social_links as $link): ?>
                                <a href="<?= h($link['url']) ?>" aria-label="<?= h($link['platform']) ?>" target="_blank">
                                    <i class="bi bi-<?= h($link['icon'] ?? strtolower($link['platform'])) ?>"></i>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h4 class="footer-title">Quick Links</h4>
                    <ul class="list-unstyled">
                        <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
                        <li><a href="<?= SITE_URL ?>/index.php?page=about">About Us</a></li>
                        <li><a href="<?= SITE_URL ?>/index.php?page=blog">Blog</a></li>
                        <li><a href="<?= SITE_URL ?>/index.php?page=contact">Contact Us</a></li>
                        <li><a href="<?= SITE_URL ?>/index.php?page=privacy">Privacy Policy</a></li>
                        <li><a href="<?= SITE_URL ?>/index.php?page=terms">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h4 class="footer-title">Recent Articles</h4>
                    <ul class="list-unstyled">
                        <?php foreach ($recent_articles as $article): ?>
                            <li class="mb-2">
                                <a href="<?= SITE_URL ?>/index.php?page=article&slug=<?= h($article['slug']) ?>">
                                    <?= h($article['title']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <hr class="mt-4 mb-4 border-top border-light">
            
            <div class="row">
                <div class="col-md-6 mb-3 mb-md-0">
                    <p class="mb-0">
                        &copy; <?= date('Y') ?> <?= h($settings['site_name'] ?? 'Enhanced CMS') ?>. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        Powered by <a href="https://github.com/yourusername/enhanced-cms" target="_blank">Enhanced CMS</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize any components that need JavaScript
            
            // Add active class to current nav item
            const currentLocation = window.location.href;
            const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
            
            navLinks.forEach(function(link) {
                if (link.href === currentLocation) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>