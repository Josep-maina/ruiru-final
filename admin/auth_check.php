<?php
// Authentication check file - include this at the top of protected admin pages


// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Check if session is still valid (optional - implement session timeout)
$session_timeout = 3600; // 1 hour in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $session_timeout) {
    // Session expired
    session_destroy();
    header('Location: login.php?expired=1');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Function to check if user has specific role
function hasRole($required_role) {
    $roles = ['staff' => 1, 'admin' => 2, 'super_admin' => 3];
    $user_role_level = $roles[$_SESSION['admin_role']] ?? 0;
    $required_role_level = $roles[$required_role] ?? 0;
    
    return $user_role_level >= $required_role_level;
}

// Function to get current admin info
function getCurrentAdmin() {
    return [
        'id' => $_SESSION['admin_id'],
        'username' => $_SESSION['admin_username'],
        'full_name' => $_SESSION['admin_full_name'],
        'role' => $_SESSION['admin_role']
    ];
}
?>
