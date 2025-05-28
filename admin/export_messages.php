<?php
session_start();

require_once 'auth_check.php';
require_once '../config/database.php';

// Connect to database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : null;
$statusFilter = $status ? "WHERE status = :status" : "";

// Get messages
$sql = "SELECT * FROM contact_messages $statusFilter ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
if ($status) {
    $stmt->bindParam(':status', $status);
}
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Set headers for CSV download
$filename = 'contact_messages_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Create file pointer
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, [
    'ID',
    'Name',
    'Email',
    'Phone',
    'Subject',
    'Message',
    'Status',
    'IP Address',
    'Created Date'
]);

// Add data rows
foreach ($messages as $message) {
    fputcsv($output, [
        $message['id'],
        $message['name'],
        $message['email'],
        $message['phone'] ?? '',
        $message['subject'],
        $message['message'],
        $message['status'],
        $message['ip_address'] ?? '',
        $message['created_at']
    ]);
}

fclose($output);
exit;
?>
