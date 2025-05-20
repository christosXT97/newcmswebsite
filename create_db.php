<?php
// create_db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use localhost as the most likely candidate
$host = 'localhost';
$username = '23p_3351';
$password = 'IJ*gNVzjp9zI(oSh';
$dbname = 'cms_database';

try {
    // Connect without specifying a database
    $conn = new mysqli($host, $username, $password);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected to MySQL server successfully<br>";
    
    // Check if database exists
    $result = $conn->query("SHOW DATABASES LIKE '$dbname'");
    
    if ($result->num_rows > 0) {
        echo "Database '$dbname' already exists<br>";
    } else {
        // Create database
        if ($conn->query("CREATE DATABASE $dbname")) {
            echo "Database created successfully<br>";
        } else {
            echo "Error creating database: " . $conn->error . "<br>";
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
?>