<?php
require_once '../config/db.php';
require_once '../includes/functions/events.php';

// Check admin access
// Add your admin authentication check here

$eventId = $_GET['event_id'] ?? 0;
$action = $_GET['action'] ?? 'list';

// Mark registrations as viewed when page loads
if ($eventId) {
    $conn->query("UPDATE event_registrations SET viewed = 1 WHERE event_id = $eventId");
}

// Handle export action
if ($action === 'export' && $eventId) {
    $event = getEventById($eventId);
    $registrations = getEventRegistrations($eventId);
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $event['title'] . ' Registrations.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Header row with all new fields
    fputcsv($output, [
        'ID', 'Full Name', 'Email', 'Age Group', 'Gender', 'Marital Status', 
        'Religion', 'Country', 'State of Residence', 'Phone Number', 
        'Telegram Number', 'How Heard', 'Occupation', 'Affiliation',
        'Participated Before', 'Expectations', 'Speaker Questions',
        'Registration Date'
    ]);
    
    // Data rows
    foreach ($registrations as $reg) {
        fputcsv($output, [
            $reg['id'],
            $reg['full_name'],
            $reg['email'],
            $reg['age_group'],
            $reg['gender'],
            $reg['marital_status'],
            $reg['religion'],
            $reg['country'],
            $reg['state_of_residence'],
            $reg['phone_number'],
            $reg['telegram_number'],
            $reg['hear_about'],
            $reg['current_occupation'],
            $reg['affiliation'],
            $reg['participated_before'],
            $reg['expectations'],
            $reg['speaker_questions'],
            $reg['registration_date']
        ]);
    }
    
    fclose($output);
    exit;
}

// Handle delete action
if ($action === 'delete') {
    $regId = $_GET['id'] ?? 0;
    if ($regId) {
        $conn->query("DELETE FROM event_registrations WHERE id = $regId");
        $_SESSION['success_message'] = 'Registration deleted successfully!';
        header("Location: event-registrations.php?event_id=$eventId");
        exit;
    }
}

