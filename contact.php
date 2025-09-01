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
    $faqs = fetchAll("SELECT * FROM faqs ORDER BY display_order ASC LIMIT 6");
} catch (Exception $e) {
    error_log("FAQ Error: " . $e->getMessage());
}
?>

<!-- Contact Header Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Get In Touch</h1>
                <p class="lead mb-4">We'd love to hear from you! Reach out with questions, feedback, or partnership inquiries.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#contact-form" class="btn btn-light btn-lg">Send a Message</a>
                    <a href="tel:<?= htmlspecialchars($contactInfo['contact_phone'] ?? '') ?>" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-phone me-2"></i> Call Us
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/contact-us.jpg" alt="Contact IYEF" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h3 class="h5">Our Location</h3>
                        <address class="mb-0">
                            <?= nl2br(htmlspecialchars($contactInfo['address'] ?? '123 NGO Street, City, Country')) ?>
                        </address>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-envelope fa-2x"></i>
                        </div>
                        <h3 class="h5">Email Us</h3>
                        <p class="mb-1">
                            <a href="mailto:<?= htmlspecialchars($contactInfo['contact_email'] ?? 'info@iyef.org') ?>" class="text-decoration-none">
                                <?= htmlspecialchars($contactInfo['contact_email'] ?? 'info@iyef.org') ?>
                            </a>
                        </p>
                        <p class="mb-0">
                            <small class="text-muted">Response time: 24-48 hours</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-phone-alt fa-2x"></i>
                        </div>
                        <h3 class="h5">Call Us</h3>
                        <p class="mb-1">
                            <a href="tel:<?= htmlspecialchars($contactInfo['contact_phone'] ?? '+1234567890') ?>" class="text-decoration-none">
                                <?= htmlspecialchars($contactInfo['contact_phone'] ?? '+123 456 7890') ?>
                            </a>
                        </p>
                        <p class="mb-0">
                            <small class="text-muted">Mon-Fri, 9am-5pm</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section id="contact-form" class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Send Us a Message</h2>
                        
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <?= $_SESSION['success_message'] ?>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php endif; ?>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger">
                                <?= $errors['general'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                                           id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                                    <?php if (isset($errors['name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['name'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-12">
                                    <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['subject']) ? 'is-invalid' : '' ?>" 
                                           id="subject" name="subject" value="<?= htmlspecialchars($_POST['subject'] ?? '') ?>" required>
                                    <?php if (isset($errors['subject'])): ?>
                                        <div class="invalid-feedback"><?= $errors['subject'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-12">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea class="form-control <?= isset($errors['message']) ? 'is-invalid' : '' ?>" 
                                              id="message" name="message" rows="5" required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                    <?php if (isset($errors['message'])): ?>
                                        <div class="invalid-feedback"><?= $errors['message'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Frequently Asked Questions</h2>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($faqs)): ?>
                    <div class="accordion" id="faqAccordion">
                        <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item border-0 shadow-sm mb-3">
                            <h3 class="accordion-header" id="faqHeading<?= $index ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faqCollapse<?= $index ?>" aria-expanded="false" 
                                        aria-controls="faqCollapse<?= $index ?>">
                                    <?= htmlspecialchars($faq['question']) ?>
                                </button>
                            </h3>
                            <div id="faqCollapse<?= $index ?>" class="accordion-collapse collapse" 
                                 aria-labelledby="faqHeading<?= $index ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="faq.php" class="btn btn-outline-primary">View All FAQs</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <p>Our FAQs section is currently being updated. Please check back later or contact us directly with your questions.</p>
                        <a href="#contact-form" class="btn btn-primary mt-2">Contact Us</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.621030318223!2d3.3792954147708736!3d6.452996295329036!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMjcnMTAuOCJOIDPCsDIyJzUxLjAiRQ!5e0!3m2!1sen!2sng!4v1620000000000!5m2!1sen!2sng" 
                                    width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center py-3">
                        <a href="https://maps.google.com/?q=<?= urlencode($contactInfo['address'] ?? '') ?>" 
                           target="_blank" class="btn btn-sm btn-primary">
                            <i class="fas fa-directions me-1"></i> Get Directions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
require_once 'includes/footer.php';
?>