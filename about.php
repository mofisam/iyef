<?php
$page_title = "About IYEF";
require_once 'includes/header.php';

// Fetch about page content from database or settings with error handling
$aboutContent = [];
try {
    $aboutContent = fetchSingle("SELECT content FROM pages WHERE slug = 'about' LIMIT 1");
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching about page content: " . $e->getMessage());
}

// Get team members with error handling
$teamMembers = [];
try {
    $teamMembers = fetchAll("
        SELECT u.full_name, u.email, p.position, p.photo, p.bio 
        FROM users u
        JOIN profiles p ON u.id = p.user_id
        WHERE p.position IN ('Founder', 'Director', 'Manager', 'Coordinator', 'Team Lead')
        AND u.is_active = 1
        ORDER BY 
            CASE p.position 
                WHEN 'Founder' THEN 1
                WHEN 'Director' THEN 2
                WHEN 'Manager' THEN 3
                WHEN 'Team Lead' THEN 4
                ELSE 5
            END,
            u.full_name
        LIMIT 12
    ");
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching team members: " . $e->getMessage());
}

// Get partners data - You should create this table or update with your actual partners
$partners = [];
try {
    $partners = fetchAll("SELECT name, logo, website FROM partners WHERE is_active = 1 ORDER BY display_order LIMIT 8");
} catch (Exception $e) {
    // Default partners if table doesn't exist
    $partners = [
        ['name' => 'UNESCO', 'logo' => 'partner-unesco.png', 'website' => '#'],
        ['name' => 'Youth Development Initiative', 'logo' => 'partner-youth.png', 'website' => '#'],
        ['name' => 'Global Education Fund', 'logo' => 'partner-education.png', 'website' => '#'],
        ['name' => 'Community Partners', 'logo' => 'partner-community.png', 'website' => '#'],
    ];
}
?>

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-80 d-flex align-items-center">
    <!-- Background with gradient overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.9), rgba(0, 123, 255, 0.85)), url('assets/images/IYEF_gt_z.jpg'); background-size: cover; background-position: center;"></div>
    
    <!-- Animated background elements -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); top: -150px; right: -100px;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.05); bottom: -100px; left: -50px;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-xl-9">
                <!-- Badge -->
                <div class="d-inline-flex align-items-center bg-white bg-opacity-20 rounded-pill px-4 py-2 mb-2 mt-4 animate-fade-in">
                    <span class="dot me-2" style="width: 10px; height: 10px; background: #4cd964; border-radius: 50%; animation: pulse 2s infinite;"></span>
                    <span class="fw-medium">Empowering Youth Since 2022</span>
                </div>
                
                <!-- Main heading -->
                <h1 class="display-2 fw-bold mb-4 text-white animate-slide-up" style="line-height: 1.2;">
                    Building <span class="text-warning">Indefatigable</span><br>
                    Kingdom Youths
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    A faith-based non-profit organization dedicated to reforming, mentoring, and empowering vulnerable adolescents to become resilient leaders making positive impacts in their communities.
                </p>
                
                <!-- Action buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <a href="#mission" class="btn btn-light btn-lg px-5 py-3 scroll-to-section">
                        <i class="fas fa-bullseye me-2"></i> Our Mission
                    </a>
                    <a href="#team" class="btn btn-outline-light btn-lg px-5 py-3 scroll-to-section">
                        Meet Our Team
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#mission" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- Mission & Vision Section - Modern Cards -->
<section id="mission" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">OUR PURPOSE</span>
                </div>
                <h2 class="display-5 fw-bold mb-4">Guided by Vision, Driven by Mission</h2>
                <p class="lead text-muted">Our foundation is built on clear principles that guide every program and initiative we undertake.</p>
            </div>
        </div>
        
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                    <div class="card-header bg-primary bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-primary text-white rounded-3 p-3 me-3">
                                <i class="fas fa-bullseye fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="h3 fw-bold mb-0">Our Mission</h3>
                                <small class="text-muted">What drives us every day</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="mission-statement bg-primary bg-opacity-5 p-4 rounded-3 mb-4">
                            <p class="mb-0 fst-italic text-white fw-medium">
                                "To reform, mentor and empower weak and vulnerable adolescents from diverse backgrounds to become indefatigable breeds of kingdom youths..."
                            </p>
                        </div>
                        <div class="mission-details">
                            <p class="mb-3">We are committed to transforming vulnerable youth through:</p>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-success bg-opacity-10 p-2 rounded-2 me-3">
                                            <i class="fas fa-graduation-cap text-success"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Comprehensive Reformation</h6>
                                            <p class="small text-muted mb-0">Holistic transformation programs</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-info bg-opacity-10 p-2 rounded-2 me-3">
                                            <i class="fas fa-hands-helping text-info"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Mentorship</h6>
                                            <p class="small text-muted mb-0">One-on-one guidance and support</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded-2 me-3">
                                            <i class="fas fa-tools text-warning"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Empowerment</h6>
                                            <p class="small text-muted mb-0">Skills and leadership development</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-purple bg-opacity-10 p-2 rounded-2 me-3">
                                            <i class="fas fa-home text-purple"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Family Development</h6>
                                            <p class="small text-muted mb-0">Training impactful youth leaders and parents</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                    <div class="card-header bg-warning bg-opacity-10 border-0 py-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-wrapper bg-warning text-dark rounded-3 p-3 me-3">
                                <i class="fas fa-eye fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="h3 fw-bold mb-0">Our Vision</h3>
                                <small class="text-muted">The future we're building</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4 p-lg-5">
                        <div class="vision-statement bg-warning bg-opacity-5 p-4 rounded-3 mb-4">
                            <p class="mb-0 fst-italic text-dark fw-medium">
                                "To raise kingdom youths who can weather the storms of life, making positive impact with their lives."
                            </p>
                        </div>
                        
                        <!-- Bible Verse Reference -->
                        <div class="bible-verse bg-dark text-white p-4 rounded-3 mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-bible me-2"></i>
                                <small class="opacity-75">Foundation Scripture</small>
                            </div>
                            <p class="mb-0 fst-italic">
                                "And they that shall be of thee shall build the old waste places: thou shalt raise up the foundations of many generations; and thou shalt be called, The repairer of the breach, The restorer of paths to dwell in."
                            </p>
                            <div class="text-end mt-2">
                                <small class="opacity-75">Isaiah 58:12 (KJV)</small>
                            </div>
                        </div>
                        
                        <div class="vision-goals">
                            <h5 class="fw-bold mb-3">Our Vision in Action</h5>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Resilient Leaders</h6>
                                            <p class="small text-muted mb-0">Developing youth who withstand life's challenges</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Community Impact</h6>
                                            <p class="small text-muted mb-0">Creating positive change-makers</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="bg-info bg-opacity-10 text-info rounded-circle p-2 me-3">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1">Sustainable Development</h6>
                                            <p class="small text-muted mb-0">Building foundations for future generations</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Content Section - Modern Design -->
