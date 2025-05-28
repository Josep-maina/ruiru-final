<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

$page_title = "Settings - RTVC Admin";

// Handle settings updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($action === 'update_general') {
            $settings = [
                'site_name' => $_POST['site_name'],
                'site_description' => $_POST['site_description'],
                'contact_email' => $_POST['contact_email'],
                'contact_phone' => $_POST['contact_phone'],
                'address' => $_POST['address'],
                'facebook_url' => $_POST['facebook_url'],
                'twitter_url' => $_POST['twitter_url'],
                'instagram_url' => $_POST['instagram_url'],
                'linkedin_url' => $_POST['linkedin_url']
            ];
            
            foreach ($settings as $key => $value) {
                $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$key, $value]);
            }
            
            $success = "General settings updated successfully";
        }
        
        if ($action === 'update_email') {
            $email_settings = [
                'smtp_host' => $_POST['smtp_host'],
                'smtp_port' => $_POST['smtp_port'],
                'smtp_username' => $_POST['smtp_username'],
                'smtp_password' => $_POST['smtp_password'],
                'smtp_encryption' => $_POST['smtp_encryption'],
                'from_email' => $_POST['from_email'],
                'from_name' => $_POST['from_name']
            ];
            
            foreach ($email_settings as $key => $value) {
                $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$key, $value]);
            }
            
            $success = "Email settings updated successfully";
        }
        
        if ($action === 'update_application') {
            $app_settings = [
                'application_deadline' => $_POST['application_deadline'],
                'application_fee' => $_POST['application_fee'],
                'max_applications_per_day' => $_POST['max_applications_per_day'],
                'auto_approve_applications' => isset($_POST['auto_approve_applications']) ? '1' : '0',
                'require_documents' => isset($_POST['require_documents']) ? '1' : '0',
                'notification_emails' => $_POST['notification_emails']
            ];
            
            foreach ($app_settings as $key => $value) {
                $sql = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$key, $value]);
            }
            
            $success = "Application settings updated successfully";
        }
        
        if ($action === 'backup_database') {
            // Simple backup functionality
            $backup_file = 'backups/backup_' . date('Y-m-d_H-i-s') . '.sql';
            
            if (!is_dir('backups')) {
                mkdir('backups', 0755, true);
            }
            
            $command = "mysqldump --user=" . DB_USER . " --password=" . DB_PASS . " --host=" . DB_HOST . " " . DB_NAME . " > " . $backup_file;
            exec($command, $output, $return_var);
            
            if ($return_var === 0) {
                $success = "Database backup created successfully: " . $backup_file;
            } else {
                $error = "Failed to create database backup";
            }
        }
        
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Get current settings
$current_settings = [];
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create settings table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(255) UNIQUE NOT NULL,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    
    $sql = "SELECT setting_key, setting_value FROM settings";
    $stmt = $pdo->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $current_settings[$row['setting_key']] = $row['setting_value'];
    }
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

function getSetting($key, $default = '') {
    global $current_settings;
    return $current_settings[$key] ?? $default;
}
?>

