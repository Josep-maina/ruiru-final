<?php
// Database configuration
$host = '127.0.0.1';          // or 'localhost'
$port = 3306;                 // MySQL default port
$dbname = 'ruiru_tvc';    // your database name
$username = 'root';           // default XAMPP user
$password = '';               // default XAMPP password (empty)

// DSN (Data Source Name)
$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $username, $password);
    // Enable exceptions for errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Connection successful (optional log)
    // echo "✅ Database connected successfully.";
} catch (PDOException $e) {
    // Connection failed
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
