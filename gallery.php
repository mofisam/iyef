<?php
require_once 'config/db.php';
require_once 'includes/functions/events.php';

$page_title = "Event Gallery";
require_once 'includes/header.php';

// Get all distinct events that have gallery photos
$eventsWithGallery = fetchAll("
    SELECT DISTINCT e.id, e.title, e.event_date, e.slug, e.image as event_image
    FROM events e
    JOIN event_gallery eg ON e.id = eg.event_id
    ORDER BY e.event_date DESC
");

// Get selected event ID from query parameter
$selectedEventId = $_GET['event_id'] ?? null;
$selectedEvent = null;

// Get selected event details if specified
if ($selectedEventId && is_numeric($selectedEventId)) {
    $selectedEvent = fetchSingle("
        SELECT e.*, 
               COUNT(eg.id) as photo_count,
               COUNT(DISTINCT es.id) as speaker_count
        FROM events e
        LEFT JOIN event_gallery eg ON e.id = eg.event_id
        LEFT JOIN event_speakers es ON e.id = es.event_id
        WHERE e.id = ?
        GROUP BY e.id
    ", [$selectedEventId]);
}

// Build the base query for photos
$photoQuery = "
    SELECT eg.*, e.title as event_title, e.slug as event_slug
    FROM event_gallery eg
    JOIN events e ON eg.event_id = e.id
";

// Add filter if specific event is selected
if ($selectedEventId && is_numeric($selectedEventId)) {
    $photoQuery .= " WHERE eg.event_id = " . intval($selectedEventId);
}

$photoQuery .= " ORDER BY e.event_date DESC, eg.created_at DESC";

// Get all photos (filtered if event_id is specified)
$allPhotos = fetchAll($photoQuery);
?>

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-60 d-flex align-items-center">
    <!-- Background with gradient overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.9), rgba(0, 123, 255, 0.85)), url('assets/images/gallery-hero.jpg'); background-size: cover; background-position: center;"></div>
    
    <!-- Animated background elements -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); top: -150px; right: -100px;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.05); bottom: -100px; left: -50px;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Main heading -->
                <h1 class="display-2 fw-bold mb-4 text-white animate-slide-up" style="line-height: 1.2;">
                    Event <span class="text-warning">Gallery</span>
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    Browse through captivating photos from our transformative events and relive the empowering moments.
                </p>
                
                <!-- Action buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <a href="#gallery" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-images me-2"></i> View Gallery
                    </a>
                    <?php if ($selectedEventId): ?>
                        <a href="gallery.php" class="btn btn-outline-light btn-lg px-5 py-3">
                            All Events
                        </a>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#gallery" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <span class="small mb-2 opacity-75">Explore Gallery</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- Event Filter - Modern Design -->
