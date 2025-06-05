<?php
session_start();
$page_title = "Contact Messages - RTVC Admin";
require_once 'auth_check.php';
require_once '../config/database.php';

// Connect to database
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle message status updates
if (isset($_POST['action']) && isset($_POST['message_id'])) {
    $messageId = (int)$_POST['message_id'];
    $action = $_POST['action'];
    
    if ($action === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
        $stmt->execute([$messageId]);
    } elseif ($action === 'mark_replied') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
        $stmt->execute([$messageId]);
    } elseif ($action === 'archive') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'archived' WHERE id = ?");
        $stmt->execute([$messageId]);
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$messageId]);
    }
    
    // Redirect to prevent form resubmission
    header('Location: messages.php');
    exit;
}

// Get messages with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Filter by status if provided
$status = isset($_GET['status']) ? $_GET['status'] : null;
$statusFilter = $status ? "WHERE status = :status" : "";

// Count total messages
$countSql = "SELECT COUNT(*) FROM contact_messages $statusFilter";
$countStmt = $pdo->prepare($countSql);
if ($status) {
    $countStmt->bindParam(':status', $status);
}
$countStmt->execute();
$totalMessages = $countStmt->fetchColumn();
$totalPages = ceil($totalMessages / $perPage);

// Get messages for current page
$sql = "SELECT * FROM contact_messages $statusFilter ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
if ($status) {
    $stmt->bindParam(':status', $status);
}
$stmt->bindParam(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get message counts by status
$statusCounts = [];
$statusTypes = ['new', 'read', 'replied', 'archived'];
foreach ($statusTypes as $type) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM contact_messages WHERE status = ?");
    $countStmt->execute([$type]);
    $statusCounts[$type] = $countStmt->fetchColumn();
}
$statusCounts['all'] = $totalMessages;

// Set stats for sidebar
$stats = [];
$stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'");
$stats['new_messages'] = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'");
$stats['pending_applications'] = $stmt->fetchColumn();

$current_admin = getCurrentAdmin();

