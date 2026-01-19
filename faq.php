<?php
// Start session at the very beginning
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = "Frequently Asked Questions";
require_once 'includes/header.php';

// Get all FAQs from database with error handling
$faqs = [];
$faqs_by_category = [];
try {
    require_once 'config/db.php';
    require_once 'config/db_functions.php';
    $faqs = fetchAll("SELECT * FROM faqs WHERE is_active = 1 ORDER BY category, display_order ASC");
    
    // Group FAQs by category for better organization
    foreach ($faqs as $faq) {
        $category = $faq['category'] ?: 'General';
        $faqs_by_category[$category][] = $faq;
    }
    
} catch (Exception $e) {
    error_log("FAQ Error: " . $e->getMessage());
    $error = "We're having trouble loading the FAQs. Please try again later.";
}

// Default FAQs if none in database
if (empty($faqs)) {
    $faqs_by_category = [
        'General' => [
            ['question' => 'What is IYEF?', 'answer' => 'IYEF (Indefatigable Youth Empowerment Foundation) is a faith-based non-profit organization dedicated to empowering vulnerable adolescents through education, mentorship, and skills training.', 'related_link' => 'about.php'],
            ['question' => 'Who can join IYEF programs?', 'answer' => 'Our programs are open to adolescents and youth aged 13-30 from all backgrounds. Specific programs may have age or eligibility requirements.', 'related_link' => 'programs.php'],
        ],
        'Programs' => [
            ['question' => 'How do I register for programs?', 'answer' => 'You can register online through our Programs page, or contact our office for assistance with registration.', 'related_link' => 'programs.php'],
            ['question' => 'Are your programs free?', 'answer' => 'Many of our programs are free or subsidized. Some advanced training programs may have minimal fees to cover materials.', 'related_link' => 'programs.php'],
        ],
        'Events' => [
            ['question' => 'How can I attend IYEF events?', 'answer' => 'Check our Events page for upcoming activities and registration details. Most events require prior registration.', 'related_link' => 'events.php'],
            ['question' => 'Can I volunteer at events?', 'answer' => 'Yes! We welcome volunteers. Please visit our Volunteer page or contact us for opportunities.', 'related_link' => 'volunteer.php'],
        ],
        'Support' => [
            ['question' => 'How can I support IYEF?', 'answer' => 'You can support through donations, volunteering, partnerships, or spreading awareness about our work.', 'related_link' => 'donate.php'],
            ['question' => 'Are donations tax-deductible?', 'answer' => 'Yes, as a registered non-profit, donations to IYEF are tax-deductible. You will receive a receipt for your donation.', 'related_link' => 'donate.php'],
        ]
    ];
}
?>

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-50 d-flex align-items-center">
    <!-- Background with pattern overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.85), rgba(0, 123, 255, 0.9)), url('assets/images/iyef_faq.jpg'); background-size: cover; background-position: center; background-attachment: fixed;"></div>

    <!-- Animated background elements -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); top: -150px; right: -100px;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.05); bottom: -100px; left: -50px;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8 col-xl-7">   
                <!-- Main heading -->
                <h1 class="display-2 fw-bold mb-4 text-white animate-slide-up" style="line-height: 1.1;">
                    How Can We <span class="text-warning">Help You?</span>
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    Get answers to common questions about our youth empowerment programs, events, and services.
                </p>
                <br><br><br>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#faq-content" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <span class="small mb-2 opacity-75">Browse Questions</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- FAQ Categories Navigation -->
<section class="sticky-top bg-white shadow-sm py-3 d-none d-lg-block" style="top: 80px; z-index: 1000;">
    <div class="container">
        <div class="d-flex justify-content-center gap-4">
            <?php foreach (array_keys($faqs_by_category) as $category): 
                $category_id = strtolower(str_replace(' ', '-', $category));
                $category_icon = match($category) {
                    'General' => 'question-circle',
                    'Programs' => 'graduation-cap',
                    'Events' => 'calendar-alt',
                    'Support' => 'handshake',
                    'Registration' => 'user-plus',
                    'Payment' => 'credit-card',
                    default => 'info-circle'
                };
            ?>
                <a href="#<?= $category_id ?>" class="text-decoration-none scroll-to-section">
                    <div class="d-flex flex-column align-items-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-2">
                            <i class="fas fa-<?= $category_icon ?> fa-lg"></i>
                        </div>
                        <span class="small fw-medium"><?= $category ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Main FAQ Content -->
