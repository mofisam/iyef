<?php
require_once 'config/db.php';
require_once 'includes/functions/events.php';

$page_title = "Upcoming Events";
require_once 'includes/header.php';

// Get upcoming events using the function
$eventsData = getAllEvents(1, 12, true); // Get first page, 12 events, upcoming only
$upcomingEvents = $eventsData['events'] ?? [];

// Get past events
$pastEvents = fetchAll("
    SELECT * FROM events 
    WHERE event_date < CURDATE() 
    ORDER BY event_date DESC 
    LIMIT 6
");

// Get event photos for gallery
$eventPhotos = fetchAll("
    SELECT eg.*, e.title as event_title
    FROM event_gallery eg
    JOIN events e ON eg.event_id = e.id
    ORDER BY e.event_date DESC, eg.created_at DESC
    LIMIT 6
");
?>

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-60 d-flex align-items-center">
    <!-- Background with gradient overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.9), rgba(0, 123, 255, 0.85)), url('assets/images/calendar.jpg'); background-size: cover; background-position: center;"></div>
    
    <!-- Animated background elements -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); top: -150px; right: -100px;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.05); bottom: -100px; left: -50px;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Main heading -->
                <h1 class="display-2 fw-bold mt-4 mb-4 text-white animate-slide-up" style="line-height: 1.2;">
                    Youth Empowerment<br>
                    <span class="text-warning">Events & Programs</span>
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    Join our transformative workshops, seminars, and community gatherings designed to empower the next generation of leaders.
                </p>
                
                <!-- Action buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <a href="#upcoming-events" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-calendar-alt me-2"></i> View Events
                    </a>
                    <a href="#register-cta" class="btn btn-outline-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-user-plus me-2"></i> Register Now
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#upcoming-events" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <span class="small mb-2 opacity-75">Explore Events</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- Upcoming Events Section - Modern Grid -->
<section id="upcoming-events" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">UPCOMING EVENTS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Mark Your Calendar</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Join our transformative programs designed specifically for youth empowerment and development.
                </p>
            </div>
        </div>
        
        <?php if (!empty($upcomingEvents)): ?>
            <div class="row g-4">
                <?php foreach ($upcomingEvents as $event): 
                    $eventDate = new DateTime($event['event_date']);
                    $eventDay = $eventDate->format('d');
                    $eventMonth = $eventDate->format('M');
                    $eventTime = $eventDate->format('g:i A');
                    $eventYear = $eventDate->format('Y');
                    
                    $regCount = fetchSingle("SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?", [$event['id']])['count'] ?? 0;
                    $capacity = $event['capacity'] ?? 0;
                    $availableSpots = $capacity > 0 ? $capacity - $regCount : 'Unlimited';
                    
                    // Determine availability badge
                    $availabilityClass = 'bg-success';
                    $availabilityText = 'Available';
                    if ($capacity > 0) {
                        if ($availableSpots <= 0) {
                            $availabilityClass = 'bg-danger';
                            $availabilityText = 'Sold Out';
                        } elseif ($availableSpots < 10) {
                            $availabilityClass = 'bg-warning';
                            $availabilityText = 'Few Spots Left';
                        }
                    }
                ?>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                        <div class="row g-0 h-100">
                            <!-- Event Image Column -->
                            <div class="col-md-5 position-relative">
                                <?php if (!empty($event['image'])): ?>
                                    <img src="<?= BASE_URL . $event['image'] ?>" 
                                         class="h-100 w-100" 
                                         alt="<?= htmlspecialchars($event['title']) ?>" 
                                         style="object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-gradient-primary h-100 w-100 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-calendar-alt fa-4x text-white"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Date Badge -->
                                <div class="position-absolute top-0 start-0 m-3">
                                    <div class="bg-white text-dark rounded-3 p-3 text-center shadow-sm">
                                        <div class="fw-bold fs-4"><?= $eventDay ?></div>
                                        <div class="small text-uppercase text-muted"><?= $eventMonth ?></div>
                                        <div class="small"><?= $eventYear ?></div>
                                    </div>
                                </div>
                                
                                <!-- Availability Badge -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge <?= $availabilityClass ?> px-3 py-2">
                                        <?= $availabilityText ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Event Details Column -->
                            <div class="col-md-7">
                                <div class="card-body p-4 p-lg-5 h-100 d-flex flex-column">
                                    <!-- Event Meta -->
                                    <div class="mb-3">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 me-2">
                                            <i class="fas fa-clock me-1"></i> <?= $eventTime ?>
                                        </span>
                                        <?php if ($capacity > 0): ?>
                                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                                <i class="fas fa-users me-1"></i> <?= $availableSpots ?> spots left
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Event Title -->
                                    <h3 class="h3 fw-bold mb-3">
                                        <a href="event.php?slug=<?= $event['slug'] ?>" class="text-decoration-none text-dark hover-primary">
                                            <?= htmlspecialchars($event['title']) ?>
                                        </a>
                                    </h3>
                                    
                                    <!-- Location -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Location</h6>
                                            <p class="text-muted mb-0"><?= htmlspecialchars($event['location'] ?? 'To be announced') ?></p>
                                        </div>
                                    </div>
                                    
                                    <!-- Description -->
                                    <p class="card-text text-muted mb-4 flex-grow-1">
                                        <?= strlen($event['description']) > 150 ? 
                                            substr(strip_tags($event['description']), 0, 150) . '...' : 
                                            strip_tags($event['description']) ?>
                                    </p>
                                    
                                    <!-- Action Buttons -->
                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                                        <div>
                                            <span class="text-muted small">
                                                <i class="fas fa-user-check me-1"></i> <?= $regCount ?> registered
                                            </span>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a href="event.php?slug=<?= $event['slug'] ?>" class="btn btn-outline-primary">
                                                <i class="fas fa-info-circle me-1"></i> Details
                                            </a>
                                            <?php if ($capacity <= 0 || $availableSpots > 0): ?>
                                                <a href="event-register.php?event_id=<?= $event['id'] ?>" class="btn btn-primary">
                                                    <i class="fas fa-user-plus me-1"></i> Register
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" disabled>
                                                    <i class="fas fa-times me-1"></i> Sold Out
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- View All Events Button -->
            <div class="text-center mt-5 pt-4">
                <a href="events-all.php" class="btn btn-outline-primary btn-lg px-5">
                    <i class="fas fa-calendar-check me-2"></i> View All Upcoming Events
                </a>
            </div>
        <?php else: ?>
            <!-- No Events State -->
            <div class="card border-dashed border-3 border-muted rounded-4 text-center py-5">
                <div class="card-body">
                    <div class="empty-state-icon mb-4">
                        <i class="fas fa-calendar-alt fa-4x text-muted opacity-50"></i>
                    </div>
                    <h3 class="h2 fw-bold mb-3">No Upcoming Events</h3>
                    <p class="lead text-muted mb-4">
                        We're currently planning our next youth empowerment events.<br>
                        Check back soon or subscribe to get notified.
                    </p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="#newsletter-cta" class="btn btn-primary btn-lg scroll-to-section">
                            <i class="fas fa-bell me-2"></i> Notify Me
                        </a>
                        <a href="#past-events" class="btn btn-outline-primary btn-lg scroll-to-section">
                            <i class="fas fa-history me-2"></i> View Past Events
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Past Events Section - Compact View -->
<section id="past-events" class="bg-light py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">PREVIOUS EVENTS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Event Highlights & Recaps</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Relive the impact and memories from our previous youth empowerment gatherings.
                </p>
            </div>
        </div>
        
        <?php if (!empty($pastEvents)): ?>
            <div class="row g-4">
                <?php foreach ($pastEvents as $event): 
                    $eventDate = new DateTime($event['event_date']);
                    $eventDay = $eventDate->format('d');
                    $eventMonth = $eventDate->format('M');
                    $eventYear = $eventDate->format('Y');
                    $regCount = fetchSingle("SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?", [$event['id']])['count'] ?? 0;
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="position-relative">
                            <?php if (!empty($event['image'])): ?>
                                <img src="<?= BASE_URL . $event['image'] ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($event['title']) ?>" 
                                     style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-gradient-dark h-100 w-100" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-calendar-alt fa-4x text-white"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Past Event Badge -->
                            <div class="position-absolute top-0 start-0 m-3">
                                <span class="badge bg-dark bg-opacity-75 text-white px-3 py-2">
                                    <i class="fas fa-history me-1"></i> Past Event
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-light text-dark text-center p-2 rounded-2 me-3">
                                    <div class="fw-bold fs-4"><?= $eventDay ?></div>
                                    <div class="small text-uppercase"><?= $eventMonth ?></div>
                                </div>
                                <div>
                                    <h3 class="h5 fw-bold mb-1"><?= htmlspecialchars($event['title']) ?></h3>
                                    <small class="text-muted"><?= $eventYear ?></small>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">Attendance</h6>
                                    <p class="text-muted mb-0"><?= $regCount ?> participants</p>
                                </div>
                            </div>
                            
                            <p class="card-text text-muted small mb-4">
                                <?= strlen($event['description']) > 100 ? 
                                    substr(strip_tags($event['description']), 0, 100) . '...' : 
                                    strip_tags($event['description']) ?>
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-map-marker-alt me-1"></i> <?= htmlspecialchars($event['location']) ?>
                                </span>
                                <a href="event.php?slug=<?= $event['slug'] ?>" class="btn btn-sm btn-outline-primary">
                                    View Recap
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- View All Past Events -->
            <div class="text-center mt-5">
                <a href="events-past.php" class="btn btn-outline-primary btn-lg px-5">
                    <i class="fas fa-archive me-2"></i> View All Past Events
                </a>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4 opacity-50"></i>
                        <h4 class="fw-bold mb-3">No Past Events Yet</h4>
                        <p class="text-muted mb-4">Our event history will be available after our first events.</p>
                        <a href="#newsletter-cta" class="btn btn-outline-primary">Stay Updated</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Event Gallery Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">EVENT GALLERY</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Memories & Moments</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Capturing the energy, learning, and connections from our youth empowerment events.
                </p>
            </div>
        </div>
        
        <?php if (!empty($eventPhotos)): ?>
            <div class="row g-3">
                <?php foreach ($eventPhotos as $photo): ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?= BASE_URL . $photo['image_url'] ?>" 
                       class="gallery-item d-block position-relative overflow-hidden rounded-3 shadow-sm"
                       data-lightbox="event-gallery" 
                       data-title="<?= htmlspecialchars($photo['caption'] ?? $photo['event_title']) ?>">
                        <img src="<?= BASE_URL . ($photo['thumbnail_url'] ?? $photo['image_url']) ?>" 
                             alt="<?= htmlspecialchars($photo['caption'] ?? $photo['event_title']) ?>" 
                             class="img-fluid w-100 h-100"
                             style="height: 180px; object-fit: cover; transition: transform 0.5s ease;">
                        <div class="gallery-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                            <div class="text-white text-center p-3">
                                <i class="fas fa-search-plus fa-2x mb-2"></i>
                                <small class="d-block"><?= htmlspecialchars($photo['event_title']) ?></small>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Gallery Actions -->
            <div class="text-center mt-5">
                <a href="gallery.php" class="btn btn-outline-primary btn-lg me-3">
                    <i class="fas fa-images me-2"></i> View Full Gallery
                </a>
                <a href="submit-photo.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-camera me-2"></i> Submit Your Photos
                </a>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-images fa-4x text-muted mb-4 opacity-50"></i>
                        <h4 class="fw-bold mb-3">Gallery Coming Soon</h4>
                        <p class="text-muted mb-4">Event photos will be available after our upcoming events.</p>
                        <a href="#upcoming-events" class="btn btn-outline-primary scroll-to-section">
                            <i class="fas fa-calendar-alt me-2"></i> Attend Our Next Event
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Event Highlights Section -->
<section class="bg-light py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="position-relative rounded-4 overflow-hidden shadow-lg">
                    <!-- Video Thumbnail or Image -->
                    <div class="ratio ratio-16x9">
                        <img src="assets/images/blog12.png" 
                             alt="Youth Empowerment Summit Highlights" 
                             class="w-100 h-100 object-fit-cover rounded-4">
                    </div>
                    <!-- Play Button Overlay -->
                    <div class="position-absolute top-50 start-50 translate-middle">
                        <button class="btn btn-light btn-lg rounded-circle p-3 shadow" 
                                data-bs-toggle="modal" 
                                data-bs-target="#videoModal">
                            <i class="fas fa-play text-primary fa-2x"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 ps-lg-5">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">WHY ATTEND</span>
                </div>
                <h2 class="display-5 fw-bold mb-4">Transformative Experience Awaits</h2>
                <p class="lead text-muted mb-5">
                    Our events are designed to provide comprehensive growth opportunities for young leaders and change-makers.
                </p>
                
                <!-- Benefits List -->
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-4">
                            <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                                <i class="fas fa-hands-helping fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h5 fw-bold mb-2">Interactive Sessions</h4>
                                <p class="text-muted mb-0">Engaging workshops led by industry experts and youth leaders.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-4">
                            <div class="icon-wrapper bg-info bg-opacity-10 text-info rounded-3 p-3 me-3">
                                <i class="fas fa-network-wired fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h5 fw-bold mb-2">Networking Opportunities</h4>
                                <p class="text-muted mb-0">Connect with peers, mentors, and industry professionals.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-4">
                            <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                                <i class="fas fa-tools fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h5 fw-bold mb-2">Practical Skills</h4>
                                <p class="text-muted mb-0">Hands-on training that you can apply immediately in your community.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start mb-4">
                            <div class="icon-wrapper bg-purple bg-opacity-10 text-purple rounded-3 p-3 me-3">
                                <i class="fas fa-award fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="h5 fw-bold mb-2">Certification</h4>
                                <p class="text-muted mb-0">Receive certificates of participation and achievement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Registration CTA -->
