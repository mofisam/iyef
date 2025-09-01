<?php
require_once '../config/db.php';
require_once '../includes/functions/events.php';
require_once '../includes/functions/speakers.php';
require_once '../includes/functions/gallery.php';

// Check admin access

$action = $_GET['action'] ?? 'list';
$eventId = $_GET['id'] ?? 0;
$subAction = $_GET['sub'] ?? '';

// Initialize default event data
$event = [
    'title' => '',
    'description' => '',
    'location' => '',
    'event_date' => date('Y-m-d H:i:s'),
    'capacity' => 100,
    'has_food' => 0,
    'image' => '',
    'schedule' => '[]'
];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle main event form
    if ($action === 'add' || $action === 'edit') {
        // Only process if it's not a speaker submission
        if ($subAction !== 'speaker' && $subAction !== 'gallery') {
            // Basic validation
            if (empty($_POST['title'])) {
                $error = "Event title is required";
            } else {
                $eventData = [
                    'title' => trim($_POST['title']),
                    'description' => trim($_POST['description'] ?? ''),
                    'location' => trim($_POST['location'] ?? ''),
                    'event_date' => ($_POST['event_date'] ?? date('Y-m-d')) . ' ' . ($_POST['event_time'] ?? '19:00'),
                    'capacity' => (int)($_POST['capacity'] ?? 100),
                    'has_food' => isset($_POST['has_food']) ? 1 : 0,
                    'schedule' => isset($_POST['schedule']) ? json_encode($_POST['schedule']) : '[]'
                ];

                // Handle image upload
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $uploadDir = '../assets/uploads/events/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }
                    
                    $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $eventData['image'] = '/assets/uploads/events/' . $fileName;
                        
                        // Delete old image if it exists
                        if ($action === 'edit' && !empty($_POST['existing_image']) && $_POST['existing_image'] !== $eventData['image']) {
                            $oldImage = '../' . ltrim($_POST['existing_image'], '/');
                            if (file_exists($oldImage)) {
                                unlink($oldImage);
                            }
                        }
                    }
                } elseif (isset($_POST['remove_image'])) {
                    $eventData['image'] = '';
                    if (!empty($_POST['existing_image'])) {
                        $oldImage = '../' . ltrim($_POST['existing_image'], '/');
                        if (file_exists($oldImage)) {
                            unlink($oldImage);
                        }
                    }
                } else {
                    $eventData['image'] = $_POST['existing_image'] ?? '';
                }

                if ($action === 'add') {
                    $result = createEvent($eventData);
                    if ($result['status'] === 'success') {
                        $_SESSION['success_message'] = 'Event added successfully!';
                        header('Location: events.php?action=edit&id=' . $result['id']);
                        exit;
                    } else {
                        $error = $result['message'] ?? 'Failed to add event';
                    }
                } elseif ($action === 'edit') {
                    if (updateEvent($eventId, $eventData)) {
                        $_SESSION['success_message'] = 'Event updated successfully!';
                        header('Location: events.php?action=edit&id=' . $eventId);
                        exit;
                    } else {
                        $error = 'Failed to update event';
                    }
                }
            }
        }
    }
    
    // Handle speaker form separately
    if ($subAction === 'speaker') {
        $speakerData = [
            'event_id' => $eventId,
            'name' => trim($_POST['name'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
            'twitter' => trim($_POST['twitter'] ?? ''),
            'linkedin' => trim($_POST['linkedin'] ?? ''),
            'display_order' => (int)($_POST['display_order'] ?? 0)
        ];
        
        // Handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/speakers/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $speakerData['photo'] = $fileName;
            }
        }
        
        if (isset($_POST['speaker_id']) && !empty($_POST['speaker_id'])) {
            // Update existing speaker
            $result = updateSpeaker($_POST['speaker_id'], $speakerData);
            if ($result) {
                $_SESSION['success_message'] = 'Speaker updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to update speaker';
            }
        } else {
            // Add new speaker
            $result = addSpeaker($speakerData);
            if ($result) {
                $_SESSION['success_message'] = 'Speaker added successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to add speaker';
            }
        }
        
        header("Location: events.php?action=edit&id=$eventId#speakers");
        exit;
    }
    
    // Handle gallery form
    if ($subAction === 'gallery') {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/gallery/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoData = [
                    'event_id' => $eventId,
                    'image_url' => '/assets/uploads/gallery/' . $fileName,
                    'caption' => trim($_POST['caption'] ?? '')
                ];
                addGalleryPhoto($eventId, $photoData);
            }
        }
        header("Location: events.php?action=edit&id=$eventId#gallery");
        exit;
    }
}

