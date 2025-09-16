<?php

// Verify admin access with multiple checks
if (!isset($_SESSION['user_id'], $_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: /admin/login.php');
    exit;
}

// Define BASE_URL if not already defined
defined('BASE_URL') or define('BASE_URL', '/');

try {
    // Get site settings including logo from database
    $settings = fetchSingle("SELECT * FROM settings LIMIT 1");
    
    // Initialize unread counts with default values
    $unreadCounts = [
        'volunteers' => 0,
        'registrations' => 0,
        'donations' => 0,
        'messages' => 0
    ];

    // Safely get unread counts with error handling
    $unreadCounts['volunteers'] = fetchSingle("SELECT COUNT(*) as count FROM volunteers WHERE viewed = 0")['count'] ?? 0;
    
    $registrationsCount = fetchSingle("
        SELECT COUNT(*) as count FROM (
            SELECT id FROM program_registrations WHERE viewed = 0
            UNION ALL
            SELECT id FROM event_registrations WHERE viewed = 0
        ) AS combined
    ");
    $unreadCounts['registrations'] = $registrationsCount['count'] ?? 0;
    
    $unreadCounts['donations'] = fetchSingle("SELECT COUNT(*) as count FROM donations WHERE receipt_sent = 0")['count'] ?? 0;
    
    // Add contact messages count if messages table exists
    try {
        $unreadCounts['messages'] = fetchSingle("SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0")['count'] ?? 0;
    } catch (Exception $e) {
        error_log("Contact messages table not accessible: " . $e->getMessage());
    }

} catch (Exception $e) {
    // Log error but continue with default values
    error_log("Database error in sidebar: " . $e->getMessage());
    $settings = ['site_name' => 'IYEF ADMIN'];
    $unreadCounts = array_fill_keys(array_keys($unreadCounts), 0);
}
?>

<!-- Sidebar -->
<div class="sidebar bg-dark text-white" id="sidebar">
    <div class="sidebar-header text-center py-4">
        <a href="<?= BASE_URL ?>admin" class="d-block text-decoration-none">
            <?php if (!empty($settings['site_logo'])): ?>
                <img src="<?= htmlspecialchars(BASE_URL . ltrim($settings['site_logo'], '/'), ENT_QUOTES) ?>" 
                     alt="<?= htmlspecialchars($settings['site_name'] ?? 'IYEF Admin') ?>" 
                     height="40"
                     onerror="this.src='<?= BASE_URL ?>assets/images/IYEF_logo.jpg'">
            <?php else: ?>
                <div class="text-white fw-bold fs-4">IYEF ADMIN</div>
            <?php endif; ?>
            <h5 class="mt-2 mb-0 text-truncate px-2"><?= htmlspecialchars($settings['site_name'] ?? 'IYEF ADMIN') ?></h5>
        </a>
    </div>
    
    <div class="sidebar-menu-container">
        <div class="sidebar-menu">
            <ul class="list-unstyled">
                <!-- Dashboard -->
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                        <?php if (array_sum($unreadCounts) > 0): ?>
                            <span class="badge bg-danger float-end"><?= array_sum($unreadCounts) ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- Content Management -->
                <li class="sidebar-title">Content Management</li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/pages.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'pages.php' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt me-2"></i> Pages
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/blog/posts.php" class="sidebar-link <?= strpos($_SERVER['PHP_SELF'], '/admin/blog/') !== false ? 'active' : '' ?>">
                        <i class="fas fa-blog me-2"></i> Blog
                    </a>
                    <ul class="sidebar-submenu">
                        <li><a href="<?= BASE_URL ?>admin/blog/posts.php">All Posts</a></li>
                        <li><a href="<?= BASE_URL ?>admin/blog/categories.php">Categories</a></li>
                        <li><a href="<?= BASE_URL ?>admin/blog/add-post.php">Add New</a></li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/faqs.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'faqs.php' ? 'active' : '' ?>">
                        <i class="fas fa-question-circle me-2"></i> FAQs
                    </a>
                </li>
                
                <!-- Programs & Events -->
                <li class="sidebar-title">Programs & Events</li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/programs.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'programs.php' ? 'active' : '' ?>">
                        <i class="fas fa-project-diagram me-2"></i> Programs
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/events.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'events.php' ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt me-2"></i> Events
                    </a>
                </li>
                
                <!-- Registrations -->
                <li class="sidebar-title">Registrations</li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/program-registrations.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'program-registrations.php' ? 'active' : '' ?>">
                        <i class="fas fa-users me-2"></i> Program Registrations
                        <?php if ($unreadCounts['registrations'] > 0): ?>
                            <span class="badge bg-danger float-end"><?= $unreadCounts['registrations'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/event-registrations.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'event-registrations.php' ? 'active' : '' ?>">
                        <i class="fas fa-user-check me-2"></i> Event Registrations
                    </a>
                </li>
                
                <!-- Volunteers & Donations -->
                <li class="sidebar-title">Engagement</li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/volunteers.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'volunteers.php' ? 'active' : '' ?>">
                        <i class="fas fa-hands-helping me-2"></i> Volunteers
                        <?php if ($unreadCounts['volunteers'] > 0): ?>
                            <span class="badge bg-danger float-end"><?= $unreadCounts['volunteers'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/donations.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'donations.php' ? 'active' : '' ?>">
                        <i class="fas fa-donate me-2"></i> Donations
                        <?php if ($unreadCounts['donations'] > 0): ?>
                            <span class="badge bg-danger float-end"><?= $unreadCounts['donations'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/messages.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : '' ?>">
                        <i class="fas fa-envelope me-2"></i> Contact Messages
                        <?php if ($unreadCounts['messages'] > 0): ?>
                            <span class="badge bg-danger float-end"><?= $unreadCounts['messages'] ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                
                <!-- User Management -->
                <li class="sidebar-title">User Management</li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/users.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : '' ?>">
                        <i class="fas fa-users-cog me-2"></i> Users
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/profile.php?user_id=<?= $_SESSION['user_id'] ?>" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
                        <i class="fas fa-user-edit me-2"></i> My Profile
                    </a>
                </li>
                
                <!-- Settings -->
                <li class="sidebar-title">Settings</li>
                <li class="sidebar-item">
                    <a href="<?= BASE_URL ?>admin/settings.php" class="sidebar-link <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
                        <i class="fas fa-cog me-2"></i> Site Settings
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Toggle Button (for mobile) -->
<button class="sidebar-toggle btn btn-dark d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="fas fa-bars"></i>
</button>

<script>
// Enhanced sidebar toggle with localStorage persistence
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    
    // Load saved state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
        sidebar.classList.add('collapsed');
    }
    
    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });
    
    // Auto-collapse on small screens
    function handleResize() {
        if (window.innerWidth < 992) {
            sidebar.classList.add('collapsed');
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize();
});
</script>