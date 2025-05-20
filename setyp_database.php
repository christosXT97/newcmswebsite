<?php
// Script to set up your database on the school server

// Configuration
$config = [
    'db_host' => '194.197.245.5',
    'db_username' => '23p_3351',
    'db_password' => 'IJ*gNVzjp9zI(oSh',
    'db_name' => 'cms_database',
    'sql_file' => __DIR__ . '/database.sql',
];

// Connect to MySQL server
echo "Connecting to MySQL server {$config['db_host']}...\n";
try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']}",
        $config['db_username'],
        $config['db_password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    echo "Creating database {$config['db_name']}...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['db_name']}`");
    
    // Select the database
    echo "Selecting database {$config['db_name']}...\n";
    $pdo->exec("USE `{$config['db_name']}`");
    
    // Read SQL file
    echo "Reading SQL file {$config['sql_file']}...\n";
    $sql = file_get_contents($config['sql_file']);
    
    // Execute SQL by splitting it into individual statements
    echo "Executing SQL...\n";
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            $pdo->exec($statement);
            echo ".";
        }
    }
    
    echo "\nDatabase setup complete!\n";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
}