<?php
require_once 'config/db.php';
require_once 'includes/functions/events.php';

$page_title = "Upcoming Events";
require_once 'includes/header.php';

// Get upcoming events
$upcomingEvents = fetchAll("
    SELECT * FROM events 
    WHERE event_date >= CURDATE() 
    ORDER BY event_date ASC
");

// Get past events
$pastEvents = fetchAll("
    SELECT * FROM events 
    WHERE event_date < CURDATE() 
    ORDER BY event_date DESC 
    LIMIT 6
");
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Upcoming Events</h1>
                <p class="lead mb-4">Join us for workshops, seminars, and special programs designed to empower youth.</p>
                <a href="#event-list" class="btn btn-light btn-lg">View Events</a>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/calendar.jpg" alt="IYEF Events" class="img-fluid rounded shadow" width="55%">
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events -->
<section id="event-list" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Events Calendar</h2>
            <p class="lead text-muted">Mark your calendar for these upcoming opportunities</p>
        </div>
        
        <?php if (!empty($upcomingEvents)): ?>
            <div class="row g-4">
                <?php foreach ($upcomingEvents as $event): 
                    $eventDate = new DateTime($event['event_date']);
                    $regCount = fetchSingle("SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?", [$event['id']])['count'];
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <?php if (!empty($event['image'])): ?>
                            <img src="<?=BASE_URL . $event['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($event['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar-alt fa-4x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary text-white text-center p-2 rounded me-3" style="width: 60px;">
                                    <div class="fw-bold"><?= $eventDate->format('M') ?></div>
                                    <div class="fs-5"><?= $eventDate->format('d') ?></div>
                                </div>
                                <div>
                                    <h3 class="h5 mb-0"><?= htmlspecialchars($event['title']) ?></h3>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i> <?= $eventDate->format('g:i A') ?></small>
                                </div>
                            </div>
                            <p class="card-text">
                                <i class="fas fa-map-marker-alt text-primary me-1"></i> 
                                <?= htmlspecialchars($event['location']) ?>
                            </p>
                            <p class="card-text">
                                <?= strlen($event['description']) > 150 ? 
                                    substr(strip_tags($event['description']), 0, 150) . '...' : 
                                    strip_tags($event['description']) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                            <span class="badge bg-primary">
                                <?= $regCount ?> registration<?= $regCount != 1 ? 's' : '' ?>
                            </span>
                            <div>
                                <a href="event.php?slug=<?= $event['slug'] ?>" class="btn btn-sm btn-outline-primary me-1">Details</a>
                                <a href="event-register.php?event_id=<?= $event['id'] ?>" class="btn btn-sm btn-primary">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-5">
                        <i class="fas fa-calendar-alt fa-4x text-muted mb-4"></i>
                        <h3 class="h4">No Upcoming Events Scheduled</h3>
                        <p class="text-muted">Check back later for upcoming events or view our past events below.</p>
                        <a href="#past-events" class="btn btn-outline-primary">View Past Events</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Past Events -->
<section id="past-events" class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Past Events</h2>
            <p class="lead text-muted">Relive our previous impactful gatherings</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($pastEvents)): ?>
                <?php foreach ($pastEvents as $event): 
                    $eventDate = new DateTime($event['event_date']);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light text-muted text-center p-2 rounded me-3" style="width: 60px;">
                                    <div class="fw-bold"><?= $eventDate->format('M') ?></div>
                                    <div class="fs-5"><?= $eventDate->format('d') ?></div>
                                </div>
                                <div>
                                    <h3 class="h5 mb-0"><?= htmlspecialchars($event['title']) ?></h3>
                                    <small class="text-muted"><i class="fas fa-clock me-1"></i> <?= $eventDate->format('g:i A') ?></small>
                                </div>
                            </div>
                            <p class="card-text">
                                <i class="fas fa-map-marker-alt text-muted me-1"></i> 
                                <?= htmlspecialchars($event['location']) ?>
                            </p>
                            <a href="event.php?slug=<?= $event['slug'] ?>" class="btn btn-sm btn-outline-primary">View Recap</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                            <h3 class="h4">No Past Events Available</h3>
                            <p class="text-muted">Check back after we've held some events.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Event Highlights -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="ratio ratio-16x9">
                    <img src="assets/images/blog12.png" alt="">
                </div>
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Event Highlights</h2>
                <p>Watch highlights from our recent youth empowerment summit where over 200 young people gathered to learn, network, and grow.</p>
                <div class="d-flex mb-3">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5">Interactive Sessions</h4>
                        <p>Engaging workshops and discussions led by industry experts.</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5">Networking Opportunities</h4>
                        <p>Connect with peers and mentors in your field of interest.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5">Practical Skills</h4>
                        <p>Hands-on training that you can apply immediately.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Photo Gallery -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Event Gallery</h2>
            <p class="lead text-muted">Visual memories from our past events</p>
        </div>
        
        <div class="row g-3">
            <?php
            $eventPhotos = fetchAll("
                SELECT * 
                FROM event_gallery eg
                JOIN events e ON eg.event_id = e.id
                ORDER BY e.event_date DESC
                LIMIT 6
            ");
            
            
            foreach ($eventPhotos as $photo):
            ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?= $photo['image_url'] ?>" data-lightbox="event-gallery" data-title="<?= htmlspecialchars($photo['caption']) ?>">
                    <img src="<?=BASE_URL . $photo['thumbnail_url'] ?? $photo['image_url'] ?>" alt="<?= htmlspecialchars($photo['caption']) ?>" class="img-fluid rounded shadow-sm w-100" style="height: 150px; object-fit: cover;">
                </a>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($eventPhotos)): ?>
                <div class="col-12 text-center py-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-images fa-4x text-muted mb-4"></i>
                            <h3 class="h4">No Event Photos Yet</h3>
                            <p class="text-muted">Check back after our next event for photos.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($eventPhotos)): ?>
            <div class="text-center mt-4">
                <a href="gallery.php" class="btn btn-outline-primary">View Full Gallery</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php 
function getYouTubeVideoId($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return $matches[1] ?? '';
}

require_once 'includes/footer.php'; 
?>