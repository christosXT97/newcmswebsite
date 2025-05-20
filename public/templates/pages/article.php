<?php
/**
 * Single Article View Template
 * Displays a single article with full content
 *
 * File path: /public/templates/pages/article.php
 */

// Get article slug from URL
$slug = $_GET['slug'] ?? '';

// Fetch article details
$article = null;
$categories = [];
$tags = [];
$related_articles = [];

if (!empty($slug)) {
    try {
        // Get article
        $stmt = $pdo->prepare("SELECT a.*, u.username as author_name 
                              FROM articles a 
                              LEFT JOIN users u ON a.author_id = u.id 
                              WHERE a.slug = ? AND a.status = 'published'");
        $stmt->execute([$slug]);
        $article = $stmt->fetch();
        
        if ($article) {
            // Update view count
            $stmt = $pdo->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?");
            $stmt->execute([$article['id']]);
            
            // Get categories
            $stmt = $pdo->prepare("SELECT c.name, c.slug 
                                  FROM categories c 
                                  JOIN article_category ac ON c.id = ac.category_id 
                                  WHERE ac.article_id = ?");
            $stmt->execute([$article['id']]);
            $categories = $stmt->fetchAll();
            
            // Get tags
            $stmt = $pdo->prepare("SELECT t.name, t.slug 
                                  FROM tags t 
                                  JOIN article_tag at ON t.id = at.tag_id 
                                  WHERE at.article_id = ?");
            $stmt->execute([$article['id']]);
            $tags = $stmt->fetchAll();
            
            // Get related articles (articles in the same categories)
            if (!empty($categories)) {
                $category_ids = [];
                $stmt = $pdo->prepare("SELECT c.id 
                                      FROM categories c 
                                      JOIN article_category ac ON c.id = ac.category_id 
                                      WHERE ac.article_id = ?");
                $stmt->execute([$article['id']]);
                $category_results = $stmt->fetchAll();
                
                foreach ($category_results as $cat) {
                    $category_ids[] = $cat['id'];
                }
                
                if (!empty($category_ids)) {
                    $placeholders = implode(',', array_fill(0, count($category_ids), '?'));
                    
                    $stmt = $pdo->prepare("SELECT DISTINCT a.id, a.title, a.slug, a.excerpt, a.featured_image 
                                          FROM articles a 
                                          JOIN article_category ac ON a.id = ac.article_id 
                                          WHERE ac.category_id IN ($placeholders) 
                                          AND a.id != ? 
                                          AND a.status = 'published' 
                                          ORDER BY a.published_at DESC 
                                          LIMIT 3");
                    
                    $params = $category_ids;
                    $params[] = $article['id'];
                    $stmt->execute($params);
                    
                    $related_articles = $stmt->fetchAll();
                }
            }
        }
    } catch (PDOException $e) {
        error_log('Error fetching article: ' . $e->getMessage());
    }
}

// If article not found, show 404 page
if (!$article) {
    http_response_code(404);
    require_once(__DIR__ . '/404.php');
    exit;
}

// Format the article content (preserve HTML but escape potential XSS)
$content = $article['content'];

// Set article as page title
$page_title = $article['title'];
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
            <article>
                <header class="article-header">
                    <h1><?= h($article['title']) ?></h1>
                    
                    <div class="article-meta mb-4">
                        <span><i class="bi bi-person"></i> <?= h($article['author_name'] ?? 'Admin') ?></span>
                        <span class="mx-2">|</span>
                        <span><i class="bi bi-calendar3"></i> <?= date('M j, Y', strtotime($article['published_at'])) ?></span>
                        <span class="mx-2">|</span>
                        <span><i class="bi bi-eye"></i> <?= number_format($article['view_count']) ?> views</span>
                        
                        <?php if (!empty($categories)): ?>
                            <span class="mx-2">|</span>
                            <span>
                                <i class="bi bi-folder"></i>
                                <?php foreach ($categories as $i => $category): ?>
                                    <a href="<?= SITE_URL ?>/index.php?page=category&slug=<?= h($category['slug']) ?>" class="text-decoration-none">
                                        <?= h($category['name']) ?>
                                    </a><?= $i < count($categories) - 1 ? ', ' : '' ?>
                                <?php endforeach; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($article['featured_image'])): ?>
                        <img src="<?= h($article['featured_image']) ?>" alt="<?= h($article['title']) ?>" class="article-featured-image">
                    <?php endif; ?>
                </header>
                
                <div class="article-content">
                    <?= $content ?>
                </div>
                
                <?php if (!empty($tags)): ?>
                    <div class="article-tags mt-5">
                        <h5>Tags:</h5>
                        <div>
                            <?php foreach ($tags as $tag): ?>
                                <a href="<?= SITE_URL ?>/index.php?page=tag&slug=<?= h($tag['slug']) ?>" class="badge bg-secondary text-decoration-none me-2">
                                    <?= h($tag['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="article-share mt-5">
                    <h5>Share This Article:</h5>
                    <div class="d-flex">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL . '/index.php?page=article&slug=' . $article['slug']) ?>" class="btn btn-outline-primary me-2" target="_blank" rel="noopener">
                            <i class="bi bi-facebook"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode(SITE_URL . '/index.php?page=article&slug=' . $article['slug']) ?>&text=<?= urlencode($article['title']) ?>" class="btn btn-outline-info me-2" target="_blank" rel="noopener">
                            <i class="bi bi-twitter"></i> Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode(SITE_URL . '/index.php?page=article&slug=' . $article['slug']) ?>" class="btn btn-outline-secondary me-2" target="_blank" rel="noopener">
                            <i class="bi bi-linkedin"></i> LinkedIn
                        </a>
                    </div>
                </div>
            </article>
            
            <?php if (!empty($related_articles)): ?>
                <div class="related-articles mt-5">
                    <h3 class="mb-4">Related Articles</h3>
                    <div class="row">
                        <?php foreach ($related_articles as $related): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <?php if (!empty($related['featured_image'])): ?>
                                        <img src="<?= h($related['featured_image']) ?>" class="card-img-top" alt="<?= h($related['title']) ?>">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <a href="<?= SITE_URL ?>/index.php?page=article&slug=<?= h($related['slug']) ?>" class="text-decoration-none text-reset">
                                                <?= h($related['title']) ?>
                                            </a>
                                        </h5>
                                        
                                        <?php if (!empty($related['excerpt'])): ?>
                                            <p class="card-text"><?= h(substr($related['excerpt'], 0, 100)) ?>...</p>
                                        <?php endif; ?>
                                        
                                        <a href="<?= SITE_URL ?>/index.php?page=article&slug=<?= h($related['slug']) ?>" class="btn btn-outline-primary btn-sm">Read More</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-4">
            <div class="sidebar">
                <!-- Search Widget -->
                <div class="card mb-4">
                    <div class="card-header">Search</div>
                    <div class="card-body">
                        <form action="<?= SITE_URL ?>/index.php" method="get">
                            <input type="hidden" name="page" value="search">
                            <div class="input-group">
                                <input type="text" class="form-control" name="q" placeholder="Search for..." required>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Categories Widget -->
                <div class="card mb-4">
                    <div class="card-header">Categories</div>
                    <div class="card-body">
                        <div class="row">
                            <?php 
                            // Get all categories
                            try {
                                $stmt = $pdo->query("SELECT c.name, c.slug, COUNT(ac.article_id) as article_count 
                                                    FROM categories c
                                                    LEFT JOIN article_category ac ON c.id = ac.category_id
                                                    LEFT JOIN articles a ON ac.article_id = a.id AND a.status = 'published'
                                                    GROUP BY c.id
                                                    HAVING article_count > 0
                                                    ORDER BY c.name");
                                $all_categories = $stmt->fetchAll();
                                
                                // Split categories into columns
                                $category_count = count($all_categories);
                                $half_count = ceil($category_count / 2);
                                
                                // First column
                                echo '<div class="col-lg-6">';
                                echo '<ul class="list-unstyled mb-0">';
                                for ($i = 0; $i < min($half_count, $category_count); $i++) {
                                    $cat = $all_categories[$i];
                                    echo '<li><a href="' . SITE_URL . '/index.php?page=category&slug=' . h($cat['slug']) . '" class="text-decoration-none">' . h($cat['name']) . ' (' . $cat['article_count'] . ')</a></li>';
                                }
                                echo '</ul>';
                                echo '</div>';
                                
                                // Second column
                                echo '<div class="col-lg-6">';
                                echo '<ul class="list-unstyled mb-0">';
                                for ($i = $half_count; $i < $category_count; $i++) {
                                    $cat = $all_categories[$i];
                                    echo '<li><a href="' . SITE_URL . '/index.php?page=category&slug=' . h($cat['slug']) . '" class="text-decoration-none">' . h($cat['name']) . ' (' . $cat['article_count'] . ')</a></li>';
                                }
                                echo '</ul>';
                                echo '</div>';
                                
                            } catch (PDOException $e) {
                                error_log('Error fetching categories: ' . $e->getMessage());
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Posts Widget -->
                <div class="card mb-4">
                    <div class="card-header">Recent Posts</div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <?php foreach (array_slice($recent_articles, 0, 5) as $recent): ?>
                                <li class="mb-3">
                                    <a href="<?= SITE_URL ?>/index.php?page=article&slug=<?= h($recent['slug']) ?>" class="text-decoration-none">
                                        <?= h($recent['title']) ?>
                                    </a>
                                    <div class="small text-muted">
                                        <?= date('M j, Y', strtotime($recent['published_at'])) ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Tags Widget -->
                <div class="card">
                    <div class="card-header">Tags</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <?php 
                            // Get all tags
                            try {
                                $stmt = $pdo->query("SELECT t.name, t.slug, COUNT(at.article_id) as article_count 
                                                    FROM tags t
                                                    LEFT JOIN article_tag at ON t.id = at.tag_id
                                                    LEFT JOIN articles a ON at.article_id = a.id AND a.status = 'published'
                                                    GROUP BY t.id
                                                    HAVING article_count > 0
                                                    ORDER BY article_count DESC
                                                    LIMIT 20");
                                $all_tags = $stmt->fetchAll();
                                
                                foreach ($all_tags as $tag) {
                                    echo '<a href="' . SITE_URL . '/index.php?page=tag&slug=' . h($tag['slug']) . '" class="badge bg-secondary text-decoration-none me-2 mb-2">';
                                    echo h($tag['name']) . ' (' . $tag['article_count'] . ')';
                                    echo '</a>';
                                }
                                
                            } catch (PDOException $e) {
                                error_log('Error fetching tags: ' . $e->getMessage());
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>