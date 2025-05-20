<?php
// Get article ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// If no ID provided, redirect to articles list
if (empty($id)) {
    redirect('index.php?page=articles');
}

// Fetch article from database
try {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    
    // If article not found, redirect to articles list
    if (!$article) {
        $_SESSION['message'] = 'Article not found';
        $_SESSION['message_type'] = 'danger';
        redirect('index.php?page=articles');
    }
} catch (Exception $e) {
    $_SESSION['message'] = 'Error: ' . $e->getMessage();
    $_SESSION['message_type'] = 'danger';
    redirect('index.php?page=articles');
}

// Get image URL (check both fields)
$image_url = null;
if (!empty($article['image_url'])) {
    $image_url = $article['image_url'];
} elseif (!empty($article['featured_image'])) {
    $image_url = $article['featured_image'];
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Edit Article</h1>
        <a href="?page=articles" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Articles
        </a>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?? 'info' ?> alert-dismissible fade show" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="controllers/update_article.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="content1" class="form-label">Content (Paragraph 1)</label>
                    <textarea class="form-control" id="content1" name="content1" rows="5" required><?= htmlspecialchars($article['content1'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="content2" class="form-label">Content (Paragraph 2)</label>
                    <textarea class="form-control" id="content2" name="content2" rows="5"><?= htmlspecialchars($article['content2'] ?? '') ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                        <option value="archived" <?= $article['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Featured Image</label>
                    
                    <?php if (!empty($image_url)): ?>
                        <div class="mb-3">
                            <img src="../<?= htmlspecialchars($image_url) ?>" alt="Current Image" class="img-fluid" style="max-width: 300px; max-height: 200px;">
                            <p class="text-muted mt-2">Current image. Upload a new one to replace it.</p>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Update Article</button>
            </form>
        </div>
    </div>
</div>