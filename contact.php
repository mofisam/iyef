<?php
// Start session at the absolute beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Contact Us";

// Handle form submission before any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config/db.php';
    require_once 'config/db_functions.php';
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    $errors = [];
    
    // Validate inputs
    if (empty($name)) {
        $errors['name'] = 'Please enter your name';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Please enter your email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($subject)) {
        $errors['subject'] = 'Please enter a subject';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Please enter your message';
    }
    
    if (empty($errors)) {
        $inserted = insertRecord('contact_messages', [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'ip_address' => $_SERVER['REMOTE_ADDR']
        ]);
        
        if ($inserted) {
            $_SESSION['success_message'] = 'Thank you for your message! We will get back to you soon.';
            header('Location: contact.php');
            exit;
        } else {
            $errors['general'] = 'There was an error sending your message. Please try again.';
        }
    }
}

// Now include header after all potential redirects
require_once 'includes/header.php';

// Get contact info
$contactInfo = fetchSingle("SELECT * FROM settings LIMIT 1");

// Get FAQs (with error handling)
$faqs = [];
try {
    $faqs = fetchAll("SELECT * FROM faqs WHERE is_active = 1 ORDER BY display_order ASC LIMIT 6");
} catch (Exception $e) {
    error_log("FAQ Error: " . $e->getMessage());
}
?>

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-60 d-flex align-items-center">
    <!-- Background with gradient overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.9), rgba(0, 123, 255, 0.85)), url('assets/images/contact-hero.jpg'); background-size: cover; background-position: center;"></div>
    
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
                    Let's Connect &<br>
                    <span class="text-warning">Empower Together</span>
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    Reach out to us for inquiries, partnerships, or to learn how you can join our mission of transforming youth lives worldwide.
                </p>
                
                <!-- Action buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <a href="#contact-form" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-paper-plane me-2"></i> Send Message
                    </a>
                    <a href="#contact-info" class="btn btn-outline-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-phone-alt me-2"></i> Contact Info
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#contact-info" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <span class="small mb-2 opacity-75">Explore Contact Options</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- Contact Information Section - Modern Cards -->
<section id="contact-info" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">GET IN TOUCH</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Multiple Ways to Connect</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Choose your preferred method to reach out to our dedicated support team
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- Location Card -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                    <div class="card-header bg-primary bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-primary text-white rounded-3 p-3 me-3">
                                <i class="fas fa-map-marker-alt fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="h4 fw-bold mb-0">Visit Us</h3>
                                <small class="text-muted">Our physical location</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="location-info">
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                        <i class="fas fa-building"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold mb-2">Office Address</h5>
                                    <address class="mb-0 text-muted">
                                        <?= nl2br(htmlspecialchars($contactInfo['address'] ?? '123 Youth Empowerment Street<br>City, Country')) ?>
                                    </address>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold mb-2">Working Hours</h5>
                                    <p class="mb-0 text-muted">Monday - Friday: 9:00 AM - 5:00 PM<br>Saturday: 10:00 AM - 2:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-center">
                        <a href="https://maps.google.com/?q=<?= urlencode($contactInfo['address'] ?? '') ?>" 
                           target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-directions me-1"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Email Card -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                    <div class="card-header bg-success bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-success text-white rounded-3 p-3 me-3">
                                <i class="fas fa-envelope fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="h4 fw-bold mb-0">Email Us</h3>
                                <small class="text-muted">Preferred for detailed inquiries</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="email-info">
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                                        <i class="fas fa-at"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold mb-2">Primary Email</h5>
                                    <a href="mailto:<?= htmlspecialchars($contactInfo['contact_email'] ?? 'info@iyef.org') ?>" 
                                       class="text-decoration-none text-muted fw-bold">
                                        <?= htmlspecialchars($contactInfo['contact_email'] ?? 'info@iyef.org') ?>
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                        <i class="fas fa-headset"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold mb-2">Support Email</h5>
                                    <a href="mailto:support@iyef.org" class="text-decoration-none text-muted fw-bold">
                                        support@iyef.org
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                                        <i class="fas fa-history"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold mb-2">Response Time</h5>
                                    <p class="mb-0 text-muted">Typically within <span class="fw-bold">24-48 hours</span> during business days</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-center">
                        <a href="mailto:<?= htmlspecialchars($contactInfo['contact_email'] ?? 'info@iyef.org') ?>" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-paper-plane me-1"></i> Send Email
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Phone & Social Card -->
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                    <div class="card-header bg-warning bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-warning text-white rounded-3 p-3 me-3">
                                <i class="fas fa-phone-alt fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="h4 fw-bold mb-0">Call & Connect</h3>
                                <small class="text-muted">Direct communication channels</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="contact-channels">
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                        <i class="fas fa-phone-volume"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold mb-2">Phone Support</h5>
                                    <a href="tel:<?= htmlspecialchars($contactInfo['contact_phone'] ?? '+1234567890') ?>" 
                                       class="text-decoration-none text-dark fw-bold">
                                        <?= htmlspecialchars($contactInfo['contact_phone'] ?? '+123 456 7890') ?>
                                    </a>
                                    <p class="small text-muted mb-0 mt-1">Available Mon-Fri, 9am-5pm</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-start mb-4">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="fw-bold mb-2">WhatsApp</h5>
                                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $contactInfo['contact_phone'] ?? '') ?>" 
                                       target="_blank" class="text-decoration-none text-success fw-bold">
                                        Message on WhatsApp
                                    </a>
                                    <p class="small text-muted mb-0 mt-1">Quick responses via chat</p>
                                </div>
                            </div>
                            <div class="social-links">
                                <h6 class="fw-bold mb-3">Follow Us</h6>
                                <div class="d-flex gap-2">
                                    <?php if (!empty($contactInfo['facebook_url'])): ?>
                                        <a href="<?= htmlspecialchars($contactInfo['facebook_url']) ?>" target="_blank" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-facebook-f"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($contactInfo['twitter_url'])): ?>
                                        <a href="<?= htmlspecialchars($contactInfo['twitter_url']) ?>" target="_blank" 
                                           class="btn btn-outline-info btn-sm">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($contactInfo['instagram_url'])): ?>
                                        <a href="<?= htmlspecialchars($contactInfo['instagram_url']) ?>" target="_blank" 
                                           class="btn btn-outline-danger btn-sm">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($contactInfo['linkedin_url'] ?? '')): ?>
                                        <a href="<?= htmlspecialchars($contactInfo['linkedin_url'] ?? '') ?>" target="_blank" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fab fa-linkedin-in"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 text-center">
                        <a href="tel:<?= htmlspecialchars($contactInfo['contact_phone'] ?? '+1234567890') ?>" 
                           class="btn btn-warning btn-sm text-white">
                            <i class="fas fa-phone me-1"></i> Call Now
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section - Modern Design -->
<section id="contact-form" class="py-5 bg-gradient-light">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="position-relative">
                    <!-- Illustration/Image -->
                    <div class="text-center">
                        <div class="bg-primary bg-opacity-10 rounded-4 p-5 mb-4">
                            <i class="fas fa-comments fa-5x text-primary"></i>
                        </div>
                        <h3 class="h3 fw-bold mb-3">Direct Message Portal</h3>
                        <p class="text-muted mb-4">
                            Fill out the form and our team will get back to you promptly. We're committed to responding to every inquiry.
                        </p>
                        
                        <!-- Benefits List -->
                        <div class="text-start">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Quick Response</h6>
                                    <p class="small text-muted mb-0">Guaranteed reply within 48 hours</p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Secure & Private</h6>
                                    <p class="small text-muted mb-0">Your information is protected</p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-1">Multiple Departments</h6>
                                    <p class="small text-muted mb-0">Connects you with the right team</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-header bg-white border-0 py-4">
                        <div class="text-center">
                            <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-2">
                                <span class="small fw-bold">SEND MESSAGE</span>
                            </div>
                            <h2 class="h2 fw-bold mb-0">Contact Form</h2>
                            <small class="text-muted">Fill in your details below</small>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-lg-5">
                        <!-- Success/Error Messages -->
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success alert-dismissible fade show animate-fade-in" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle fa-lg me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <?= $_SESSION['success_message'] ?>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show animate-fade-in" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle fa-lg me-2"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <?= $errors['general'] ?>
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Contact Form -->
                        <form method="POST" id="contactForm" class="needs-validation" novalidate>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                               id="name" name="name" placeholder="Your Full Name" 
                                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                        <label for="name">Full Name <span class="text-danger">*</span></label>
                                        <?php if (isset($errors['name'])): ?>
                                            <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                        <?php else: ?>
                                            <div class="valid-feedback">Looks good!</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                               id="email" name="email" placeholder="name@example.com" 
                                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                        <label for="email">Email Address <span class="text-danger">*</span></label>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                        <?php else: ?>
                                            <div class="valid-feedback">Looks good!</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" 
                                               id="subject" name="subject" placeholder="Message Subject" 
                                               value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                                        <label for="subject">Subject <span class="text-danger">*</span></label>
                                        <?php if (isset($errors['subject'])): ?>
                                            <div class="invalid-feedback"><?= $errors['subject'] ?></div>
                                        <?php else: ?>
                                            <div class="valid-feedback">Looks good!</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" 
                                                  id="message" name="message" placeholder="Your Message" 
                                                  style="height: 150px" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                        <label for="message">Message <span class="text-danger">*</span></label>
                                        <div class="form-text">Please provide detailed information about your inquiry</div>
                                        <?php if (isset($errors['message'])): ?>
                                            <div class="invalid-feedback"><?= $errors['message'] ?></div>
                                        <?php else: ?>
                                            <div class="valid-feedback">Looks good!</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Form Submission -->
                                <div class="col-12 mt-4">
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg py-3">
                                            <i class="fas fa-paper-plane me-2"></i> Send Message
                                        </button>
                                    </div>
                                    <p class="text-center text-muted small mt-3">
                                        By submitting this form, you agree to our 
                                        <a href="privacy.php" class="text-decoration-none">Privacy Policy</a>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section - Modern Design -->
