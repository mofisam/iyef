<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/testimonials.php';

$success = false;
$errors = [];
$formData = [
    'author_name' => '',
    'author_title' => '',
    'email' => '',
    'content' => '',
    'rating' => '5'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $formData = [
        'author_name' => trim($_POST['author_name']),
        'author_title' => trim($_POST['author_title']),
        'email' => trim($_POST['email']),
        'content' => trim($_POST['content']),
        'rating' => $_POST['rating']
    ];

    // Validate name
    if (empty($formData['author_name'])) {
        $errors['author_name'] = 'Please enter your name';
    } elseif (strlen($formData['author_name']) < 2) {
        $errors['author_name'] = 'Name must be at least 2 characters';
    }

    // Validate email
    if (empty($formData['email'])) {
        $errors['email'] = 'Please enter your email address';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    // Validate content
    if (empty($formData['content'])) {
        $errors['content'] = 'Please share your experience';
    } elseif (strlen($formData['content']) < 20) {
        $errors['content'] = 'Testimonial should be at least 20 characters';
    } elseif (strlen($formData['content']) > 1000) {
        $errors['content'] = 'Testimonial should not exceed 1000 characters';
    }

    // Handle image upload
    $authorImage = '';
    if (isset($_FILES['author_image']) && $_FILES['author_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['author_image']['type'], $allowedTypes)) {
            $errors['author_image'] = 'Please upload a valid image (JPEG, PNG, GIF, or WEBP)';
        } elseif ($_FILES['author_image']['size'] > $maxFileSize) {
            $errors['author_image'] = 'Image size must be less than 2MB';
        } else {
            $uploadDir = 'assets/uploads/testimonials/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['author_image']['name']));
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['author_image']['tmp_name'], $targetPath)) {
                $authorImage = '/' . $targetPath;
            } else {
                $errors['author_image'] = 'Failed to upload image. Please try again.';
            }
        }
    }

    // If no errors, submit testimonial
    if (empty($errors)) {
        $testimonialData = [
            'author_name' => $formData['author_name'],
            'author_title' => $formData['author_title'],
            'author_image' => $authorImage,
            'content' => $formData['content'],
            'rating' => (int)$formData['rating'],
            'is_approved' => 0, // Needs admin approval
            'is_featured' => 0
        ];

        $result = createTestimonial($testimonialData);

        if ($result['status'] === 'success') {
            $success = true;
            $formData = []; // Clear form on success
            
            // Send notification email to admin (optional)
            sendTestimonialNotification($testimonialData);
        } else {
            $errors['general'] = 'Sorry, there was an error submitting your testimonial. Please try again.';
        }
    }
}

$page_title = "Share Your Experience";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-5 fw-bold mb-3">Share Your IYEF Experience</h1>
                <p class="lead mb-4">Your story can inspire others and help us continue our mission of empowering youth</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#testimonial-form" class="btn btn-light btn-lg">Share Your Story</a>
                    <a href="/testimonials" class="btn btn-outline-light btn-lg">View Testimonials</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Success Message -->
<?php if ($success): ?>
<section class="py-4">
    <div class="container">
        <div class="alert alert-success text-center">
            <div class="py-3">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h3 class="mb-2">Thank You for Sharing!</h3>
                <p class="mb-1">Your testimonial has been submitted successfully and is awaiting approval.</p>
                <p class="mb-0">We appreciate you taking the time to share your experience with IYEF.</p>
            </div>
        </div>
        <div class="text-center mt-4">
            <a href="/testimonials" class="btn btn-primary me-3">View All Testimonials</a>
            <a href="/" class="btn btn-outline-primary">Return to Home</a>
        </div>
    </div>
</section>
<?php else: ?>

