<?php
// PDO Test to find the correct connection parameters
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get MySQL connection details from your hosting provider
$servername = "localhost"; // Try localhost first
$username = "23p_3351";
$password = "IJ*gNVzjp9zI(oSh";

echo "<h1>PDO Connection Test</h1>";

// Try to connect without specifying a database first
try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Connected successfully to MySQL server!</p>";
    
    // Display available databases
    $stmt = $conn->query("SHOW DATABASES");
    echo "<h2>Available Databases:</h2><ul>";
    while ($row = $stmt->fetch()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
    
    // Check if our database exists
    $databaseExists = false;
    $stmt = $conn->query("SHOW DATABASES");
    while ($row = $stmt->fetch()) {
        if ($row[0] === 'cms_database') {
            $databaseExists = true;
            break;
        }
    }
    
    if (!$databaseExists) {
        echo "<p style='color:orange'>The database 'cms_database' doesn't exist. You may need to create it first.</p>";
        
        // Try to create database
        try {
            $conn->exec("CREATE DATABASE cms_database");
            echo "<p style='color:green'>Database 'cms_database' created successfully!</p>";
        } catch(PDOException $e) {
            echo "<p style='color:red'>Error creating database: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:green'>The database 'cms_database' exists!</p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color:red'>Connection failed: " . $e->getMessage() . "</p>";
    
    // Try with 127.0.0.1
    try {
        $servername = "127.0.0.1";
        $conn = new PDO("mysql:host=$servername", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color:green'>Connected successfully using 127.0.0.1!</p>";
    } catch(PDOException $e2) {
        echo "<p style='color:red'>Connection with 127.0.0.1 also failed: " . $e2->getMessage() . "</p>";
    }
}
?>