<?php
require_once 'auth_check.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check required parameters
if (!isset($_POST['message_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$messageId = (int)$_POST['message_id'];
$action = $_POST['action'];

// Database configuration
$host = 'localhost';
$dbname = 'ruiru_tvc';
$username = 'root';
$password = '';

// Connect to database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Update message status
try {
    if ($action === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
        $stmt->execute([$messageId]);
    } elseif ($action === 'mark_replied') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
        $stmt->execute([$messageId]);
    } elseif ($action === 'archive') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'archived' WHERE id = ?");
        $stmt->execute([$messageId]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$messageId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }
    
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
