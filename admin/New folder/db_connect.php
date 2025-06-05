<?php
$host = 'localhost';
$db   = 'ruiru_tvc'; // Replace with your actual DB name
$user = 'root';      // Default user for XAMPP
$pass = '';          // Default password is usually empty in XAMPP

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Enable exceptions for PDO
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
?>
