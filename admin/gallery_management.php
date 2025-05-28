<?php
session_start();
require_once '../config/database.php';
require_once 'auth_check.php';

$page_title = "Gallery Management - RTVC Admin";

// Handle file upload
$upload_message = '';
$upload_status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Handle image deletion
        if ($_POST['action'] === 'delete' && isset($_POST['image_id'])) {
            $image_id = (int)$_POST['image_id'];
            
            try {
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Get image info before deleting
                $stmt = $pdo->prepare("SELECT file_path FROM gallery_images WHERE id = ?");
                $stmt->execute([$image_id]);
                $image = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($image) {
                    // Delete the file from server
                    $file_path = '../' . $image['file_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                    
                    // Delete from database
                    $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
                    $stmt->execute([$image_id]);
                    
                    $upload_status = 'success';
                    $upload_message = 'Image deleted successfully.';
                } else {
                    $upload_status = 'danger';
                    $upload_message = 'Image not found.';
                }
            } catch (PDOException $e) {
                $upload_status = 'danger';
                $upload_message = 'Database error: ' . $e->getMessage();
            }
        }
        // Handle category update
        elseif ($_POST['action'] === 'update_category' && isset($_POST['image_id']) && isset($_POST['category'])) {
            $image_id = (int)$_POST['image_id'];
            $category = $_POST['category'];
            
            try {
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $pdo->prepare("UPDATE gallery_images SET category = ? WHERE id = ?");
                $stmt->execute([$category, $image_id]);
                
                $upload_status = 'success';
                $upload_message = 'Category updated successfully.';
            } catch (PDOException $e) {
                $upload_status = 'danger';
                $upload_message = 'Database error: ' . $e->getMessage();
            }
        }
        // Handle image details update
        elseif ($_POST['action'] === 'update_details' && isset($_POST['image_id'])) {
            $image_id = (int)$_POST['image_id'];
            $title = $_POST['title'];
            $description = $_POST['description'];
            
            try {
                $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                $stmt = $pdo->prepare("UPDATE gallery_images SET title = ?, description = ? WHERE id = ?");
                $stmt->execute([$title, $description, $image_id]);
                
                $upload_status = 'success';
                $upload_message = 'Image details updated successfully.';
            } catch (PDOException $e) {
                $upload_status = 'danger';
                $upload_message = 'Database error: ' . $e->getMessage();
            }
        }
    }
    // Handle new image upload
    elseif (isset($_FILES['image'])) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? 'campus';
        
        // Validate file
        if ($_FILES['image']['error'] === 0) {
            if (in_array($_FILES['image']['type'], $allowed_types)) {
                if ($_FILES['image']['size'] <= $max_size) {
                    // Create upload directory if it doesn't exist
                    $upload_dir = '../img/gallery/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    // Generate unique filename
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $new_filename = uniqid('gallery_') . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    $db_path = 'img/gallery/' . $new_filename;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                        try {
                            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            // Create gallery_images table if it doesn't exist
                            $pdo->exec("CREATE TABLE IF NOT EXISTS gallery_images (
                                id INT AUTO_INCREMENT PRIMARY KEY,
                                title VARCHAR(255) NOT NULL,
                                description TEXT,
                                file_path VARCHAR(255) NOT NULL,
                                category VARCHAR(50) NOT NULL,
                                uploaded_by INT,
                                upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                            )");
                            
                            $stmt = $pdo->prepare("INSERT INTO gallery_images (title, description, file_path, category, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$title, $description, $db_path, $category, $_SESSION['admin_id'] ?? 1]);
                            
                            $upload_status = 'success';
                            $upload_message = 'Image uploaded successfully.';
                        } catch (PDOException $e) {
                            $upload_status = 'danger';
                            $upload_message = 'Database error: ' . $e->getMessage();
                        }
                    } else {
                        $upload_status = 'danger';
                        $upload_message = 'Failed to move uploaded file.';
                    }
                } else {
                    $upload_status = 'danger';
                    $upload_message = 'File size exceeds the limit of 5MB.';
                }
            } else {
                $upload_status = 'danger';
                $upload_message = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.';
            }
        } else {
            $upload_status = 'danger';
            $upload_message = 'Error uploading file: ' . $_FILES['image']['error'];
        }
    }
}

// Get filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query
$where_conditions = [];
$params = [];

