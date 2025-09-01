<?php
require_once 'config/db.php';

require_once 'includes/functions/events.php';
// includes/functions/users.php
function getUserById($userId) {
    return fetchSingle("SELECT * FROM users WHERE id = ?", [$userId]);
}
$userData = [];
if (isset($_SESSION['user_id'])) {
    require_once 'includes/functions/users.php';
    $userData = getUserById($_SESSION['user_id']);
}
// Get event by slug
$slug = $_GET['slug'] ?? '';
$event = getEventBySlug($slug);

if (!$event) {
    header('Location: /events.php');
    exit;
}

$page_title = $event['title'];
require_once 'includes/header.php';

// Format event date
$eventDate = new DateTime($event['event_date']);
$formattedDate = $eventDate->format('l, F j, Y');
$formattedTime = $eventDate->format('g:i A');

// Get registration count
$regCount = fetchSingle("SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?", [$event['id']])['count'];

// Get related events (excluding current event)
$relatedEvents = fetchAll("
    SELECT * FROM events 
    WHERE id != ? AND event_date >= CURDATE()
    ORDER BY event_date ASC 
    LIMIT 3
", [$event['id']]);

// Get speakers - with error handling
$speakers = [];
try {
    $speakers = fetchAll("
        SELECT * FROM event_speakers 
        WHERE event_id = ?
        ORDER BY display_order ASC
    ", [$event['id']]);
} catch (mysqli_sql_exception $e) {
    error_log("event_speakers table not found: " . $e->getMessage());
}

// Get gallery - with error handling
$gallery = [];
try {
    $gallery = fetchAll("
        SELECT * FROM event_gallery 
        WHERE event_id = ?
        ORDER BY created_at DESC
        LIMIT 6
    ", [$event['id']]);
} catch (mysqli_sql_exception $e) {
    error_log("event_gallery table not found: " . $e->getMessage());
}
?>

<!-- Event Header -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-white text-primary rounded p-2 me-3 text-center" style="width: 80px;">
                        <div class="fw-bold fs-5"><?= $eventDate->format('M') ?></div>
                        <div class="fs-2 fw-bold"><?= $eventDate->format('d') ?></div>
                    </div>
                    <div>
                        <h1 class="display-5 fw-bold mb-1"><?= htmlspecialchars($event['title']) ?></h1>
                        <p class="lead mb-0">
                            <?= $formattedDate ?> at <?= $formattedTime ?>
                        </p>
                    </div>
                </div>
                
                <div class="d-flex flex-wrap gap-3 mt-4">
                    <a href="#register" class="btn btn-light btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i> Register Now
                    </a>
                    <a href="#event-details" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-info-circle me-2"></i> Event Details
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <?php if (!empty($event['image'])): ?>
                    <img src="<?=BASE_URL . $event['image'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="img-fluid rounded shadow">
                <?php else: ?>
                    <div class="bg-secondary rounded shadow d-flex align-items-center justify-content-center" style="height: 300px;">
                        <i class="fas fa-calendar-alt fa-5x text-white"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Event Details -->
<section id="event-details" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="mb-4">About This Event</h2>
                        <div class="mb-4">
                            <?= $event['description'] ?>
                        </div>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                    <div>
                                        <h4 class="h5">Date & Time</h4>
                                        <p class="mb-0">
                                            <?= $formattedDate ?><br>
                                            <?= $formattedTime ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3 text-primary">
                                        <i class="fas fa-map-marker-alt fa-2x"></i>
                                    </div>
                                    <div>
                                        <h4 class="h5">Location</h4>
                                        <p class="mb-0"><?= htmlspecialchars($event['location']) ?></p>
                                        <a href="https://maps.google.com/?q=<?= urlencode($event['location']) ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="fas fa-directions me-1"></i> Get Directions
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($event['schedule'])): ?>
                            <h4 class="mb-3">Event Schedule</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-light">
                                        <tr>
                                            <th>Time</th>
                                            <th>Activity</th>
                                            <th>Speaker/Facilitator</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $schedule = json_decode($event['schedule'], true);
                                        foreach ($schedule as $item): 
                                        ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['time']) ?></td>
                                            <td><?= htmlspecialchars($item['activity']) ?></td>
                                            <td><?= htmlspecialchars($item['speaker']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Speakers Section -->
                <?php if (!empty($speakers)): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="mb-4">Featured Speakers</h2>
                        <div class="row g-4">
                            <?php foreach ($speakers as $speaker): ?>
                            <div class="col-md-6">
                                <div class="d-flex">
                                    <div class="me-3">
                                        <img src="<?= BASE_URL . $speaker['photo'] ? BASE_URL . 'assets/uploads/speakers/'.$speaker['photo'] : '/assets/images/avatar-default.png' ?>" 
                                             alt="<?= htmlspecialchars($speaker['name']) ?>" 
                                             class="rounded-circle" width="80" height="80">
                                    </div>
                                    <div>
                                        <h4 class="h5 mb-1"><?= htmlspecialchars($speaker['name']) ?></h4>
                                        <p class="text-muted mb-2"><?= htmlspecialchars($speaker['title']) ?></p>
                                        <p class="small mb-2"><?= htmlspecialchars($speaker['bio']) ?></p>
                                        <?php if (!empty($speaker['twitter'])): ?>
                                            <a href="<?= htmlspecialchars($speaker['twitter']) ?>" target="_blank" class="text-decoration-none me-2">
                                                <i class="fab fa-twitter text-primary"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($speaker['linkedin'])): ?>
                                            <a href="<?= htmlspecialchars($speaker['linkedin']) ?>" target="_blank" class="text-decoration-none">
                                                <i class="fab fa-linkedin text-primary"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Gallery Section -->
                <?php if (!empty($gallery)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="mb-4">Event Gallery</h2>
                        <div class="row g-3">
                            <?php foreach ($gallery as $photo): ?>
                            <div class="col-6 col-md-4">
                                <a href="<?= $photo['image_url'] ?>" data-lightbox="event-gallery" data-title="<?= htmlspecialchars($photo['caption']) ?>">
                                    <img src="<?= BASE_URL . $photo['thumbnail_url'] ?? $photo['image_url'] ?>" 
                                         alt="<?= htmlspecialchars($photo['caption']) ?>" 
                                         class="img-fluid rounded shadow-sm w-100" 
                                         style="height: 150px; object-fit: cover;">
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-3">
                            <a href="gallery.php?event_id=<?= $event['id'] ?>" class="btn btn-outline-primary">
                                View Full Gallery
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-4">
                <!-- Registration Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h5 mb-0">Event Registration</h3>
                            <span class="badge bg-primary"><?= $regCount ?> registered</span>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                <span><?= $formattedDate ?></span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-clock text-primary me-2"></i>
                                <span><?= $formattedTime ?></span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span><?= htmlspecialchars($event['location']) ?></span>
                            </div>
                        </div>
                        
                        <div class="progress mb-3" style="height: 10px;">
                            <?php 
                            $maxCapacity = $event['capacity'] ?? 100;
                            // Prevent division by zero by ensuring minimum capacity of 1
                            $maxCapacity = max(1, $maxCapacity);
                            $percent = min(100, ($regCount / $maxCapacity) * 100);
                            ?>
                            <div class="progress-bar bg-<?= $percent >= 90 ? 'danger' : ($percent >= 75 ? 'warning' : 'success') ?>" 
                                role="progressbar" style="width: <?= $percent ?>%" 
                                aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                        <p class="small text-muted text-center mb-4">
                            <?= $regCount ?> of <?= $maxCapacity ?> spots filled
                        </p>
                        
                        <a href="#register" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-user-plus me-2"></i> Register Now
                        </a>
                        
                        <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#shareModal">
                            <i class="fas fa-share-alt me-2"></i> Share Event
                        </button>
                    </div>
                </div>
                
                <!-- Share Modal -->
                <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="shareModalLabel">Share This Event</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="shareLink" class="form-label">Direct Link</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="shareLink" 
                                               value="<?= htmlspecialchars("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" readonly>
                                        <button class="btn btn-primary" onclick="copyShareLink()">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-center gap-3">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                                       target="_blank" class="btn btn-outline-primary">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode("Check out this event: $event[title]") ?>&url=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                                       target="_blank" class="btn btn-outline-info">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&title=<?= urlencode($event['title']) ?>&summary=<?= urlencode(strip_tags($event['description'])) ?>" 
                                       target="_blank" class="btn btn-outline-primary">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="mailto:?subject=<?= rawurlencode("Invitation to: $event[title]") ?>&body=<?= rawurlencode("I thought you might be interested in this event:\n\n$event[title]\n\n$event[description]\n\nMore details: https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                                       class="btn btn-outline-secondary">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Related Events -->
                <?php if (!empty($relatedEvents)): ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h3 class="h5 mb-3">Other Events You Might Like</h3>
                        <div class="list-group list-group-flush">
                            <?php foreach ($relatedEvents as $relatedEvent): 
                                $relatedDate = new DateTime($relatedEvent['event_date']);
                            ?>
                            <a href="event.php?slug=<?= $relatedEvent['slug'] ?>" class="list-group-item list-group-item-action border-0 px-0 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="me-3 text-center" style="width: 60px;">
                                        <div class="text-primary fw-bold"><?= $relatedDate->format('M') ?></div>
                                        <div class="fs-5"><?= $relatedDate->format('d') ?></div>
                                    </div>
                                    <div>
                                        <h4 class="h6 mb-0"><?= htmlspecialchars($relatedEvent['title']) ?></h4>
                                        <small class="text-muted"><?= $relatedDate->format('g:i A') ?></small>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>



<script>
// Copy share link function
function copyShareLink() {
    const copyText = document.getElementById("shareLink");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    
    // Show tooltip or alert that link was copied
    alert("Link copied to clipboard!");
}

// Toggle other dietary text field
document.getElementById('other_dietary').addEventListener('change', function() {
    const otherText = document.getElementById('other_dietary_text');
    otherText.style.display = this.checked ? 'block' : 'none';
    if (this.checked) {
        otherText.focus();
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>