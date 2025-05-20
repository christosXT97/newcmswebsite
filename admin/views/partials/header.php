<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?> - <?= h($settings['site_name'] ?? 'Enhanced CMS') ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    
    <style>
        /* Basic admin styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
        }
        
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            color: #fff;
            z-index: 1000;
            transition: all 0.3s;
        }
        
        .content {
            margin-left: 260px;
            transition: all 0.3s;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -260px;
            }
            
            .content {
                margin-left: 0;
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .content.active {
                margin-left: 260px;
            }
        }
        
        .nav-link {
            color: rgba(255, 255, 255, 0.75);
            border-radius: 0;
            padding: 0.75rem 1.25rem;
        }
        
        .nav-link:hover {
            color: rgba(255, 255, 255, 0.95);
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.15);
            font-weight: 500;
        }
        
        .nav-link i {
            margin-right: 0.5rem;
            width: 20px;
            text-align: center;
        }
        
        .nav-divider {
            height: 0;
            margin: 0.75rem 0;
            overflow: hidden;
            border-top: 1px solid rgba(255, 255, 255, 0.15);
        }
        
        .sidebar-header {
            padding: 1.5rem 1.25rem;
            background-color: rgba(0, 0, 0, 0.2);
        }
        
        .sidebar-footer {
            padding: 1rem 1.25rem;
            background-color: rgba(0, 0, 0, 0.2);
            position: absolute;
            bottom: 0;
            width: 100%;
        }
        
        .navbar {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .dropdown-menu {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .table th {
            font-weight: 600;
        }
        
        .badge {
            font-weight: 500;
        }
        
        /* Custom styles for form elements */
        .form-control:focus, .form-select:focus, .btn:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        
        /* Card styles */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .stat-card {
            padding: 1.5rem;
            border-radius: 0.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card .icon {
            position: absolute;
            right: 1rem;
            bottom: 1rem;
            font-size: 3rem;
            opacity: 0.2;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .stat-label {
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            color: rgba(0, 0, 0, 0.6);
        }
        
        /* Custom TinyMCE styles */
        .tox-tinymce {
            border-radius: 0.375rem !important;
        }
        
        /* Datepicker styles */
        .flatpickr-calendar {
            border-radius: 0.375rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        /* User profile image */
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0 d-flex align-items-center">
                <i class="bi bi-grid-1x2-fill me-2"></i>
                <span><?= h($settings['site_name'] ?? 'Enhanced CMS') ?></span>
            </h4>
            <p class="text-muted mb-0 small">Administration Panel</p>
        </div>
        
        <div class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="index.php?page=dashboard" class="nav-link <?= $page === 'dashboard' ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                
                <div class="nav-divider"></div>
                <li class="nav-header small text-uppercase px-3 py-2">
                    <span>Content</span>
                </li>
                
                <li class="nav-item">
                    <a href="index.php?page=articles" class="nav-link <?= $page === 'articles' ? 'active' : '' ?>">
                        <i class="bi bi-file-earmark-text"></i> Articles
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="index.php?page=categories" class="nav-link <?= $page === 'categories' ? 'active' : '' ?>">
                        <i class="bi bi-folder"></i> Categories
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="index.php?page=media" class="nav-link <?= $page === 'media' ? 'active' : '' ?>">
                        <i class="bi bi-images"></i> Media Library
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="index.php?page=menus" class="nav-link <?= $page === 'menus' ? 'active' : '' ?>">
                        <i class="bi bi-list"></i> Navigation Menus
                    </a>
                </li>
                
                <div class="nav-divider"></div>
                <li class="nav-header small text-uppercase px-3 py-2">
                    <span>Administration</span>
                </li>
                
                <li class="nav-item">
                    <a href="index.php?page=users" class="nav-link <?= $page === 'users' ? 'active' : '' ?>">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="index.php?page=settings" class="nav-link <?= $page === 'settings' ? 'active' : '' ?>">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="sidebar-footer">
            <div class="d-flex align-items-center mb-2">
                <div class="me-2">
                    <img src="https://via.placeholder.com/36" alt="User Avatar" class="user-avatar">
                </div>
                <div>
                    <div class="fw-semibold"><?= h($_SESSION['admin_username'] ?? 'Admin') ?></div>
                    <div class="text-muted small"><?= h($_SESSION['admin_role'] ?? 'Administrator') ?></div>
                </div>
            </div>
            <div class="d-grid">
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="content">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white py-3">
            <div class="container-fluid">
                <button id="sidebarToggle" class="btn btn-sm btn-outline-secondary d-md-none me-3">
                    <i class="bi bi-list"></i>
                </button>
                
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                    <li class="breadcrumb-item active"><?= h($page_title) ?></li>
                </ol>
                
                <div class="ms-auto d-flex align-items-center">
                    <a href="../index.php" class="btn btn-sm btn-outline-primary me-2" target="_blank">
                        <i class="bi bi-eye"></i> View Site
                    </a>
                    
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="quickActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-plus-circle"></i> Quick Actions
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="quickActionsDropdown">
                            <li><a class="dropdown-item" href="index.php?page=articles&action=new"><i class="bi bi-file-earmark-plus me-2"></i> New Article</a></li>
                            <li><a class="dropdown-item" href="index.php?page=categories&action=new"><i class="bi bi-folder-plus me-2"></i> New Category</a></li>
                            <li><a class="dropdown-item" href="index.php?page=media&action=upload"><i class="bi bi-upload me-2"></i> Upload Media</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="index.php?page=users&action=new"><i class="bi bi-person-plus me-2"></i> New User</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->