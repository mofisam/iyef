<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once 'config/db.php';
require_once 'config/db_functions.php';

// Fetch site settings from database
$settings_query = "SELECT * FROM settings LIMIT 1";
$settings_result = $conn->query($settings_query);
$settings = $settings_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?><?php echo $settings['site_name'] ?? 'INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION (IYEF)'; ?></title>
    <meta name="description" content="<?php echo $settings['meta_description'] ?? 'Empowering youth through education, skills training, and mentorship to reach their full potential.'; ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar bg-primary text-white py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="contact-info">
                <span class="me-3"><i class="fas fa-phone me-1"></i> <?php echo $settings['contact_phone'] ?? '+1234567890'; ?></span>
                <span><i class="fas fa-envelope me-1"></i> <?php echo $settings['contact_email'] ?? 'info@iyef.org'; ?></span>
            </div>
            <div class="social-links">
                <?php if (!empty($settings['facebook_url'])): ?>
                    <a href="<?php echo $settings['facebook_url']; ?>" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['twitter_url'])): ?>
                    <a href="<?php echo $settings['twitter_url']; ?>" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                <?php endif; ?>
                <?php if (!empty($settings['instagram_url'])): ?>
                    <a href="<?php echo $settings['instagram_url']; ?>" class="text-white"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL ?>">
                <?php if (!empty($settings['site_logo'])): ?>
                    <img src="<?php echo BASE_URL . $settings['site_logo']; ?>" alt="<?php echo $settings['site_name'] ?? 'IYEF'; ?>" height="60">
                <?php else: ?>
                    <span class="fw-bold text-primary">INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION (IYEF)</span>
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About Us</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="programsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Programs
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="programsDropdown">
                            <li><a class="dropdown-item" href="programs.php">All Programs</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <?php
                            // Fetch active programs for dropdown
                            $programs_query = "SELECT id, title, slug FROM programs ORDER BY title ASC";
                            $programs_result = $conn->query($programs_query);
                            while ($program = $programs_result->fetch_assoc()) {
                                echo '<li><a class="dropdown-item" href="program.php?slug=' . $program['slug'] . '">' . $program['title'] . '</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="blog.php">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i> My Account
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="dashboard.php">Dashboard</a></li>
                                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-primary ms-2" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary ms-2 text-white" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
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