<?php
// logout.php - Handle logout
session_start();
session_destroy();
header("Location: login.php");
exit;
?>