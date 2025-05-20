<?php
// Database connection file using mysqli instead of PDO
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to users
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/db_error.log');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$db_host = 'localhost'; // Use localhost which is most common
$db_user = '23p_3351';
$db_pass = 'IJ*gNVzjp9zI(oSh';
$db_name = 'cms_database';

// Establish connection
$mysqli = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check for connection errors
if (mysqli_connect_errno()) {
    // Log the error
    error_log("Failed to connect to MySQL: " . mysqli_connect_error());
    
    // Die with a user-friendly message
    die("We're experiencing database connectivity issues. Please try again later.");
}

// Set charset to utf8mb4
mysqli_set_charset($mysqli, 'utf8mb4');

// Helper function to safely output HTML
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Helper function for redirects
function redirect($url) {
    header("Location: $url");
    exit;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Set flash message
function set_flash_message($type, $message) {
    $_SESSION['flash_messages'][$type][] = $message;
}

// Get flash messages
function get_flash_messages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

// Safe query execution (with prepared statements)
function db_query($sql, $params = []) {
    global $mysqli;
    
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        error_log("Error preparing SQL statement: " . $mysqli->error);
        return false;
    }
    
    if (!empty($params)) {
        // Create type string for bind_param
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        
        // Bind parameters
        $stmt->bind_param($types, ...$params);
    }
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $stmt->close();
        return $result;
    } else {
        error_log("Error executing SQL statement: " . $stmt->error);
        $stmt->close();
        return false;
    }
}

// Function to fetch all rows
function db_fetch_all($sql, $params = []) {
    $result = db_query($sql, $params);
    
    if ($result === false) {
        return [];
    }
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to fetch a single row
function db_fetch_one($sql, $params = []) {
    $result = db_query($sql, $params);
    
    if ($result === false) {
        return null;
    }
    
    return $result->fetch_assoc();
}

// Function to run an INSERT/UPDATE/DELETE query
function db_execute($sql, $params = []) {
    global $mysqli;
    
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        error_log("Error preparing SQL statement: " . $mysqli->error);
        return false;
    }
    
    if (!empty($params)) {
        // Create type string for bind_param
        $types = '';
        foreach ($params as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        
        // Bind parameters
        $stmt->bind_param($types, ...$params);
    }
    
    if ($stmt->execute()) {
        $affected_rows = $stmt->affected_rows;
        $insert_id = $stmt->insert_id;
        $stmt->close();
        return ['affected_rows' => $affected_rows, 'insert_id' => $insert_id];
    } else {
        error_log("Error executing SQL statement: " . $stmt->error);
        $stmt->close();
        return false;
    }
}