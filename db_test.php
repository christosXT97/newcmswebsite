<?php
// Simple database test script
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Database credentials
$db_user = '23p_3351';
$db_pass = 'IJ*gNVzjp9zI(oSh';
$db_name = 'cms_database';

// Method 1: Using PDO
echo "<h2>Testing PDO Connection</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>PDO connection successful!</p>";
    
    // Try a simple query
    $stmt = $pdo->query("SELECT 1 AS test");
    $result = $stmt->fetch();
    echo "<p>Test query result: " . $result['test'] . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red'>PDO connection failed: " . $e->getMessage() . "</p>";
}

// Method 2: Using MySQLi
echo "<h2>Testing MySQLi Connection</h2>";
$mysqli = @mysqli_connect('localhost', $db_user, $db_pass, $db_name);
if ($mysqli) {
    echo "<p style='color:green'>MySQLi connection successful!</p>";
    
    // Try a simple query
    $result = mysqli_query($mysqli, "SELECT 1 AS test");
    $row = mysqli_fetch_assoc($result);
    echo "<p>Test query result: " . $row['test'] . "</p>";
    
    mysqli_close($mysqli);
} else {
    echo "<p style='color:red'>MySQLi connection failed: " . mysqli_connect_error() . "</p>";
}

// Show MySQL configuration variables
echo "<h2>PHP MySQL Configuration</h2>";
if (function_exists('ini_get')) {
    echo "<p>mysql.default_host: " . ini_get('mysql.default_host') . "</p>";
    echo "<p>mysqli.default_host: " . ini_get('mysqli.default_host') . "</p>";
    echo "<p>pdo_mysql.default_socket: " . ini_get('pdo_mysql.default_socket') . "</p>";
    echo "<p>mysqli.default_socket: " . ini_get('mysqli.default_socket') . "</p>";
}

// Show hostname information
echo "<h2>Server Information</h2>";
echo "<p>SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p>PHP Version: " . phpversion() . "</p>";
?>