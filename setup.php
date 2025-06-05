<?php
/**
 * Database Setup Script
 * Run this file once to set up your database and tables
 */

require_once 'config/database.php';

// Set content type
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>RTVC Database Setup</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <style>
        body { background: linear-gradient(135deg, #198754, #20c997); min-height: 100vh; }
        .setup-container { background: white; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); }
        .step { padding: 1rem; margin: 0.5rem 0; border-radius: 8px; }
        .step.success { background: #d1edff; border-left: 4px solid #0d6efd; }
        .step.error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .step.warning { background: #fff3cd; border-left: 4px solid #ffc107; }
    </style>
</head>
<body class='d-flex align-items-center justify-content-center'>
    <div class='container'>
        <div class='row justify-content-center'>
            <div class='col-md-8'>
                <div class='setup-container p-5'>
                    <div class='text-center mb-4'>
                        <i class='fas fa-database fa-3x text-success mb-3'></i>
                        <h2>RTVC Database Setup</h2>
                        <p class='text-muted'>Setting up your database and tables...</p>
                    </div>";

// Step 1: Test basic connection (without database)
echo "<div class='step'>";
echo "<h5><i class='fas fa-plug me-2'></i>Step 1: Testing Database Connection</h5>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='step success'>";
    echo "<i class='fas fa-check-circle me-2'></i>✅ Successfully connected to MySQL server at " . DB_HOST;
    echo "</div>";
} catch (PDOException $e) {
    echo "<div class='step error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Failed to connect to MySQL server: " . $e->getMessage();
    echo "<br><strong>Common solutions:</strong>";
    echo "<ul>";
    echo "<li>Check if MySQL/MariaDB is running</li>";
    echo "<li>Verify database credentials in config/database.php</li>";
    echo "<li>Make sure the database user has proper permissions</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div></div></div></div></body></html>";
    exit;
}
echo "</div>";

// Step 2: Create database
echo "<div class='step'>";
echo "<h5><i class='fas fa-database me-2'></i>Step 2: Creating Database</h5>";

if (createDatabaseIfNotExists()) {
    echo "<div class='step success'>";
    echo "<i class='fas fa-check-circle me-2'></i>✅ Database '" . DB_NAME . "' created/verified successfully";
    echo "</div>";
} else {
    echo "<div class='step error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Failed to create database '" . DB_NAME . "'";
    echo "</div>";
    echo "</div></div></div></div></body></html>";
    exit;
}
echo "</div>";

// Step 3: Test connection to specific database
echo "<div class='step'>";
echo "<h5><i class='fas fa-link me-2'></i>Step 3: Testing Database Connection</h5>";

if (testDatabaseConnection()) {
    echo "<div class='step success'>";
    echo "<i class='fas fa-check-circle me-2'></i>✅ Successfully connected to database '" . DB_NAME . "'";
    echo "</div>";
} else {
    echo "<div class='step error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Failed to connect to database '" . DB_NAME . "'";
    echo "</div>";
    echo "</div></div></div></div></body></html>";
    exit;
}
echo "</div>";

// Step 4: Create tables
echo "<div class='step'>";
echo "<h5><i class='fas fa-table me-2'></i>Step 4: Creating Tables</h5>";

if (initializeTables()) {
    echo "<div class='step success'>";
    echo "<i class='fas fa-check-circle me-2'></i>✅ All tables created successfully";
    echo "<ul class='mt-2'>";
    echo "<li>contact_messages - for contact form submissions</li>";
    echo "<li>applications - for student applications</li>";
    echo "<li>admin_users - for admin panel access</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='step error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Failed to create tables";
    echo "</div>";
    echo "</div></div></div></div></body></html>";
    exit;
}
echo "</div>";

// Step 5: Verify setup
echo "<div class='step'>";
echo "<h5><i class='fas fa-check-double me-2'></i>Step 5: Verifying Setup</h5>";

$pdo = getDatabaseConnection();
if ($pdo) {
    try {
        // Check tables exist
        $tables = ['contact_messages', 'applications', 'admin_users'];
        $allTablesExist = true;
        
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() == 0) {
                $allTablesExist = false;
                break;
            }
        }
        
        if ($allTablesExist) {
            echo "<div class='step success'>";
            echo "<i class='fas fa-check-circle me-2'></i>✅ All tables verified successfully";
            
            // Check admin user
            $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
            $adminCount = $stmt->fetchColumn();
            echo "<br><i class='fas fa-user-shield me-2'></i>Admin users: $adminCount";
            echo "</div>";
        } else {
            echo "<div class='step error'>";
            echo "<i class='fas fa-times-circle me-2'></i>❌ Some tables are missing";
            echo "</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='step error'>";
        echo "<i class='fas fa-times-circle me-2'></i>❌ Verification failed: " . $e->getMessage();
        echo "</div>";
    }
} else {
    echo "<div class='step error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Cannot connect to database for verification";
    echo "</div>";
}
echo "</div>";

// Success message
echo "<div class='alert alert-success mt-4'>";
echo "<h4><i class='fas fa-party-horn me-2'></i>Setup Complete!</h4>";
echo "<p class='mb-3'>Your RTVC database has been set up successfully. Here's what you can do now:</p>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<h6><i class='fas fa-user-shield me-2'></i>Admin Panel Access:</h6>";
echo "<ul>";
echo "<li><strong>URL:</strong> <a href='admin/login.php' target='_blank'>admin/login.php</a></li>";
echo "<li><strong>Username:</strong> admin</li>";
echo "<li><strong>Password:</strong> admin123</li>";
echo "</ul>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<h6><i class='fas fa-envelope me-2'></i>Test Contact Form:</h6>";
echo "<ul>";
echo "<li><a href='contact.php' target='_blank'>Contact Form</a></li>";
echo "<li><a href='apply.php' target='_blank'>Application Form</a></li>";
echo "</ul>";
echo "</div>";
echo "</div>";
echo "<div class='alert alert-warning mt-3'>";
echo "<strong><i class='fas fa-exclamation-triangle me-2'></i>Important:</strong> ";
echo "Please change the default admin password after first login and delete this setup.php file for security!";
echo "</div>";
echo "</div>";

echo "<div class='text-center mt-4'>";
echo "<a href='admin/login.php' class='btn btn-success btn-lg me-3'>";
echo "<i class='fas fa-sign-in-alt me-2'></i>Go to Admin Panel";
echo "</a>";
echo "<a href='contact.php' class='btn btn-outline-success btn-lg'>";
echo "<i class='fas fa-envelope me-2'></i>Test Contact Form";
echo "</a>";
echo "</div>";

echo "</div></div></div></div></body></html>";
?>