if ($category_filter !== 'all') {
    $where_conditions[] = "category = ?";
    $params[] = $category_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(title LIKE ? OR description LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param]);
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if table exists
    $table_exists = false;
    $stmt = $pdo->query("SHOW TABLES LIKE 'gallery_images'");
    if ($stmt->rowCount() > 0) {
        $table_exists = true;
    }
    
    if (!$table_exists) {
        // Create gallery_images table
        $pdo->exec("CREATE TABLE IF NOT EXISTS gallery_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            file_path VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL,
            uploaded_by INT,
            upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }
    
    // Get total count
    $count_sql = "SELECT COUNT(*) FROM gallery_images $where_clause";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_records = $count_stmt->fetchColumn();
    $total_pages = ceil($total_records / $per_page);
    
    // Get images
    $sql = "SELECT * FROM gallery_images $where_clause ORDER BY upload_date DESC LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get categories for filter
    $categories_sql = "SELECT DISTINCT category FROM gallery_images ORDER BY category";
    $categories_stmt = $pdo->query($categories_sql);
    $categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);
    
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
    .gallery-card {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        height: 250px;
        margin-bottom: 20px;
    }
    
    .gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }
    
    .gallery-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .gallery-card:hover .gallery-image {
        transform: scale(1.05);
    }
    
    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(25, 135, 84, 0.9), rgba(255, 193, 7, 0.8));
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s ease;
    }
    
    .gallery-card:hover .gallery-overlay {
        opacity: 1;
    }
    
    .gallery-content {
        text-align: center;
        color: white;
        padding: 1rem;
    }
    
    .gallery-content h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .gallery-content p {
        font-size: 0.9rem;
        margin-bottom: 1rem;
        opacity: 0.9;
    }
    
    .upload-zone {
        border: 2px dashed #198754;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }
    
    .upload-zone:hover, .upload-zone.dragover {
        background: rgba(25, 135, 84, 0.1);
        border-color: #198754;
    }
    
    .filter-section {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .category-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        background: #e9ecef;
        color: #495057;
    }
    
    .category-badge.courses { background: #d1e7dd; color: #0f5132; }
    .category-badge.events { background: #cfe2ff; color: #084298; }
    .category-badge.campus { background: #f8d7da; color: #842029; }
    .category-badge.sports { background: #fff3cd; color: #664d03; }
    
    .main-content {
        background: #f8f9fa;
        min-height: 100vh;
    }
</style>
";

include 'includes/admin_header.php';
?>

<?php include 'includes/sidebar.php'; ?>

<!-- Main Content -->
<div class="col-md-10 main-content p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-images me-2"></i>Gallery Management</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-upload me-2"></i>Upload New Images
        </button>
    </div>

    <?php if (!empty($upload_message)): ?>
        <div class="alert alert-<?php echo $upload_status; ?> alert-dismissible fade show">
            <?php echo $upload_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Upload Zone -->
    <div class="upload-zone" id="dropZone">
        <i class="fas fa-cloud-upload-alt fa-3x text-success mb-3"></i>
        <h4>Drag & Drop Images Here</h4>
        <p class="text-muted">or click the Upload button above</p>
    </div>

    <!-- Filters -->
    <div class="filter-section">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="all" <?php echo $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" 
                                <?php echo $category_filter === $category ? 'selected' : ''; ?>>
                            <?php echo ucfirst(htmlspecialchars($category)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Search by title or description" 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="gallery_management.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Gallery Grid -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                Gallery Images (<?php echo $total_records; ?> total)
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php if (empty($images)): ?>
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No images found. Upload some images to get started!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($images as $image): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="gallery-card">
                                <img src="../<?php echo htmlspecialchars($image['file_path']); ?>" alt="<?php echo htmlspecialchars($image['title']); ?>" class="gallery-image">
                                <div class="gallery-overlay">
                                    <div class="gallery-content">
                                        <h5><?php echo htmlspecialchars($image['title']); ?></h5>
                                        <p><?php echo htmlspecialchars(substr($image['description'], 0, 50)) . (strlen($image['description']) > 50 ? '...' : ''); ?></p>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-light" onclick="viewImage(<?php echo $image['id']; ?>, '<?php echo htmlspecialchars(addslashes($image['title'])); ?>', '<?php echo htmlspecialchars(addslashes($image['description'])); ?>', '<?php echo htmlspecialchars($image['file_path']); ?>', '<?php echo htmlspecialchars($image['category']); ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light" onclick="editImage(<?php echo $image['id']; ?>, '<?php echo htmlspecialchars(addslashes($image['title'])); ?>', '<?php echo htmlspecialchars(addslashes($image['description'])); ?>', '<?php echo htmlspecialchars($image['category']); ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-light" onclick="confirmDelete(<?php echo $image['id']; ?>, '<?php echo htmlspecialchars(addslashes($image['title'])); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <span class="position-absolute top-0 end-0 m-2 category-badge <?php echo htmlspecialchars($image['category']); ?>">
                                    <?php echo ucfirst(htmlspecialchars($image['category'])); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page-1; ?>&category=<?php echo $category_filter; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&category=<?php echo $category_filter; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page+1; ?>&category=<?php echo $category_filter; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload New Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="gallery_management.php" method="post" enctype="multipart/form-data" id="uploadForm">
                    <div class="mb-3">
                        <label for="image" class="form-label">Select Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <small class="text-muted">Max file size: 5MB. Allowed formats: JPG, PNG, GIF, WebP</small>
                    </div>
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="courses">Courses</option>
                            <option value="events">Events</option>
                            <option value="campus" selected>Campus</option>
                            <option value="sports">Sports</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- View Image Modal -->
<div class="modal fade" id="viewImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewImageTitle">Image Title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="/placeholder.svg" alt="" id="viewImageSrc" class="img-fluid rounded mb-3" style="max-height: 500px;">
                <p id="viewImageDescription" class="text-muted"></p>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="category-badge" id="viewImageCategory">Category</span>
                    <small class="text-muted">Click image to enlarge</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editImageBtn">
                    <i class="fas fa-edit me-2"></i>Edit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Image Modal -->
<div class="modal fade" id="editImageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Image Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="gallery_management.php" method="post" id="editForm">
                    <input type="hidden" name="action" value="update_details">
                    <input type="hidden" name="image_id" id="editImageId">
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editCategory" class="form-label">Category</label>
                        <select class="form-select" id="editCategory" name="category" required>
                            <option value="courses">Courses</option>
                            <option value="events">Events</option>
                            <option value="campus">Campus</option>
                            <option value="sports">Sports</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the image "<span id="deleteImageTitle"></span>"?</p>
                <p class="text-danger"><strong>Warning:</strong> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="gallery_management.php" method="post">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="image_id" id="deleteImageId">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$additional_js = "
<script>
    // Drag and drop functionality
    const dropZone = document.getElementById('dropZone');
    
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            // Open upload modal and set the file
            const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
            document.getElementById('image').files = files;
            uploadModal.show();
        }
    });
    
    dropZone.addEventListener('click', function() {
        const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
        uploadModal.show();
    });
    
    // View image
    function viewImage(id, title, description, filePath, category) {
        document.getElementById('viewImageTitle').textContent = title;
        document.getElementById('viewImageSrc').src = '../' + filePath;
        document.getElementById('viewImageSrc').alt = title;
        document.getElementById('viewImageDescription').textContent = description;
        
        const categoryBadge = document.getElementById('viewImageCategory');
        categoryBadge.textContent = category.charAt(0).toUpperCase() + category.slice(1);
        categoryBadge.className = 'category-badge ' + category;
        
        // Set up edit button
        document.getElementById('editImageBtn').onclick = function() {
            // Hide view modal and show edit modal
            bootstrap.Modal.getInstance(document.getElementById('viewImageModal')).hide();
            editImage(id, title, description, category);
        };
        
        const viewModal = new bootstrap.Modal(document.getElementById('viewImageModal'));
        viewModal.show();
    }
    
    // Edit image
    function editImage(id, title, description, category) {
        document.getElementById('editImageId').value = id;
        document.getElementById('editTitle').value = title;
        document.getElementById('editDescription').value = description;
        document.getElementById('editCategory').value = category;
        
        const editModal = new bootstrap.Modal(document.getElementById('editImageModal'));
        editModal.show();
    }
    
    // Confirm delete
    function confirmDelete(id, title) {
        document.getElementById('deleteImageId').value = id;
        document.getElementById('deleteImageTitle').textContent = title;
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Image preview before upload
    document.getElementById('image').addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You could add preview functionality here if needed
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Form validation
    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('image');
        const titleInput = document.getElementById('title');
        
        if (!fileInput.files.length) {
            e.preventDefault();
            alert('Please select an image to upload.');
            return false;
        }
        
        if (!titleInput.value.trim()) {
            e.preventDefault();
            alert('Please enter a title for the image.');
            return false;
        }
        
        // Show loading state
        const submitBtn = this.querySelector('button[type=\"submit\"]');
        submitBtn.innerHTML = '<i class=\"fas fa-spinner fa-spin me-2\"></i>Uploading...';
        submitBtn.disabled = true;
    });
</script>
";

include 'includes/admin_footer.php';
?>
