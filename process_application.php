<?php
// Prevent any output before JSON response
ob_start();

// Database configuration - UPDATE THESE VALUES
$host = 'localhost';
$dbname = 'ruiru_tvc';
$username = 'root'; // Change to your database username
$password = '';     // Change to your database password

// Set content type to JSON and prevent caching
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Enable CORS if needed
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Clear any previous output
ob_clean();

// Function to send JSON response and exit
function sendResponse($success, $message, $data = null) {
    // Clear any output buffer
    if (ob_get_level()) {
        ob_clean();
    }
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    if ($data) {
        $response = array_merge($response, $data);
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Disable error display to prevent HTML in JSON response
ini_set('display_errors', 0);
error_reporting(0);

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method. Only POST requests are allowed.');
    }

    // Create PDO connection
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        sendResponse(false, 'Database connection failed. Please try again later.');
    }

    // Generate application ID
    $applicationId = 'RTVC-' . strtoupper(uniqid()) . '-' . date('Y');
    
    // Collect and validate form data
    $requiredFields = ['fullName', 'email', 'phoneNumber', 'dateOfBirth', 'gender', 'courseOfInterest'];
    $formData = [];
    
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            sendResponse(false, "Required field '$field' is missing.");
        }
        $formData[$field] = trim($_POST[$field]);
    }
    
    // Optional fields
    $optionalFields = [
        'idNumber', 'nationality', 'kcseMeanGrade', 'address', 'emergencyContact',
        'guardianName', 'guardianPhone', 'guardianRelationship', 'guardianEmail',
        'communicationMethod', 'additionalInfo'
    ];
    
    foreach ($optionalFields as $field) {
        $formData[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : '';
    }
    
    // Handle checkboxes
    $formData['termsConditions'] = isset($_POST['termsConditions']) ? 1 : 0;
    $formData['marketingConsent'] = isset($_POST['marketingConsent']) ? 1 : 0;
    
    // Validate email format
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, 'Invalid email format.');
    }
    
    // Check if table exists, if not create it
    $createTableSQL = "CREATE TABLE IF NOT EXISTS applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        application_id VARCHAR(50) UNIQUE NOT NULL,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        id_number VARCHAR(20),
        phone_number VARCHAR(20) NOT NULL,
        date_of_birth DATE NOT NULL,
        gender ENUM('male', 'female', 'other') NOT NULL,
        nationality VARCHAR(100),
        kcse_mean_grade VARCHAR(10),
        course_of_interest VARCHAR(255) NOT NULL,
        address TEXT,
        emergency_contact VARCHAR(255),
        guardian_name VARCHAR(255),
        guardian_phone VARCHAR(20),
        guardian_relationship VARCHAR(100),
        guardian_email VARCHAR(255),
        communication_method VARCHAR(50),
        additional_info TEXT,
        terms_conditions BOOLEAN DEFAULT FALSE,
        marketing_consent BOOLEAN DEFAULT FALSE,
        application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'under_review', 'approved', 'rejected', 'waitlisted') DEFAULT 'pending',
        admin_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($createTableSQL);
    
    // Prepare SQL statement
    $sql = "INSERT INTO applications (
        application_id, full_name, email, id_number, phone_number, 
        date_of_birth, gender, nationality, kcse_mean_grade, course_of_interest,
        address, emergency_contact, guardian_name, guardian_phone, guardian_relationship,
        guardian_email, communication_method, additional_info, terms_conditions,
        marketing_consent, status
    ) VALUES (
        :application_id, :full_name, :email, :id_number, :phone_number,
        :date_of_birth, :gender, :nationality, :kcse_mean_grade, :course_of_interest,
        :address, :emergency_contact, :guardian_name, :guardian_phone, :guardian_relationship,
        :guardian_email, :communication_method, :additional_info, :terms_conditions,
        :marketing_consent, 'pending'
    )";
    
    $stmt = $pdo->prepare($sql);
    
    // Execute the statement
    $result = $stmt->execute([
        ':application_id' => $applicationId,
        ':full_name' => $formData['fullName'],
        ':email' => $formData['email'],
        ':id_number' => $formData['idNumber'],
        ':phone_number' => $formData['phoneNumber'],
        ':date_of_birth' => $formData['dateOfBirth'],
        ':gender' => $formData['gender'],
        ':nationality' => $formData['nationality'],
        ':kcse_mean_grade' => $formData['kcseMeanGrade'],
        ':course_of_interest' => $formData['courseOfInterest'],
        ':address' => $formData['address'],
        ':emergency_contact' => $formData['emergencyContact'],
        ':guardian_name' => $formData['guardianName'],
        ':guardian_phone' => $formData['guardianPhone'],
        ':guardian_relationship' => $formData['guardianRelationship'],
        ':guardian_email' => $formData['guardianEmail'],
        ':communication_method' => $formData['communicationMethod'],
        ':additional_info' => $formData['additionalInfo'],
        ':terms_conditions' => $formData['termsConditions'],
        ':marketing_consent' => $formData['marketingConsent']
    ]);
    
    if ($result) {
        // Return success response
        sendResponse(true, 'Application submitted successfully', [
            'applicationId' => $applicationId
        ]);
    } else {
        sendResponse(false, 'Failed to save application. Please try again.');
    }
    
} catch (PDOException $e) {
    // Log error but don't expose details
    error_log("Database error: " . $e->getMessage());
    sendResponse(false, 'Database error occurred. Please try again later.');
} catch (Exception $e) {
    // Log error but don't expose details
    error_log("General error: " . $e->getMessage());
    sendResponse(false, 'An error occurred while processing your application. Please try again.');
}
?>