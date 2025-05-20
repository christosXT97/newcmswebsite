<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Add New Article</h1>
        <a href="?page=articles" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Articles
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="controllers/save_article.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="content1" class="form-label">Content (Paragraph 1)</label>
                    <textarea class="form-control" id="content1" name="content1" rows="5" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="content2" class="form-label">Content (Paragraph 2)</label>
                    <textarea class="form-control" id="content2" name="content2" rows="5"></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Featured Image</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Save Article</button>
            </form>
        </div>
    </div>
</div>