<section class="py-5 bg-gradient-light">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">FILTER GALLERY</span>
                </div>
                <h2 class="h2 fw-bold mb-3">Browse by Event</h2>
                <p class="text-muted">Select an event to view its photo collection</p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-4">
                        <form id="galleryFilter">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                                <i class="fas fa-filter"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="form-label fw-bold mb-0">Filter Gallery</label>
                                            <small class="text-muted d-block">Choose an event</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select form-select-lg border-0 shadow-sm" 
                                            id="eventFilter" 
                                            onchange="filterGallery()">
                                        <option value="">All Events</option>
                                        <?php foreach ($eventsWithGallery as $event): 
                                            $eventDate = new DateTime($event['event_date']);
                                            $photoCount = fetchSingle("
                                                SELECT COUNT(*) as count 
                                                FROM event_gallery 
                                                WHERE event_id = ?
                                            ", [$event['id']])['count'];
                                        ?>
                                        <option value="<?= $event['id'] ?>" 
                                            <?= ($selectedEventId == $event['id']) ? 'selected' : '' ?>
                                            data-photos="<?= $photoCount ?>">
                                            <?= htmlspecialchars($event['title']) ?> 
                                            <small class="text-muted">
                                                (<?= $eventDate->format('M j, Y') ?> • <?= $photoCount ?> photos)
                                            </small>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="fas fa-images me-1"></i>
                                        <?= count($allPhotos) ?> photos
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Selected Event Header -->
<?php if ($selectedEvent): ?>
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="position-relative">
                    <img src="<?= BASE_URL . (!empty($selectedEvent['image']) ? $selectedEvent['image'] : 'assets/images/event-default.jpg') ?>" 
                         alt="<?= htmlspecialchars($selectedEvent['title']) ?>" 
                         class="img-fluid rounded-4 shadow-lg">
                    <div class="position-absolute bottom-0 start-0 bg-primary text-white p-3 rounded-3" 
                         style="transform: translate(-10px, 10px);">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt fa-lg me-2"></i>
                            <div>
                                <div class="fw-bold"><?= date('M j, Y', strtotime($selectedEvent['event_date'])) ?></div>
                                <small><?= date('h:i A', strtotime($selectedEvent['event_date'])) ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">EVENT OVERVIEW</span>
                </div>
                <h2 class="display-5 fw-bold mb-3"><?= htmlspecialchars($selectedEvent['title']) ?></h2>
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Location</h6>
                                <p class="text-muted mb-0"><?= htmlspecialchars($selectedEvent['location'] ?? 'Multiple Venues') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">Gallery</h6>
                                <p class="text-muted mb-0"><?= $selectedEvent['photo_count'] ?> Photos</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <p class="lead text-muted mb-4"><?= $selectedEvent['description'] ?></p>
                
                <div class="d-flex flex-wrap gap-3">
                    <a href="event.php?slug=<?= $selectedEvent['slug'] ?>" 
                       class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-info-circle me-2"></i> Event Details
                    </a>
                    <?php if ($selectedEvent['speaker_count'] > 0): ?>
                        <a href="event.php?slug=<?= $selectedEvent['slug'] ?>#speakers" 
                           class="btn btn-outline-primary btn-lg px-4">
                            <i class="fas fa-microphone me-2"></i> View Speakers
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Gallery Section - Modern Grid -->
<section id="gallery" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">PHOTO GALLERY</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">
                    <?= $selectedEvent ? 'Event Photos' : 'All Event Galleries' ?>
                </h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    <?= $selectedEvent 
                        ? 'Browse through photos from this memorable event' 
                        : 'Explore photos from our various youth empowerment events' 
                    ?>
                </p>
            </div>
        </div>
        
        <?php if (!empty($allPhotos)): ?>
            <!-- Photo Grid -->
            <div class="row gallery-grid" data-masonry='{"percentPosition": true}'>
                <?php foreach ($allPhotos as $index => $photo): ?>
                <div class="col-6 col-md-4 col-lg-3 mb-4 gallery-item">
                    <div class="card border-0 shadow-lg hover-lift overflow-hidden">
                        <a href="<?= BASE_URL . $photo['image_url'] ?>" 
                           class="gallery-link"
                           data-lightbox="gallery"
                           data-title="<?= htmlspecialchars($photo['caption'] ?? $photo['event_title']) ?>"
                           data-index="<?= $index ?>">
                            <div class="position-relative overflow-hidden" style="height: 250px;">
                                <img src="<?= BASE_URL . ($photo['thumbnail_url'] ?? $photo['image_url']) ?>" 
                                     alt="<?= htmlspecialchars($photo['caption'] ?? 'Event Photo') ?>" 
                                     class="img-fluid w-100 h-100"
                                     style="object-fit: cover; transition: transform 0.5s ease;">
                                <div class="position-absolute top-0 end-0 m-3">
                                    <div class="bg-white rounded-circle p-2 shadow-sm">
                                        <i class="fas fa-expand-alt text-primary"></i>
                                    </div>
                                </div>
                                <div class="image-overlay position-absolute top-0 start-0 w-100 h-100 bg-gradient-dark opacity-0"></div>
                            </div>
                        </a>
                        <div class="card-body p-3">
                            <?php if (!empty($photo['caption'])): ?>
                                <p class="card-text small mb-2"><?= htmlspecialchars($photo['caption']) ?></p>
                            <?php endif; ?>
                            <?php if (!$selectedEventId): ?>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <a href="gallery.php?event_id=<?= fetchSingle("SELECT id FROM events WHERE slug = ?", [$photo['event_slug']])['id'] ?? '' ?>" 
                                           class="text-decoration-none text-primary">
                                            <?= htmlspecialchars($photo['event_title']) ?>
                                        </a>
                                    </span>
                                    <button class="btn btn-sm btn-outline-primary share-photo" 
                                            data-url="<?= BASE_URL . $photo['image_url'] ?>"
                                            data-title="<?= htmlspecialchars($photo['caption'] ?? $photo['event_title']) ?>">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Lightbox Counter -->
            <div class="text-center mt-4">
                <div class="lightbox-counter d-inline-flex align-items-center bg-white shadow-sm rounded-pill px-4 py-2">
                    <span class="text-muted me-2">Viewing:</span>
                    <span id="currentImage" class="fw-bold">1</span>
                    <span class="text-muted mx-1">of</span>
                    <span class="fw-bold"><?= count($allPhotos) ?></span>
                    <span class="text-muted ms-2">photos</span>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Empty State -->
            <div class="col-12">
                <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-images fa-4x text-muted mb-4 opacity-50"></i>
                        <h4 class="fw-bold mb-3">No Photos Available</h4>
                        <p class="text-muted mb-4">
                            <?= $selectedEventId 
                                ? 'This event gallery is being prepared. Check back soon!' 
                                : 'Our event galleries are being curated. Photos will be available shortly.' 
                            ?>
                        </p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <?php if ($selectedEventId): ?>
                                <a href="gallery.php" class="btn btn-outline-primary">Browse All Events</a>
                            <?php endif; ?>
                            <a href="events.php" class="btn btn-primary">View Upcoming Events</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Event Thumbnails Section -->
<?php if (!$selectedEventId && !empty($eventsWithGallery)): ?>
<section class="bg-light py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">EVENT GALLERIES</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Browse by Event</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Click on any event to explore its photo collection
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php foreach (array_slice($eventsWithGallery, 0, 6) as $event): 
                $eventDate = new DateTime($event['event_date']);
                $photoCount = fetchSingle("
                    SELECT COUNT(*) as count 
                    FROM event_gallery 
                    WHERE event_id = ?
                ", [$event['id']])['count'];
                
                $coverPhoto = fetchSingle("
                    SELECT image_url 
                    FROM event_gallery 
                    WHERE event_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 1
                ", [$event['id']]);
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                    <a href="gallery.php?event_id=<?= $event['id'] ?>" class="text-decoration-none">
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <?php if ($coverPhoto): ?>
                                <img src="<?= BASE_URL . $coverPhoto['image_url'] ?>" 
                                     class="img-fluid w-100 h-100"
                                     alt="<?= htmlspecialchars($event['title']) ?>"
                                     style="object-fit: cover; transition: transform 0.5s ease;">
                            <?php else: ?>
                                <div class="bg-gradient-primary w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-calendar-alt fa-3x text-white"></i>
                                </div>
                            <?php endif; ?>
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white" 
                                 style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                <h5 class="fw-bold mb-1"><?= htmlspecialchars($event['title']) ?></h5>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= $eventDate->format('M j, Y') ?>
                                    </small>
                                    <small>
                                        <i class="fas fa-images me-1"></i>
                                        <?= $photoCount ?> photos
                                    </small>
                                </div>
                            </div>
                        </div>
                    </a>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="event.php?slug=<?= $event['slug'] ?>" 
                               class="btn btn-sm btn-outline-primary">
                                Event Details
                            </a>
                            <a href="gallery.php?event_id=<?= $event['id'] ?>" 
                               class="btn btn-sm btn-primary">
                                View Gallery
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($eventsWithGallery) > 6): ?>
            <div class="text-center mt-5">
                <a href="events.php" class="btn btn-outline-primary btn-lg px-5">
                    <i class="fas fa-calendar-alt me-2"></i> View All Events
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">Share Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="share-options">
                    <div class="row g-3">
                        <div class="col-6">
                            <button class="btn btn-outline-primary w-100 share-facebook">
                                <i class="fab fa-facebook-f me-2"></i> Facebook
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-info w-100 share-twitter">
                                <i class="fab fa-twitter me-2"></i> Twitter
                            </button>
                        </div>
                        <div class="col-12">
                            <div class="input-group">
                                <input type="text" class="form-control" id="shareUrl" readonly>
                                <button class="btn btn-outline-secondary" onclick="copyShareUrl()">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced interactivity -->
<script src="https://cdn.jsdelivr.net/npm/masonry-layout@4.2.2/dist/masonry.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">

<script>
document.addEventListener('DOMContentLoaded', function() {
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
            
            // Image zoom effect
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1.05)';
            }
            
            // Show overlay
            const overlay = this.querySelector('.image-overlay');
            if (overlay) {
                overlay.style.opacity = '0.3';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            
            // Reset image zoom
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1)';
            }
            
            // Hide overlay
            const overlay = this.querySelector('.image-overlay');
            if (overlay) {
                overlay.style.opacity = '0';
            }
        });
    });
    
    // Initialize masonry layout
    const galleryGrid = document.querySelector('.gallery-grid');
    if (galleryGrid) {
        const masonry = new Masonry(galleryGrid, {
            itemSelector: '.gallery-item',
            percentPosition: true,
            gutter: 16
        });
        
        // Refresh masonry after images load
        imagesLoaded(galleryGrid, function() {
            masonry.layout();
        });
    }
    
    // Lightbox customization
    lightbox.option({
        'resizeDuration': 300,
        'wrapAround': true,
        'showImageNumberLabel': false,
        'albumLabel': 'Photo %1 of %2',
        'disableScrolling': true
    });
    
    // Update counter when lightbox opens
    document.addEventListener('onShow', function() {
        document.querySelector('.lightbox-counter').classList.remove('d-none');
    });
    
    // Hide counter when lightbox closes
    document.addEventListener('onClose', function() {
        document.querySelector('.lightbox-counter').classList.add('d-none');
    });
    
    // Update counter when image changes
    document.addEventListener('onSlideAfterChange', function() {
        const current = document.querySelector('.lb-number');
        if (current) {
            document.getElementById('currentImage').textContent = current.textContent.split(' ')[0];
        }
    });
    
    // Share functionality
    const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
    let currentShareUrl = '';
    let currentShareTitle = '';
    
    document.querySelectorAll('.share-photo').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            currentShareUrl = this.getAttribute('data-url');
            currentShareTitle = this.getAttribute('data-title');
            document.getElementById('shareUrl').value = currentShareUrl;
            shareModal.show();
        });
    });
    
    // Social sharing
    document.querySelector('.share-facebook').addEventListener('click', function() {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentShareUrl)}`, '_blank');
    });
    
    document.querySelector('.share-twitter').addEventListener('click', function() {
        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(currentShareUrl)}&text=${encodeURIComponent(currentShareTitle)}`, '_blank');
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
});

