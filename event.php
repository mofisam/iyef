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

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-70 d-flex align-items-center">
    <!-- Background with gradient overlay -->
    <?php if (!empty($event['image'])): ?>
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.85), rgba(0, 123, 255, 0.9)), url('<?= BASE_URL . $event['image'] ?>'); background-size: cover; background-position: center;"></div>
    <?php else: ?>
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary"></div>
    <?php endif; ?>
    
    <!-- Animated background elements -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); top: -150px; right: -100px;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.05); bottom: -100px; left: -50px;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row">
            <div class="col-lg-8">
                <!-- Main heading -->
                <h1 class="display-2 fw-bold mb-4 text-white animate-slide-up" style="line-height: 1.2;">
                    <?= htmlspecialchars($event['title']) ?>
                </h1>
                
                <!-- Event Meta -->
                <div class="row g-3 mb-5 animate-fade-in" style="animation-delay: 0.3s;">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-20 text-primary rounded-circle p-3 me-3">
                                <i class="fas fa-calendar-alt fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-white opacity-75 small">Date</div>
                                <div class="fw-bold text-white"><?= $formattedDate ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-20 text-primary rounded-circle p-3 me-3">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-white opacity-75 small">Time</div>
                                <div class="fw-bold text-white"><?= $formattedTime ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-20 text-primary rounded-circle p-3 me-3">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <div>
                                <div class="text-white opacity-75 small">Spots Filled</div>
                                <div class="fw-bold text-white"><?= $regCount ?> Registered</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action buttons -->
                <div class="d-flex flex-wrap gap-3 mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <a href="#register" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-user-plus me-2"></i> Register Now
                    </a>
                    <a href="#event-details" class="btn btn-outline-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-info-circle me-2"></i> Event Details
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#event-details" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <span class="small mb-2 opacity-75">Explore Event Details</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- Main Content Section -->
<section id="event-details" class="py-5">
    <div class="container py-5">
        <div class="row">
            <!-- Main Content Column -->
            <div class="col-lg-8">
                <!-- About Event Card -->
                <div class="card border-0 shadow-lg mb-5 hover-lift overflow-hidden">
                    <div class="card-header bg-primary bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-primary text-white rounded-3 p-3 me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="h3 fw-bold mb-0">About This Event</h2>
                                <small class="text-muted">Youth empowerment and transformation</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="event-description mb-5">
                            <?= $event['description'] ?>
                        </div>
                        
                        <!-- Event Details Grid -->
                        <div class="row g-4 mb-5">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-3 me-3">
                                                <i class="fas fa-clock fa-lg"></i>
                                            </div>
                                            <div>
                                                <h4 class="h5 fw-bold mb-2">Date & Time</h4>
                                                <p class="mb-2 fw-bold"><?= $formattedDate ?></p>
                                                <p class="text-muted mb-0">Starts at <?= $formattedTime ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-2 p-3 me-3">
                                                <i class="fas fa-map-marker-alt fa-lg"></i>
                                            </div>
                                            <div>
                                                <h4 class="h5 fw-bold mb-2">Location</h4>
                                                <p class="mb-2 fw-bold"><?= htmlspecialchars($event['location']) ?></p>
                                                <a href="https://maps.google.com/?q=<?= urlencode($event['location']) ?>" 
                                                   target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                    <i class="fas fa-directions me-1"></i> Get Directions
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Event Schedule -->
                        <?php if (!empty($event['schedule'])): ?>
                            <div class="mb-5">
                                <h3 class="h4 fw-bold mb-4 d-flex align-items-center">
                                    <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-2 p-2 me-3">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    Event Schedule
                                </h3>
                                <div class="timeline">
                                    <?php 
                                    $schedule = json_decode($event['schedule'], true);
                                    foreach ($schedule as $index => $item): 
                                    ?>
                                    <div class="timeline-item <?= $index % 2 == 0 ? 'left' : 'right' ?>">
                                        <div class="timeline-content bg-light rounded-3 ">
                                            <div class="timeline-time text-primary fw-bold mb-2">
                                                <i class="fas fa-clock me-2"></i><?= htmlspecialchars($item['time']) ?>
                                            </div>
                                            <h5 class="fw-bold mb-2"><?= htmlspecialchars($item['activity']) ?></h5>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-user-tie me-2"></i>
                                                <?= htmlspecialchars($item['speaker']) ?>
                                            </p>
                                        </div>
                                        <div class="timeline-marker">
                                            <div class="marker-dot"></div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Speakers Section -->
                <?php if (!empty($speakers)): ?>
                <div class="card border-0 shadow-lg mb-5 hover-lift overflow-hidden">
                    <div class="card-header bg-success bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-success text-white rounded-3 p-3 me-3">
                                <i class="fas fa-microphone-alt fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="h3 fw-bold mb-0">Featured Speakers</h2>
                                <small class="text-muted">Meet our expert panel</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-4">
                            <?php foreach ($speakers as $speaker): 
                                $speakerPhoto = !empty($speaker['photo']) ? (BASE_URL . 'assets/uploads/speakers/' . $speaker['photo']) : (BASE_URL . 'assets/images/avatar-default.png');
                            ?>
                            <div class="col-lg-6">
                                <div class="card border-0 bg-light h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start mb-4">
                                            <div class="flex-shrink-0">
                                                <img src="<?= $speakerPhoto ?>" 
                                                     alt="<?= htmlspecialchars($speaker['name']) ?>" 
                                                     class="rounded-circle" width="80" height="80" 
                                                     style="object-fit: cover;">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="h5 fw-bold mb-1"><?= htmlspecialchars($speaker['name']) ?></h4>
                                                <p class="text-success mb-2"><?= htmlspecialchars($speaker['title']) ?></p>
                                                <div class="social-links">
                                                    <?php if (!empty($speaker['twitter'])): ?>
                                                        <a href="<?= htmlspecialchars($speaker['twitter']) ?>" target="_blank" 
                                                           class="text-decoration-none me-2">
                                                            <i class="fab fa-twitter text-info"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($speaker['linkedin'])): ?>
                                                        <a href="<?= htmlspecialchars($speaker['linkedin']) ?>" target="_blank" 
                                                           class="text-decoration-none">
                                                            <i class="fab fa-linkedin text-primary"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-muted mb-0"><?= htmlspecialchars(substr($speaker['bio'], 0, 150)) ?><?= strlen($speaker['bio']) > 150 ? '...' : '' ?></p>
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
                <div class="card border-0 shadow-lg hover-lift overflow-hidden">
                    <div class="card-header bg-warning bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-warning text-white rounded-3 p-3 me-3">
                                <i class="fas fa-images fa-2x"></i>
                            </div>
                            <div>
                                <h2 class="h3 fw-bold mb-0">Event Gallery</h2>
                                <small class="text-muted">Moments from our events</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="row g-3">
                            <?php foreach ($gallery as $photo): 
                                $imageUrl = $photo['image_url'];
                                $thumbnailUrl = $photo['thumbnail_url'] ?? $photo['image_url'];
                            ?>
                            <div class="col-6 col-md-4">
                                <a href="<?= BASE_URL . $imageUrl ?>" class="gallery-item" data-lightbox="event-gallery" 
                                   data-title="<?= htmlspecialchars($photo['caption']) ?>">
                                    <div class="position-relative overflow-hidden rounded-3" style="height: 200px;">
                                        <img src="<?= BASE_URL . $thumbnailUrl ?>" 
                                             alt="<?= htmlspecialchars($photo['caption']) ?>" 
                                             class="w-100 h-100" 
                                             style="object-fit: cover; transition: transform 0.5s ease;">
                                        <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-search-plus fa-2x text-white"></i>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="text-center mt-4">
                            <a href="gallery.php?event_id=<?= $event['id'] ?>" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-images me-2"></i> View Full Gallery
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar Column -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="sticky-sidebar">
                    <!-- Registration Widget -->
                    <div class="card border-0 shadow-lg mb-4 hover-lift">
                        <div class="card-header bg-primary text-white border-0 py-4">
                            <div class="text-center">
                                <h3 class="h4 fw-bold mb-0">Register Now</h3>
                                <small class="opacity-75">Secure your spot today</small>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- Event Meta -->
                            <div class="event-meta mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= $formattedDate ?></div>
                                        <small class="text-muted"><?= $formattedTime ?></small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold"><?= htmlspecialchars($event['location']) ?></div>
                                        <small class="text-muted">Event Venue</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Registration Progress -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="fw-bold">Available Spots</span>
                                    <span class="badge bg-primary"><?= $regCount ?> registered</span>
                                </div>
                                <?php 
                                $maxCapacity = $event['capacity'] ?? 100;
                                $maxCapacity = max(1, $maxCapacity);
                                $percent = min(100, ($regCount / $maxCapacity) * 100);
                                $remaining = $maxCapacity - $regCount;
                                $statusClass = $percent >= 90 ? 'danger' : ($percent >= 75 ? 'warning' : 'success');
                                ?>
                                <div class="progress mb-2" style="height: 10px;">
                                    <div class="progress-bar bg-<?= $statusClass ?>" 
                                         role="progressbar" style="width: <?= $percent ?>%" 
                                         aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <small class="text-muted">
                                        <?php if ($remaining <= 0): ?>
                                            <span class="text-danger fw-bold">Fully Booked!</span>
                                        <?php else: ?>
                                            <span class="text-<?= $statusClass ?> fw-bold"><?= $remaining ?> spots remaining</span>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-3">
                                <a href="#register" class="btn btn-primary btn-lg py-3 scroll-to-section">
                                    <i class="fas fa-user-plus me-2"></i> Register Now
                                </a>
                                <button class="btn btn-outline-primary btn-lg py-3" data-bs-toggle="modal" data-bs-target="#shareModal">
                                    <i class="fas fa-share-alt me-2"></i> Share Event
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Related Events Widget -->
                    <?php if (!empty($relatedEvents)): ?>
                    <div class="card border-0 shadow-lg hover-lift">
                        <div class="card-header bg-info bg-opacity-10 border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-info text-white rounded-3 p-2 me-3">
                                    <i class="fas fa-calendar-star fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="h5 fw-bold mb-0">Related Events</h3>
                                    <small class="text-muted">More youth opportunities</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="related-events-list">
                                <?php foreach ($relatedEvents as $relatedEvent): 
                                    $relatedDate = new DateTime($relatedEvent['event_date']);
                                ?>
                                <a href="event.php?slug=<?= $relatedEvent['slug'] ?>" 
                                   class="related-event-item d-flex align-items-start mb-3 pb-3 border-bottom">
                                    <div class="flex-shrink-0">
                                        <div class="date-badge bg-light text-dark rounded-3 p-3 text-center shadow-sm">
                                            <div class="fw-bold fs-5"><?= $relatedDate->format('d') ?></div>
                                            <div class="small text-uppercase text-muted"><?= $relatedDate->format('M') ?></div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h5 class="fw-bold mb-1 line-clamp-2"><?= htmlspecialchars($relatedEvent['title']) ?></h5>
                                        <small class="text-muted d-block">
                                            <i class="fas fa-clock me-1"></i><?= $relatedDate->format('g:i A') ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($relatedEvent['location']) ?>
                                        </small>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="text-center mt-3">
                                <a href="events.php" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-calendar-alt me-1"></i> View All Events
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Registration Form Section -->
<section id="register" class="py-5 bg-gradient-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-header bg-white border-0 py-4">
                        <div class="text-center">
                            <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-2">
                                <span class="small fw-bold">REGISTRATION</span>
                            </div>
                            <h2 class="h1 fw-bold mb-3">Register for Event</h2>
                            <p class="lead text-muted mb-0">Fill in your details below to secure your spot</p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-lg-5">
                        <?php include_once 'event-registration-form.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Share This Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label">Share via Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="shareLink" 
                               value="<?= htmlspecialchars("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" readonly>
                        <button class="btn btn-primary" onclick="copyShareLink()" id="copyBtn">
                            <i class="fas fa-copy me-1"></i> Copy
                        </button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted" id="copyMessage"></small>
                    </div>
                </div>
                                
                <div class="text-center">
                    <h6 class="mb-3">Share on Social Media</h6>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                           target="_blank" class="btn btn-outline-primary btn-lg rounded-circle">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= urlencode("Check out this event: $event[title]") ?>&url=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                           target="_blank" class="btn btn-outline-info btn-lg rounded-circle">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&title=<?= urlencode($event['title']) ?>&summary=<?= urlencode(strip_tags($event['description'])) ?>" 
                           target="_blank" class="btn btn-outline-primary btn-lg rounded-circle">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="mailto:?subject=<?= rawurlencode("Invitation to: $event[title]") ?>&body=<?= rawurlencode("I thought you might be interested in this event:\n\n$event[title]\n\n$event[description]\n\nMore details: https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                           class="btn btn-outline-secondary btn-lg rounded-circle">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for internal links
    const scrollLinks = document.querySelectorAll('.scroll-to-section');
    scrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Card hover effects
    const cards = document.querySelectorAll('.hover-lift');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.transition = 'transform 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Gallery image hover effects
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
        const img = item.querySelector('img');
        item.addEventListener('mouseenter', function() {
            img.style.transform = 'scale(1.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            img.style.transform = 'scale(1)';
        });
    });
    
    // Related events hover
    const relatedItems = document.querySelectorAll('.related-event-item');
    relatedItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Sticky sidebar
    const sidebar = document.querySelector('.sticky-sidebar');
    if (sidebar) {
        const offsetTop = sidebar.offsetTop;
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > offsetTop) {
                sidebar.style.position = 'sticky';
                sidebar.style.top = '20px';
            } else {
                sidebar.style.position = 'relative';
                sidebar.style.top = '0';
            }
        });
    }
    
    // Share link copy
    window.copyShareLink = function() {
        const copyText = document.getElementById("shareLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        
        const copyBtn = document.getElementById("copyBtn");
        const copyMessage = document.getElementById("copyMessage");
        
        copyBtn.innerHTML = '<i class="fas fa-check me-1"></i> Copied!';
        copyBtn.classList.remove('btn-primary');
        copyBtn.classList.add('btn-success');
        copyMessage.textContent = "Link copied to clipboard!";
        copyMessage.className = "text-success";
        
        setTimeout(function() {
            copyBtn.innerHTML = '<i class="fas fa-copy me-1"></i> Copy';
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-primary');
            copyMessage.textContent = "";
        }, 2000);
    };
    
    // Scroll indicator animation
    const scrollIndicator = document.querySelector('.scroll-indicator');
    if (scrollIndicator) {
        setInterval(() => {
            scrollIndicator.style.transform = 'translateY(-5px)';
            setTimeout(() => {
                scrollIndicator.style.transform = 'translateY(0)';
            }, 500);
        }, 1000);
    }
});
</script>

