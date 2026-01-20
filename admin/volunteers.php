<?php
require_once '../config/db.php';
require_once '../includes/functions/volunteers.php';

// Check admin access
// Add your admin authentication here

// Handle actions
$action = $_GET['action'] ?? 'list';
$volunteerId = $_GET['id'] ?? 0;
$searchTerm = $_GET['search'] ?? '';
$page = $_GET['page'] ?? 1;
$status = $_GET['status'] ?? 'all';

// Handle delete action
if ($action === 'delete' && $volunteerId) {
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
    if (markVolunteerAsViewed($volunteerId)) {
        $_SESSION['success_message'] = 'Volunteer marked as viewed!';
    }
    header('Location: volunteers.php');
    exit;
}

// Handle mark all as viewed
if ($action === 'view-all') {
    if (markAllVolunteersAsViewed()) {
        $_SESSION['success_message'] = 'All volunteers marked as viewed!';
    }
    header('Location: volunteers.php');
    exit;
}

// Handle status update
if ($action === 'update-status' && $volunteerId) {
    $newStatus = $_POST['status'] ?? '';
    if ($newStatus && updateVolunteerStatus($volunteerId, $newStatus)) {
        $_SESSION['success_message'] = 'Volunteer status updated successfully!';
    }
    header('Location: volunteers.php');
    exit;
}

// Handle export
if ($action === 'export') {
    $csvContent = exportVolunteersToCSV();
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=volunteers_' . date('Y-m-d') . '.csv');
    echo $csvContent;
    exit;
}

