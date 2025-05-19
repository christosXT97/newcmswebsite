<?php
// update_settings.php - Handle updating company settings
session_start();

// Database configuration
$db_host = 'localhost';
$db_name = 'newcmswebsite'; // Changed from cms_database to match your database name
$db_user = 'root';
$db_pass = '';

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

// Create database connection
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $_SESSION['error'] = "Database connection failed: " . $e->getMessage();
    header("Location: index.php");
    exit;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $logo = $_POST['logo'] ?? 'LOGO';
    $company_name = $_POST['company_name'] ?? 'Your Company';
    $company_desc = $_POST['company_desc'] ?? '';
    
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // Check if settings exist first
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE setting_key = ?");
        
        // Update or insert logo_text
        $checkStmt->execute(['logo_text']);
        if ($checkStmt->fetchColumn() > 0) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'logo_text'");
            $stmt->execute([$logo]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('logo_text', ?)");
            $stmt->execute([$logo]);
        }
        
        // Update or insert company_name
        $checkStmt->execute(['company_name']);
        if ($checkStmt->fetchColumn() > 0) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'company_name'");
            $stmt->execute([$company_name]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('company_name', ?)");
            $stmt->execute([$company_name]);
        }
        
        // Update or insert company_desc
        $checkStmt->execute(['company_desc']);
        if ($checkStmt->fetchColumn() > 0) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'company_desc'");
            $stmt->execute([$company_desc]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('company_desc', ?)");
            $stmt->execute([$company_desc]);
        }
        
        // Commit transaction
        $pdo->commit();
        
        $_SESSION['success'] = "Company information updated successfully!";
    } catch(PDOException $e) {
        // Rollback on error
        $pdo->rollBack();
        $_SESSION['error'] = "Error updating company information: " . $e->getMessage();
    }
    
    header("Location: index.php");
    exit;
}

// Redirect if accessed directly without POST
header("Location: index.php");
exit;
?>