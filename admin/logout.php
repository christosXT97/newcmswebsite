<?php
// Include the database connection
require_once(__DIR__ . '/../config.php');

// Clear all session variables
$_SESSION = [];

// If a session cookie is used, clear it
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;