<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['id'])) {
    echo json_encode(['success' => false, 'message' => 'Application ID required']);
    exit;
}

$application_id = (int)$input['id'];

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get application details before deletion for logging
    $check_sql = "SELECT id, full_name, email, course_of_interest FROM applications WHERE id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$application_id]);
    $application = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        echo json_encode(['success' => false, 'message' => 'Application not found']);
        exit;
    }
    
    // Delete the application
    $sql = "DELETE FROM applications WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$application_id]);
    
    if ($stmt->rowCount() > 0) {
        // Log the deletion
        $log_sql = "INSERT INTO application_logs (application_id, action, details, admin_id, created_at) VALUES (?, ?, ?, ?, NOW())";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            $application_id, 
            'deleted', 
            "Application deleted: " . $application['full_name'] . " (" . $application['email'] . ")",
            $_SESSION['admin_id'] ?? 1
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Application deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Application not found']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
