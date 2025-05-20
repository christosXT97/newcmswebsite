<?php
// Include the configuration
require_once(__DIR__ . '/../config.php');

// Check if user is logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Get the page parameter
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Sanitize parameters
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page);
$action = preg_replace('/[^a-zA-Z0-9_-]/', '', $action);

// Set page title based on current page
$page_title = ucfirst($page);

// Make sure views directory exists
$views_dir = __DIR__ . '/views';
if (!file_exists($views_dir)) {
    mkdir($views_dir, 0755, true);
}

// Make sure the views/page directory exists
$page_dir = $views_dir . '/' . $page;
if (!file_exists($page_dir)) {
    mkdir($page_dir, 0755, true);
}

// Check if the action file exists, otherwise create a placeholder
$action_file = $page_dir . '/' . $action . '.php';
if (!file_exists($action_file)) {
    $placeholder = <<<PHP
<?php
// Placeholder for {$page}/{$action}
echo "<h1>{$page_title}</h1>";
echo "<p>This is a placeholder for the {$page}/{$action} view. Add your content here.</p>";
PHP;
    file_put_contents($action_file, $placeholder);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background-color: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }
        
        .content {
            flex: 1;
            padding: 20px;
        }
        
        .sidebar .nav-link {
            color: #333;
        }
        
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h3 class="mb-3">Admin Panel</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>" href="index.php?page=dashboard">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page === 'articles' ? 'active' : '' ?>" href="index.php?page=articles">
                    <i class="bi bi-file-earmark-text"></i> Articles
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page === 'categories' ? 'active' : '' ?>" href="index.php?page=categories">
                    <i class="bi bi-folder"></i> Categories
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $page === 'settings' ? 'active' : '' ?>" href="index.php?page=settings">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>

    <!-- Content -->
    <div class="content">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
                <?= $_SESSION['message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <?php include($action_file); ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>