<!-- Testimonial Form Section -->
<section id="testimonial-form" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Share Your Story</h2>
                        <p class="text-center text-muted mb-4">
                            Tell us about your experience with IYEF. Your feedback helps us improve and inspires others to join our mission.
                        </p>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?= $errors['general'] ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" novalidate>
                            <div class="row g-3">
                                <!-- Personal Information -->
                                <div class="col-md-6">
                                    <label for="author_name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['author_name']) ? 'is-invalid' : '' ?>" 
                                           id="author_name" name="author_name" 
                                           value="<?= htmlspecialchars($formData['author_name']) ?>" required>
                                    <?php if (isset($errors['author_name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['author_name'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="author_title" class="form-label">Your Title/Role</label>
                                    <input type="text" class="form-control" id="author_title" name="author_title" 
                                           value="<?= htmlspecialchars($formData['author_title']) ?>"
                                           placeholder="e.g., Program Participant, Volunteer, Parent">
                                    <div class="form-text">Optional - how are you connected to IYEF?</div>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" 
                                           value="<?= htmlspecialchars($formData['email']) ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                    <?php endif; ?>
                                    <div class="form-text">We'll never share your email with anyone else.</div>
                                </div>
                                
                                <!-- Photo Upload -->
                                <div class="col-12">
                                    <label for="author_image" class="form-label">Your Photo</label>
                                    <input type="file" class="form-control <?= isset($errors['author_image']) ? 'is-invalid' : '' ?>" 
                                           id="author_image" name="author_image" accept="image/*">
                                    <?php if (isset($errors['author_image'])): ?>
                                        <div class="invalid-feedback"><?= $errors['author_image'] ?></div>
                                    <?php endif; ?>
                                    <div class="form-text">Optional - JPEG, PNG, or GIF under 2MB</div>
                                </div>
                                
                                <!-- Rating -->
                                <div class="col-12">
                                    <label class="form-label">Your Rating</label>
                                    <div class="rating-stars mb-3">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" 
                                                   <?= $formData['rating'] == $i ? 'checked' : '' ?>>
                                            <label for="star<?= $i ?>" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">
                                                <i class="fas fa-star"></i>
                                            </label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <!-- Testimonial Content -->
                                <div class="col-12">
                                    <label for="content" class="form-label">Your Experience <span class="text-danger">*</span></label>
                                    <textarea class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" 
                                              id="content" name="content" rows="6" 
                                              placeholder="Share how IYEF has impacted you or your community..." 
                                              required><?= htmlspecialchars($formData['content']) ?></textarea>
                                    <?php if (isset($errors['content'])): ?>
                                        <div class="invalid-feedback"><?= $errors['content'] ?></div>
                                    <?php endif; ?>
                                    <div class="form-text">
                                        <span id="charCount">0</span>/1000 characters
                                    </div>
                                </div>
                                
                                <!-- Privacy Consent -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input <?= isset($errors['consent']) ? 'is-invalid' : '' ?>" 
                                               type="checkbox" id="consent" name="consent" required>
                                        <label class="form-check-label" for="consent">
                                            I give permission for IYEF to use my testimonial on their website and marketing materials
                                        </label>
                                        <?php if (isset($errors['consent'])): ?>
                                            <div class="invalid-feedback"><?= $errors['consent'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Share Your Story
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Guidelines Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h3 class="text-center mb-4">Testimonial Guidelines</h3>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5>Do Share</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Your personal experience</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Specific programs you participated in</li>
                                    <li><i class="fas fa-check text-success me-2"></i>How IYEF helped you or your community</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Positive changes you've observed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex">
                            <div class="me-3 text-muted">
                                <i class="fas fa-times-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5>Please Avoid</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-times text-danger me-2"></i>Personal contact information</li>
                                    <li><i class="fas fa-times text-danger me-2"></i>Negative comments about others</li>
                                    <li><i class="fas fa-times text-danger me-2"></i>Promotional content</li>
                                    <li><i class="fas fa-times text-danger me-2"></i>Offensive language</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Existing Testimonials Preview -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2>What Others Are Saying</h2>
            <p class="lead">Read inspiring stories from our community members</p>
        </div>
        <?php
        // Display 3 featured testimonials
        $featuredTestimonials = getFeaturedTestimonials(3);
        if (!empty($featuredTestimonials)):
        ?>
        <div class="row g-4">
            <?php foreach ($featuredTestimonials as $testimonial): ?>
            <div class="col-md-4">
                <div class="card h-100 testimonial-card">
                    <div class="card-body text-center p-4">
                        <div class="testimonial-rating text-warning mb-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= $testimonial['rating'] ? '' : '-half-alt' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <div class="testimonial-content mb-4">
                            <p class="fst-italic">"<?= htmlspecialchars(substr($testimonial['content'], 0, 150)) ?>..."</p>
                        </div>
                        <div class="testimonial-author">
                            <?php if (!empty($testimonial['author_image'])): ?>
                                <img src="<?= $testimonial['author_image'] ?>" alt="<?= htmlspecialchars($testimonial['author_name']) ?>" 
                                     class="rounded-circle mb-2" width="60" height="60">
                            <?php endif; ?>
                            <h6 class="mb-1"><?= htmlspecialchars($testimonial['author_name']) ?></h6>
                            <?php if ($testimonial['author_title']): ?>
                                <p class="text-muted small mb-0"><?= htmlspecialchars($testimonial['author_title']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/testimonials" class="btn btn-outline-primary">View All Testimonials</a>
        </div>
        <?php else: ?>
        <div class="text-center py-4">
            <p class="text-muted">No testimonials yet. Be the first to share your story!</p>
        </div>
        <?php endif; ?>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>