<?php
require_once 'config/db.php';
require_once 'includes/functions/programs.php';

$page_title = "Our Programs";
require_once 'includes/header.php';

// Get all active programs
$activePrograms = getActivePrograms();
?>

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-60 d-flex align-items-center">
    <!-- Background with gradient overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.9), rgba(0, 123, 255, 0.85)), url('assets/images/programs-hero.jpg'); background-size: cover; background-position: center;"></div>
    
    <!-- Animated background elements -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); top: -150px; right: -100px;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.05); bottom: -100px; left: -50px;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Badge -->
                <div class="d-inline-flex align-items-center bg-white bg-opacity-20 text-white rounded-pill px-4 py-2 mb-4 animate-fade-in">
                    <span class="dot me-2" style="width: 10px; height: 10px; background: #4cd964; border-radius: 50%; animation: pulse 2s infinite;"></span>
                    <span class="fw-medium">Transforming Lives Since 2021</span>
                </div>
                
                <!-- Main heading -->
                <h1 class="display-2 fw-bold mb-4 text-white animate-slide-up" style="line-height: 1.2;">
                    Youth Empowerment<br>
                    <span class="text-warning">Programs</span>
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    Discover comprehensive initiatives designed to equip young people with skills, knowledge, and opportunities for a brighter future.
                </p>
                
                <!-- Action buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <a href="#program-list" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-play-circle me-2"></i> Explore Programs
                    </a>
                    <a href="#program-benefits" class="btn btn-outline-light btn-lg px-5 py-3 scroll-to-section">
                        Why Join Us?
                    </a>
                </div>
                
                <!-- Quick Stats -->
                <div class="row g-4 mt-5 pt-4 border-top border-white border-opacity-25 animate-fade-in" style="animation-delay: 0.7s;">
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="<?= count($activePrograms) ?>">0</div>
                            <div class="small text-uppercase opacity-75">Active Programs</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="500">0</div>
                            <div class="small text-uppercase opacity-75">Youth Trained</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="95">0</div>
                            <div class="small text-uppercase opacity-75">Success Rate</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="25">0</div>
                            <div class="small text-uppercase opacity-75">Communities Reached</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#program-list" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <span class="small mb-2 opacity-75">Discover Our Programs</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- Programs List - Modern Grid -->
<section id="program-list" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">ACTIVE PROGRAMS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Transformative Initiatives</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Browse our current programs designed to empower youth through education, skills development, and mentorship.
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($activePrograms)): ?>
                <?php foreach ($activePrograms as $program): 
                    $startDate = $program['start_date'] ? date('M j, Y', strtotime($program['start_date'])) : 'Ongoing';
                    $endDate = $program['end_date'] ? date('M j, Y', strtotime($program['end_date'])) : 'Present';
                    $isOngoing = empty($program['end_date']) || strtotime($program['end_date']) > time();
                ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 shadow-lg overflow-hidden hover-lift">
                        <!-- Program Image -->
                        <div class="position-relative overflow-hidden" style="height: 200px;">
                            <?php if (!empty($program['image'])): ?>
                                <img src="<?= htmlspecialchars($program['image']) ?>" 
                                     class="card-img-top w-100 h-100" 
                                     alt="<?= htmlspecialchars($program['title']) ?>" 
                                     style="object-fit: cover; transition: transform 0.5s ease;">
                            <?php else: ?>
                                <div class="bg-gradient-primary w-100 h-100 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-project-diagram fa-4x text-white"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Status Badge -->
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge <?= $isOngoing ? 'bg-success' : 'bg-warning' ?> rounded-pill px-3 py-2">
                                    <?= $isOngoing ? 'Active' : 'Upcoming' ?>
                                </span>
                            </div>
                            
                            <!-- Date Overlay -->
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white" 
                                 style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <small><?= "$startDate - $endDate" ?></small>
                                    </div>
                                    <div class="text-end">
                                        <small><i class="fas fa-users me-1"></i> Open Enrollment</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="card-body p-4">
                            <h3 class="h4 fw-bold mb-3"><?= htmlspecialchars($program['title']) ?></h3>
                            <p class="card-text text-muted mb-4">
                                <?= strlen($program['description']) > 120 ? 
                                    substr(strip_tags($program['description']), 0, 120) . '...' : 
                                    strip_tags($program['description']) ?>
                            </p>
                            
                            <!-- Program Tags -->
                            <div class="mb-4">
                                <?php 
                                // You can add program categories/tags here
                                $tags = ['Youth Development', 'Skills Training', 'Mentorship'];
                                foreach ($tags as $tag): 
                                ?>
                                <span class="badge bg-primary bg-opacity-10 text-primary me-1 mb-1"><?= $tag ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent border-top-0 pt-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="program.php?slug=<?= htmlspecialchars($program['slug']) ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    Learn More <i class="fas fa-arrow-right ms-2"></i>
                                </a>
                                <a href="program-register.php?program_id=<?= $program['id'] ?>" 
                                   class="btn btn-primary btn-sm">
                                    <i class="fas fa-user-plus me-1"></i> Register Now
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-12">
                    <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-calendar-plus fa-4x text-muted mb-4 opacity-50"></i>
                            <h4 class="fw-bold mb-3">New Programs Coming Soon</h4>
                            <p class="text-muted mb-4">We are preparing exciting new empowerment programs. Stay tuned for updates!</p>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <a href="contact.php" class="btn btn-outline-primary">Get Notified</a>
                                <a href="#past-programs" class="btn btn-primary scroll-to-section">View Past Programs</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Program Benefits - Modern Design -->