<section class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">COMMON QUESTIONS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Find quick answers to common questions about our programs, partnerships, and youth empowerment initiatives.
                </p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if (!empty($faqs)): ?>
                    <div class="row g-4">
                        <?php foreach (array_chunk($faqs, 3) as $faqChunk): ?>
                            <?php foreach ($faqChunk as $faq): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 border-0 shadow-sm hover-lift">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-start mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                                    <i class="fas fa-question-circle"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="fw-bold mb-2"><?= htmlspecialchars($faq['question']) ?></h5>
                                                <p class="text-muted small mb-0"><?= nl2br(htmlspecialchars(substr($faq['answer'], 0, 100))) ?><?= strlen($faq['answer']) > 100 ? '...' : '' ?></p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary w-100 faq-modal-btn" 
                                                data-question="<?= htmlspecialchars($faq['question']) ?>" 
                                                data-answer="<?= htmlspecialchars($faq['answer']) ?>">
                                            Read Full Answer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-5">
                        <a href="faq.php" class="btn btn-outline-primary btn-lg px-5">
                            <i class="fas fa-question-circle me-2"></i> View All FAQs
                        </a>
                    </div>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-question-circle fa-4x text-muted mb-4 opacity-50"></i>
                                <h4 class="fw-bold mb-3">FAQs Coming Soon</h4>
                                <p class="text-muted mb-4">Our frequently asked questions are being curated to better serve you.</p>
                                <a href="#contact-form" class="btn btn-outline-primary">Ask a Question</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Map Section - Modern Design -->
