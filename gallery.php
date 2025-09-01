<?php
require_once 'config/db.php';
require_once 'includes/functions/events.php';

$page_title = "Event Gallery";
require_once 'includes/header.php';

// Get all distinct events that have gallery photos
$eventsWithGallery = fetchAll("
    SELECT DISTINCT e.id, e.title, e.event_date, e.slug 
    FROM events e
    JOIN event_gallery eg ON e.id = eg.event_id
    ORDER BY e.event_date DESC
");

// Get selected event ID from query parameter
$selectedEventId = $_GET['event_id'] ?? null;

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

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-4">Event Gallery</h1>
                <p class="lead mb-4">Browse photos from our past events and relive the memorable moments.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#gallery" class="btn btn-light btn-lg">View Photos</a>
                    <?php if ($selectedEventId): ?>
                        <a href="gallery.php" class="btn btn-outline-light btn-lg">View All Events</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-4">
                <img src="assets/images/gallery-hero.jpg" alt="Event Gallery" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Event Filter -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body py-3">
                        <form id="galleryFilter">
                            <div class="row align-items-center">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <label for="eventFilter" class="form-label mb-0">Filter by Event:</label>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-select" id="eventFilter" onchange="filterGallery()">
                                        <option value="">All Events</option>
                                        <?php foreach ($eventsWithGallery as $event): 
                                            $eventDate = new DateTime($event['event_date']);
                                        ?>
                                        <option value="<?= $event['id'] ?>" 
                                            <?= ($selectedEventId == $event['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($event['title']) ?> (<?= $eventDate->format('M j, Y') ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 text-md-end mt-2 mt-md-0">
                                    <span class="badge bg-primary">
                                        <?= count($allPhotos) ?> photo<?= count($allPhotos) != 1 ? 's' : '' ?>
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

<!-- Gallery Section -->
<section id="gallery" class="py-5">
    <div class="container">
        <?php if (!empty($allPhotos)): ?>
            <?php if ($selectedEventId): ?>
                <div class="text-center mb-5">
                    <h2 class="fw-bold mb-2">
                        <?= htmlspecialchars($allPhotos[0]['event_title']) ?>
                    </h2>
                    <a href="event.php?slug=<?= $allPhotos[0]['event_slug'] ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-info-circle me-1"></i> View Event Details
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="row g-3 gallery-grid">
                <?php foreach ($allPhotos as $photo): ?>
                <div class="col-6 col-md-4 col-lg-3 gallery-item">
                    <div class="card border-0 shadow-sm h-100">
                        <a href="<?= BASE_URL . $photo['image_url'] ?>" data-lightbox="gallery" data-title="<?= htmlspecialchars($photo['caption']) ?>">
                            <img src="<?= BASE_URL . ($photo['thumbnail_url'] ?? $photo['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($photo['caption']) ?>" 
                                 class="card-img-top img-fluid" 
                                 style="height: 200px; object-fit: cover;">
                        </a>
                        <div class="card-body">
                            <?php if (!empty($photo['caption'])): ?>
                                <p class="card-text small"><?= htmlspecialchars($photo['caption']) ?></p>
                            <?php endif; ?>
                            <?php if (!$selectedEventId): ?>
                                <p class="card-text small text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <a href="event.php?slug=<?= $photo['event_slug'] ?>" class="text-muted">
                                        <?= htmlspecialchars($photo['event_title']) ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Lightbox counter -->
            <div class="text-center mt-4 d-none" id="lightboxCounter">
                <span id="currentImage">1</span> of <?= count($allPhotos) ?>
            </div>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body py-5">
                        <i class="fas fa-images fa-4x text-muted mb-4"></i>
                        <h3 class="h4">No Photos Found</h3>
                        <p class="text-muted">
                            <?= $selectedEventId ? 'This event has no photos yet.' : 'There are no photos in the gallery yet.' ?>
                        </p>
                        <a href="events.php" class="btn btn-outline-primary">Browse Events</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Events -->
<?php if (!$selectedEventId && !empty($eventsWithGallery)): ?>
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Browse Events</h2>
            <p class="lead text-muted">View photos from specific events</p>
        </div>
        
        <div class="row g-4">
            <?php foreach (array_slice($eventsWithGallery, 0, 3) as $event): 
                $eventDate = new DateTime($event['event_date']);
                $photoCount = fetchSingle("
                    SELECT COUNT(*) as count 
                    FROM event_gallery 
                    WHERE event_id = ?
                ", [$event['id']])['count'];
            ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <a href="gallery.php?event_id=<?= $event['id'] ?>">
                        <?php 
                        $coverPhoto = fetchSingle("
                            SELECT image_url 
                            FROM event_gallery 
                            WHERE event_id = ? 
                            ORDER BY created_at DESC 
                            LIMIT 1
                        ", [$event['id']]);
                        ?>
                        <?php if ($coverPhoto): ?>
                            <img src="<?= BASE_URL . $coverPhoto['image_url'] ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($event['title']) ?>" 
                                 style="height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary" style="height: 180px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-calendar-alt fa-3x text-white"></i>
                            </div>
                        <?php endif; ?>
                    </a>
                    <div class="card-body">
                        <h3 class="h5">
                            <a href="gallery.php?event_id=<?= $event['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($event['title']) ?>
                            </a>
                        </h3>
                        <p class="small text-muted mb-2">
                            <i class="fas fa-calendar-alt me-1"></i>
                            <?= $eventDate->format('F j, Y') ?>
                        </p>
                        <p class="small text-muted mb-0">
                            <i class="fas fa-images me-1"></i>
                            <?= $photoCount ?> photo<?= $photoCount != 1 ? 's' : '' ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <div class="d-flex justify-content-between">
                            <a href="event.php?slug=<?= $event['slug'] ?>" class="btn btn-sm btn-outline-primary">
                                Event Details
                            </a>
                            <a href="gallery.php?event_id=<?= $event['id'] ?>" class="btn btn-sm btn-primary">
                                View Photos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (count($eventsWithGallery) > 3): ?>
            <div class="text-center mt-4">
                <a href="events.php" class="btn btn-outline-primary">View All Events</a>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<script>
// Filter gallery by event
function filterGallery() {
    const eventId = document.getElementById('eventFilter').value;
    if (eventId) {
        window.location.href = 'gallery.php?event_id=' + eventId;
    } else {
        window.location.href = 'gallery.php';
    }
}

// Initialize lightbox with counter
document.addEventListener('DOMContentLoaded', function() {
    lightbox.option({
        'resizeDuration': 200,
        'wrapAround': true,
        'albumLabel': 'Photo %1 of %2'
    });
    
    // Show counter when lightbox opens
    document.addEventListener('onShow', function() {
        document.getElementById('lightboxCounter').classList.remove('d-none');
    });
    
    // Hide counter when lightbox closes
    document.addEventListener('onClose', function() {
        document.getElementById('lightboxCounter').classList.add('d-none');
    });
    
    // Update counter when image changes
    document.addEventListener('onSlideAfterChange', function() {
        const current = document.querySelector('.lb-number');
        if (current) {
            document.getElementById('currentImage').textContent = current.textContent.split(' ')[0];
        }
    });
});

// Masonry layout for gallery
window.addEventListener('load', function() {
    if (typeof Masonry !== 'undefined') {
        new Masonry('.gallery-grid', {
            itemSelector: '.gallery-item',
            percentPosition: true,
            gutter: 12
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>