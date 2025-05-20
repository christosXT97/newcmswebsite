<?php
// Database credentials
$db_host = 'localhost';
$db_name = 'cms_database';
$db_user = 'root';
$db_pass = ''; // Use your password if you have one

// Test connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h1>Database Connection Successful!</h1>";
    
    // Test query
    $stmt = $conn->query("SELECT * FROM settings LIMIT 3");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Sample Data:</h2>";
    echo "<pre>";
    print_r($rows);
    echo "</pre>";
    
} catch(PDOException $e) {
    echo "<h1>Connection failed:</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    
    echo "<h2>Debugging information:</h2>";
    echo "<ul>";
    echo "<li>Host: $db_host</li>";
    echo "<li>Database: $db_name</li>";
    echo "<li>Username: $db_user</li>";
    echo "<li>Password: " . ($db_pass ? "(password set)" : "(empty password)") . "</li>";
    echo "</ul>";
}
?>