<section id="faq-content" class="py-5">
    <div class="container py-5">
        <?php if (isset($error)): ?>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="alert alert-danger text-center shadow-sm">
                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                        <h4 class="alert-heading">Oops! Something went wrong</h4>
                        <p><?= $error ?></p>
                        <div class="mt-3">
                            <a href="contact.php" class="btn btn-danger">Contact Support</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Left sidebar for mobile category filter -->
                <div class="col-lg-3 mb-5 mb-lg-0">
                    <div class="" style="top: 120px;">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 pb-0">
                                <h5 class="fw-bold mb-3"><i class="fas fa-filter me-2"></i> Filter by Category</h5>
                            </div>
                            <div class="card-body p-3">
                                <div class="nav flex-column" id="categoryFilter">
                                    <a href="#all" class="nav-link active category-filter-btn mb-2" data-category="all">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-layer-group me-2 text-primary"></i>
                                            <span>All Questions</span>
                                            <span class="badge bg-primary rounded-pill ms-auto" id="all-count">0</span>
                                        </div>
                                    </a>
                                    <?php foreach (array_keys($faqs_by_category) as $index => $category): 
                                        $category_id = strtolower(str_replace(' ', '-', $category));
                                        $category_icon = match($category) {
                                            'General' => 'question-circle',
                                            'Programs' => 'graduation-cap',
                                            'Events' => 'calendar-alt',
                                            'Support' => 'handshake',
                                            'Registration' => 'user-plus',
                                            'Payment' => 'credit-card',
                                            default => 'info-circle'
                                        };
                                    ?>
                                        <a href="#<?= $category_id ?>" class="nav-link category-filter-btn mb-2" data-category="<?= $category_id ?>">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-<?= $category_icon ?> me-2 text-primary"></i>
                                                <span><?= $category ?></span>
                                                <span class="badge bg-secondary rounded-pill ms-auto category-count" data-category="<?= $category_id ?>">0</span>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="card border-0 shadow-sm mt-4">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2"></i> FAQ Stats</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Questions:</span>
                                    <span class="fw-bold" id="total-questions">0</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Categories:</span>
                                    <span class="fw-bold"><?= count($faqs_by_category) ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Recently Updated:</span>
                                    <span class="fw-bold"><?= date('M d') ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Accordion -->
                <div class="col-lg-9">
                    <!-- FAQ Categories -->
                    <?php foreach ($faqs_by_category as $category => $category_faqs): 
                        $category_id = strtolower(str_replace(' ', '-', $category));
                    ?>
                        <div class="category-section mb-5" id="<?= $category_id ?>">
                            <div class="d-flex align-items-center mb-4">
                                <?php 
                                $category_icon = match($category) {
                                    'General' => 'question-circle',
                                    'Programs' => 'graduation-cap',
                                    'Events' => 'calendar-alt',
                                    'Support' => 'handshake',
                                    'Registration' => 'user-plus',
                                    'Payment' => 'credit-card',
                                    default => 'info-circle'
                                };
                                $category_color = match($category) {
                                    'General' => 'primary',
                                    'Programs' => 'success',
                                    'Events' => 'warning',
                                    'Support' => 'info',
                                    'Registration' => 'purple',
                                    'Payment' => 'danger',
                                    default => 'secondary'
                                };
                                ?>
                                <div class="icon-wrapper bg-<?= $category_color ?> bg-opacity-10 text-<?= $category_color ?> rounded-3 p-3 me-3">
                                    <i class="fas fa-<?= $category_icon ?> fa-2x"></i>
                                </div>
                                <div>
                                    <h2 class="h3 fw-bold mb-1"><?= $category ?> Questions</h2>
                                    <p class="text-muted mb-0"><?= count($category_faqs) ?> questions in this category</p>
                                </div>
                            </div>
                            
                            <div class="accordion" id="faqAccordion<?= $category_id ?>">
                                <?php foreach ($category_faqs as $index => $faq): 
                                    $faq_id = $category_id . '-' . $index;
                                ?>
                                    <div class="accordion-item border-0 shadow-sm mb-3 faq-item" 
                                         data-category="<?= $category_id ?>"
                                         data-question="<?= htmlspecialchars(strtolower($faq['question'])) ?>"
                                         data-answer="<?= htmlspecialchars(strtolower($faq['answer'])) ?>">
                                        <h3 class="accordion-header">
                                            <button class="accordion-button collapsed py-4" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#faqCollapse<?= $faq_id ?>"
                                                    aria-expanded="false" 
                                                    aria-controls="faqCollapse<?= $faq_id ?>">
                                                <div class="d-flex align-items-center w-100">
                                                    <div class="flex-shrink-0">
                                                        <div class="bg-<?= $category_color ?> bg-opacity-10 text-<?= $category_color ?> rounded-circle p-2 me-3">
                                                            <i class="fas fa-question"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-start">
                                                        <span class="fw-bold"><?= htmlspecialchars($faq['question']) ?></span>
                                                        <div class="mt-1">
                                                            <small class="text-muted">
                                                                <i class="fas fa-tag me-1"></i><?= $category ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="flex-shrink-0">
                                                        
                                                    </div>
                                                </div>
                                            </button>
                                        </h3>
                                        <div id="faqCollapse<?= $faq_id ?>" class="accordion-collapse collapse" 
                                             data-bs-parent="#faqAccordion<?= $category_id ?>">
                                            <div class="accordion-body py-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0">
                                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                                            <i class="fas fa-lightbulb"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="faq-answer-content">
                                                            <?= nl2br(htmlspecialchars($faq['answer'])) ?>
                                                        </div>
                                                        
                                                        <?php if (!empty($faq['related_link'])): ?>
                                                            <div class="mt-4">
                                                                <a href="<?= htmlspecialchars($faq['related_link']) ?>" 
                                                                   class="btn btn-sm btn-outline-<?= $category_color ?>">
                                                                    <i class="fas fa-external-link-alt me-1"></i> 
                                                                    Learn More About This
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Helpful Buttons -->
                                                        <div class="mt-4 pt-3 border-top">
                                                            <small class="text-muted me-3">Was this helpful?</small>
                                                            <button class="btn btn-sm btn-outline-success helpful-btn" data-faq="<?= $faq_id ?>">
                                                                <i class="fas fa-thumbs-up me-1"></i> Yes
                                                            </button>
                                                            <button class="btn btn-sm btn-outline-danger helpful-btn" data-faq="<?= $faq_id ?>">
                                                                <i class="fas fa-thumbs-down me-1"></i> No
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Contact CTA Section -->
<section id="contact-section" class="py-5 position-relative overflow-hidden">
    <!-- Background pattern -->
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary opacity-95"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
        style="background-image: url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='none' stroke='white' stroke-width='0.5' stroke-opacity='0.1'/%3E%3C/svg%3E&quot;);
                background-size: 100px 100px;
                pointer-events: none;">
    </div>

    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="bg-white rounded-4 p-5 shadow-lg">
                    <div class="mb-4">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-4 d-inline-block mb-3">
                            <i class="fas fa-comments fa-2x"></i>
                        </div>
                        <h2 class="display-6 fw-bold mb-3">Still Need Help?</h2>
                        <p class="lead text-muted mb-4">
                            Can't find what you're looking for? Our support team is ready to assist you.
                        </p>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                        <i class="fas fa-envelope fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Email Us</h5>
                                    <p class="text-muted small mb-3">Get detailed responses within 24 hours</p>
                                    <a href="contact.php" class="btn btn-outline-primary btn-sm">Send Message</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                        <i class="fas fa-phone fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Call Us</h5>
                                    <p class="text-muted small mb-3">Speak directly with our support team</p>
                                    <a href="tel:<?= htmlspecialchars($contactInfo['contact_phone'] ?? '+1234567890') ?>" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-phone me-1"></i> Call Now
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-0 bg-light h-100">
                                <div class="card-body p-4 text-center">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                        <i class="fas fa-comment-dots fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Live Chat</h5>
                                    <p class="text-muted small mb-3">Get instant answers during business hours</p>
                                    <button class="btn btn-outline-warning btn-sm" id="liveChatBtn">
                                        <i class="fas fa-comment me-1"></i> Start Chat
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Help Options -->
                    <div class="mt-5 pt-4 border-top">
                        <h6 class="fw-bold mb-3">Other Ways to Get Help:</h6>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            <a href="programs.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-book me-1"></i> Program Guides
                            </a>
                            <a href="events.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-calendar me-1"></i> Event Calendar
                            </a>
                            <a href="blog.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-newspaper me-1"></i> Help Articles
                            </a>
                            <a href="resources.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-download me-1"></i> Download Resources
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Live Chat Modal -->
<div class="modal fade" id="liveChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-comment-dots me-2"></i> Live Chat Support
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center py-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-4 d-inline-block mb-3">
                        <i class="fas fa-headset fa-2x"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Chat Support Coming Soon</h5>
                    <p class="text-muted mb-4">Our live chat feature is currently under development. In the meantime, please use email or phone support.</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="contact.php" class="btn btn-primary">Email Support</a>
                        <button class="btn btn-outline-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for enhanced interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize counts
    updateFAQCounts();
    
    // Category filtering
    const categoryFilterBtns = document.querySelectorAll('.category-filter-btn');
    const faqItems = document.querySelectorAll('.faq-item');
    
    categoryFilterBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.getAttribute('data-category');
            
            // Update active state
            categoryFilterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            if (category === 'all') {
                // Show all FAQs
                faqItems.forEach(item => item.style.display = 'block');
                document.querySelectorAll('.category-section').forEach(section => {
                    section.style.display = 'block';
                });
            } else {
                // Filter by category
                faqItems.forEach(item => {
                    const itemCategory = item.getAttribute('data-category');
                    item.style.display = itemCategory === category ? 'block' : 'none';
                });
                
                // Show/hide category sections
                document.querySelectorAll('.category-section').forEach(section => {
                    section.style.display = section.id === category ? 'block' : 'none';
                });
            }
            
            // Smooth scroll to the section
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement && category !== 'all') {
                setTimeout(() => {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }, 100);
            }
        });
    });
    
    // Smooth scrolling for internal links
    const scrollLinks = document.querySelectorAll('.scroll-to-section');
    scrollLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
                
                // Update category filter
                if (targetId !== '#faq-content' && targetId !== '#all') {
                    const category = targetId.substring(1); // Remove #
                    categoryFilterBtns.forEach(btn => {
                        const btnCategory = btn.getAttribute('href').substring(1);
                        if (btnCategory === category) {
                            btn.click();
                        }
                    });
                }
            }
        });
    });
    
    // Helpful buttons
    const helpfulBtns = document.querySelectorAll('.helpful-btn');
    helpfulBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const faqId = this.getAttribute('data-faq');
            const isHelpful = this.classList.contains('btn-outline-success');
            
            // Update button appearance
            this.classList.remove('btn-outline-success', 'btn-outline-danger');
            this.classList.add(isHelpful ? 'btn-success' : 'btn-danger');
            this.disabled = true;
            
            // Store feedback in localStorage
            const feedbackKey = `faq-feedback-${faqId}`;
            localStorage.setItem(feedbackKey, isHelpful ? 'yes' : 'no');
            
            // Show thank you message
            const parentDiv = this.closest('.border-top');
            const thankYouMsg = document.createElement('small');
            thankYouMsg.className = 'text-success fst-italic ms-3';
            thankYouMsg.textContent = 'Thank you for your feedback!';
            parentDiv.appendChild(thankYouMsg);
        });
    });
    
    // Live chat modal
    const liveChatBtn = document.getElementById('liveChatBtn');
    if (liveChatBtn) {
        liveChatBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(document.getElementById('liveChatModal'));
            modal.show();
        });
    }
    
    // Accordion arrow rotation
    const accordionButtons = document.querySelectorAll('.accordion-button');
    accordionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const arrow = this.querySelector('.accordion-arrow');
            if (arrow) {
                arrow.style.transition = 'transform 0.3s ease';
                if (this.classList.contains('collapsed')) {
                    arrow.style.transform = 'rotate(0deg)';
                } else {
                    arrow.style.transform = 'rotate(180deg)';
                }
            }
        });
    });
    
    // Initialize accordion arrows
    accordionButtons.forEach(button => {
        const arrow = button.querySelector('.accordion-arrow');
        if (arrow) {
            arrow.style.transition = 'transform 0.3s ease';
            arrow.style.transform = 'rotate(0deg)';
        }
    });
    
    // Update FAQ counts
    function updateFAQCounts() {
        const totalQuestions = faqItems.length;
        document.getElementById('total-questions').textContent = totalQuestions;
        document.getElementById('all-count').textContent = totalQuestions;
        
        // Count by category
        const categoryCounts = {};
        faqItems.forEach(item => {
            const category = item.getAttribute('data-category');
            categoryCounts[category] = (categoryCounts[category] || 0) + 1;
        });
        
        // Update category counts
        document.querySelectorAll('.category-count').forEach(element => {
            const category = element.getAttribute('data-category');
            element.textContent = categoryCounts[category] || 0;
        });
    }
    
    // Add animation to FAQ items on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);
    
    faqItems.forEach(item => {
        observer.observe(item);
    });
    
    // Auto-expand first FAQ in each category on page load
    document.querySelectorAll('.category-section').forEach(section => {
        const firstFaq = section.querySelector('.faq-item');
        if (firstFaq) {
            const collapseButton = firstFaq.querySelector('.accordion-button');
            if (collapseButton) {
                // Add a small delay for better UX
                setTimeout(() => {
                    collapseButton.click();
                }, 300);
            }
        }
    });
});
</script>

