<?php
/**
 * Public Website Main Entry Point
 * This file is the main entry point for the public-facing website
 *
 * File path: /public/index.php
 */

// Include the application configuration
require_once(__DIR__ . '/../app/config.php');

// Get settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    error_log('Error fetching settings: ' . $e->getMessage());
}

// Get page from URL or default to home
$page = $_GET['page'] ?? 'home';

// Sanitize page name to prevent directory traversal
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);

// Get featured articles for the homepage
$featured_articles = [];
if ($page === 'home') {
    try {
        $stmt = $pdo->query("SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.published_at, 
                             u.username as author_name 
                             FROM articles a 
                             LEFT JOIN users u ON a.author_id = u.id 
                             WHERE a.is_featured = 1 AND a.status = 'published' 
                             ORDER BY a.published_at DESC 
                             LIMIT 3");
        $featured_articles = $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Error fetching featured articles: ' . $e->getMessage());
    }
}

// Get recent articles
$recent_articles = [];
try {
    $limit = getenv('RECENT_ARTICLES_LIMIT') ?: 5;
    $stmt = $pdo->prepare("SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.published_at, 
                           u.username as author_name 
                           FROM articles a 
                           LEFT JOIN users u ON a.author_id = u.id 
                           WHERE a.status = 'published' 
                           ORDER BY a.published_at DESC 
                           LIMIT ?");
    $stmt->bindParam(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    $recent_articles = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Error fetching recent articles: ' . $e->getMessage());
}

// Get navigation menu
$menu_items = [];
try {
    $stmt = $pdo->query("SELECT mi.* 
                         FROM menu_items mi 
                         JOIN menus m ON mi.menu_id = m.id 
                         WHERE m.location = 'primary' 
                         ORDER BY mi.order ASC");
    $menu_items = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Error fetching menu: ' . $e->getMessage());
}

// Get social links
$social_links = [];
try {
    $stmt = $pdo->query("SELECT * FROM social_links WHERE is_active = 1 ORDER BY `order` ASC");
    $social_links = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log('Error fetching social links: ' . $e->getMessage());
}

// Include the header
require_once(__DIR__ . '/templates/header.php');

// Include the page content
$page_file = __DIR__ . '/templates/pages/' . $page . '.php';
if (file_exists($page_file)) {
    require_once($page_file);
} else {
    // Page not found, include 404 template
    http_response_code(404);
    require_once(__DIR__ . '/templates/pages/404.php');
}

// Include the footer
require_once(__DIR__ . '/templates/footer.php');