<?php
// Get statistics for badges (if not already loaded)
if (!isset($stats)) {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Contact messages stats
        $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
        $stats['new_messages'] = $stmt->fetchColumn();
        
        // Applications stats  
        $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
        $stats['pending_applications'] = $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        $stats['new_messages'] = 0;
        $stats['pending_applications'] = 0;
    }
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="col-md-2 sidebar text-white py-4">
    <div class="text-center mb-4">
        <i class="fas fa-graduation-cap fa-2x mb-2"></i>
        <h4>RTVC Admin</h4>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page === 'index.php' || $current_page === 'admin.php') ? 'active' : ''; ?>"
                href="index.php">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'applications.php' ? 'active' : ''; ?>"
                href="applications.php">
                <i class="fas fa-user-graduate me-2"></i> Applications
                <?php if ($stats['pending_applications'] > 0): ?>
                <span class="badge bg-warning text-dark ms-2"><?php echo $stats['pending_applications']; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                <i class="fas fa-envelope me-2"></i> Messages
                <?php if ($stats['new_messages'] > 0): ?>
                <span class="badge bg-danger ms-2"><?php echo $stats['new_messages']; ?></span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'gallery_management.php' ? 'active' : ''; ?>" href="gallery_management.php">
                <i class="fas fa-image me-2"></i> Gallery Management
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'users.php' ? 'active' : ''; ?>" href="users.php">
                <i class="fas fa-users me-2"></i> Admin Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $current_page === 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        <li class="nav-item mt-5">
            <a class="nav-link" href="logout.php">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>