<?php
// Disable error display and start output buffering to prevent JSON corruption
ini_set('display_errors', 0);
error_reporting(0);
ob_start();

// Include database configuration
require_once 'config/database.php';

// Set content type to JSON and disable caching
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Function to send JSON response and exit cleanly
function sendResponse($success, $message, $data = null) {
    // Clear any output that might have been generated
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if ($data) {
        $response = array_merge($response, $data);
    }
    
    // Ensure clean JSON output
    header('Content-Type: application/json');
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Function to log errors without displaying them
function logError($message) {
    error_log("[RTVC Contact Form] " . date('Y-m-d H:i:s') . " - " . $message);
}

try {
    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Invalid request method. Only POST requests are allowed.');
    }

    // Get database connection
    $pdo = getDatabaseConnection();
    if (!$pdo) {
        logError("Database connection failed");
        sendResponse(false, 'Database connection failed. Please check your database configuration.');
    }

    // Collect and validate form data
    $requiredFields = ['name', 'email', 'subject', 'message', 'privacy'];
    $formData = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            sendResponse(false, "Required field '$field' is missing or empty.");
        }
        $formData[$field] = trim($_POST[$field]);
    }
    
    // Optional fields
    $formData['phone'] = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    
    // Validate email format
    if (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        sendResponse(false, 'Please provide a valid email address.');
    }
    
    // Check for spam (honeypot field)
    if (!empty($_POST['website'])) {
        logError("Spam submission detected from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        // Return success to fool bots
        sendResponse(true, 'Message sent successfully.');
    }
    
    // Validate message length
    if (strlen($formData['message']) < 10) {
        sendResponse(false, 'Message must be at least 10 characters long.');
    }
    
    if (strlen($formData['message']) > 5000) {
        sendResponse(false, 'Message is too long. Please keep it under 5000 characters.');
    }
    
    // Prepare and execute insert statement
    $sql = "INSERT INTO contact_messages (
        name, email, phone, subject, message, privacy_consent, ip_address, user_agent
    ) VALUES (
        :name, :email, :phone, :subject, :message, :privacy_consent, :ip_address, :user_agent
    )";
    
    $stmt = $pdo->prepare($sql);
    
    $result = $stmt->execute([
        ':name' => $formData['name'],
        ':email' => $formData['email'],
        ':phone' => $formData['phone'],
        ':subject' => $formData['subject'],
        ':message' => $formData['message'],
        ':privacy_consent' => 1, // Since privacy field is required, it's always true
        ':ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ]);
    
    if ($result) {
        $messageId = $pdo->lastInsertId();
        
        // Try to send emails (don't fail if email doesn't work)
        try {
            sendAdminNotification($formData);
            sendUserConfirmation($formData);
        } catch (Exception $e) {
            logError("Email sending failed: " . $e->getMessage());
            // Continue anyway - message was saved
        }
        
        logError("Contact form submission successful - ID: $messageId, Email: " . $formData['email']);
        sendResponse(true, 'Your message has been sent successfully! We will respond to you shortly.', [
            'messageId' => $messageId
        ]);
    } else {
        logError("Failed to save contact message for: " . $formData['email']);
        sendResponse(false, 'Failed to save your message. Please try again.');
    }
    
} catch (PDOException $e) {
    logError("Database error: " . $e->getMessage());
    sendResponse(false, 'A database error occurred. Please try again later.');
} catch (Exception $e) {
    logError("General error: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred. Please try again.');
}

// Email functions
function sendAdminNotification($formData) {
    $adminEmail = 'ruirutvc@gmail.com';
    $subject = "New Contact Form Submission - RTVC Website";
    
    $message = "
    <html>
    <head>
        <title>New Contact Form Submission</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #198754; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; }
            .field { margin-bottom: 10px; padding: 8px; background: #f8f9fa; border-left: 3px solid #198754; }
            .label { font-weight: bold; color: #198754; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
        </div>
        <div class='content'>
            <p>A new message has been submitted through the contact form.</p>
            
            <div class='field'>
                <span class='label'>Name:</span> " . htmlspecialchars($formData['name']) . "
            </div>
            <div class='field'>
                <span class='label'>Email:</span> " . htmlspecialchars($formData['email']) . "
            </div>
            <div class='field'>
                <span class='label'>Phone:</span> " . htmlspecialchars($formData['phone'] ?: 'Not provided') . "
            </div>
            <div class='field'>
                <span class='label'>Subject:</span> " . htmlspecialchars($formData['subject']) . "
            </div>
            <div class='field'>
                <span class='label'>Message:</span><br>
                " . nl2br(htmlspecialchars($formData['message'])) . "
            </div>
            <div class='field'>
                <span class='label'>Submitted:</span> " . date('Y-m-d H:i:s') . "
            </div>
            
            <p><strong>Please respond to this inquiry promptly.</strong></p>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: RTVC Website <no-reply@ruirutvc.ac.ke>' . "\r\n";
    $headers .= 'Reply-To: ' . $formData['email'] . "\r\n";
    
    return mail($adminEmail, $subject, $message, $headers);
}

function sendUserConfirmation($formData) {
    $subject = "Thank you for contacting Ruiru Technical and Vocational College";
    
    $message = "
    <html>
    <head>
        <title>Message Received</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background-color: #198754; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .footer { background-color: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; }
            .highlight { background-color: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Ruiru Technical and Vocational College</h2>
            <p>Message Received Successfully</p>
        </div>
        <div class='content'>
            <p>Dear " . htmlspecialchars($formData['name']) . ",</p>
            <p>Thank you for contacting Ruiru Technical and Vocational College. We have successfully received your message regarding <strong>\"" . htmlspecialchars($formData['subject']) . "\"</strong>.</p>
            
            <div class='highlight'>
                <p><strong>What happens next?</strong></p>
                <ul>
                    <li>Our team will review your inquiry within 24 hours</li>
                    <li>You will receive a detailed response within 1-2 business days</li>
                    <li>For urgent matters, please call us directly</li>
                </ul>
            </div>
            
            <p><strong>Contact Information:</strong></p>
            <ul>
                <li><strong>Phone:</strong> +254 746 319 919 | +254 789 869 499</li>
                <li><strong>Email:</strong> ruirutvc@gmail.com</li>
                <li><strong>Address:</strong> P.O. Box 416-00232 Ruiru, Kiambu County</li>
            </ul>
            
            <p>Thank you for your interest in Ruiru Technical and Vocational College!</p>
            
            <p>Best regards,<br>
            <strong>Ruiru Technical and Vocational College</strong><br>
            Customer Support Team</p>
        </div>
        <div class='footer'>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>Visit our website: <a href='#'>www.ruirutvc.ac.ke</a></p>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: Ruiru TVC <no-reply@ruirutvc.ac.ke>' . "\r\n";
    $headers .= 'Reply-To: ruirutvc@gmail.com' . "\r\n";
    
    return mail($formData['email'], $subject, $message, $headers);
}
?>
