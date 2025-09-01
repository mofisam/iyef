<?php
require_once '../config/db.php';
require_once '../includes/functions/testimonials.php';



$action = $_GET['action'] ?? 'list';
$testimonialId = $_GET['id'] ?? 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $testimonialData = [
            'author_name' => trim($_POST['author_name']),
            'author_title' => trim($_POST['author_title']),
            'content' => trim($_POST['content']),
            'rating' => (int)$_POST['rating'],
            'is_approved' => isset($_POST['is_approved']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'author_image' => $_POST['existing_image'] // Default to existing image
        ];

        // Handle image upload
        if (isset($_FILES['author_image']) && $_FILES['author_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/testimonials/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['author_image']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['author_image']['tmp_name'], $targetPath)) {
                $testimonialData['author_image'] = '/assets/uploads/testimonials/' . $fileName;
                
                // Delete old image if it exists
                if ($action === 'edit' && !empty($_POST['existing_image']) && $_POST['existing_image'] !== $testimonialData['author_image']) {
                    $oldImage = '../' . ltrim($_POST['existing_image'], '/');
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
            }
        }

        if ($action === 'add') {
            $result = createTestimonial($testimonialData);
            if ($result['status'] === 'success') {
                $_SESSION['success_message'] = 'Testimonial added successfully!';
                header('Location: testimonials.php');
                exit;
            } else {
                $error = $result['message'];
            }
        } elseif ($action === 'edit') {
            if (updateTestimonial($testimonialId, $testimonialData)) {
                $_SESSION['success_message'] = 'Testimonial updated successfully!';
                header('Location: testimonials.php');
                exit;
            } else {
                $error = 'Failed to update testimonial';
            }
        }
    }
}

// Handle quick actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'approve':
            if (approveTestimonial($testimonialId)) {
                $_SESSION['success_message'] = 'Testimonial approved!';
            }
            break;
            
        case 'toggle_featured':
            if (toggleFeaturedTestimonial($testimonialId)) {
                $_SESSION['success_message'] = 'Featured status updated!';
            }
            break;
            
        case 'delete':
            $testimonial = getTestimonialById($testimonialId);
            if ($testimonial) {
                if (deleteTestimonial($testimonialId)) {
                    $_SESSION['success_message'] = 'Testimonial deleted successfully!';
                } else {
                    $_SESSION['error_message'] = 'Failed to delete testimonial';
                }
            }
            break;
    }
    
    if (isset($_GET['action']) && $_GET['action'] !== 'list') {
        header('Location: testimonials.php');
        exit;
    }
}

// Set page title and breadcrumb
$page_title = "Manage Testimonials";
$breadcrumb = [
    ['title' => 'Testimonials', 'active' => $action === 'list'],
    ['title' => 'Add New', 'active' => $action === 'add'],
    ['title' => 'Edit', 'active' => $action === 'edit']
];

require_once 'includes/header.php';

// Get statistics
$stats = getTestimonialsStats();
?>

