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

if (!isset($input['id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$application_id = (int)$input['id'];
$status = $input['status'];

$valid_statuses = ['pending', 'under_review', 'approved', 'rejected'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if application exists
    $check_sql = "SELECT id, full_name, email FROM applications WHERE id = ?";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$application_id]);
    $application = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$application) {
        echo json_encode(['success' => false, 'message' => 'Application not found']);
        exit;
    }
    
    // Update the status
    $sql = "UPDATE applications SET status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $application_id]);
    
    if ($stmt->rowCount() > 0) {
        // Log the status change
        $log_sql = "INSERT INTO application_logs (application_id, action, details, admin_id, created_at) VALUES (?, ?, ?, ?, NOW())";
        $log_stmt = $pdo->prepare($log_sql);
        $log_stmt->execute([
            $application_id, 
            'status_change', 
            "Status changed to: " . $status,
            $_SESSION['admin_id'] ?? 1
        ]);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Status updated successfully',
            'new_status' => $status
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
