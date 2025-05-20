<div class="container-fluid">
    <h1>Dashboard</h1>
    <p>Welcome to the admin dashboard.</p>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Articles</h5>
                    <?php
                    try {
                        $count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
                        echo "<p class='card-text display-4'>$count</p>";
                    } catch (Exception $e) {
                        echo "<p class='card-text text-danger'>Error: " . $e->getMessage() . "</p>";
                    }
                    ?>
                    <a href="?page=articles" class="btn btn-primary">Manage Articles</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Categories</h5>
                    <?php
                    try {
                        $count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
                        echo "<p class='card-text display-4'>$count</p>";
                    } catch (Exception $e) {
                        echo "<p class='card-text text-danger'>Error: " . $e->getMessage() . "</p>";
                    }
                    ?>
                    <a href="?page=categories" class="btn btn-primary">Manage Categories</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Users</h5>
                    <?php
                    try {
                        $count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
                        echo "<p class='card-text display-4'>$count</p>";
                    } catch (Exception $e) {
                        echo "<p class='card-text text-danger'>Error: " . $e->getMessage() . "</p>";
                    }
                    ?>
                    <a href="#" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Articles</h5>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 5");
                        $recent_articles = $stmt->fetchAll();
                        
                        if (count($recent_articles) > 0) {
                            echo '<ul class="list-group list-group-flush">';
                            foreach ($recent_articles as $article) {
                                echo '<li class="list-group-item d-flex justify-content-between align-items-center">';
                                echo $article['title'];
                                echo '<a href="?page=articles&action=edit&id=' . $article['id'] . '" class="btn btn-sm btn-outline-primary">Edit</a>';
                                echo '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p class="text-center">No articles found</p>';
                        }
                    } catch (Exception $e) {
                        echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="?page=articles&action=new" class="list-group-item list-group-item-action">
                            <i class="bi bi-plus-circle me-2"></i> Create New Article
                        </a>
                        <a href="?page=categories&action=new" class="list-group-item list-group-item-action">
                            <i class="bi bi-folder-plus me-2"></i> Create New Category
                        </a>
                        <a href="?page=settings" class="list-group-item list-group-item-action">
                            <i class="bi bi-gear me-2"></i> Update Site Settings
                        </a>
                        <a href="../index.php" target="_blank" class="list-group-item list-group-item-action">
                            <i class="bi bi-eye me-2"></i> View Website
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>