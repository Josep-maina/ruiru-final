<?php
/**
 * Database Configuration File
 * Update these settings according to your hosting environment
 */

// Database configuration - UPDATE THESE VALUES FOR YOUR SERVER
define('DB_HOST', 'localhost');
define('DB_NAME', 'ruiru_tvc');
define('DB_USER', 'root');        // Change this to your database username
define('DB_PASS', '');            // Change this to your database password
define('DB_CHARSET', 'utf8mb4');

// Create database connection function
function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Log the actual error for debugging
        error_log("Database connection error: " . $e->getMessage());
        return false;
    }
}

// Test database connection
function testDatabaseConnection() {
    $pdo = getDatabaseConnection();
    if ($pdo) {
        return true;
    }
    return false;
}

// Create database if it doesn't exist
function createDatabaseIfNotExists() {
    try {
        // Connect without specifying database
        $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $sql = "CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $pdo->exec($sql);
        
        return true;
    } catch (PDOException $e) {
        error_log("Database creation error: " . $e->getMessage());
        return false;
    }
}

// Initialize database tables
function initializeTables() {
    $pdo = getDatabaseConnection();
    if (!$pdo) {
        return false;
    }
    
    try {
        // Create contact_messages table
        $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            privacy_consent BOOLEAN DEFAULT FALSE,
            status ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
            ip_address VARCHAR(45),
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        
        // Create applications table
        $sql = "CREATE TABLE IF NOT EXISTS applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            application_id VARCHAR(50) UNIQUE NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            id_number VARCHAR(20),
            phone_number VARCHAR(20) NOT NULL,
            date_of_birth DATE NOT NULL,
            gender ENUM('male', 'female', 'other') NOT NULL,
            nationality VARCHAR(100) DEFAULT 'Kenyan',
            kcse_mean_grade VARCHAR(10),
            course_of_interest VARCHAR(255) NOT NULL,
            address TEXT,
            emergency_contact VARCHAR(255),
            guardian_name VARCHAR(255),
            guardian_phone VARCHAR(20),
            guardian_relationship VARCHAR(100),
            guardian_email VARCHAR(255),
            communication_method VARCHAR(50) DEFAULT 'email',
            additional_info TEXT,
            terms_conditions BOOLEAN DEFAULT FALSE,
            marketing_consent BOOLEAN DEFAULT FALSE,
            application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('pending', 'under_review', 'approved', 'rejected', 'waitlisted') DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_application_id (application_id),
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_course (course_of_interest),
            INDEX idx_application_date (application_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        
        // Create admin_users table
        $sql = "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(255) NOT NULL,
            role ENUM('super_admin', 'admin', 'staff') DEFAULT 'admin',
            is_active BOOLEAN DEFAULT TRUE,
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        
        // Create default admin user if none exists
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users");
        $userCount = $stmt->fetchColumn();
        
        if ($userCount == 0) {
            $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, full_name, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute(['admin', 'admin@ruirutvc.ac.ke', $defaultPassword, 'System Administrator', 'super_admin']);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log("Table creation error: " . $e->getMessage());
        return false;
    }
}
?>
