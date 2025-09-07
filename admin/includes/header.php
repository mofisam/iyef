<?php
session_start();
require_once '../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'config/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'config/db_functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'includes/functions/users.php');

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get current user data
$currentUser = getUserById($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' | ' : '' ?>IYEF Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="http://localhost/web/IYEF/assets/css/admin.css">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body class="admin-body">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-lg-none" href="#">
                <img src="/assets/images/logo-white.png" alt="IYEF" height="30">
            </a>
            
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($currentUser['full_name']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="admin/profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>admin/logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <div class="wrapper">
        <?php require_once 'sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Page Header -->
            <header class="page-header bg-light shadow-sm py-3">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="page-title mb-0"><?= $page_title ?? 'Dashboard' ?></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>admin">Home</a></li>
                                <?php if (isset($breadcrumb)): ?>
                                   <?php foreach ($breadcrumb as $item): ?>
                                        <li class="breadcrumb-item <?= !empty($item['active']) ? 'active' : '' ?>">
                                            <?php if (!empty($item['active'])): ?>
                                                <?= $item['title'] ?>
                                            <?php elseif (isset($item['url'])): ?>
                                                <a href="<?= $item['title'] ?>"><?= $item['title'] ?></a>
                                            <?php else: ?>
                                                <?= $item['title'] ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>

                                <?php endif; ?>
                            </ol>
                        </nav>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="container-fluid py-4">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= $_SESSION['success_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= $_SESSION['error_message'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>