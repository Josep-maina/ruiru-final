<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

if (!isset($_GET['export']) || $_GET['export'] !== 'csv') {
    header('Location: applications.php');
    exit;
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$course_filter = isset($_GET['course']) ? $_GET['course'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_conditions = [];
$params = [];

if ($status_filter !== 'all') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if ($course_filter !== 'all') {
    $where_conditions[] = "course_of_interest = ?";
    $params[] = $course_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM applications $where_clause ORDER BY application_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set headers for CSV download
    $filename = 'applications_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Add BOM for proper UTF-8 encoding in Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Add CSV headers
    $headers = [
        'Application ID',
        'Full Name', 
        'Email', 
        'Phone', 
        'Date of Birth', 
        'Gender', 
        'ID Number', 
        'Course of Interest', 
        'Status', 
        'Address', 
        'Previous School', 
        'Year Completed', 
        'Grades/Results', 
        'Emergency Contact', 
        'Emergency Phone', 
        'Application Date',
        'Last Updated'
    ];
    fputcsv($output, $headers);
    
    // Add data rows
    foreach ($applications as $app) {
        $row = [
            $app['id'],
            $app['full_name'] ?? '',
            $app['email'] ?? '',
            $app['phone'] ?? '',
            $app['date_of_birth'] ?? '',
            $app['gender'] ?? '',
            $app['id_number'] ?? '',
            $app['course_of_interest'] ?? '',
            ucfirst($app['status'] ?? ''),
            $app['address'] ?? '',
            $app['previous_school'] ?? '',
            $app['year_completed'] ?? '',
            $app['grades'] ?? '',
            $app['emergency_contact'] ?? '',
            $app['emergency_phone'] ?? '',
            $app['application_date'] ?? '',
            $app['updated_at'] ?? $app['application_date'] ?? ''
        ];
        fputcsv($output, $row);
    }
    
    // Add summary at the end
    fputcsv($output, []); // Empty row
    fputcsv($output, ['Summary']);
    fputcsv($output, ['Total Applications', count($applications)]);
    fputcsv($output, ['Export Date', date('Y-m-d H:i:s')]);
    fputcsv($output, ['Exported By', getCurrentAdmin()['full_name'] ?? 'Admin']);
    
    fclose($output);
    
} catch(PDOException $e) {
    header('Location: applications.php?error=' . urlencode('Export failed: ' . $e->getMessage()));
    exit;
}
?>