// Set page title and breadcrumb
$page_title = "Event Registrations";
$breadcrumb = [
    ['title' => 'Events', 'url' => 'events.php'],
    ['title' => 'Registrations', 'active' => true]
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="card admin-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <div>
                <h5 class="mb-0">
                    <?php if ($eventId): ?>
                        <?php 
                        $event = getEventById($eventId);
                        $eventDate = new DateTime($event['event_date']);
                        echo htmlspecialchars($event['title']) . ' Registrations';
                        ?>
                        <div class="text-muted small mt-1">
                            <?= $eventDate->format('M j, Y g:i A') ?> • <?= htmlspecialchars($event['location']) ?>
                        </div>
                    <?php else: ?>
                        All Event Registrations
                    <?php endif; ?>
                </h5>
                <div class="text-muted small mt-1">
                    <?php 
                    $count = $eventId ? 
                        $conn->query("SELECT COUNT(*) FROM event_registrations WHERE event_id = $eventId")->fetch_row()[0] : 
                        $conn->query("SELECT COUNT(*) FROM event_registrations")->fetch_row()[0];
                    echo $count . ' registration' . ($count != 1 ? 's' : '');
                    ?>
                </div>
            </div>
            <?php if ($eventId): ?>
                <div>
                    <a href="event-registrations.php?action=export&event_id=<?= $eventId ?>" 
                       class="btn btn-sm btn-outline-success me-2">
                        <i class="fas fa-file-export me-1"></i> Export CSV
                    </a>
                    <a href="events.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Events
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 data-table">
                    <thead class="bg-light">
                        <tr>
                            <th width="50">ID</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Demographics</th>
                            <th>Additional Info</th>
                            <th>Registered</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $registrations = $eventId ? 
                            getEventRegistrations($eventId) : 
                            fetchAll("
                                SELECT er.*, e.title as event_title, e.event_date
                                FROM event_registrations er
                                JOIN events e ON er.event_id = e.id
                                ORDER BY er.registration_date DESC
                            ");
                        
                        foreach ($registrations as $reg):
                            $regDate = new DateTime($reg['registration_date']);
                        ?>
                        <tr>
                            <td><?= $reg['id'] ?></td>
                            <td>
                                <strong><?= htmlspecialchars($reg['full_name']) ?></strong>
                                <div class="text-muted small"><?= htmlspecialchars($reg['email']) ?></div>
                                <?php if (!$eventId): ?>
                                    <div class="text-muted small mt-1"><?= htmlspecialchars($reg['event_title']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($reg['phone_number']): ?>
                                    <div><i class="fas fa-phone me-1"></i> <?= $reg['phone_number'] ?></div>
                                <?php endif; ?>
                                <?php if ($reg['telegram_number']): ?>
                                    <div class="text-muted small"><i class="fab fa-telegram me-1"></i> <?= $reg['telegram_number'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div><span class="badge bg-info"><?= $reg['age_group'] ?></span></div>
                                <div class="small"><?= $reg['gender'] ?></div>
                                <?php if ($reg['state_of_residence']): ?>
                                    <div class="text-muted small"><?= $reg['state_of_residence'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($reg['current_occupation']): ?>
                                    <div class="small"><?= $reg['current_occupation'] ?></div>
                                <?php endif; ?>
                                <?php if ($reg['hear_about']): ?>
                                    <div class="text-muted small">Heard via: <?= $reg['hear_about'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $regDate->format('M j, Y') ?>
                                <div class="text-muted small"><?= $regDate->format('g:i A') ?></div>
                            </td>
                            <td class="table-actions">
                                <button type="button" class="btn btn-sm btn-outline-primary view-details" 
                                        data-bs-toggle="modal" data-bs-target="#detailsModal"
                                        data-registration='<?= htmlspecialchars(json_encode($reg), ENT_QUOTES, 'UTF-8') ?>'>
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="event-registrations.php?action=delete&id=<?= $reg['id'] ?>&event_id=<?= $eventId ?>" 
                                   class="btn btn-sm btn-outline-danger confirm-delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($registrations)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">No registrations found</td>
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
                <h5 class="modal-title" id="detailsModalLabel">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-3">Personal Information</h6>
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
                                <th>Telegram:</th>
                                <td id="detail-telegram"></td>
                            </tr>
                            <tr>
                                <th>Age Group:</th>
                                <td id="detail-age-group"></td>
                            </tr>
                            <tr>
                                <th>Gender:</th>
                                <td id="detail-gender"></td>
                            </tr>
                            <tr>
                                <th>Marital Status:</th>
                                <td id="detail-marital-status"></td>
                            </tr>
                            <tr>
                                <th>Religion:</th>
                                <td id="detail-religion"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Location Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">Country:</th>
                                <td id="detail-country"></td>
                            </tr>
                            <tr>
                                <th>State of Residence:</th>
                                <td id="detail-state"></td>
                            </tr>
                        </table>

                        <h6 class="mb-3 mt-4">Event Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <th width="40%">How heard about event:</th>
                                <td id="detail-hear-about"></td>
                            </tr>
                            <tr>
                                <th>Occupation:</th>
                                <td id="detail-occupation"></td>
                            </tr>
                            <tr>
                                <th>Affiliation:</th>
                                <td id="detail-affiliation"></td>
                            </tr>
                            <tr>
                                <th>Participated before:</th>
                                <td id="detail-participated-before"></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="mb-3">Expectations</h6>
                        <div class="bg-light p-3 rounded" id="detail-expectations"></div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3">Speaker Questions</h6>
                        <div class="bg-light p-3 rounded" id="detail-speaker-questions"></div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <table class="table table-sm">
                            <tr>
                                <th width="15%">Registered On:</th>
                                <td id="detail-registered-date"></td>
                            </tr>
                            <?php if (!$eventId): ?>
                                <tr>
                                    <th>Event:</th>
                                    <td id="detail-event-title"></td>
                                </tr>
                                <tr>
                                    <th>Event Date:</th>
                                    <td id="detail-event-date"></td>
                                </tr>
                            <?php endif; ?>
                        </table>
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
            const regData = JSON.parse(button.getAttribute('data-registration'));
            
            // Personal Information
            document.getElementById('detail-name').textContent = regData.full_name || 'N/A';
            document.getElementById('detail-email').textContent = regData.email || 'N/A';
            document.getElementById('detail-phone').textContent = regData.phone_number || 'N/A';
            document.getElementById('detail-telegram').textContent = regData.telegram_number || 'N/A';
            document.getElementById('detail-age-group').textContent = regData.age_group || 'N/A';
            document.getElementById('detail-gender').textContent = regData.gender || 'N/A';
            document.getElementById('detail-marital-status').textContent = regData.marital_status || 'N/A';
            document.getElementById('detail-religion').textContent = regData.religion || 'N/A';
            
            // Location Information
            document.getElementById('detail-country').textContent = regData.country || 'N/A';
            document.getElementById('detail-state').textContent = regData.state_of_residence || 'N/A';
            
            // Event Information
            document.getElementById('detail-hear-about').textContent = regData.hear_about || 'N/A';
            document.getElementById('detail-occupation').textContent = regData.current_occupation || 'N/A';
            document.getElementById('detail-affiliation').textContent = regData.affiliation || 'N/A';
            document.getElementById('detail-participated-before').textContent = regData.participated_before || 'N/A';
            
            // Expectations and Questions
            document.getElementById('detail-expectations').textContent = regData.expectations || 'N/A';
            document.getElementById('detail-speaker-questions').textContent = regData.speaker_questions || 'N/A';
            
            // Registration and Event Info
            const regDate = new Date(regData.registration_date);
            document.getElementById('detail-registered-date').textContent = 
                regDate.toLocaleDateString() + ' at ' + regDate.toLocaleTimeString();
            
            if (!<?= $eventId ? 'true' : 'false' ?>) {
                document.getElementById('detail-event-title').textContent = regData.event_title || 'N/A';
                if (regData.event_date) {
                    const eventDate = new Date(regData.event_date);
                    document.getElementById('detail-event-date').textContent = 
                        eventDate.toLocaleDateString() + ' at ' + eventDate.toLocaleTimeString();
                } else {
                    document.getElementById('detail-event-date').textContent = 'N/A';
                }
            }
        });
    }
    
    // Delete confirmation
    document.querySelectorAll('.confirm-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this registration?')) {
                e.preventDefault();
            }
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>