<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

$page_title = "Users Management - RTVC Admin";

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if ($action === 'add_user') {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
$password_raw = trim($_POST['password']);
$password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
            $role = $_POST['role'];
            $full_name = trim($_POST['full_name']);
            
            // Check if username or email already exists
            $check_sql = "SELECT COUNT(*) FROM admin_users WHERE username = ? OR email = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$username, $email]);
            
            if ($check_stmt->fetchColumn() > 0) {
                $error = "Username or email already exists";
            } else {
                $sql = "INSERT INTO admin_users (username, email, password_hash, role, full_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $email, $password_hash, $role, $full_name]);
                $success = "User added successfully";
            }
        }
        
        if ($action === 'update_user') {
            $user_id = $_POST['user_id'];
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $role = $_POST['role'];
            $full_name = trim($_POST['full_name']);
            $status = $_POST['status'];
            
            $sql = "UPDATE admin_users SET username = ?, email = ?, role = ?, full_name = ?, status = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $email, $role, $full_name, $status, $user_id]);
            $success = "User updated successfully";
        }
        
        if ($action === 'delete_user') {
            $user_id = $_POST['user_id'];
            
            // Don't allow deleting the current user
            if ($user_id == $_SESSION['admin_id']) {
                $error = "You cannot delete your own account";
            } else {
                $sql = "DELETE FROM admin_users WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$user_id]);
                $success = "User deleted successfully";
            }
        }
        
        if ($action === 'reset_password') {
            $user_id = $_POST['user_id'];
            $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            
            $sql = "UPDATE admin_users SET password_hash = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$new_password, $user_id]);
            $success = "Password reset successfully";
        }
        
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Get users
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM admin_users ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
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
                <h2><i class="fas fa-users me-2"></i>Users Management</h2>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-plus me-2"></i>Add User
                </button>
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

            <!-- Users Table -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Admin Users</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No users found</p>
                                    </td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td>#<?php echo $user['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['full_name'] ?? $user['username']); ?></strong>
                                        <?php if ($user['id'] == $_SESSION['admin_id']): ?>
                                        <span class="badge bg-info ms-2">You</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="role-badge role-<?php echo $user['role'] ?? 'admin'; ?>">
                                            <?php echo ucfirst($user['role'] ?? 'admin'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $user['status'] ?? 'active'; ?>">
                                            <?php echo ucfirst($user['status'] ?? 'active'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                                    if ($user['last_login']) {
                                                        echo date('M j, Y g:i A', strtotime($user['last_login']));
                                                    } else {
                                                        echo 'Never';
                                                    }
                                                    ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="editUser(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning"
                                            onclick="resetPassword(<?php echo $user['id']; ?>)">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_user">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="moderator">Moderator</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="moderator">Moderator</option>
                            <option value="viewer">Viewer</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="reset_password">
                    <input type="hidden" name="user_id" id="reset_user_id">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        The user will need to use this new password to log in.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
    $additional_js = <<<HTML
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_full_name').value = user.full_name || '';
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('edit_role').value = user.role || 'admin';
            document.getElementById('edit_status').value = user.status || 'active';
            
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        function resetPassword(userId) {
            document.getElementById('reset_user_id').value = userId;
            new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
        }

        function deleteUser(userId, username) {
            if (confirm(`Are you sure you want to delete user "\${username}"? This action cannot be undone.`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="user_id" value="\${userId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
HTML;
    ?>

<?php include 'includes/admin_footer.php'; ?>