<?php
// Fetch all articles from database
try {
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY id DESC");
    $articles = $stmt->fetchAll();
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Error fetching articles: ' . $e->getMessage() . '</div>';
    $articles = [];
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Articles</h1>
        <a href="?page=articles&action=new" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add New Article
        </a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($articles)): ?>
                <p class="text-center py-3">No articles found. Click the "Add New Article" button to create your first article.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover border-bottom">
                        <thead>
                            <tr>
                                <th class="px-4">ID</th>
                                <th style="width: 40%;">Title</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($articles as $article): ?>
                                <tr>
                                    <td class="px-4"><?= $article['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php
                                            // Check all possible image field names
                                            $image_url = null;
                                            if (!empty($article['image_url'])) {
                                                $image_url = $article['image_url'];
                                            } elseif (!empty($article['featured_image'])) {
                                                $image_url = $article['featured_image'];
                                            }
                                            ?>
                                            
                                            <?php if (!empty($image_url)): ?>
                                                <div class="me-3">
                                                    <img src="../<?= h($image_url) ?>" alt="Article Image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <strong><?= h($article['title']) ?></strong>
                                                <?php if ($article['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark ms-2">Featured</span>
                                                <?php endif; ?>
                                                <div class="small text-muted mt-1">
                                                    <?php
                                                    // Show excerpt or first part of content
                                                    if (!empty($article['excerpt'])) {
                                                        echo h(substr($article['excerpt'], 0, 70)) . '...';
                                                    } elseif (!empty($article['content1'])) {
                                                        echo h(substr($article['content1'], 0, 70)) . '...';
                                                    } elseif (!empty($article['content'])) {
                                                        echo h(substr($article['content'], 0, 70)) . '...';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($article['status'] === 'published'): ?>
                                            <span class="badge bg-success">Published</span>
                                        <?php elseif ($article['status'] === 'draft'): ?>
                                            <span class="badge bg-warning text-dark">Draft</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Archived</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('Y-m-d H:i:s', strtotime($article['created_at'])) ?></td>
                                    <td class="text-end px-4">
                                        <a href="../index.php?article=<?= h($article['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="?page=articles&action=edit&id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete(<?= $article['id'] ?>, '<?= h(addslashes($article['title'])) ?>')" title="Delete">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the article "<span id="articleTitle"></span>"?</p>
                <p class="text-danger">This action cannot be undone!</p>
            </div>
            <div class="modal-footer">
                <form action="controllers/delete_article.php" method="post">
                    <input type="hidden" name="article_id" id="articleId">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(id, title) {
        // Set values in the modal
        document.getElementById('articleId').value = id;
        document.getElementById('articleTitle').textContent = title;
        
        // Show the modal
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
</script>