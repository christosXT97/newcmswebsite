<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Diagnostics</h1>";

// System information
echo "<h2>System Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p>Server Name: " . $_SERVER['SERVER_NAME'] . "</p>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Current Directory: " . getcwd() . "</p>";

// Try different connection methods
echo "<h2>Testing Connection Methods</h2>";

// Connection parameters
$username = '23p_3351';
$password = 'IJ*gNVzjp9zI(oSh';
$database = 'cms_database';

// Method 1: mysqli using hostname
try_mysqli_connect('localhost', $username, $password, $database, 'Standard localhost');
try_mysqli_connect('127.0.0.1', $username, $password, $database, 'Localhost IP');
try_mysqli_connect('db', $username, $password, $database, 'db hostname');
try_mysqli_connect('mysql', $username, $password, $database, 'mysql hostname');
try_mysqli_connect('database', $username, $password, $database, 'database hostname');
try_mysqli_connect($_SERVER['SERVER_NAME'], $username, $password, $database, 'SERVER_NAME');

// Method 2: mysqli using socket
try_mysqli_socket($username, $password, $database);

// Method 3: PDO connections
try_pdo_connect('mysql:host=localhost;dbname=' . $database, $username, $password, 'PDO localhost');
try_pdo_connect('mysql:host=127.0.0.1;dbname=' . $database, $username, $password, 'PDO localhost IP');
try_pdo_connect('mysql:unix_socket=/var/run/mysqld/mysqld.sock;dbname=' . $database, $username, $password, 'PDO socket path 1');
try_pdo_connect('mysql:unix_socket=/tmp/mysql.sock;dbname=' . $database, $username, $password, 'PDO socket path 2');

// Method 4: Check for MySQL configuration file
echo "<h2>MySQL Configuration</h2>";
$mysql_config_files = [
    '/etc/my.cnf',
    '/etc/mysql/my.cnf',
    '~/.my.cnf'
];

foreach ($mysql_config_files as $config_file) {
    echo "<p>Checking for MySQL config file: $config_file - ";
    if (file_exists($config_file)) {
        echo "<span style='color:green'>File exists</span></p>";
    } else {
        echo "<span style='color:red'>Not found</span></p>";
    }
}

// Check local environment config
echo "<h2>Local Environment Check</h2>";
if (function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
    echo "<pre>";
    echo shell_exec('hostname');
    echo "</pre>";
}

// Helper functions
function try_mysqli_connect($host, $username, $password, $database, $method_name) {
    echo "<h3>Testing: $method_name</h3>";
    echo "<p>Host: $host, Database: $database, Username: $username</p>";
    
    $conn = @mysqli_connect($host, $username, $password, $database);
    
    if ($conn) {
        echo "<p style='color:green'>✓ SUCCESS: Connected via $method_name!</p>";
        echo "<p>MySQL Server Version: " . mysqli_get_server_info($conn) . "</p>";
        
        echo "<h4>Available Databases:</h4>";
        $result = mysqli_query($conn, "SHOW DATABASES");
        echo "<ul>";
        while ($row = mysqli_fetch_row($result)) {
            echo "<li>" . htmlspecialchars($row[0]) . "</li>";
        }
        echo "</ul>";
        
        mysqli_close($conn);
    } else {
        echo "<p style='color:red'>✗ FAILED: " . mysqli_connect_error() . "</p>";
    }
    echo "<hr>";
}

function try_mysqli_socket($username, $password, $database) {
    echo "<h3>Testing: Unix Socket Connection</h3>";
    echo "<p>Username: $username, Database: $database</p>";
    
    // Try to find MySQL socket
    $possible_socket_locations = [
        '/var/run/mysqld/mysqld.sock',
        '/var/lib/mysql/mysql.sock',
        '/tmp/mysql.sock',
        '/tmp/mysql.sock'
    ];
    
    foreach ($possible_socket_locations as $socket) {
        echo "<p>Testing socket at: $socket - ";
        if (file_exists($socket)) {
            echo "<span style='color:green'>Socket exists</span></p>";
            $conn = @mysqli_connect(null, $username, $password, $database, null, $socket);
            
            if ($conn) {
                echo "<p style='color:green'>✓ SUCCESS: Connected via socket: $socket!</p>";
                mysqli_close($conn);
            } else {
                echo "<p style='color:red'>✗ FAILED: " . mysqli_connect_error() . "</p>";
            }
        } else {
            echo "<span style='color:red'>Socket not found</span></p>";
        }
    }
    echo "<hr>";
}

function try_pdo_connect($dsn, $username, $password, $method_name) {
    echo "<h3>Testing: $method_name</h3>";
    echo "<p>DSN: $dsn, Username: $username</p>";
    
    try {
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color:green'>✓ SUCCESS: Connected via $method_name!</p>";
        echo "<p>PDO Driver Name: " . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . "</p>";
        
        echo "<h4>PDO Connection Attributes:</h4>";
        echo "<ul>";
        echo "<li>Server Version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "</li>";
        echo "<li>Server Info: " . $pdo->getAttribute(PDO::ATTR_SERVER_INFO) . "</li>";
        echo "<li>Connection Status: " . $pdo->getAttribute(PDO::ATTR_CONNECTION_STATUS) . "</li>";
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p style='color:red'>✗ FAILED: " . $e->getMessage() . "</p>";
    }
    echo "<hr>";
}
?>

<h2>Available PDO Drivers</h2>
<ul>
<?php foreach(PDO::getAvailableDrivers() as $driver): ?>
    <li><?= htmlspecialchars($driver) ?></li>
<?php endforeach; ?>
</ul>

<h2>Next Steps</h2>
<p>After running this diagnostic, look for the green "SUCCESS" messages to see which connection method works. Then update your db_connect.php file to use that method.</p>