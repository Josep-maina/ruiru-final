<?php
session_start();

// Include database configuration
require_once '../config/database.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Check for logout message
if (isset($_GET['logout'])) {
    $success = 'You have been logged out successfully.';
}

// Check for session expired message
if (isset($_GET['expired'])) {
    $error = 'Your session has expired. Please login again.';
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Get database connection
        $pdo = getDatabaseConnection();
        
        if (!$pdo) {
            $error = 'Database connection failed. Please check your database configuration.';
        } else {
            try {
                // Initialize tables if they don't exist
                initializeTables();
                
                // Verify login credentials
                $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND is_active = 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password_hash'])) {
                    // Login successful
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    $_SESSION['admin_full_name'] = $user['full_name'];
                    $_SESSION['admin_role'] = $user['role'];
                    $_SESSION['last_activity'] = time();
                    
                    // Update last login
                    $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
                    $stmt->execute([$user['id']]);
                    
                    // Redirect to dashboard
                    header('Location: dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
                
            } catch (PDOException $e) {
                $error = 'Database error occurred. Please try again.';
                error_log("Admin login database error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RTVC</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header h2 {
            margin: 0;
            font-weight: 600;
        }
        
        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .default-credentials {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
        
        .default-credentials h6 {
            color: #198754;
            margin-bottom: 0.5rem;
        }
        
        .logo {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }
        
        .setup-link {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2>RTVC Admin</h2>
            <p>Administration Panel</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                </div>
                
                <button type="submit" class="btn btn-success btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>
            
            <div class="default-credentials">
                <h6><i class="fas fa-info-circle me-2"></i>Default Login Credentials:</h6>
                <p class="mb-1"><strong>Username:</strong> admin</p>
                <p class="mb-0"><strong>Password:</strong> admin123</p>
                <small class="text-muted">Please change these credentials after first login!</small>
            </div>
            
            <div class="setup-link">
                <small class="text-muted">
                    Having database issues? 
                    <a href="../setup.php" class="text-success">Run Database Setup</a>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-focus on username field
        document.getElementById('username').focus();
        
        // Add loading state to login button
        document.querySelector('form').addEventListener('submit', function() {
            const btn = document.querySelector('.btn-login');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Logging in...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