<section id="program-benefits" class="py-5 bg-gradient-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">WHY JOIN US</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Benefits of Our Programs</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Experience holistic development that goes beyond traditional education and training.
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 p-3 mb-4 mx-auto">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-3">Practical Skills</h4>
                        <p class="text-muted small mb-0">Gain market-relevant skills that prepare you for employment and entrepreneurship.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-3 p-3 mb-4 mx-auto">
                            <i class="fas fa-hands-helping fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-3">Mentorship</h4>
                        <p class="text-muted small mb-0">Connect with experienced mentors guiding your personal and professional growth.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="icon-wrapper bg-warning bg-opacity-10 text-warning rounded-3 p-3 mb-4 mx-auto">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-3">Community Network</h4>
                        <p class="text-muted small mb-0">Join a supportive network of youth working toward similar goals and aspirations.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 shadow-sm hover-lift">
                    <div class="card-body p-4 text-center">
                        <div class="icon-wrapper bg-info bg-opacity-10 text-info rounded-3 p-3 mb-4 mx-auto">
                            <i class="fas fa-award fa-2x"></i>
                        </div>
                        <h4 class="h5 fw-bold mb-3">Certification</h4>
                        <p class="text-muted small mb-0">Receive recognized certificates validating your participation and achievements.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials - Modern Design -->
