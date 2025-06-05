<?php
/**
 * Database Connection Test
 * Use this to test your database connection and see detailed error information
 */

require_once 'config/database.php';

// Set content type
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Connection Test - RTVC</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <style>
        body { background: #f8f9fa; }
        .test-container { background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .test-item { padding: 1rem; margin: 0.5rem 0; border-radius: 8px; }
        .test-success { background: #d1edff; border-left: 4px solid #0d6efd; }
        .test-error { background: #f8d7da; border-left: 4px solid #dc3545; }
        .test-warning { background: #fff3cd; border-left: 4px solid #ffc107; }
        .config-display { background: #f8f9fa; padding: 1rem; border-radius: 8px; font-family: monospace; }
    </style>
</head>
<body class='py-5'>
    <div class='container'>
        <div class='row justify-content-center'>
            <div class='col-md-10'>
                <div class='test-container p-4'>
                    <div class='text-center mb-4'>
                        <i class='fas fa-database fa-3x text-primary mb-3'></i>
                        <h2>Database Connection Test</h2>
                        <p class='text-muted'>Testing your RTVC database configuration...</p>
                    </div>";

// Display current configuration
echo "<div class='mb-4'>";
echo "<h5><i class='fas fa-cog me-2'></i>Current Configuration:</h5>";
echo "<div class='config-display'>";
echo "<strong>Host:</strong> " . DB_HOST . "<br>";
echo "<strong>Database:</strong> " . DB_NAME . "<br>";
echo "<strong>Username:</strong> " . DB_USER . "<br>";
echo "<strong>Password:</strong> " . (empty(DB_PASS) ? '[Empty]' : '[Set - ' . strlen(DB_PASS) . ' characters]') . "<br>";
echo "<strong>Charset:</strong> " . DB_CHARSET;
echo "</div>";
echo "</div>";

// Test 1: Basic MySQL connection
echo "<div class='test-item'>";
echo "<h5><i class='fas fa-plug me-2'></i>Test 1: MySQL Server Connection</h5>";

try {
    $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<div class='test-success'>";
    echo "<i class='fas fa-check-circle me-2'></i>✅ Successfully connected to MySQL server";
    echo "</div>";
    
    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<small class='text-muted'>MySQL Version: $version</small>";
    
} catch (PDOException $e) {
    echo "<div class='test-error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Failed to connect to MySQL server";
    echo "<br><strong>Error:</strong> " . $e->getMessage();
    echo "<br><strong>Common solutions:</strong>";
    echo "<ul>";
    echo "<li>Check if MySQL/MariaDB service is running</li>";
    echo "<li>Verify the hostname (try '127.0.0.1' instead of 'localhost')</li>";
    echo "<li>Check username and password in config/database.php</li>";
    echo "<li>Ensure the database user has connection privileges</li>";
    echo "</ul>";
    echo "</div>";
    echo "</div></div></div></body></html>";
    exit;
}
echo "</div>";

// Test 2: Database existence
echo "<div class='test-item'>";
echo "<h5><i class='fas fa-database me-2'></i>Test 2: Database Existence</h5>";

try {
    $stmt = $pdo->query("SHOW DATABASES LIKE '" . DB_NAME . "'");
    if ($stmt->rowCount() > 0) {
        echo "<div class='test-success'>";
        echo "<i class='fas fa-check-circle me-2'></i>✅ Database '" . DB_NAME . "' exists";
        echo "</div>";
    } else {
        echo "<div class='test-warning'>";
        echo "<i class='fas fa-exclamation-triangle me-2'></i>⚠️ Database '" . DB_NAME . "' does not exist";
        echo "<br>It will be created automatically when you run setup.php";
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<div class='test-error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Cannot check database existence: " . $e->getMessage();
    echo "</div>";
}
echo "</div>";

// Test 3: Database connection
echo "<div class='test-item'>";
echo "<h5><i class='fas fa-link me-2'></i>Test 3: Database Connection</h5>";

$pdo_db = getDatabaseConnection();
if ($pdo_db) {
    echo "<div class='test-success'>";
    echo "<i class='fas fa-check-circle me-2'></i>✅ Successfully connected to database '" . DB_NAME . "'";
    echo "</div>";
} else {
    echo "<div class='test-error'>";
    echo "<i class='fas fa-times-circle me-2'></i>❌ Failed to connect to database '" . DB_NAME . "'";
    echo "<br>The database may not exist or you may not have access to it.";
    echo "</div>";
}
echo "</div>";

// Test 4: Table existence (if database connection works)
if ($pdo_db) {
    echo "<div class='test-item'>";
    echo "<h5><i class='fas fa-table me-2'></i>Test 4: Table Existence</h5>";
    
    $tables = ['contact_messages', 'applications', 'admin_users'];
    $existingTables = [];
    $missingTables = [];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo_db->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                $existingTables[] = $table;
            } else {
                $missingTables[] = $table;
            }
        } catch (PDOException $e) {
            $missingTables[] = $table;
        }
    }
    
    if (count($existingTables) > 0) {
        echo "<div class='test-success'>";
        echo "<i class='fas fa-check-circle me-2'></i>✅ Existing tables: " . implode(', ', $existingTables);
        echo "</div>";
    }
    
    if (count($missingTables) > 0) {
        echo "<div class='test-warning'>";
        echo "<i class='fas fa-exclamation-triangle me-2'></i>⚠️ Missing tables: " . implode(', ', $missingTables);
        echo "<br>These will be created when you run setup.php";
        echo "</div>";
    }
    
    echo "</div>";
    
    // Test 5: Data check (if tables exist)
    if (in_array('admin_users', $existingTables)) {
        echo "<div class='test-item'>";
        echo "<h5><i class='fas fa-users me-2'></i>Test 5: Admin Users</h5>";
        
        try {
            $stmt = $pdo_db->query("SELECT COUNT(*) FROM admin_users");
            $adminCount = $stmt->fetchColumn();
            
            if ($adminCount > 0) {
                echo "<div class='test-success'>";
                echo "<i class='fas fa-check-circle me-2'></i>✅ Found $adminCount admin user(s)";
                echo "</div>";
            } else {
                echo "<div class='test-warning'>";
                echo "<i class='fas fa-exclamation-triangle me-2'></i>⚠️ No admin users found";
                echo "<br>Default admin will be created when you run setup.php";
                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='test-error'>";
            echo "<i class='fas fa-times-circle me-2'></i>❌ Cannot check admin users: " . $e->getMessage();
            echo "</div>";
        }
        
        echo "</div>";
    }
}

// Summary and next steps
echo "<div class='alert alert-info mt-4'>";
echo "<h5><i class='fas fa-info-circle me-2'></i>Next Steps:</h5>";

if (!$pdo_db || count($missingTables ?? []) > 0) {
    echo "<ol>";
    echo "<li><strong>Run Database Setup:</strong> <a href='setup.php' class='btn btn-primary btn-sm ms-2'>Run Setup</a></li>";
    echo "<li><strong>Test Contact Form:</strong> After setup, test the contact form</li>";
    echo "<li><strong>Access Admin Panel:</strong> Login to admin panel with default credentials</li>";
    echo "</ol>";
} else {
    echo "<p class='mb-3'>✅ Your database is properly configured! You can now:</p>";
    echo "<div class='d-flex gap-2 flex-wrap'>";
    echo "<a href='contact.php' class='btn btn-success'>Test Contact Form</a>";
    echo "<a href='admin/login.php' class='btn btn-primary'>Access Admin Panel</a>";
    echo "<a href='apply.php' class='btn btn-outline-success'>Test Application Form</a>";
    echo "</div>";
}

echo "</div>";

// Configuration help
echo "<div class='alert alert-warning mt-3'>";
echo "<h6><i class='fas fa-wrench me-2'></i>Configuration Help:</h6>";
echo "<p class='mb-2'>If you're having connection issues, check these common configurations:</p>";
echo "<div class='row'>";
echo "<div class='col-md-6'>";
echo "<strong>XAMPP/WAMP:</strong>";
echo "<ul class='small'>";
echo "<li>Host: localhost</li>";
echo "<li>Username: root</li>";
echo "<li>Password: (empty)</li>";
echo "</ul>";
echo "</div>";
echo "<div class='col-md-6'>";
echo "<strong>cPanel/Shared Hosting:</strong>";
echo "<ul class='small'>";
echo "<li>Host: localhost</li>";
echo "<li>Username: cpanel_username</li>";
echo "<li>Password: cpanel_password</li>";
echo "</ul>";
echo "</div>";
echo "</div>";
echo "<small>Edit these settings in <code>config/database.php</code></small>";
echo "</div>";

echo "</div></div></div></body></html>";
?>
