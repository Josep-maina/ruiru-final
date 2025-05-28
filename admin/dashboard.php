<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

$page_title = "Dashboard - RTVC Admin";

// Connect to database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get statistics
$stats = [];

// Contact messages stats
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages");
    $stats['total_messages'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
    $stats['new_messages'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE DATE(created_at) = CURDATE()");
    $stats['today_messages'] = $stmt->fetchColumn();
} catch (PDOException $e) {
    $stats['total_messages'] = 0;
    $stats['new_messages'] = 0;
    $stats['today_messages'] = 0;
}

// Applications stats
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications");
    $stats['total_applications'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
    $stats['pending_applications'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE DATE(created_at) = CURDATE()");
    $stats['today_applications'] = $stmt->fetchColumn();
} catch (PDOException $e) {
    $stats['total_applications'] = 0;
    $stats['pending_applications'] = 0;
    $stats['today_applications'] = 0;
}

// Get recent messages
try {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
    $recent_messages = $stmt->fetchAll();
} catch (PDOException $e) {
    $recent_messages = [];
}

// Get recent applications
try {
    $stmt = $pdo->query("SELECT * FROM applications ORDER BY created_at DESC LIMIT 5");
    $recent_applications = $stmt->fetchAll();
} catch (PDOException $e) {
    $recent_applications = [];
}

$current_admin = getCurrentAdmin();

include 'includes/admin_header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="col-md-10 py-4">
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">Welcome back, <?php echo htmlspecialchars($current_admin['full_name']); ?>!</h2>
                <p class="text-muted mb-0">Here's what's happening at RTVC today.</p>
            </div>
            <div class="text-end">
                <small class="text-muted">Last login: <?php echo date('M d, Y H:i'); ?></small><br>
                <span class="badge bg-success"><?php echo ucfirst($current_admin['role']); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card messages">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-primary"><?php echo $stats['total_messages']; ?></div>
                        <h6 class="text-muted mb-0">Total Messages</h6>
                    </div>
                    <i class="fas fa-envelope fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card applications">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-success"><?php echo $stats['total_applications']; ?></div>
                        <h6 class="text-muted mb-0">Total Applications</h6>
                    </div>
                    <i class="fas fa-user-graduate fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card new">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-danger"><?php echo $stats['new_messages']; ?></div>
                        <h6 class="text-muted mb-0">New Messages</h6>
                    </div>
                    <i class="fas fa-bell fa-2x text-danger opacity-50"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="stat-card today">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-number text-warning"><?php echo $stats['today_messages'] + $stats['today_applications']; ?></div>
                        <h6 class="text-muted mb-0">Today's Activity</h6>
                    </div>
                    <i class="fas fa-calendar-day fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="row g-4">
        <!-- Recent Messages -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope me-2 text-primary"></i>Recent Messages
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (count($recent_messages) > 0): ?>
                        <?php foreach ($recent_messages as $message): ?>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($message['name']); ?></h6>
                                        <p class="mb-1 text-muted small"><?php echo htmlspecialchars($message['subject']); ?></p>
                                        <small class="text-muted"><?php echo date('M d, H:i', strtotime($message['created_at'])); ?></small>
                                    </div>
                                    <span class="badge bg-<?php echo $message['status'] === 'new' ? 'danger' : ($message['status'] === 'read' ? 'primary' : 'success'); ?>">
                                        <?php echo ucfirst($message['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center mt-3">
                            <a href="messages.php" class="btn btn-outline-primary btn-sm">View All Messages</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No messages yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Recent Applications -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2 text-success"></i>Recent Applications
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (count($recent_applications) > 0): ?>
                        <?php foreach ($recent_applications as $application): ?>
                            <div class="recent-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($application['full_name']); ?></h6>
                                        <p class="mb-1 text-muted small"><?php echo htmlspecialchars($application['course_of_interest']); ?></p>
                                        <small class="text-muted"><?php echo date('M d, H:i', strtotime($application['created_at'])); ?></small>
                                    </div>
                                    <span class="badge bg-<?php echo $application['status'] === 'pending' ? 'warning' : ($application['status'] === 'approved' ? 'success' : 'secondary'); ?>">
                                        <?php echo ucfirst($application['status']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center mt-3">
                            <a href="applications.php" class="btn btn-outline-success btn-sm">View All Applications</a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center py-3">No applications yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