<section class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">SUCCESS STORIES</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Voices of Transformation</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Hear directly from youth whose lives have been transformed through our empowerment programs.
                </p>
            </div>
        </div>
        
        <div class="row g-4">
            <?php
            $testimonials = fetchAll("
                SELECT t.* 
                FROM testimonials t
                WHERE t.is_approved = 1 AND t.is_featured = 1
                ORDER BY t.created_at DESC
                LIMIT 3
            ");
            
            if (!empty($testimonials)):
                foreach ($testimonials as $testimonial):
                    $initials = '';
                    $parts = explode(' ', $testimonial['author_name']);
                    foreach ($parts as $part) {
                        $initials .= strtoupper(substr($part, 0, 1));
                    }
                    $initials = substr($initials, 0, 2);
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-lg hover-lift">
                    <div class="card-body p-4">
                        <!-- Quote Icon -->
                        <div class="text-primary mb-3">
                            <i class="fas fa-quote-left fa-2x opacity-25"></i>
                        </div>
                        
                        <!-- Testimonial Content -->
                        <p class="card-text fst-italic text-muted mb-4">
                            "<?= htmlspecialchars($testimonial['content']) ?>"
                        </p>
                        
                        <!-- Author Info -->
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <?php if (!empty($testimonial['author_image'])): ?>
                                    <img src="<?= htmlspecialchars($testimonial['author_image']) ?>" 
                                         class="rounded-circle" 
                                         alt="<?= htmlspecialchars($testimonial['author_name']) ?>"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <span class="fw-bold"><?= $initials ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($testimonial['author_name']) ?></h5>
                                <?php if (!empty($testimonial['author_title'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars($testimonial['author_title']) ?></small>
                                <?php endif; ?>
                                <div class="text-warning small mt-1">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fas fa-star<?= $i < $testimonial['rating'] ? '' : '-empty' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (count($testimonials) < 3): ?>
                <!-- View All Testimonials Card -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-dashed border-2 border-muted">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-5">
                            <i class="fas fa-comments fa-4x text-muted mb-4 opacity-50"></i>
                            <h4 class="fw-bold mb-3">More Stories</h4>
                            <p class="text-muted mb-4">Discover more inspiring success stories from our program participants.</p>
                            <a href="testimonials.php" class="btn btn-outline-primary">
                                View All Testimonials
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php else: ?>
                <!-- Empty State -->
                <div class="col-12">
                    <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-comments fa-4x text-muted mb-4 opacity-50"></i>
                            <h4 class="fw-bold mb-3">Success Stories Coming Soon</h4>
                            <p class="text-muted mb-4">Our participants' inspiring stories will be shared here soon.</p>
                            <a href="#program-list" class="btn btn-outline-primary scroll-to-section">Join Our Programs</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Past Programs - Modern Table -->
<section id="past-programs" class="bg-light py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">OUR LEGACY</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Past Programs Archive</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">
                    Explore our history of successful initiatives that have impacted thousands of youth globally.
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-header bg-white border-0 py-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="h4 fw-bold mb-0">Program History</h3>
                                <small class="text-muted">Completed programs with impact metrics</small>
                            </div>
                            <div class="text-end">
                                <small class="text-muted">Showing 10 most recent</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="ps-4">Program</th>
                                        <th>Duration</th>
                                        <th class="text-center">Participants</th>
                                        <th class="text-center">Impact Score</th>
                                        <th class="pe-4 text-end">Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $pastPrograms = fetchAll("
                                        SELECT p.*, COUNT(pr.id) as participants 
                                        FROM programs p
                                        LEFT JOIN program_registrations pr ON p.id = pr.program_id
                                        WHERE p.end_date < CURDATE() OR (p.start_date IS NOT NULL AND p.end_date IS NULL)
                                        GROUP BY p.id
                                        ORDER BY p.end_date DESC
                                        LIMIT 10
                                    ");
                                    
                                    if (!empty($pastPrograms)):
                                        foreach ($pastPrograms as $program):
                                            $startDate = $program['start_date'] ? date('M j, Y', strtotime($program['start_date'])) : 'N/A';
                                            $endDate = $program['end_date'] ? date('M j, Y', strtotime($program['end_date'])) : 'N/A';
                                            $impactScore = min(100, ($program['participants'] * 5) + 75); // Example calculation
                                    ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                                        <i class="fas fa-project-diagram"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="fw-bold mb-0"><?= htmlspecialchars($program['title']) ?></h6>
                                                    <small class="text-muted"><?= substr(strip_tags($program['description']), 0, 80) ?>...</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                <small><?= "$startDate - $endDate" ?></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                                <?= $program['participants'] ?> Youth
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 8px; width: 80px; margin: 0 auto;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?= $impactScore ?>%" 
                                                     aria-valuenow="<?= $impactScore ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <small class="text-muted"><?= $impactScore ?>%</small>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="program.php?slug=<?= htmlspecialchars($program['slug']) ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="fas fa-history fa-3x mb-3 opacity-50"></i>
                                                    <p class="mb-0">No past programs available at the moment.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <?php if (!empty($pastPrograms)): ?>
                    <div class="card-footer bg-white border-0 py-3 text-center">
                        <a href="programs-archive.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-archive me-1"></i> View Full Archive
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary opacity-95"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"40\" fill=\"none\" stroke=\"white\" stroke-width=\"0.5\" stroke-opacity=\"0.1\"/></svg>'); background-size: 100px 100px; opacity: 0.5;"></div>
    
    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-4 fw-bold text-white mb-4">Ready to Transform Your Future?</h2>
                <p class="lead text-white opacity-75 mb-5">
                    Join our programs and embark on a journey of growth, learning, and empowerment with IYEF.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="#program-list" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-search me-2"></i> Browse Programs
                    </a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg px-5 py-3">
                        <i class="fas fa-question-circle me-2"></i> Ask Questions
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for enhanced interactivity -->
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
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-light {
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
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

.dot {
    animation: pulse 2s infinite;
}

.border-dashed {
    border-style: dashed !important;
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.icon-wrapper:hover {
    transform: rotate(5deg) scale(1.1);
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

<?php require_once 'includes/footer.php'; ?>