<section id="register-cta" class="py-5 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary opacity-95"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"40\" fill=\"none\" stroke=\"white\" stroke-width=\"0.5\" stroke-opacity=\"0.1\"/></svg>'); background-size: 100px 100px; opacity: 0.5;"></div>
    
    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-4 fw-bold text-white mb-4">Ready to Transform Your Future?</h2>
                <p class="lead text-white opacity-75 mb-5">
                    Join hundreds of empowered youth who have transformed their lives through our events. 
                    Don't miss this opportunity to grow, connect, and lead.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="#upcoming-events" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-calendar-check me-2"></i> Browse Events
                    </a>
                    <a href="contact.php?subject=Event%20Inquiry" class="btn btn-outline-light btn-lg px-5 py-3">
                        <i class="fas fa-question-circle me-2"></i> Have Questions?
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter CTA -->
<section id="newsletter-cta" class="py-5 bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4 p-lg-5">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-4 mb-4 mx-auto" style="width: 100px; height: 100px;">
                            <i class="fas fa-bell fa-3x"></i>
                        </div>
                        <h2 class="h1 fw-bold mb-3">Never Miss an Event</h2>
                        <p class="lead text-muted mb-4">
                            Subscribe to our newsletter and be the first to know about upcoming events, 
                            registration deadlines, and exclusive opportunities.
                        </p>
                        
                        <form action="/subscribe.php" method="POST" class="row g-3 justify-content-center">
                            <div class="col-md-8">
                                <div class="input-group input-group-lg">
                                    <input type="email" name="email" 
                                           class="form-control border-primary" 
                                           placeholder="Enter your email address" 
                                           required>
                                    <button type="submit" class="btn btn-primary px-4">
                                        Subscribe
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <p class="small text-muted mt-3 mb-0">
                            We respect your privacy. Unsubscribe at any time.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Youth Empowerment Summit Highlights</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ratio ratio-16x9">
                    <!-- Replace with your video embed code -->
                    <div class="d-flex align-items-center justify-content-center bg-dark text-white h-100">
                        <div class="text-center">
                            <i class="fas fa-play-circle fa-5x mb-3"></i>
                            <h4>Event Highlights Video</h4>
                            <p class="mb-0">Video content will be available soon</p>
                        </div>
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
    
    // Counter animation
    const counters = document.querySelectorAll('.counter');
    const speed = 200;
    
    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / speed;
            
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target;
            }
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCount();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(counter);
    });
    
    // Card hover effects
    const cards = document.querySelectorAll('.hover-lift');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.transition = 'transform 0.3s ease';
            
            // Image zoom effect
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1.05)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            
            // Reset image zoom
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1)';
            }
        });
    });
    
    // Gallery hover effects
    const galleryItems = document.querySelectorAll('.gallery-item');
    galleryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1.1)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1)';
            }
        });
    });
    
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
    
    // Initialize Lightbox for gallery
    if (typeof lightbox !== 'undefined') {
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Image %1 of %2'
        });
    }
});
</script>

<!-- Add custom CSS for animations -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
    --success-gradient: linear-gradient(135deg, #28a745, #1e7e34);
    --warning-gradient: linear-gradient(135deg, #ffc107, #e0a800);
    --purple: #6f42c1;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-dark {
    background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.9));
}

.bg-purple {
    background-color: var(--purple) !important;
}

.text-purple {
    color: var(--purple) !important;
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
}

.hover-primary:hover {
    color: #007bff !important;
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

.min-vh-60 {
    min-height: 60vh;
}


.border-dashed {
    border-style: dashed !important;
}

.icon-wrapper {
    transition: all 0.3s ease;
}

.icon-wrapper:hover {
    transform: scale(1.1);
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

.empty-state-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.object-fit-cover {
    object-fit: cover;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem;
    }
    
    .display-5 {
        font-size: 1.75rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
    
    .scroll-indicator {
        width: 40px;
        height: 40px;
    }
}

@media (max-width: 576px) {
    .display-2 {
        font-size: 2rem;
    }
    
    .min-vh-60 {
        min-height: 50vh;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
}
</style>

<?php 
require_once 'includes/footer.php'; 
?>