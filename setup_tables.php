<?php
// Script to set up tables in your existing cms_database on the school server
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Use your exact school server database credentials
$config = [
    'db_host' => 'localhost', // Try with 'localhost' since your school server might require this
    'db_username' => '23p_3351',
    'db_password' => 'IJ*gNVzjp9zI(oSh',
    'db_name' => 'cms_database' // Your existing database
];

echo "<h1>Setting up tables for cms_database</h1>";

try {
    // Connect to the existing database
    echo "Connecting to database {$config['db_name']} on {$config['db_host']}...<br>";
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']}",
        $config['db_username'],
        $config['db_password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Connected successfully!<br>";
    
    // Get the SQL content
    $sql = file_get_contents('school_db_setup.sql'); // Save the SQL above to this file
    
    if (!$sql) {
        die("Could not read SQL file!<br>");
    }
    
    // Execute SQL by splitting it into individual statements
    echo "Executing SQL statements...<br>";
    $statements = explode(';', $sql);
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo "<span style='color:green'>✓</span> ";
                $success_count++;
            } catch (PDOException $e) {
                echo "<span style='color:red'>✗</span> ";
                $error_count++;
                echo "<div style='color:red; margin-left:20px;'>" . $e->getMessage() . "</div>";
            }
        }
    }
    
    echo "<hr><h2>Setup Complete</h2>";
    echo "<p>Successfully executed $success_count statements.</p>";
    if ($error_count > 0) {
        echo "<p>$error_count statements had errors (most likely because tables already exist).</p>";
    }
    
    // Check if tables exist now
    echo "<h2>Verifying Tables</h2>";
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>Table: $table</li>";
    }
    echo "</ul>";
    
    echo "<p>Setup process completed. You can now <a href='index.php'>go to your website</a>.</p>";
    
} catch (PDOException $e) {
    die("<div style='color:red; font-weight:bold;'>Database error: " . $e->getMessage() . "</div>");
}