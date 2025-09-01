<?php
require_once '../config/db.php';
require_once '../includes/functions/volunteers.php';

// Check admin access

$action = $_GET['action'] ?? 'list';
$volunteerId = $_GET['id'] ?? 0;

// Handle delete action
if ($action === 'delete') {
    if (deleteRecord('volunteers', 'id = ?', [$volunteerId])) {
        $_SESSION['success_message'] = 'Volunteer application deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete volunteer application';
    }
    header('Location: volunteers.php');
    exit;
}

// Handle mark as viewed
if ($action === 'view' && $volunteerId) {
    updateRecord('volunteers', ['viewed' => 1], 'id = ?', [$volunteerId]);
    header('Location: volunteers.php');
    exit;
}

// Set page title and breadcrumb
$page_title = "Volunteer Applications";
$breadcrumb = [
    ['title' => 'Volunteers', 'active' => true]
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="card admin-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0">Volunteer Applications</h5>
            <div>
                <a href="volunteers.php?action=view-all" class="btn btn-sm btn-outline-success me-2">
                    <i class="fas fa-check-circle me-1"></i> Mark All as Viewed
                </a>
                <a href="volunteers.php?action=export" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-file-export me-1"></i> Export
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 data-table">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Skills</th>
                            <th>Status</th>
                            <th>Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $volunteers = getAllVolunteers(1, 100)['volunteers'];
                        foreach ($volunteers as $volunteer):
                            $appliedDate = new DateTime($volunteer['applied_at']);
                        ?>
                        <tr class="<?= !$volunteer['viewed'] ? 'table-warning' : '' ?>">
                            <td><?= $volunteer['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($volunteer['full_name']) ?></strong>
                            </td>
                            <td>
                                <div><?= $volunteer['email'] ?></div>
                                <?php if ($volunteer['phone']): ?>
                                    <div class="text-muted small"><?= $volunteer['phone'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= $volunteer['skills'] ? htmlspecialchars(substr($volunteer['skills'], 0, 50) . (strlen($volunteer['skills']) > 50 ? '...' : '')) : 'N/A' ?></td>
                            <td>
                                <?php if (!$volunteer['viewed']): ?>
                                    <span class="badge bg-warning text-dark">New</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Viewed</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $appliedDate->format('M j, Y') ?></td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary view-details" 
                                        data-bs-toggle="modal" data-bs-target="#detailsModal"
                                        data-name="<?= htmlspecialchars($volunteer['full_name']) ?>"
                                        data-email="<?= htmlspecialchars($volunteer['email']) ?>"
                                        data-phone="<?= htmlspecialchars($volunteer['phone'] ?? 'N/A') ?>"
                                        data-skills="<?= htmlspecialchars($volunteer['skills'] ?? 'N/A') ?>"
                                        data-availability="<?= htmlspecialchars($volunteer['availability'] ?? 'N/A') ?>"
                                        data-motivation="<?= htmlspecialchars($volunteer['motivation'] ?? 'N/A') ?>"
                                        data-date="<?= $appliedDate->format('M j, Y g:i A') ?>"
                                        data-status="<?= $volunteer['viewed'] ? 'Viewed' : 'New' ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="volunteers.php?action=view&id=<?= $volunteer['id'] ?>" 
                                   class="btn btn-sm btn-outline-success" title="Mark as Viewed">
                                    <i class="fas fa-check"></i>
                                </a>
                                <a href="volunteers.php?action=delete&id=<?= $volunteer['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger confirm-delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($volunteers)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No volunteer applications found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Volunteer Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Personal Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Full Name:</th>
                                <td id="detail-name"></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td id="detail-email"></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td id="detail-phone"></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td id="detail-status"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Volunteer Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Skills:</th>
                                <td id="detail-skills"></td>
                            </tr>
                            <tr>
                                <th>Availability:</th>
                                <td id="detail-availability"></td>
                            </tr>
                            <tr>
                                <th>Applied On:</th>
                                <td id="detail-date"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Motivation</h6>
                        <div class="border p-3 rounded bg-light" id="detail-motivation"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// View details modal
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('detailsModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            
            document.getElementById('detail-name').textContent = button.getAttribute('data-name');
            document.getElementById('detail-email').textContent = button.getAttribute('data-email');
            document.getElementById('detail-phone').textContent = button.getAttribute('data-phone');
            document.getElementById('detail-skills').textContent = button.getAttribute('data-skills');
            document.getElementById('detail-availability').textContent = button.getAttribute('data-availability');
            document.getElementById('detail-motivation').textContent = button.getAttribute('data-motivation');
            document.getElementById('detail-date').textContent = button.getAttribute('data-date');
            document.getElementById('detail-status').textContent = button.getAttribute('data-status');
        });
    }
    
    // Handle "Mark All as Viewed" button
    const markAllBtn = document.querySelector('a[href*="action=view-all"]');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to mark all applications as viewed?')) {
                window.location.href = 'volunteers.php?action=view-all';
            }
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>