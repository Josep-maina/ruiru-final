<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'RTVC Admin'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar {
            background: linear-gradient(135deg, #198754, #20c997);
            min-height: 100vh;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
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
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
            border-left: 4px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card.messages { border-left-color: #0d6efd; }
        .stat-card.applications { border-left-color: #198754; }
        .stat-card.new { border-left-color: #dc3545; }
        .stat-card.today { border-left-color: #ffc107; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .recent-item {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .recent-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .admin-header {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
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
        .status-active { background: #d1edff; color: #0c5460; }
        .status-inactive { background: #f8d7da; color: #721c24; }
        
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
        
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .role-admin { background: #e7f3ff; color: #0066cc; }
        .role-moderator { background: #fff3cd; color: #856404; }
        .role-viewer { background: #e2e3e5; color: #383d41; }
        
        .nav-pills .nav-link {
            border-radius: 10px;
            margin-right: 0.5rem;
        }
        
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #198754, #20c997);
        }
    </style>
    <?php if (isset($additional_css)): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