<!-- Add custom CSS -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.min-vh-50 {
    min-height: 50vh;
}

/* Animations */
.animate-fade-in {
    animation: fadeIn 0.8s ease-out;
}

.animate-slide-up {
    animation: slideUp 0.8s ease-out;
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

/* FAQ Item Styling */
.faq-item {
    transition: all 0.3s ease;
}

.faq-item:hover {
    transform: translateX(5px);
}

.accordion-button {
    background-color: white;
    transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    color: var(--bs-primary);
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(var(--bs-primary-rgb), 0.3);
}

.accordion-body {
    background-color: rgba(var(--bs-primary-rgb), 0.02);
}

/* Category Filter */
.category-filter-btn.active {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
    border-radius: 8px;
    color: var(--bs-primary);
}

.category-filter-btn:hover:not(.active) {
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 8px;
}

/* Scroll Indicator */
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

.dot {
    animation: pulse 2s infinite;
}

/* Icon Wrapper */
.icon-wrapper {
    transition: all 0.3s ease;
}

.icon-wrapper:hover {
    transform: scale(1.05);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
    
    .scroll-indicator {
        width: 40px;
        height: 40px;
    }
    
    
    .btn-lg {
        padding: 0.75rem 1.5rem !important;
        font-size: 1rem !important;
    }
}

@media (max-width: 576px) {
    .display-2 {
        font-size: 2rem;
    }
    
    .accordion-button {
        padding: 1rem !important;
    }
    
    .hero-section {
        min-height: 60vh;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>