<!-- Add custom CSS for animations -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-primary {
    background: var(--primary-gradient);
}

.bg-gradient-light {
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
}

.animate-fade-in {
    animation: fadeIn 1s ease-out;
}

.animate-slide-up {
    animation: slideUp 1s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(76, 217, 100, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(76, 217, 100, 0); }
    100% { box-shadow: 0 0 0 0 rgba(76, 217, 100, 0); }
}

.scroll-indicator {
    animation: bounce 2s infinite;
    background: rgba(255, 255, 255, 0.1);
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.scroll-indicator:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

.min-vh-70 {
    min-height: 70vh;
}

.min-vh-60 {
    min-height: 60vh;
}

.icon-wrapper {
    transition: all 0.3s ease;
}

.icon-wrapper:hover {
    transform: scale(1.1);
}

/* Timeline styling */
.timeline {
    position: relative;
    padding-left: 37px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: linear-gradient(to bottom, #007bff, #28a745);
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 20px;
}

.marker-dot {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #007bff;
    border: 3px solid white;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
}

.gallery-overlay {
    background: rgba(0, 123, 255, 0.8);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.sticky-sidebar {
    transition: all 0.3s ease;
}

/* Event registration progress */
.progress {
    overflow: hidden;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem;
    }
    
    .min-vh-70 {
        min-height: 50vh;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .scroll-indicator {
        width: 40px;
        height: 40px;
    }
    
    .sticky-sidebar {
        position: relative !important;
        top: 0 !important;
    }
    
    .timeline {
        padding-left: 20px;
    }
    
    .timeline::before {
        left: 10px;
    }
    
    .timeline-marker {
        left: -20px;
    }
}

@media (max-width: 576px) {
    .display-2 {
        font-size: 2rem;
    }
    
    .btn-lg.rounded-circle {
        width: 50px;
        height: 50px;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>