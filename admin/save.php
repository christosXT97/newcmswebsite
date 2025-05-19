<?php
require_once('../config.php');

$title = $_POST['title'];
$content1 = $_POST['content1'];
$content2 = $_POST['content2'];
$image_url = '';

if (!empty($_FILES['image']['name'])) {
    $fileName = time() . '_' . basename($_FILES['image']['name']);
    $target = $upload_dir . $fileName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], '../' . $target)) {
        $image_url = $site_url . $target;
    }
}

$stmt = $pdo->prepare("INSERT INTO articles (title, content1, content2, image_url) VALUES (?, ?, ?, ?)");
$stmt->execute([$title, $content1, $content2, $image_url]);

header("Location: index.php");
exit;
?>