// Additional CSS for this page
$additional_css = "
<style>
    .message-row { 
        cursor: pointer; 
        transition: all 0.3s ease;
    }
    
    .message-row:hover { 
        background-color: #f8f9fa;
        transform: translateX(5px);
    }
    
    .status-badge.new { background-color: #dc3545; }
    .status-badge.read { background-color: #0d6efd; }
    .status-badge.replied { background-color: #198754; }
    .status-badge.archived { background-color: #6c757d; }
    
    .message-preview { 
        max-height: 50px; 
        overflow: hidden; 
        text-overflow: ellipsis; 
        white-space: nowrap; 
    }
    
    .filter-buttons .btn {
        border-radius: 20px;
        margin-right: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .export-section {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
</style>
";

include 'includes/admin_header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="col-md-10 main-content p-4">
    <!-- Page Header -->
    <div class="admin-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1"><i class="fas fa-envelope me-2 text-primary"></i>Contact Messages</h2>
                <p class="text-muted mb-0">Manage and respond to contact form submissions</p>
            </div>
            <div>
                <a href="export_messages.php" class="btn btn-outline-success">
                    <i class="fas fa-file-export me-2"></i> Export CSV
                </a>
            </div>
        </div>
    </div>
    
    <!-- Status Filters -->
    <div class="export-section">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
            <div class="filter-buttons">
                <a href="messages.php" class="btn btn-outline-secondary <?php echo !$status ? 'active' : ''; ?>">
                    <i class="fas fa-list me-1"></i> All 
                    <span class="badge bg-secondary"><?php echo $statusCounts['all']; ?></span>
                </a>
                <a href="messages.php?status=new" class="btn btn-outline-danger <?php echo $status === 'new' ? 'active' : ''; ?>">
                    <i class="fas fa-exclamation-circle me-1"></i> New 
                    <span class="badge bg-danger"><?php echo $statusCounts['new']; ?></span>
                </a>
                <a href="messages.php?status=read" class="btn btn-outline-primary <?php echo $status === 'read' ? 'active' : ''; ?>">
                    <i class="fas fa-eye me-1"></i> Read 
                    <span class="badge bg-primary"><?php echo $statusCounts['read']; ?></span>
                </a>
                <a href="messages.php?status=replied" class="btn btn-outline-success <?php echo $status === 'replied' ? 'active' : ''; ?>">
                    <i class="fas fa-reply me-1"></i> Replied 
                    <span class="badge bg-success"><?php echo $statusCounts['replied']; ?></span>
                </a>
                <a href="messages.php?status=archived" class="btn btn-outline-secondary <?php echo $status === 'archived' ? 'active' : ''; ?>">
                    <i class="fas fa-archive me-1"></i> Archived 
                    <span class="badge bg-secondary"><?php echo $statusCounts['archived']; ?></span>
                </a>
            </div>
            <div class="text-muted">
                <small><i class="fas fa-info-circle me-1"></i> Showing <?php echo count($messages); ?> of <?php echo $totalMessages; ?> messages</small>
            </div>
        </div>
    </div>
    
    <!-- Messages Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">Subject</th>
                            <th class="py-3">Message</th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($messages) > 0): ?>
                            <?php foreach ($messages as $message): ?>
                                <tr class="message-row" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $message['id']; ?>">
                                    <td class="px-4 py-3"><strong>#<?php echo $message['id']; ?></strong></td>
                                    <td class="py-3">
                                        <div class="fw-semibold"><?php echo htmlspecialchars($message['name']); ?></div>
                                    </td>
                                    <td class="py-3">
                                        <small class="text-muted"><?php echo htmlspecialchars($message['email']); ?></small>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-medium"><?php echo htmlspecialchars($message['subject']); ?></div>
                                    </td>
                                    <td class="py-3">
                                        <div class="message-preview text-muted">
                                            <?php echo htmlspecialchars(substr($message['message'], 0, 50)) . (strlen($message['message']) > 50 ? '...' : ''); ?>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?></small>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge status-badge <?php echo $message['status']; ?> text-white">
                                            <?php echo ucfirst($message['status']); ?>
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation();">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <?php if ($message['status'] === 'new'): ?>
                                                    <li>
                                                        <form method="post" action="messages.php" class="d-inline">
                                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                            <input type="hidden" name="action" value="mark_read">
                                                            <button type="submit" class="dropdown-item" onclick="event.stopPropagation();">
                                                                <i class="fas fa-check me-2"></i> Mark as Read
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($message['status'] === 'read'): ?>
                                                    <li>
                                                        <form method="post" action="messages.php" class="d-inline">
                                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                            <input type="hidden" name="action" value="mark_replied">
                                                            <button type="submit" class="dropdown-item" onclick="event.stopPropagation();">
                                                                <i class="fas fa-reply me-2"></i> Mark as Replied
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <?php if ($message['status'] !== 'archived'): ?>
                                                    <li>
                                                        <form method="post" action="messages.php" class="d-inline">
                                                            <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                            <input type="hidden" name="action" value="archive">
                                                            <button type="submit" class="dropdown-item" onclick="event.stopPropagation();">
                                                                <i class="fas fa-archive me-2"></i> Archive
                                                            </button>
                                                        </form>
                                                    </li>
                                                <?php endif; ?>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="post" action="messages.php" onsubmit="return confirm('Are you sure you want to delete this message?');" class="d-inline">
                                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                        <input type="hidden" name="action" value="delete">
                                                        <button type="submit" class="dropdown-item text-danger" onclick="event.stopPropagation();">
                                                            <i class="fas fa-trash me-2"></i> Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Message Modal -->
                                <div class="modal fade" id="messageModal<?php echo $message['id']; ?>" tabindex="-1" aria-labelledby="messageModalLabel<?php echo $message['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title" id="messageModalLabel<?php echo $message['id']; ?>">
                                                    <i class="fas fa-envelope me-2 text-primary"></i>Message from <?php echo htmlspecialchars($message['name']); ?>
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-muted">From:</label>
                                                            <p class="mb-0"><?php echo htmlspecialchars($message['name']); ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-muted">Email:</label>
                                                            <p class="mb-0">
                                                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="text-decoration-none">
                                                                    <?php echo htmlspecialchars($message['email']); ?>
                                                                </a>
                                                            </p>
                                                        </div>
                                                        <?php if (!empty($message['phone'])): ?>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-muted">Phone:</label>
                                                                <p class="mb-0">
                                                                    <a href="tel:<?php echo htmlspecialchars($message['phone']); ?>" class="text-decoration-none">
                                                                        <?php echo htmlspecialchars($message['phone']); ?>
                                                                    </a>
                                                                </p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-muted">Date:</label>
                                                            <p class="mb-0"><?php echo date('F d, Y H:i:s', strtotime($message['created_at'])); ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-muted">Status:</label>
                                                            <p class="mb-0">
                                                                <span class="badge status-badge <?php echo $message['status']; ?> text-white">
                                                                    <?php echo ucfirst($message['status']); ?>
                                                                </span>
                                                            </p>
                                                        </div>
                                                        <?php if (!empty($message['ip_address'])): ?>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold text-muted">IP Address:</label>
                                                                <p class="mb-0"><small class="text-muted"><?php echo htmlspecialchars($message['ip_address']); ?></small></p>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold text-muted">
                                                        <i class="fas fa-tag me-2"></i>Subject:
                                                    </label>
                                                    <div class="p-3 bg-light rounded">
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($message['subject']); ?></h6>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold text-muted">
                                                        <i class="fas fa-comment me-2"></i>Message:
                                                    </label>
                                                    <div class="p-3 bg-light rounded">
                                                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo htmlspecialchars($message['subject']); ?>" class="btn btn-success">
                                                    <i class="fas fa-reply me-2"></i> Reply via Email
                                                </a>
                                                <?php if ($message['status'] === 'new'): ?>
                                                    <form method="post" action="messages.php" class="d-inline">
                                                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                        <input type="hidden" name="action" value="mark_read">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-check me-2"></i> Mark as Read
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-2"></i> Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                                        <h5>No messages found</h5>
                                        <p class="mb-0">
                                            <?php if ($status): ?>
                                                No messages with status "<?php echo htmlspecialchars($status); ?>" found.
                                            <?php else: ?>
                                                No contact messages have been received yet.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="p-4 border-top">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                    <i class="fas fa-chevron-left me-1"></i> Previous
                                </a>
                            </li>
                            
                            <?php 
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);
                            
                            if ($start > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1<?php echo $status ? '&status=' . $status : ''; ?>">1</a>
                                </li>
                                <?php if ($start > 2): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . $status : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($end < $totalPages): ?>
                                <?php if ($end < $totalPages - 1): ?>
                                    <li class="page-item disabled"><span class="page-link">...</span></li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $totalPages; ?><?php echo $status ? '&status=' . $status : ''; ?>"><?php echo $totalPages; ?></a>
                                </li>
                            <?php endif; ?>
                            
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . $status : ''; ?>">
                                    Next <i class="fas fa-chevron-right ms-1"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Additional JavaScript for this page
$additional_js = "
<script>
    // Auto-mark message as read when modal is opened
    document.addEventListener('DOMContentLoaded', function() {
        const messageModals = document.querySelectorAll('.modal');
        messageModals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', function() {
                const messageId = this.id.replace('messageModal', '');
                const statusBadge = this.querySelector('.status-badge');
                
                if (statusBadge && statusBadge.textContent.trim().toLowerCase() === 'new') {
                    // Auto-mark as read when opened
                    fetch('update_message_status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: \`message_id=\${messageId}&action=mark_read\`
                    }).then(response => {
                        if (response.ok) {
                            statusBadge.textContent = 'Read';
                            statusBadge.className = 'badge status-badge read text-white';
                        }
                    });
                }
            });
        });
        
        // Add loading states to action buttons
        document.querySelectorAll('form[method=\"post\"]').forEach(form => {
            form.addEventListener('submit', function() {
                const button = this.querySelector('button[type=\"submit\"]');
                if (button) {
                    button.disabled = true;
                    button.innerHTML = '<i class=\"fas fa-spinner fa-spin me-2\"></i>Processing...';
                }
            });
        });
    });
</script>
";

include 'includes/admin_footer.php';
?>