<section class="bg-light py-5 position-relative">
    <div class="position-absolute top-0 start-0 w-100 h-50 bg-primary bg-opacity-5"></div>
    
    <div class="container py-5 position-relative">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-header bg-white border-0 py-4">
                        <div class="text-center">
                            <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                                <span class="small fw-bold">WHO WE ARE</span>
                            </div>
                            <h2 class="display-6 fw-bold mb-3">Indefatigable Youth Empowerment Foundation</h2>
                            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                                A beacon of hope for vulnerable adolescents globally
                            </p>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 p-lg-5">
                        <?php if (!empty($aboutContent['content'])): ?>
                            <?= $aboutContent['content'] ?>
                        <?php else: ?>
                            <!-- Organization Overview -->
                            <div class="row align-items-center mb-5">
                                <div class="col-lg-4 mb-4 mb-lg-0">
                                    <div class="position-relative">
                                        <img src="assets/images/IYEF_logo.jpg" alt="IYEF Logo" class="img-fluid rounded-3 shadow">
                                        <div class="position-absolute bottom-0 start-0 bg-primary text-white p-3 rounded-2" style="transform: translate(-10px, 10px);">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-award fa-lg me-2"></i>
                                                <div>
                                                    <div class="fw-bold">Since 2022</div>
                                                    <small>Empowering Youth</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <h3 class="h3 fw-bold mb-3">Our Foundation</h3>
                                    <p class="lead text-muted mb-4">
                                        <strong>INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION (IYEF)</strong> is a registered non-profit, non-governmental, and faith-based organization dedicated to empowering and uplifting weak and vulnerable adolescents from diverse backgrounds globally through innovative reformation initiatives.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2 mb-4">
                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                            <i class="fas fa-hands-helping me-1"></i> Non-Profit
                                        </span>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                            <i class="fas fa-globe me-1"></i> Global Reach
                                        </span>
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                            <i class="fas fa-cross me-1"></i> Faith-Based
                                        </span>
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                            <i class="fas fa-graduation-cap me-1"></i> SDG-4 Advocate
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Core Values Section -->
                            <div class="mb-5">
                                <h3 class="h3 fw-bold mb-4 text-center">Our Core Values</h3>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-primary bg-opacity-5 h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="bg-primary bg-opacity-10 text-white rounded-2 p-3 me-3">
                                                        <i class="fas fa-heart fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="h5 fw-bold mb-2">Compassion</h4>
                                                        <p class="text-white mb-0">We approach every youth with empathy and understanding, recognizing their unique challenges and potential with Christ-like love.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-success bg-opacity-5 h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="bg-success bg-opacity-10 text-white rounded-2 p-3 me-3">
                                                        <i class="fas fa-shield-alt fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="h5 fw-bold mb-2">Integrity</h4>
                                                        <p class="text-white mb-0">We maintain the highest ethical and biblical standards in all our programs, partnerships, and interactions.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-warning bg-opacity-5 h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="bg-warning bg-opacity-10 text-white rounded-2 p-3 me-3">
                                                        <i class="fas fa-hands-helping fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="h5 fw-bold mb-2">Empowerment</h4>
                                                        <p class="text-white mb-0">We equip youth with spiritual, educational, and practical tools to transform their own lives and communities.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 bg-info bg-opacity-5 h-100">
                                            <div class="card-body p-4">
                                                <div class="d-flex align-items-start">
                                                    <div class="bg-info bg-opacity-10 text-white rounded-2 p-3 me-3">
                                                        <i class="fas fa-globe-africa fa-lg"></i>
                                                    </div>
                                                    <div>
                                                        <h4 class="h5 fw-bold mb-2">Inclusivity</h4>
                                                        <p class="text-white mb-0">We serve youth from all backgrounds without discrimination, reflecting God's love for all people.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Our Approach Section -->
                            <div>
                                <h3 class="h3 fw-bold mb-4 text-center">Our Holistic Approach</h3>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <div class="text-center p-3">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-4 mb-3 mx-auto" style="width: 100px; height: 100px;">
                                                <i class="fas fa-book-open fa-2x"></i>
                                            </div>
                                            <h4 class="h5 fw-bold mb-2">Educational Support</h4>
                                            <p class="small text-muted">Providing access to quality education aligned with SDG-4 goals and spiritual development.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3">
                                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-4 mb-3 mx-auto" style="width: 100px; height: 100px;">
                                                <i class="fas fa-tools fa-2x"></i>
                                            </div>
                                            <h4 class="h5 fw-bold mb-2">Skills Training</h4>
                                            <p class="small text-muted">Vocational and technical skills acquisition for self-reliance and entrepreneurship.</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3">
                                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-4 mb-3 mx-auto" style="width: 100px; height: 100px;">
                                                <i class="fas fa-hands-helping fa-2x"></i>
                                            </div>
                                            <h4 class="h5 fw-bold mb-2">Spiritual Mentorship</h4>
                                            <p class="small text-muted">Christ-centered guidance nurturing faith-based values and leadership principles.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Impact Stats Section - Animated -->