// Filter gallery by event
function filterGallery() {
    const eventId = document.getElementById('eventFilter').value;
    if (eventId) {
        window.location.href = 'gallery.php?event_id=' + eventId;
    } else {
        window.location.href = 'gallery.php';
    }
}

// Copy share URL
function copyShareUrl() {
    const shareUrl = document.getElementById('shareUrl');
    shareUrl.select();
    shareUrl.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Show copied feedback
    const copyBtn = document.querySelector('[onclick="copyShareUrl()"]');
    const originalHTML = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="fas fa-check"></i>';
    setTimeout(() => {
        copyBtn.innerHTML = originalHTML;
    }, 2000);
}

// Wait for images to load
function imagesLoaded(container, callback) {
    const images = container.querySelectorAll('img');
    let loaded = 0;
    
    function imageLoaded() {
        loaded++;
        if (loaded === images.length) {
            callback();
        }
    }
    
    images.forEach(img => {
        if (img.complete) {
            imageLoaded();
        } else {
            img.addEventListener('load', imageLoaded);
            img.addEventListener('error', imageLoaded);
        }
    });
}
</script>

<!-- Add custom CSS for animations -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-light {
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.bg-gradient-dark {
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.9));
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

.min-vh-60 {
    min-height: 60vh;
}

.border-dashed {
    border-style: dashed !important;
}

.image-overlay {
    transition: opacity 0.3s ease;
}

.gallery-item {
    transition: transform 0.3s ease;
}

.lightbox-counter {
    transition: all 0.3s ease;
}

.lb-data .lb-caption {
    font-size: 1rem !important;
    line-height: 1.4 !important;
}

/* Lightbox custom styles */
.lightbox .lb-image {
    border-radius: 8px !important;
}

.lightbox .lb-nav a.lb-prev,
.lightbox .lb-nav a.lb-next {
    opacity: 0.8 !important;
}

.lightbox .lb-nav a.lb-prev:hover,
.lightbox .lb-nav a.lb-next:hover {
    opacity: 1 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem;
    }
    
    .display-4, .display-5 {
        font-size: 2rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
    
    .scroll-indicator {
        width: 40px;
        height: 40px;
    }
    
    .gallery-grid {
        column-count: 2 !important;
    }
}

@media (max-width: 576px) {
    .display-2 {
        font-size: 2rem;
    }
    
    .min-vh-60 {
        min-height: 50vh;
    }
    
    .gallery-grid {
        column-count: 1 !important;
    }
}

@media (min-width: 992px) {
    .gallery-grid {
        column-count: 4 !important;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>