<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Application ID required']);
    exit;
}

$application_id = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM applications WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$application_id]);
    $application = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($application) {
        // Format the data for display
        $application['date_of_birth'] = $application['date_of_birth'] ?? 'Not provided';
        $application['gender'] = $application['gender'] ?? 'Not provided';
        $application['id_number'] = $application['id_number'] ?? 'Not provided';
        $application['address'] = $application['address'] ?? 'Not provided';
        $application['emergency_contact'] = $application['emergency_contact'] ?? 'Not provided';
        $application['emergency_phone'] = $application['emergency_phone'] ?? 'Not provided';
        $application['previous_school'] = $application['previous_school'] ?? 'Not provided';
        $application['year_completed'] = $application['year_completed'] ?? 'Not provided';
        $application['grades'] = $application['grades'] ?? 'Not provided';
        
        echo json_encode(['success' => true, 'application' => $application]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Application not found']);
    }
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