<section class="py-5 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary opacity-90"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"40\" fill=\"none\" stroke=\"white\" stroke-width=\"0.5\" stroke-opacity=\"0.1\"/></svg>'); background-size: 100px 100px; opacity: 0.5;"></div>
    
    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-white mb-3">Our Impact Journey</h2>
            <p class="lead text-white opacity-75 mx-auto" style="max-width: 700px;">
                Transforming lives through faith-based empowerment since our inception
            </p>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="text-white">
                    <div class="display-4 fw-bold mb-2 counter" data-target="5000">0</div>
                    <p class="mb-0 opacity-75">Youth Empowered</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-white">
                    <div class="display-4 fw-bold mb-2 counter" data-target="50">0</div>
                    <p class="mb-0 opacity-75">Programs Conducted</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-white">
                    <div class="display-4 fw-bold mb-2 counter" data-target="15">0</div>
                    <p class="mb-0 opacity-75">Communities Reached</p>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-white">
                    <div class="display-4 fw-bold mb-2 counter" data-target="100">0</div>
                    <p class="mb-0 opacity-75">Volunteers Engaged</p>
                </div>
            </div>
        </div>
        
        <!-- Additional Impact Metrics -->
        <div class="row g-4 text-center mt-5 pt-4 border-top border-white border-opacity-25">
            <div class="col-md-4">
                <div class="text-white">
                    <div class="h2 fw-bold mb-2">25+</div>
                    <p class="small opacity-75">Training Workshops</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-white">
                    <div class="h2 fw-bold mb-2">3</div>
                    <p class="small opacity-75">Countries Active</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-white">
                    <div class="h2 fw-bold mb-2">∞</div>
                    <p class="small opacity-75">Lives Touched</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section - Modern Design -->
