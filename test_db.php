<?php
// Database connection test file
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try different connection methods
echo "<h1>Database Connection Test</h1>";

// Method 1: Using localhost
try {
    echo "<h2>Testing with localhost:</h2>";
    $pdo1 = new PDO("mysql:host=localhost;dbname=cms_database", "23p_3351", "IJ*gNVzjp9zI(oSh");
    $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Connection successful!</p>";
    
    // Test a query
    $stmt = $pdo1->query("SHOW TABLES");
    echo "<p>Tables in database:</p><ul>";
    while ($row = $stmt->fetch()) {
        echo "<li>" . htmlspecialchars($row[0]) . "</li>";
    }
    echo "</ul>";
} catch(PDOException $e) {
    echo "<p style='color:red'>Connection failed: " . $e->getMessage() . "</p>";
}

// Method 2: Using 127.0.0.1
try {
    echo "<h2>Testing with 127.0.0.1:</h2>";
    $pdo2 = new PDO("mysql:host=127.0.0.1;dbname=cms_database", "23p_3351", "IJ*gNVzjp9zI(oSh");
    $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Connection successful!</p>";
} catch(PDOException $e) {
    echo "<p style='color:red'>Connection failed: " . $e->getMessage() . "</p>";
}

// Method 3: Using server IP
try {
    echo "<h2>Testing with server IP:</h2>";
    $pdo3 = new PDO("mysql:host=194.197.245.5;dbname=cms_database", "23p_3351", "IJ*gNVzjp9zI(oSh");
    $pdo3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color:green'>Connection successful!</p>";
} catch(PDOException $e) {
    echo "<p style='color:red'>Connection failed: " . $e->getMessage() . "</p>";
}

// Display phpinfo for additional debugging
echo "<h2>PHP Information:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>PDO Drivers: </p><ul>";
foreach(PDO::getAvailableDrivers() as $driver) {
    echo "<li>" . htmlspecialchars($driver) . "</li>";
}
echo "</ul>";