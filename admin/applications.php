<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

$page_title = "Applications Management - RTVC Admin";

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$course_filter = isset($_GET['course']) ? $_GET['course'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_conditions = [];
$params = [];

if ($status_filter !== 'all') {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if ($course_filter !== 'all') {
    $where_conditions[] = "course_of_interest = ?";
    $params[] = $course_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(full_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM applications $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $per_page);
    
    // Get applications
    $sql = "SELECT * FROM applications $where_clause ORDER BY application_date DESC LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get courses for filter
    $courses_sql = "SELECT DISTINCT course_of_interest FROM applications WHERE course_of_interest IS NOT NULL AND course_of_interest != '' ORDER BY course_of_interest";
    $courses_stmt = $pdo->query($courses_sql);
    $courses = $courses_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Get stats for sidebar
    $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
    $stats['new_messages'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
    $stats['pending_applications'] = $stmt->fetchColumn();
    
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

$additional_css = "
<style>
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    .status-pending { background: #fff3cd; color: #856404; }
    .status-approved { background: #d1edff; color: #0c5460; }
    .status-rejected { background: #f8d7da; color: #721c24; }
    .status-under-review { background: #e2e3e5; color: #383d41; }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    .btn-action {
        padding: 0.25rem 0.5rem;
        margin: 0.125rem;
        border-radius: 0.375rem;
    }
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .main-content {
        background: #f8f9fa;
        min-height: 100vh;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
</style>
";

include 'includes/admin_header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="col-md-10 main-content p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-file-alt me-2"></i>Applications Management</h2>
        <button class="btn btn-primary" onclick="exportApplications()">
            <i class="fas fa-download me-2"></i>Export CSV
        </button>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="under_review" <?php echo $status_filter === 'under_review' ? 'selected' : ''; ?>>Under Review</option>
                    <option value="approved" <?php echo $status_filter === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Course</label>
                <select name="course" class="form-select">
                    <option value="all">All Courses</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo htmlspecialchars($course); ?>" 
                                <?php echo $course_filter === $course ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by name, email, or phone" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="applications.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Applications Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                Applications (<?php echo $total_records; ?> total)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($applications)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No applications found</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td>#<?php echo $app['id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($app['full_name']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($app['email']); ?></td>
                                    <td><?php echo htmlspecialchars($app['phone_number']); ?></td>
                                    <td><?php echo htmlspecialchars($app['course_of_interest']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo str_replace('_', '-', $app['status']); ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $app['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($app['application_date'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-action" 
                                                onclick="viewApplication(<?php echo $app['id']; ?>)"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle btn-action" 
                                                    data-bs-toggle="dropdown"
                                                    title="Change Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $app['id']; ?>, 'pending')">
                                                    <i class="fas fa-clock text-warning me-2"></i>Pending
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $app['id']; ?>, 'under_review')">
                                                    <i class="fas fa-search text-info me-2"></i>Under Review
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $app['id']; ?>, 'approved')">
                                                    <i class="fas fa-check text-success me-2"></i>Approved
                                                </a></li>
                                                <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $app['id']; ?>, 'rejected')">
                                                    <i class="fas fa-times text-danger me-2"></i>Rejected
                                                </a></li>
                                            </ul>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger btn-action" 
                                                onclick="deleteApplication(<?php echo $app['id']; ?>)"
                                                title="Delete Application">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page-1; ?>&status=<?php echo $status_filter; ?>&course=<?php echo $course_filter; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&course=<?php echo $course_filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page+1; ?>&status=<?php echo $status_filter; ?>&course=<?php echo $course_filter; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Application Details Modal -->
<div class="modal fade" id="applicationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="applicationDetails">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading application details...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = "
<script>
    function viewApplication(id) {
        // Show modal with loading state
        const modal = new bootstrap.Modal(document.getElementById('applicationModal'));
        modal.show();
        
        fetch(`get_application.php?id=\${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const app = data.application;
                    document.getElementById('applicationDetails').innerHTML = `
                        <div class=\"row\">
                            <div class=\"col-md-6\">
                                <h6><i class=\"fas fa-user me-2\"></i>Personal Information</h6>
                                <p><strong>Name:</strong> \${app.full_name || 'Not provided'}</p>
                                <p><strong>Email:</strong> <a href=\"mailto:\${app.email}\">\${app.email || 'Not provided'}</a></p>
                                <p><strong>Phone:</strong> <a href=\"tel:\${app.phone}\">\${app.phone || 'Not provided'}</a></p>
                                <p><strong>Date of Birth:</strong> \${app.date_of_birth}</p>
                                <p><strong>Gender:</strong> \${app.gender}</p>
                                <p><strong>ID Number:</strong> \${app.id_number}</p>
                            </div>
                            <div class=\"col-md-6\">
                                <h6><i class=\"fas fa-graduation-cap me-2\"></i>Application Details</h6>
                                <p><strong>Course:</strong> \${app.course_of_interest || 'Not specified'}</p>
                                <p><strong>Status:</strong> <span class=\"status-badge status-\${app.status.replace('_', '-')}\">\${app.status.replace('_', ' ').toUpperCase()}</span></p>
                                <p><strong>Applied:</strong> \${new Date(app.application_date).toLocaleDateString()}</p>
                                <p><strong>Address:</strong> \${app.address}</p>
                                <p><strong>Emergency Contact:</strong> \${app.emergency_contact}</p>
                                <p><strong>Emergency Phone:</strong> \${app.emergency_phone}</p>
                            </div>
                        </div>
                        <div class=\"row mt-3\">
                            <div class=\"col-12\">
                                <h6><i class=\"fas fa-school me-2\"></i>Education Background</h6>
                                <p><strong>Previous School:</strong> \${app.previous_school}</p>
                                <p><strong>Year Completed:</strong> \${app.year_completed}</p>
                                <p><strong>Grades:</strong> \${app.grades}</p>
                            </div>
                        </div>
                    `;
                } else {
                    document.getElementById('applicationDetails').innerHTML = `
                        <div class=\"alert alert-danger\">
                            <i class=\"fas fa-exclamation-triangle me-2\"></i>
                            Error loading application details: \${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('applicationDetails').innerHTML = `
                    <div class=\"alert alert-danger\">
                        <i class=\"fas fa-exclamation-triangle me-2\"></i>
                        Error loading application details. Please try again.
                    </div>
                `;
            });
    }

    function updateStatus(id, status) {
        if (confirm(`Are you sure you want to change the status to \${status.replace('_', ' ')}?`)) {
            // Show loading state
            const button = event.target.closest('button');
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i>';
            button.disabled = true;
            
            fetch('update_application_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', 'Status updated successfully!');
                    // Reload page after short delay
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', 'Error updating status: ' + data.message);
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Error updating status. Please try again.');
                button.innerHTML = originalHtml;
                button.disabled = false;
            });
        }
    }

    function deleteApplication(id) {
        if (confirm('Are you sure you want to delete this application? This action cannot be undone.')) {
            // Show loading state
            const button = event.target.closest('button');
            const originalHtml = button.innerHTML;
            button.innerHTML = '<i class=\"fas fa-spinner fa-spin\"></i>';
            button.disabled = true;
            
            fetch('delete_application.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', 'Application deleted successfully!');
                    // Reload page after short delay
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', 'Error deleting application: ' + data.message);
                    button.innerHTML = originalHtml;
                    button.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Error deleting application. Please try again.');
                button.innerHTML = originalHtml;
                button.disabled = false;
            });
        }
    }

    function exportApplications() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'csv');
        window.location.href = 'export_applications.php?' + params.toString();
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-\${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            \${message}
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\"></button>
        `;
        
        // Insert at the top of main content
        const mainContent = document.querySelector('.main-content');
        mainContent.insertBefore(alertDiv, mainContent.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>
";

include 'includes/admin_footer.php';
?>