// Delete speaker
if (isset($_GET['sub']) && $_GET['sub'] === 'delete_speaker') {
    $eventId = $_GET['id'] ?? null;
    $speakerId = $_GET['speaker_id'] ?? 0;

    if (deleteSpeaker($speakerId)) {
        $_SESSION['success_message'] = 'Speaker deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete speaker';
    }
    header("Location: events.php?action=edit&id=$eventId#speakers");
    exit;
}

// Delete gallery Image
if (isset($_GET['sub']) && $_GET['sub'] === 'delete_gallery') {
    $eventId = $_GET['id'] ?? null;
    $photoId = $_GET['photo_id'] ?? 0;

    if (deleteGalleryPhoto($photoId)) {
            $_SESSION['success_message'] = 'Photo deleted successfully!';
       } else {
           $_SESSION['error_message'] = 'Failed to delete photo';
       }
       header("Location: events.php?action=edit&id=$eventId#gallery");
       exit;
}

// Handle delete actions
if ($action === 'delete') {
    if ($subAction === 'speaker') {
        $speakerId = $_GET['speaker_id'] ?? 0;
        deleteSpeaker($speakerId);
        header("Location: events.php?action=edit&id=$eventId#speakers");
        exit;
    } elseif ($subAction === 'delete_gallery') {
        $photoId = $_GET['photo_id'] ?? 0;
        if (deleteGalleryPhoto($photoId)) {
            $_SESSION['success_message'] = 'Photo deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete photo';
        }
        header("Location: events.php?action=edit&id=$eventId#gallery");
        exit;
    } else {
        $event = getEventById($eventId);
        if ($event) {
            if (deleteEvent($eventId)) {
                // Delete associated image
                if (!empty($event['image'])) {
                    $imagePath = '../' . ltrim($event['image'], '/');
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                $_SESSION['success_message'] = 'Event deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete event';
            }
        }
        header('Location: events.php');
        exit;
    }
}

// Load event data if editing
if ($action === 'edit') {
    $event = getEventById($eventId);
    if (!$event) {
        $_SESSION['error_message'] = 'Event not found';
        header('Location: events.php');
        exit;
    }
}

// Format event date/time
$eventDateTime = new DateTime($event['event_date']);
$formattedDate = $eventDateTime->format('Y-m-d');
$formattedTime = $eventDateTime->format('H:i');
$scheduleItems = json_decode($event['schedule'] ?? '[]', true);

// Set page title and breadcrumb
$page_title = "Manage Events";
$breadcrumb = [
    ['title' => 'Events', 'active' => $action === 'list'],
    ['title' => 'Add New', 'active' => $action === 'add'],
    ['title' => 'Edit', 'active' => $action === 'edit']
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if ($action === 'list'): ?>
        <!-- Events List -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">All Events</h5>
                <a href="events.php?action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 data-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">ID</th>
                                <th>Title</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Capacity</th>
                                <th>Registrations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $events = getAllEvents(1, 100, false)['events'];
                            foreach ($events as $eventItem):
                                $itemDate = new DateTime($eventItem['event_date']);
                                $regCount = fetchSingle("SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?", [$eventItem['id']])['count'];
                            ?>
                            <tr>
                                <td><?= $eventItem['id'] ?></td>
                                <td>
                                    <a href="events.php?action=edit&id=<?= $eventItem['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($eventItem['title']) ?>
                                    </a>
                                </td>
                                <td><?= $itemDate->format('M j, Y g:i A') ?></td>
                                <td><?= htmlspecialchars($eventItem['location']) ?></td>
                                <td><?= $eventItem['capacity'] ?></td>
                                <td>
                                    <a href="event-registrations.php?event_id=<?= $eventItem['id'] ?>">
                                        <?= $regCount ?> / <?= $eventItem['capacity'] ?>
                                    </a>
                                </td>
                                <td class="table-actions">
                                    <a href="events.php?action=edit&id=<?= $eventItem['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="events.php?action=delete&id=<?= $eventItem['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($events)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">No events found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Event Form -->
        <div class="card admin-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><?= $action === 'add' ? 'Add New' : 'Edit' ?> Event</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-md-12">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= htmlspecialchars($event['title']) ?>" required>
                        </div>
                        
                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control summernote" id="description" name="description" 
                                      rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
                        </div>
                        
                        <!-- Location -->
                        <div class="col-md-12">
                            <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?= htmlspecialchars($event['location']) ?>" required>
                        </div>
                        
                        <!-- Date & Time -->
                        <div class="col-md-6">
                            <label for="event_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="event_date" name="event_date" 
                                   value="<?= $formattedDate ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="event_time" class="form-label">Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="event_time" name="event_time" 
                                   value="<?= $formattedTime ?>" required>
                        </div>
                        
                        <!-- Capacity -->
                        <div class="col-md-6">
                            <label for="capacity" class="form-label">Capacity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="capacity" name="capacity" 
                                   value="<?= $event['capacity'] ?>" min="1" required>
                        </div>
                        
                        <!-- Has Food -->
                        <div class="col-md-6">
                            <div class="form-check mt-4 pt-3">
                                <input class="form-check-input" type="checkbox" id="has_food" name="has_food" 
                                       <?= $event['has_food'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="has_food">Will food be served?</label>
                            </div>
                        </div>
                        
                        <!-- Schedule -->
                        <div class="col-12">
                            <label class="form-label">Event Schedule</label>
                            <div id="schedule-items">
                                <?php foreach ($scheduleItems as $index => $item): ?>
                                <div class="row g-2 mb-2 schedule-item">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="schedule[<?= $index ?>][time]" 
                                               placeholder="Time" value="<?= htmlspecialchars($item['time'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="schedule[<?= $index ?>][activity]" 
                                               placeholder="Activity" value="<?= htmlspecialchars($item['activity'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" class="form-control" name="schedule[<?= $index ?>][speaker]" 
                                               placeholder="Speaker" value="<?= htmlspecialchars($item['speaker'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-schedule">×</button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" id="add-schedule" class="btn btn-sm btn-outline-secondary mt-2">
                                <i class="fas fa-plus"></i> Add Schedule Item
                            </button>
                        </div>
                        
                        <!-- Image -->
                        <div class="col-md-12">
                            <label for="image" class="form-label">Featured Image</label>
                            <?php if (!empty($event['image'])): ?>
                                <div class="mb-3">
                                    <img src=" <?= BASE_URL . $event['image'] ?>" alt="Current image" class="img-thumbnail" style="max-height: 200px;">
                                    <input type="hidden" name="existing_image" value="<?= $event['image'] ?>">
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">Remove current image</label>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="existing_image" value="">
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Recommended size: 800x450 pixels</div>
                        </div>
                        
                        <!-- Submit -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Add Event' : 'Update Event' ?></button>
                            <a href="events.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($action === 'edit'): ?>
            <!-- Speakers Section -->
            <div class="card admin-card mt-4" id="speakers">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Event Speakers</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#speakerModal">
                        <i class="fas fa-plus me-1"></i> Add Speaker
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php 
                    $speakers = getEventSpeakers($eventId);
                    if (!empty($speakers)):
                    ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Title</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($speakers as $speaker): ?>
                                    <tr>
                                        <td>
                                            <?php if (!empty($speaker['photo'])): ?>
                                                <img src="<?= BASE_URL ?>assets/uploads/speakers/<?= $speaker['photo'] ?>"height="50" width="50" class="rounded-circle">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-light" style="width:50px;height:50px;"></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($speaker['name']) ?></td>
                                        <td><?= htmlspecialchars($speaker['title']) ?></td>
                                        <td><?= $speaker['display_order'] ?></td>
                                        <td class="table-actions">
                                            <button class="btn btn-sm btn-outline-primary edit-speaker" 
                                                    data-speaker-id="<?= $speaker['id'] ?>" 
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="events.php?action=edit&id=<?= $eventId ?>&sub=delete_speaker&speaker_id=<?= $speaker['id'] ?>" 
                                            class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">No speakers added yet</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Gallery Section -->
            <div class="card admin-card mt-4" id="gallery">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0">Event Gallery</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#galleryModal">
                        <i class="fas fa-plus me-1"></i> Add Photo
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php 
                    $gallery = getEventGallery($eventId);
                    if (!empty($gallery)):
                    ?>
                        <div class="row g-2 p-3">
                            <?php foreach ($gallery as $photo): ?>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="gallery-item position-relative">
                                    <img src="<?= BASE_URL . $photo['image_url'] ?>" class="img-fluid rounded" style="height: 120px; width: 100%; object-fit: cover;">
                                    <div class="gallery-actions position-absolute top-0 end-0 p-1">
                                        <a href="events.php?action=edit&id=<?= $eventId ?>&sub=delete_gallery&photo_id=<?= $photo['id'] ?>" 
                                           class="btn btn-sm btn-danger confirm-delete" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                    <div class="p-2 small text-truncate" title="<?= htmlspecialchars($photo['caption']) ?>">
                                        <?= htmlspecialchars($photo['caption']) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">No photos added yet</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Speaker Modal -->
            <div class="modal fade" id="speakerModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data" action="events.php?action=edit&id=<?= $eventId ?>&sub=speaker">
                            <div class="modal-header">
                                <h5 class="modal-title" id="speakerModalLabel">Add Speaker</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="speaker_id" id="speaker_id" value="">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="title" class="form-label">Title/Position</label>
                                        <input type="text" class="form-control" id="stitle" name="title">
                                    </div>
                                    <div class="col-12">
                                        <label for="bio" class="form-label">Bio</label>
                                        <textarea class="form-control" id="bio" name="bio" rows="3"></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="twitter" class="form-label">Twitter Profile</label>
                                        <input type="url" class="form-control" id="twitter" name="twitter" placeholder="https://twitter.com/username">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="linkedin" class="form-label">LinkedIn Profile</label>
                                        <input type="url" class="form-control" id="linkedin" name="linkedin" placeholder="https://linkedin.com/in/username">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="display_order" class="form-label">Display Order</label>
                                        <input type="number" class="form-control" id="display_order" name="display_order" value="0">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="photo" class="form-label">Photo</label>
                                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Speaker</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Gallery Modal -->
            <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data" action="events.php?action=edit&id=<?= $eventId ?>&sub=gallery">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Gallery Photo</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="galleryPhoto" class="form-label">Photo <span class="text-danger">*</span></label>
                                    <input type="file" class="form-control" id="galleryPhoto" name="photo" accept="image/*" required>
                                </div>
                                <div class="mb-3">
                                    <label for="photoCaption" class="form-label">Caption</label>
                                    <input type="text" class="form-control" id="photoCaption" name="caption">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Photo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script>
            // Handle speaker edit
            document.querySelectorAll('.edit-speaker').forEach(btn => {
                btn.addEventListener('click', function() {
                    const speakerId = this.getAttribute('data-speaker-id');
                    fetch(`../includes/ajax/get-speaker.php?id=${speakerId}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('speakerModalLabel').textContent = 'Edit Speaker';
                            document.getElementById('speaker_id').value = data.id;
                            document.getElementById('name').value = data.name;
                            document.getElementById('stitle').value = data.title;
                            document.getElementById('bio').value = data.bio;
                            document.getElementById('twitter').value = data.twitter || '';
                            document.getElementById('linkedin').value = data.linkedin || '';
                            document.getElementById('display_order').value = data.display_order;
                            
                            const modal = new bootstrap.Modal(document.getElementById('speakerModal'));
                            modal.show();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Failed to load speaker data');
                        });
                });
            });

            // Schedule management
            document.getElementById('add-schedule').addEventListener('click', function() {
                const container = document.getElementById('schedule-items');
                const index = container.querySelectorAll('.schedule-item').length;
                
                const div = document.createElement('div');
                div.className = 'row g-2 mb-2 schedule-item';
                div.innerHTML = `
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="schedule[${index}][time]" placeholder="Time">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="schedule[${index}][activity]" placeholder="Activity">
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="schedule[${index}][speaker]" placeholder="Speaker">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm remove-schedule">×</button>
                    </div>
                `;
                
                container.appendChild(div);
            });

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-schedule')) {
                    e.target.closest('.schedule-item').remove();
                }
            });

            // Delete confirmation
            document.querySelectorAll('.confirm-delete').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this item?')) {
                        e.preventDefault();
                        return false;
                    }
                    // If confirmed, let the default action proceed
                    return true;
                });
            });
            </script>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>