<section class="bg-light py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">FIND US</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Our Location</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Visit our headquarters or connect with our regional offices around the world
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-body p-0">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.621030318223!2d3.3792954147708736!3d6.452996295329036!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMjcnMTAuOCJOIDPCsDIyJzUxLjAiRQ!5e0!3m2!1sen!2sng!4v1620000000000!5m2!1sen!2sng" 
                                    style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="h4 fw-bold mb-4">Visit Our Office</h3>
                        
                        <div class="mb-4">
                            <h5 class="fw-bold mb-2"><i class="fas fa-map-marker-alt text-primary me-2"></i> Headquarters</h5>
                            <address class="text-muted mb-0">
                                <?= nl2br(htmlspecialchars($contactInfo['address'] ?? '123 Youth Empowerment Street<br>City, Country')) ?>
                            </address>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="fw-bold mb-2"><i class="fas fa-clock text-success me-2"></i> Office Hours</h5>
                            <p class="text-muted mb-0">
                                <strong>Weekdays:</strong> 9:00 AM - 5:00 PM<br>
                                <strong>Saturdays:</strong> 10:00 AM - 2:00 PM<br>
                                <strong>Sundays:</strong> Closed
                            </p>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="https://maps.google.com/?q=<?= urlencode($contactInfo['address'] ?? '') ?>" 
                               target="_blank" class="btn btn-primary">
                                <i class="fas fa-directions me-2"></i> Get Directions
                            </a>
                            <a href="tel:<?= htmlspecialchars($contactInfo['contact_phone'] ?? '+1234567890') ?>" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-phone me-2"></i> Call Before Visiting
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Modal -->
<div class="modal fade" id="faqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="faqModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="faqModalAnswer"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(event) {
            if (!this.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            this.classList.add('was-validated');
        });
    }
    
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
    
    // FAQ Modal
    const faqModalBtns = document.querySelectorAll('.faq-modal-btn');
    const faqModal = new bootstrap.Modal(document.getElementById('faqModal'));
    
    faqModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const question = this.getAttribute('data-question');
            const answer = this.getAttribute('data-answer');
            
            document.getElementById('faqModalTitle').textContent = question;
            document.getElementById('faqModalAnswer').textContent = answer;
            
            faqModal.show();
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

.bg-gradient-light {
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
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

.form-floating > .form-control:focus,
.form-floating > .form-control:not(:placeholder-shown) {
    padding-top: 1.625rem;
    padding-bottom: 0.625rem;
}

.form-floating > label {
    transition: all 0.3s ease;
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
}
</style>

<?php 
require_once 'includes/footer.php';
?>