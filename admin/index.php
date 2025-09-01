<?php
$page_title = "Admin Dashboard";
$breadcrumb = [
    ['title' => 'Dashboard', 'active' => true]
];
require_once 'includes/header.php';
require_once '../includes/functions/donations.php';
require_once '../includes/functions/programs.php';
require_once '../includes/functions/events.php';
require_once '../includes/functions/volunteers.php';

// Get stats for dashboard
$totalDonations = getTotalDonations();
$activePrograms = count(getActivePrograms());
$upcomingEvents = count(fetchAll("SELECT id FROM events WHERE event_date >= CURDATE()"));
$newVolunteers = fetchSingle("SELECT COUNT(*) as count FROM volunteers WHERE viewed = 0")['count'];
$recentDonations = getAllDonations(1, 5)['donations'];
?>
<div class="row g-4">
    <!-- Stats Cards -->
    <div class="col-md-6 col-lg-3">
        <div class="card admin-card stat-card border-left-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value text-primary">$<?= number_format($totalDonations, 2) ?></div>
                        <div class="stat-label">Total Donations</div>
                    </div>
                    <div class="card-icon text-primary">
                        <i class="fas fa-donate"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card admin-card stat-card border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value text-success"><?= $activePrograms ?></div>
                        <div class="stat-label">Active Programs</div>
                    </div>
                    <div class="card-icon text-success">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card admin-card stat-card border-left-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value text-info"><?= $upcomingEvents ?></div>
                        <div class="stat-label">Upcoming Events</div>
                    </div>
                    <div class="card-icon text-info">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 col-lg-3">
        <div class="card admin-card stat-card border-left-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-value text-warning"><?= $newVolunteers ?></div>
                        <div class="stat-label">New Volunteers</div>
                    </div>
                    <div class="card-icon text-warning">
                        <i class="fas fa-hands-helping"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Donations -->
    <div class="col-lg-8">
        <div class="card admin-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">Recent Donations</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Donor</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDonations as $donation): ?>
                                <tr>
                                    <td>
                                        <?php if ($donation['user_id']): ?>
                                            <?= htmlspecialchars($donation['full_name']) ?>
                                        <?php else: ?>
                                            Anonymous
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $donation['currency'] ?> <?= number_format($donation['amount'], 2) ?></td>
                                    <td><?= date('M j, Y', strtotime($donation['donated_at'])) ?></td>
                                    <td>
                                        <?php if ($donation['receipt_sent']): ?>
                                            <span class="badge bg-success">Receipt Sent</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($recentDonations)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No recent donations found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 py-3">
                <a href="donations.php" class="btn btn-sm btn-primary">View All Donations</a>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card admin-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="blog/add-post.php" class="btn btn-outline-primary text-start">
                        <i class="fas fa-plus me-2"></i> Add New Blog Post
                    </a>
                    <a href="programs.php?action=add" class="btn btn-outline-success text-start">
                        <i class="fas fa-plus me-2"></i> Add New Program
                    </a>
                    <a href="events.php?action=add" class="btn btn-outline-info text-start">
                        <i class="fas fa-plus me-2"></i> Add New Event
                    </a>
                    <a href="settings.php" class="btn btn-outline-secondary text-start">
                        <i class="fas fa-cog me-2"></i> Update Site Settings
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card admin-card mt-4">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">Recent Activity</h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <?php
                    $recentActivity = fetchAll("
                        SELECT * FROM (
                            SELECT 'blog_post' as type, id, title, published_at as date FROM blog_posts ORDER BY published_at DESC LIMIT 3
                        ) AS blog_sub
                        UNION ALL
                        SELECT * FROM (
                            SELECT 'program' as type, id, title, created_at as date FROM programs ORDER BY created_at DESC LIMIT 3
                        ) AS program_sub
                        UNION ALL
                        SELECT * FROM (
                            SELECT 'event' as type, id, title, created_at as date FROM events ORDER BY created_at DESC LIMIT 3
                        ) AS event_sub
                        ORDER BY date DESC
                        LIMIT 5
                    ");
                
                    
                    foreach ($recentActivity as $activity):
                        $icon = '';
                        $url = '';
                        
                        switch ($activity['type']) {
                            case 'blog_post':
                                $icon = 'fas fa-blog';
                                $url = 'blog/edit-post.php?id=' . $activity['id'];
                                break;
                            case 'program':
                                $icon = 'fas fa-project-diagram';
                                $url = 'programs.php?action=edit&id=' . $activity['id'];
                                break;
                            case 'event':
                                $icon = 'fas fa-calendar-alt';
                                $url = 'events.php?action=edit&id=' . $activity['id'];
                                break;
                        }
                    ?>
                    <a href="<?= $url ?>" class="list-group-item list-group-item-action border-0 px-0 py-2">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-primary">
                                <i class="<?= $icon ?> fa-lg"></i>
                            </div>
                            <div>
                                <h6 class="mb-0"><?= htmlspecialchars($activity['title']) ?></h6>
                                <small class="text-muted"><?= date('M j, Y', strtotime($activity['date'])) ?></small>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <?php if (empty($recentActivity)): ?>
                        <div class="text-center py-3 text-muted">No recent activity</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once 'includes/footer.php'; ?>