<?php
require_once('../config.php');

$id = $_POST['id'];
$title = $_POST['title'];
$content1 = $_POST['content1'];
$content2 = $_POST['content2'];
$image_url = '';

// Get existing image
$stmt = $pdo->prepare("SELECT image_url FROM articles WHERE id = ?");
$stmt->execute([$id]);
$existing = $stmt->fetch();

if (!empty($_FILES['image']['name'])) {
    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $target = $upload_dir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $target)) {
        $image_url = $site_url . $target;
    }
} else {
    $image_url = $existing['image_url']; // keep existing if no new upload
}

$update = $pdo->prepare("UPDATE articles SET title = ?, content1 = ?, content2 = ?, image_url = ? WHERE id = ?");
$update->execute([$title, $content1, $content2, $image_url, $id]);

header("Location: index.php");
exit;
