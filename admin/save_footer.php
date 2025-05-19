<?php
require_once('../config.php');

$logo = $_POST['logo'] ?? '';
$footer = $_POST['footer'] ?? '';

$stmt = $pdo->prepare("UPDATE settings SET logo_text = ?, footer_text = ? WHERE id = 1");
$stmt->execute([$logo, $footer]);

header("Location: index.php");
exit;
