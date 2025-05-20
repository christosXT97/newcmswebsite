<?php
// Include the database connection
require_once('db_connect.php');

// Check table structure
try {
    $result = $pdo->query("DESCRIBE articles");
    echo "<h2>Articles Table Structure</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch()) {
        echo "<tr>";
        foreach ($row as $key => $value) {
            if (!is_numeric($key)) {
                echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>