<?php include 'includes/admin_header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-cog me-2"></i>System Settings</h2>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <?php echo htmlspecialchars($success); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Settings Navigation -->
                <ul class="nav nav-pills mb-4" id="settingsTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="general-tab" data-bs-toggle="pill" data-bs-target="#general" type="button">
                            <i class="fas fa-info-circle me-2"></i>General
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="email-tab" data-bs-toggle="pill" data-bs-target="#email" type="button">
                            <i class="fas fa-envelope me-2"></i>Email
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="application-tab" data-bs-toggle="pill" data-bs-target="#application" type="button">
                            <i class="fas fa-file-alt me-2"></i>Applications
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="backup-tab" data-bs-toggle="pill" data-bs-target="#backup" type="button">
                            <i class="fas fa-database me-2"></i>Backup
                        </button>
                    </li>
                </ul>

                <!-- Settings Content -->
                <div class="tab-content" id="settingsTabContent">
                    <!-- General Settings -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>General Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_general">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Site Name</label>
                                                <input type="text" name="site_name" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('site_name', 'Ruiru Technical and Vocational College')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Contact Email</label>
                                                <input type="email" name="contact_email" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('contact_email', 'info@rtvc.ac.ke')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Contact Phone</label>
                                                <input type="text" name="contact_phone" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('contact_phone', '+254 700 000 000')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Site Description</label>
                                                <textarea name="site_description" class="form-control" rows="3"><?php echo htmlspecialchars(getSetting('site_description', 'Leading technical and vocational education in Kenya')); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars(getSetting('address', 'Ruiru, Kiambu County, Kenya')); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h6 class="mt-4 mb-3">Social Media Links</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Facebook URL</label>
                                                <input type="url" name="facebook_url" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('facebook_url')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Twitter URL</label>
                                                <input type="url" name="twitter_url" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('twitter_url')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Instagram URL</label>
                                                <input type="url" name="instagram_url" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('instagram_url')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">LinkedIn URL</label>
                                                <input type="url" name="linkedin_url" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('linkedin_url')); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save General Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Email Settings -->
                    <div class="tab-pane fade" id="email" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Email Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_email">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">SMTP Host</label>
                                                <input type="text" name="smtp_host" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('smtp_host', 'smtp.gmail.com')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">SMTP Port</label>
                                                <input type="number" name="smtp_port" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('smtp_port', '587')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">SMTP Username</label>
                                                <input type="text" name="smtp_username" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('smtp_username')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">SMTP Password</label>
                                                <input type="password" name="smtp_password" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('smtp_password')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">SMTP Encryption</label>
                                                <select name="smtp_encryption" class="form-select">
                                                    <option value="tls" <?php echo getSetting('smtp_encryption', 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                                    <option value="ssl" <?php echo getSetting('smtp_encryption') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                                    <option value="none" <?php echo getSetting('smtp_encryption') === 'none' ? 'selected' : ''; ?>>None</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">From Email</label>
                                                <input type="email" name="from_email" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('from_email', 'noreply@rtvc.ac.ke')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">From Name</label>
                                                <input type="text" name="from_name" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('from_name', 'RTVC Admin')); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Configure SMTP settings to enable email notifications for applications and contact forms.
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Email Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Application Settings -->
                    <div class="tab-pane fade" id="application" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Application Settings</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_application">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Application Deadline</label>
                                                <input type="date" name="application_deadline" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('application_deadline')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Application Fee (KES)</label>
                                                <input type="number" name="application_fee" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('application_fee', '1000')); ?>">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Max Applications Per Day</label>
                                                <input type="number" name="max_applications_per_day" class="form-control" 
                                                       value="<?php echo htmlspecialchars(getSetting('max_applications_per_day', '100')); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Notification Emails</label>
                                                <textarea name="notification_emails" class="form-control" rows="3" 
                                                          placeholder="admin@rtvc.ac.ke, registrar@rtvc.ac.ke"><?php echo htmlspecialchars(getSetting('notification_emails', 'admin@rtvc.ac.ke')); ?></textarea>
                                                <small class="form-text text-muted">Separate multiple emails with commas</small>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="auto_approve_applications" 
                                                           <?php echo getSetting('auto_approve_applications') === '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Auto-approve applications</label>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="require_documents" 
                                                           <?php echo getSetting('require_documents', '1') === '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label">Require document uploads</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Application Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Backup Settings -->
                    <div class="tab-pane fade" id="backup" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-database me-2"></i>Database Backup</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Create Backup</h6>
                                        <p class="text-muted">Create a backup of your database to protect your data.</p>
                                        <form method="POST">
                                            <input type="hidden" name="action" value="backup_database">
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-download me-2"></i>Create Backup
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Existing Backups</h6>
                                        <div class="backup-list">
                                            <?php
                                            $backup_dir = 'backups/';
                                            if (is_dir($backup_dir)) {
                                                $backups = glob($backup_dir . '*.sql');
                                                if (!empty($backups)) {
                                                    foreach ($backups as $backup) {
                                                        $filename = basename($backup);
                                                        $filesize = filesize($backup);
                                                        $filedate = date('M j, Y g:i A', filemtime($backup));
                                                        echo "<div class='d-flex justify-content-between align-items-center border-bottom py-2'>";
                                                        echo "<div>";
                                                        echo "<strong>$filename</strong><br>";
                                                        echo "<small class='text-muted'>$filedate • " . number_format($filesize/1024, 2) . " KB</small>";
                                                        echo "</div>";
                                                        echo "<a href='$backup' class='btn btn-sm btn-outline-primary' download>";
                                                        echo "<i class='fas fa-download'></i>";
                                                        echo "</a>";
                                                        echo "</div>";
                                                    }
                                                } else {
                                                    echo "<p class='text-muted'>No backups found</p>";
                                                }
                                            } else {
                                                echo "<p class='text-muted'>Backup directory not found</p>";
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning mt-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Important:</strong> Regular backups are essential for data protection. 
                                    Store backups in a secure location outside your web server.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php include 'includes/admin_footer.php'; ?>
