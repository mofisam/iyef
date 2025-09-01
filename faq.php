<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Frequently Asked Questions";
require_once 'includes/header.php';

// Get all FAQs from database with error handling
$faqs = [];
try {
    require_once 'config/db.php';
    require_once 'config/db_functions.php';
    $faqs = fetchAll("SELECT * FROM faqs ORDER BY display_order ASC");
} catch (Exception $e) {
    error_log("FAQ Error: " . $e->getMessage());
    $error = "We're having trouble loading the FAQs. Please try again later.";
}
?>

<!-- FAQ Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Frequently Asked Questions</h1>
                <p class="lead mb-4">Find answers to common questions about our programs, events, and services.</p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#faq-accordion" class="btn btn-light btn-lg">Browse FAQs</a>
                    <a href="#contact-prompt" class="btn btn-outline-light btn-lg">Still have questions?</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="/assets/images/faq-hero.jpg" alt="FAQs" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Main FAQ Section -->
<section class="py-5" id="faq-accordion">
    <div class="container">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center">
                <?= $error ?>
            </div>
        <?php elseif (!empty($faqs)): ?>
            <div class="row justify-content-center">
                <div class="col-lg-10">
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
                                    
                                    <?php if (!empty($faq['related_link'])): ?>
                                        <div class="mt-3">
                                            <a href="<?= htmlspecialchars($faq['related_link']) ?>" class="btn btn-sm btn-outline-primary">
                                                Learn More
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="alert alert-info">
                    <p>Our FAQs are currently being updated. Please check back soon or contact us directly.</p>
                    <a href="contact.php" class="btn btn-primary mt-2">Contact Us</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FAQ Categories Section -->
<section class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Browse by Category</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-project-diagram fa-2x"></i>
                        </div>
                        <h3 class="h5">Programs</h3>
                        <p class="mb-3">Questions about our youth empowerment programs and initiatives</p>
                        <a href="#program-faqs" class="btn btn-sm btn-outline-primary">View Questions</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                        <h3 class="h5">Events</h3>
                        <p class="mb-3">Information about upcoming workshops, seminars, and activities</p>
                        <a href="#event-faqs" class="btn btn-sm btn-outline-primary">View Questions</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-hand-holding-heart fa-2x"></i>
                        </div>
                        <h3 class="h5">Support</h3>
                        <p class="mb-3">How to get involved, donate, or partner with us</p>
                        <a href="#support-faqs" class="btn btn-sm btn-outline-primary">View Questions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Prompt Section -->
<section class="py-5" id="contact-prompt">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body p-4 p-md-5 text-center">
                        <h2 class="mb-4">Still have questions?</h2>
                        <p class="lead mb-4">We're here to help! Contact our team directly for more information.</p>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="contact.php" class="btn btn-light btn-lg">Contact Us</a>
                            <a href="tel:<?= htmlspecialchars($contactInfo['contact_phone'] ?? '') ?>" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-phone me-2"></i> Call Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>