<div class="container-fluid">
    <?php if ($action === 'list'): ?>
        <!-- Testimonials List -->
        <div class="row mb-4">
            <!-- Statistics Cards -->
            <div class="col-md-3">
                <div class="card admin-card stat-card border-left-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-value text-primary"><?= $stats['total'] ?></div>
                                <div class="stat-label">Total Testimonials</div>
                            </div>
                            <div class="card-icon text-primary">
                                <i class="fas fa-comments"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card stat-card border-left-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-value text-success"><?= $stats['approved'] ?></div>
                                <div class="stat-label">Approved</div>
                            </div>
                            <div class="card-icon text-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card stat-card border-left-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-value text-warning"><?= $stats['pending'] ?></div>
                                <div class="stat-label">Pending Approval</div>
                            </div>
                            <div class="card-icon text-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card admin-card stat-card border-left-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="stat-value text-info"><?= $stats['featured'] ?></div>
                                <div class="stat-label">Featured</div>
                            </div>
                            <div class="card-icon text-info">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">All Testimonials</h5>
                <a href="testimonials.php?action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 data-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">ID</th>
                                <th>Author</th>
                                <th>Content</th>
                                <th>Rating</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $testimonials = getAllTestimonials(1, 100, false)['testimonials'];
                            foreach ($testimonials as $testimonial):
                                $createdAt = new DateTime($testimonial['created_at']);
                                $contentPreview = strlen($testimonial['content']) > 100 ? 
                                    substr($testimonial['content'], 0, 100) . '...' : $testimonial['content'];
                            ?>
                            <tr>
                                <td><?= $testimonial['id'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($testimonial['author_image'])): ?>
                                            <img src="<?= $testimonial['author_image'] ?>" alt="<?= htmlspecialchars($testimonial['author_name']) ?>" 
                                                 class="rounded-circle me-2" width="40" height="40">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-semibold"><?= htmlspecialchars($testimonial['author_name']) ?></div>
                                            <?php if ($testimonial['author_title']): ?>
                                                <div class="text-muted small"><?= htmlspecialchars($testimonial['author_title']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($contentPreview) ?></td>
                                <td>
                                    <div class="text-warning">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $testimonial['rating'] ? '' : '-half-alt' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($testimonial['is_approved']): ?>
                                        <span class="badge bg-success">Approved</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                    <?php if ($testimonial['is_featured']): ?>
                                        <span class="badge bg-info ms-1">Featured</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $createdAt->format('M j, Y') ?></td>
                                <td class="table-actions">
                                    <?php if (!$testimonial['is_approved']): ?>
                                        <a href="testimonials.php?action=approve&id=<?= $testimonial['id'] ?>" 
                                           class="btn btn-sm btn-outline-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="testimonials.php?action=toggle_featured&id=<?= $testimonial['id'] ?>" 
                                       class="btn btn-sm btn-outline-info" title="<?= $testimonial['is_featured'] ? 'Unfeature' : 'Feature' ?>">
                                        <i class="fas fa-star"></i>
                                    </a>
                                    <a href="testimonials.php?action=edit&id=<?= $testimonial['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="testimonials.php?action=delete&id=<?= $testimonial['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($testimonials)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">No testimonials found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Testimonial Form -->
        <?php
        $testimonial = [];
        if ($action === 'edit') {
            $testimonial = getTestimonialById($testimonialId);
            if (!$testimonial) {
                $_SESSION['error_message'] = 'Testimonial not found';
                header('Location: testimonials.php');
                exit;
            }
        }
        ?>
        
        <div class="card admin-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><?= $action === 'add' ? 'Add New' : 'Edit' ?> Testimonial</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <!-- Author Information -->
                        <div class="col-md-6">
                            <label for="author_name" class="form-label">Author Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="author_name" name="author_name" 
                                   value="<?= htmlspecialchars($testimonial['author_name'] ?? '') ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="author_title" class="form-label">Author Title/Position</label>
                            <input type="text" class="form-control" id="author_title" name="author_title" 
                                   value="<?= htmlspecialchars($testimonial['author_title'] ?? '') ?>">
                        </div>
                        
                        <!-- Author Image -->
                        <div class="col-md-12">
                            <label for="author_image" class="form-label">Author Photo</label>
                            <?php if (!empty($testimonial['author_image'])): ?>
                                <div class="mb-3">
                                    <img src="<?= $testimonial['author_image'] ?>" alt="Current photo" class="img-thumbnail" style="max-height: 200px;">
                                    <input type="hidden" name="existing_image" value="<?= $testimonial['author_image'] ?>">
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">Remove current photo</label>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="existing_image" value="">
                            <?php endif; ?>
                            <input type="file" class="form-control" id="author_image" name="author_image" accept="image/*">
                            <div class="form-text">Recommended: Square image, minimum 200x200 pixels</div>
                        </div>
                        
                        <!-- Content -->
                        <div class="col-md-12">
                            <label for="content" class="form-label">Testimonial Content <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="content" name="content" rows="5" 
                                      required><?= htmlspecialchars($testimonial['content'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- Rating -->
                        <div class="col-md-6">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" id="rating" name="rating">
                                <option value="5" <?= ($testimonial['rating'] ?? 5) == 5 ? 'selected' : '' ?>>5 Stars - Excellent</option>
                                <option value="4" <?= ($testimonial['rating'] ?? 5) == 4 ? 'selected' : '' ?>>4 Stars - Very Good</option>
                                <option value="3" <?= ($testimonial['rating'] ?? 5) == 3 ? 'selected' : '' ?>>3 Stars - Good</option>
                                <option value="2" <?= ($testimonial['rating'] ?? 5) == 2 ? 'selected' : '' ?>>2 Stars - Fair</option>
                                <option value="1" <?= ($testimonial['rating'] ?? 5) == 1 ? 'selected' : '' ?>>1 Star - Poor</option>
                            </select>
                        </div>
                        
                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="is_approved" name="is_approved" 
                                       <?= ($testimonial['is_approved'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_approved">Approved</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                       <?= ($testimonial['is_featured'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_featured">Featured</label>
                            </div>
                        </div>
                        
                        <!-- Submit -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Add Testimonial' : 'Update Testimonial' ?></button>
                            <a href="testimonials.php" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>