<section id="team" class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">OUR LEADERSHIP</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Meet Our Dedicated Team</h2>
                <p class="lead text-muted">Passionate individuals committed to transforming youth lives through faith-based leadership</p>
            </div>
        </div>
        
        <?php if (!empty($teamMembers)): ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($teamMembers as $member): 
                    $initials = '';
                    $names = explode(' ', $member['full_name']);
                    foreach ($names as $name) {
                        $initials .= strtoupper(substr($name, 0, 1));
                    }
                    $position = ucwords(str_replace('_', ' ', $member['position']));
                ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-lg h-100 hover-lift overflow-hidden">
                        <div class="team-card-img position-relative" style="height: 250px; overflow: hidden;">
                            <?php if (!empty($member['photo'])): ?>
                                <img src="<?= BASE_URL . $member['photo'] ?>" class="card-img-top w-100 h-100" alt="<?= htmlspecialchars($member['full_name']) ?>" style="object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-primary w-100 h-100 d-flex align-items-center justify-content-center">
                                    <span class="display-1 text-white fw-bold"><?= substr($initials, 0, 2) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                <div class="d-flex justify-content-between align-items-end">
                                    <div>
                                        <h5 class="fw-bold mb-0"><?= htmlspecialchars($member['full_name']) ?></h5>
                                        <small><?= htmlspecialchars($position) ?></small>
                                    </div>
                                    <div class="social-links">
                                        <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="text-white" title="Email">
                                            <i class="fas fa-envelope"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <?php if (!empty($member['bio'])): ?>
                                <p class="card-text text-muted small"><?= htmlspecialchars(substr($member['bio'], 0, 120)) . (strlen($member['bio']) > 120 ? '...' : '') ?></p>
                            <?php else: ?>
                                <p class="card-text text-muted small">Dedicated to empowering youth through faith-based leadership and mentorship.</p>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge bg-primary bg-opacity-10 text-primary">Team Member</span>
                                <button class="btn btn-sm btn-outline-primary team-modal-btn" data-name="<?= htmlspecialchars($member['full_name']) ?>" data-position="<?= htmlspecialchars($position) ?>" data-bio="<?= htmlspecialchars($member['bio'] ?? 'Passionate about youth empowerment and spiritual development.') ?>">
                                    View Profile
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-users fa-4x text-muted mb-4 opacity-50"></i>
                        <h4 class="fw-bold mb-3">Meet Our Team</h4>
                        <p class="text-muted mb-4">Our dedicated leadership team information is being updated with new profiles.</p>
                        <a href="contact.php" class="btn btn-outline-primary">Contact Leadership</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Partners Section - Modern Design -->
