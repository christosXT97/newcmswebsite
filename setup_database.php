<?php
// Script to set up your database on the school server
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuration
$config = [
    'db_host' => 'localhost', // Try localhost instead of the IP
    'db_username' => '23p_3351',
    'db_password' => 'IJ*gNVzjp9zI(oSh',
    'db_name' => 'cms_database',
    'sql_file' => __DIR__ . '/database.sql',
];

// Connect to MySQL server
echo "Connecting to MySQL server {$config['db_host']}...<br>";
try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']}",
        $config['db_username'],
        $config['db_password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!<br>";
    
    // Create database if it doesn't exist
    echo "Creating database {$config['db_name']}...<br>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}`");
    
    // Select the database
    echo "Selecting database {$config['db_name']}...<br>";
    $pdo->exec("USE `{$config['db_name']}`");
    
    // Check if SQL file exists
    if (!file_exists($config['sql_file'])) {
        die("Error: SQL file not found at {$config['sql_file']}<br>");
    }
    
    // Read SQL file
    echo "Reading SQL file {$config['sql_file']}...<br>";
    $sql = file_get_contents($config['sql_file']);
    
    // Execute SQL by splitting it into individual statements
    echo "Executing SQL...<br>";
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                echo ".";
            } catch (PDOException $e) {
                echo "<br>Error executing statement: " . $e->getMessage() . "<br>";
                echo "Statement: " . $statement . "<br>";
            }
        }
    }
    
    echo "<br>Database setup complete!<br>";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "<br>");
}