// Get volunteers based on search or status filter
if ($searchTerm) {
    $result = searchVolunteers($searchTerm, $page, 20);
} elseif ($status !== 'all') {
    $offset = ($page - 1) * 20;
    $volunteers = fetchAll("
        SELECT * FROM volunteers 
        WHERE status = ? 
        ORDER BY submitted_at DESC 
        LIMIT 20 OFFSET ?
    ", [$status, $offset]);
    
    $total = fetchSingle("SELECT COUNT(*) as total FROM volunteers WHERE status = ?", [$status])['total'];
    $totalPages = ceil($total / 20);
    
    $result = [
        'volunteers' => $volunteers,
        'pagination' => [
            'current_page' => $page,
            'per_page' => 20,
            'total_items' => $total,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages
        ]
    ];
} else {
    $result = getAllVolunteers($page, 20);
}

$volunteers = $result['volunteers'];
$pagination = $result['pagination'];

// Set page title and breadcrumb
$page_title = "Volunteer Applications";
$breadcrumb = [
    ['title' => 'Volunteers', 'active' => true]
];

require_once 'includes/header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Volunteer Applications</h1>
        <div class="btn-group" role="group">
            <a href="volunteers.php?action=view-all" class="btn btn-outline-success">
                <i class="fas fa-check-circle me-2"></i>Mark All as Viewed
            </a>
            <a href="volunteers.php?action=export" class="btn btn-outline-primary">
                <i class="fas fa-file-export me-2"></i>Export CSV
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Applications</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= $pagination['total_items'] ?? 0 ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Review</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= fetchSingle("SELECT COUNT(*) as total FROM volunteers WHERE viewed = 0")['total'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= fetchSingle("SELECT COUNT(*) as total FROM volunteers WHERE status = 'approved'")['total'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= fetchSingle("SELECT COUNT(*) as total FROM volunteers WHERE MONTH(submitted_at) = MONTH(CURRENT_DATE())")['total'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" 
                               placeholder="Search by name, email, phone..." 
                               value="<?= htmlspecialchars($searchTerm) ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if ($searchTerm): ?>
                            <a href="volunteers.php" class="btn btn-outline-secondary ms-2">Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="btn-group float-end" role="group">
                        <a href="volunteers.php?status=all" 
                           class="btn btn-outline-secondary <?= $status === 'all' ? 'active' : '' ?>">
                            All
                        </a>
                        <a href="volunteers.php?status=pending" 
                           class="btn btn-outline-warning <?= $status === 'pending' ? 'active' : '' ?>">
                            Pending
                        </a>
                        <a href="volunteers.php?status=approved" 
                           class="btn btn-outline-success <?= $status === 'approved' ? 'active' : '' ?>">
                            Approved
                        </a>
                        <a href="volunteers.php?status=rejected" 
                           class="btn btn-outline-danger <?= $status === 'rejected' ? 'active' : '' ?>">
                            Rejected
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Volunteers Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Education</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($volunteers as $volunteer): 
                            $submittedDate = new DateTime($volunteer['submitted_at']);
                            $socialMedia = $volunteer['social_media'] ? 
                                implode(', ', json_decode($volunteer['social_media'], true)) : '';
                        ?>
                        <tr class="<?= !$volunteer['viewed'] ? 'table-warning' : '' ?>">
                            <td><?= $volunteer['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($volunteer['full_name']) ?></strong>
                                <div class="small text-muted">
                                    <?= $volunteer['gender'] ?>, <?= $volunteer['age'] ?> years
                                </div>
                            </td>
                            <td>
                                <div><?= $volunteer['email'] ?></div>
                                <div class="small text-muted"><?= $volunteer['phone_number'] ?></div>
                                <?php if ($volunteer['whatsapp_number']): ?>
                                    <div class="small text-muted">WA: <?= $volunteer['whatsapp_number'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($volunteer['education_level']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($volunteer['occupation_course']) ?></div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($volunteer['state_of_origin']) ?></div>
                                <div class="small text-muted"><?= htmlspecialchars($volunteer['nationality']) ?></div>
                            </td>
                            <td>
                                <?php if (!$volunteer['viewed']): ?>
                                    <span class="badge bg-warning text-dark">New</span>
                                <?php endif; ?>
                                <span class="badge bg-<?= 
                                    $volunteer['status'] === 'approved' ? 'success' : 
                                    ($volunteer['status'] === 'rejected' ? 'danger' : 'secondary')
                                ?>">
                                    <?= ucfirst($volunteer['status']) ?>
                                </span>
                            </td>
                            <td>
                                <div><?= $submittedDate->format('M j, Y') ?></div>
                                <div class="small text-muted"><?= $submittedDate->format('g:i A') ?></div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary view-details" 
                                            data-bs-toggle="modal" data-bs-target="#detailsModal"
                                            data-id="<?= $volunteer['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <a href="volunteers.php?action=view&id=<?= $volunteer['id'] ?>" 
                                       class="btn btn-outline-success" title="Mark as Viewed">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-info dropdown-toggle" 
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form method="POST" action="volunteers.php?action=update-status&id=<?= $volunteer['id'] ?>" 
                                                  class="dropdown-item p-0">
                                                <input type="hidden" name="status" value="pending">
                                                <button type="submit" class="dropdown-item">Set as Pending</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="volunteers.php?action=update-status&id=<?= $volunteer['id'] ?>" 
                                                  class="dropdown-item p-0">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="dropdown-item text-success">Approve</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="volunteers.php?action=update-status&id=<?= $volunteer['id'] ?>" 
                                                  class="dropdown-item p-0">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="dropdown-item text-danger">Reject</button>
                                            </form>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a href="volunteers.php?action=delete&id=<?= $volunteer['id'] ?>" 
                                               class="dropdown-item text-danger confirm-delete">
                                                <i class="fas fa-trash-alt me-1"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($volunteers)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>No volunteer applications found</h5>
                                    <p class="text-muted"><?= $searchTerm ? 'Try a different search term' : 'All applications will appear here' ?></p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="p-3 border-top">
                    <ul class="pagination justify-content-center mb-0">
                        <?php if ($pagination['has_prev']): ?>
                            <li class="page-item">
                                <a class="page-link" href="volunteers.php?page=<?= $page - 1 ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $status !== 'all' ? '&status=' . $status : '' ?>">
                                    Previous
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="volunteers.php?page=<?= $i ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $status !== 'all' ? '&status=' . $status : '' ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <li class="page-item">
                                <a class="page-link" href="volunteers.php?page=<?= $page + 1 ?><?= $searchTerm ? '&search=' . urlencode($searchTerm) : '' ?><?= $status !== 'all' ? '&status=' . $status : '' ?>">
                                    Next
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Volunteer Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading volunteer details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirm delete
    document.querySelectorAll('.confirm-delete').forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this volunteer application? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });
    
    // View details modal - AJAX version
    document.querySelectorAll('.view-details').forEach(button => {
        button.addEventListener('click', function() {
            const volunteerId = this.getAttribute('data-id');
            
            fetch(`../includes/ajax/get_volunteer_details.php?id=${volunteerId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const volunteer = data.volunteer;
                        
                        // Decode JSON fields
                        let socialMedia = '';
                        if (volunteer.social_media) {
                            try {
                                const socialMediaArray = JSON.parse(volunteer.social_media);
                                socialMedia = socialMediaArray.join(', ');
                            } catch (e) {
                                socialMedia = volunteer.social_media;
                            }
                        }
                        
                        const html = `
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr><th>Full Name:</th><td>${escapeHtml(volunteer.full_name)}</td></tr>
                                                <tr><th>Email:</th><td>${escapeHtml(volunteer.email)}</td></tr>
                                                <tr><th>Phone:</th><td>${escapeHtml(volunteer.phone_number)}</td></tr>
                                                <tr><th>WhatsApp:</th><td>${escapeHtml(volunteer.whatsapp_number || 'N/A')}</td></tr>
                                                <tr><th>Gender:</th><td>${escapeHtml(volunteer.gender)}</td></tr>
                                                <tr><th>Date of Birth:</th><td>${escapeHtml(volunteer.dob)}</td></tr>
                                                <tr><th>Age:</th><td>${volunteer.age}</td></tr>
                                                <tr><th>Marital Status:</th><td>${escapeHtml(volunteer.marital_status)}</td></tr>
                                                <tr><th>State of Origin:</th><td>${escapeHtml(volunteer.state_of_origin)}</td></tr>
                                                <tr><th>Nationality:</th><td>${escapeHtml(volunteer.nationality)}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Education & Career</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr><th>Education Level:</th><td>${escapeHtml(volunteer.education_level)}</td></tr>
                                                <tr><th>Occupation/Course:</th><td>${escapeHtml(volunteer.occupation_course)}</td></tr>
                                                <tr><th>Level/Class:</th><td>${escapeHtml(volunteer.level_class || 'N/A')}</td></tr>
                                                <tr><th>Work/School:</th><td>${escapeHtml(volunteer.work_school_ppa || 'N/A')}</td></tr>
                                                <tr><th>Last CGPA:</th><td>${escapeHtml(volunteer.last_cgpa || 'N/A')}</td></tr>
                                                <tr><th>Hobbies:</th><td>${escapeHtml(volunteer.hobbies || 'N/A')}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-share-alt me-2"></i>Social Media</h6>
                                        </div>
                                        <div class="card-body">
                                            <p><strong>Platforms:</strong> ${escapeHtml(socialMedia || 'N/A')}</p>
                                            <p><strong>Other:</strong> ${escapeHtml(volunteer.social_media_other || 'N/A')}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="card mb-3">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-pray me-2"></i>Spiritual Life</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr><th>Born Again:</th><td>${escapeHtml(volunteer.born_again)}</td></tr>
                                                <tr><th>Holy Spirit Baptism:</th><td>${escapeHtml(volunteer.holy_spirit_baptism)}</td></tr>
                                                <tr><th>Discovered Purpose:</th><td>${escapeHtml(volunteer.discovered_purpose)}</td></tr>
                                                <tr><th>Denomination:</th><td>${escapeHtml(volunteer.denomination)}</td></tr>
                                                <tr><th>Passionate About Youth:</th><td>${escapeHtml(volunteer.passionate_about_youth)}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Application Info</h6>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-sm">
                                                <tr><th>Submitted:</th><td>${escapeHtml(volunteer.submitted_at)}</td></tr>
                                                <tr><th>IP Address:</th><td>${escapeHtml(volunteer.ip_address || 'N/A')}</td></tr>
                                                <tr><th>Viewed:</th><td>${volunteer.viewed ? 'Yes' : 'No'}</td></tr>
                                                <tr><th>Status:</th><td>${escapeHtml(volunteer.status)}</td></tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-purple text-white">
                                            <h6 class="mb-0"><i class="fas fa-star me-2"></i>Gifts & Talents</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>${escapeHtml(volunteer.gifts_talents || 'N/A')}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-dark text-white">
                                            <h6 class="mb-0"><i class="fas fa-home me-2"></i>Residential Address</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>${escapeHtml(volunteer.residential_address || 'N/A')}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0"><i class="fas fa-heart me-2"></i>Salvation Experience</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>${escapeHtml(volunteer.salvation_experience || 'N/A')}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-cross me-2"></i>God-given Purpose</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>${escapeHtml(volunteer.god_given_purpose || 'N/A')}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Motivation</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>${escapeHtml(volunteer.motivation || 'N/A')}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ${volunteer.questions ? `
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-warning text-dark">
                                            <h6 class="mb-0"><i class="fas fa-question-circle me-2"></i>Questions</h6>
                                        </div>
                                        <div class="card-body">
                                            <p>${escapeHtml(volunteer.questions)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            ` : ''}
                        `;
                        
                        document.getElementById('modalBody').innerHTML = html;
                    } else {
                        document.getElementById('modalBody').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Failed to load volunteer details. Please try again.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('modalBody').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Error loading volunteer details. Please check your connection.
                        </div>
                    `;
                });
        });
    });
    
    // Escape HTML function
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>

<style>
.bg-purple {
    background-color: #6f42c1 !important;
}

.card-header {
    font-weight: 600;
}

.table-sm th {
    width: 40%;
    font-weight: 600;
}

.table-sm td {
    width: 60%;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

.badge {
    font-size: 0.75em;
    padding: 0.3em 0.6em;
}
</style>

<?php 
// Display success/error messages
if (isset($_SESSION['success_message'])) {
    echo '<script>showNotification("success", "' . $_SESSION['success_message'] . '");</script>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo '<script>showNotification("error", "' . $_SESSION['error_message'] . '");</script>';
    unset($_SESSION['error_message']);
}

require_once 'includes/footer.php'; 
?>