<section class="bg-light py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">OUR PARTNERS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Strategic Partnerships</h2>
                <p class="lead text-muted">Collaborating with organizations and communities to amplify our impact</p>
            </div>
        </div>
        
        <div class="row g-4 align-items-center justify-content-center">
            <?php foreach ($partners as $partner): ?>
            <div class="col-6 col-md-3 col-lg-2">
                <div class="partner-card bg-white rounded-3 p-4 shadow-sm d-flex align-items-center justify-content-center hover-lift" style="height: 120px;">
                    <?php if (file_exists('assets/images/' . $partner['logo'])): ?>
                        <img src="assets/images/<?= $partner['logo'] ?>" alt="<?= $partner['name'] ?>" class="img-fluid" style="max-height: 60px; object-fit: contain;">
                    <?php else: ?>
                        <div class="text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-2">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <small class="text-muted"><?= $partner['name'] ?></small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-5 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-dark opacity-95"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: url('data:image/svg+xml,<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 100 100\"><circle cx=\"50\" cy=\"50\" r=\"40\" fill=\"none\" stroke=\"white\" stroke-width=\"0.5\" stroke-opacity=\"0.1\"/></svg>'); background-size: 100px 100px; opacity: 0.5;"></div>
    
    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-4 fw-bold text-white mb-4">Join Our Mission of Transformation</h2>
                <p class="lead text-white opacity-75 mb-5">
                    Be part of raising the next generation of indefatigable kingdom youths who will make lasting impacts in their communities.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="donate.php" class="btn btn-light btn-lg px-5 py-3">
                        <i class="fas fa-donate me-2"></i> Support Our Work
                    </a>
                    <a href="volunteer.php" class="btn btn-outline-light btn-lg px-5 py-3">
                        <i class="fas fa-hands-helping me-2"></i> Become a Volunteer
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Member Modal -->
<div class="modal fade" id="teamMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="modalMemberName"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted" id="modalMemberPosition"></p>
                <p id="modalMemberBio"></p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" class="btn btn-primary" id="modalMemberEmail">Contact</a>
            </div>
        </div>
    </div>
</div>

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
    
    // Team member modal
    const teamModalBtns = document.querySelectorAll('.team-modal-btn');
    const teamMemberModal = new bootstrap.Modal(document.getElementById('teamMemberModal'));
    
    teamModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const name = this.getAttribute('data-name');
            const position = this.getAttribute('data-position');
            const bio = this.getAttribute('data-bio');
            
            document.getElementById('modalMemberName').textContent = name;
            document.getElementById('modalMemberPosition').textContent = position;
            document.getElementById('modalMemberBio').textContent = bio;
            
            teamMemberModal.show();
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

<!-- Add custom CSS -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
    --success-gradient: linear-gradient(135deg, #28a745, #1e7e34);
    --warning-gradient: linear-gradient(135deg, #ffc107, #e0a800);
    --purple: #6f42c1;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-gradient-dark {
    background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.9));
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

.min-vh-80 {
    min-height: 80vh;
}

.dot {
    animation: pulse 2s infinite;
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

.team-card-img {
    transition: transform 0.5s ease;
}

.team-card-img:hover {
    transform: scale(1.05);
}

.partner-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.partner-card:hover {
    transform: translateY(-5px);
}

.mission-statement, .vision-statement {
    border-left: 4px solid;
    font-size: 1.1rem;
}

.mission-statement {
    border-left-color: var(--bs-primary);
}

.vision-statement {
    border-left-color: var(--bs-warning);
}

.bible-verse {
    font-size: 0.95rem;
    line-height: 1.6;
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
    
    .counter {
        font-size: 2.5rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>