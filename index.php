<?php
$page_title = "Empowering Youth for a Better Future";
require_once 'includes/header.php';
?>

<!-- Hero Section - Full Background Image -->
<section class="hero-section position-relative overflow-hidden min-vh-100 d-flex align-items-center">
    <!-- Background image with gradient overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.85), rgba(0, 123, 255, 0.9)), url('assets/images/IYEF_hero1.jpg'); background-size: cover; background-position: center; background-attachment: fixed;"></div>
    
    <!-- Animated shapes background -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 400px; height: 400px; background: rgba(255, 255, 255, 0.1); top: -200px; right: -150px;"></div>
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.05); bottom: -150px; left: -100px;"></div>
        <div class="position-absolute" style="width: 200px; height: 200px; border: 2px solid rgba(255, 255, 255, 0.1); top: 20%; left: 10%; border-radius: 50%;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8 col-xl-7">
                <!-- Badge -->
                <div class="d-inline-flex align-items-center bg-white bg-opacity-20 rounded-pill px-4 py-2 mb-2 mt-4 animate-fade-in">
                    <span class="dot me-2" style="width: 10px; height: 10px; background: #4cd964; border-radius: 50%; animation: pulse 2s infinite;"></span>
                    <span class="fw-medium">Join 100+ Empowered Youths Worldwide</span>
                </div>
                
                <!-- Main heading with animated text -->
                <h1 class="display-2 fw-bold mb-4 text-white animate-slide-up" style="line-height: 1.1;">
                    Transforming <span class="text-warning">Youth</span><br>
                    <span class="before-typed-text">-</span><span class="typed-text" id="typed-text"></span>
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    We bridge the gap between potential and opportunity through education, mentorship, and practical skills training for vulnerable adolescents worldwide.
                </p>
                
                <!-- Action buttons -->
                <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 animate-fade-in" style="animation-delay: 0.5s;">
                    <a href="programs.php" class="btn btn-light btn-lg px-5 py-3 d-flex align-items-center shadow-lg">
                        <i class="fas fa-play-circle me-3 fs-4"></i> 
                        <div class="text-start">
                            <div class="fw-bold">Explore Programs</div>
                            <small class="opacity-75">Discover Opportunities</small>
                        </div>
                    </a>
                    <a href="about.php" class="btn btn-outline-light btn-lg px-5 py-3 d-flex align-items-center">
                        <i class="fas fa-compass me-3 fs-4"></i>
                        <div class="text-start">
                            <div class="fw-bold">Our Mission</div>
                            <small class="opacity-75">Learn More</small>
                        </div>
                    </a>
                </div>
                
                <!-- Stats Section -->
                <div class="row g-4 mt-5 pt-4 border-top border-white border-opacity-25 animate-fade-in" style="animation-delay: 0.7s;">
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="100">0</div>
                            <div class="small text-uppercase opacity-75">Youths Trained</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="10">0</div>
                            <div class="small text-uppercase opacity-75">Communities</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="15">0</div>
                            <div class="small text-uppercase opacity-75">Programs</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="text-white text-center">
                            <div class="display-4 fw-bold mb-2 counter" data-target="3">0</div>
                            <div class="small text-uppercase opacity-75">Years Active</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#about" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-indicator-link">
            <span class="small mb-2 opacity-75">Scroll to explore</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- About Section - Redesigned -->
<section id="about" class="py-5 position-relative">
    <!-- Background pattern -->
    <div class="position-absolute top-0 end-0 w-50 h-100 opacity-5"
        style="background-image: url('data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\' preserveAspectRatio=\'none\'><path d=\'M0,0 L100,0 L100,100 Z\' fill=\'%23007bff\'/></svg>');
                background-size: cover;
                background-repeat: no-repeat;
                z-index: -1;">
    </div>
    
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="position-relative">
                    <img src="assets/images/IYEF_gt1.jpg" alt="About IYEF" class="img-fluid rounded-4 shadow-lg">
                    
                    <!-- Accent element -->
                    <div class="position-absolute bottom-0 start-0 bg-primary text-white p-4 rounded-3" style="transform: translate(-20px, 20px); max-width: 250px;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-handshake fa-2x me-3"></i>
                            <div>
                                <h5 class="fw-bold mb-0">Since 2021</h5>
                                <small class="opacity-75">Empowering Youth</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7 ps-lg-5">
                <!-- Section badge -->
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">ABOUT US</span>
                </div>
                
                <h2 class="display-5 fw-bold mb-4">Building Resilient Kingdom Youths</h2>
                
                <div class="mb-4">
                    <p class="lead text-muted">
                        <strong>Indefatigable Youth Empowerment Foundation (IYEF)</strong> is a beacon of hope dedicated to empowering vulnerable adolescents from diverse backgrounds globally through innovative reformation initiatives.
                    </p>
                </div>
                
                <!-- Key points -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="bg-success bg-opacity-10 p-2 rounded-2 me-3">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Registered Non-Profit</h6>
                                <p class="small text-muted mb-0">Fully compliant with regulatory standards</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="bg-info bg-opacity-10 p-2 rounded-2 me-3">
                                <i class="fas fa-globe text-info"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Global Reach</h6>
                                <p class="small text-muted mb-0">Operating across multiple continents</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="bg-warning bg-opacity-10 p-2 rounded-2 me-3">
                                <i class="fas fa-cross text-warning"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Faith-Based</h6>
                                <p class="small text-muted mb-0">Rooted in Christian principles and values</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="bg-purple bg-opacity-10 p-2 rounded-2 me-3">
                                <i class="fas fa-bullseye text-purple"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">SDG-4 Advocacy</h6>
                                <p class="small text-muted mb-0">Championing quality education for all</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <a href="about.php" class="btn btn-primary btn-lg px-4">
                    Learn Our Story <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section - Interactive Cards -->
<section class="py-5 bg-gradient-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                <span class="small fw-bold">OUR MISSION</span>
            </div>
            <h2 class="display-5 fw-bold mb-3">Raising Sound, Resilient Kingdom Youths</h2>
            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                We transform vulnerable adolescents into empowered leaders through comprehensive development programs.
            </p>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg hover-lift position-relative overflow-hidden">
                    <div class="card-gradient position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, #007bff, #0056b3); opacity: 0; transition: opacity 0.3s;"></div>
                    <div class="card-body p-4 position-relative" style="z-index: 2;">
                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 p-3 mb-4" style="width: 70px; height: 70px;">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Quality Education</h3>
                        <p class="text-muted mb-4">
                            Providing access to quality education that breaks the cycle of poverty and opens doors to brighter futures.
                        </p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Scholarship programs</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Digital literacy training</li>
                            <li><i class="fas fa-check-circle text-primary me-2"></i> Career guidance</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 position-relative" style="z-index: 2;">
                        <a href="programs.php?category=education" class="text-primary text-decoration-none fw-bold">
                            Explore Education Programs <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg hover-lift position-relative overflow-hidden">
                    <div class="card-gradient position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, #28a745, #1e7e34); opacity: 0; transition: opacity 0.3s;"></div>
                    <div class="card-body p-4 position-relative" style="z-index: 2;">
                        <div class="icon-wrapper bg-success bg-opacity-10 text-success rounded-3 p-3 mb-4" style="width: 70px; height: 70px;">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Skills Development</h3>
                        <p class="text-muted mb-4">
                            Equipping youth with practical, market-relevant skills for self-reliance and economic independence.
                        </p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Vocational training</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Entrepreneurship workshops</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Technical skills development</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 position-relative" style="z-index: 2;">
                        <a href="programs.php?category=skills" class="text-success text-decoration-none fw-bold">
                            View Skills Programs <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card h-100 border-0 shadow-lg hover-lift position-relative overflow-hidden">
                    <div class="card-gradient position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, #6f42c1, #563d7c); opacity: 0; transition: opacity 0.3s;"></div>
                    <div class="card-body p-4 position-relative" style="z-index: 2;">
                        <div class="icon-wrapper bg-purple bg-opacity-10 text-purple rounded-3 p-3 mb-4" style="width: 70px; height: 70px;">
                            <i class="fas fa-compass fa-2x"></i>
                        </div>
                        <h3 class="h4 fw-bold mb-3">Holistic Mentorship</h3>
                        <p class="text-muted mb-4">
                            Providing character-building mentorship that develops ethical leaders with strong moral foundations.
                        </p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="fas fa-check-circle text-purple me-2"></i> One-on-one counseling</li>
                            <li class="mb-2"><i class="fas fa-check-circle text-purple me-2"></i> Leadership training</li>
                            <li><i class="fas fa-check-circle text-purple me-2"></i> Spiritual guidance</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 position-relative" style="z-index: 2;">
                        <a href="programs.php?category=mentorship" class="text-purple text-decoration-none fw-bold">
                            Discover Mentorship <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section - Modern Grid -->
<section class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">OUR PROGRAMS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Transformative Youth Programs</h2>
                <p class="lead text-muted">Designed to address specific challenges and unlock potential in vulnerable adolescents.</p>
            </div>
            <div class="col-lg-4 text-lg-end align-self-end">
                <a href="programs.php" class="btn btn-outline-primary btn-lg px-4">
                    View All Programs <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        
        <div class="row g-4">
            <?php
            // Fetch featured programs
            $programs_query = "SELECT id, title, slug, description, image FROM programs ORDER BY created_at DESC LIMIT 3";
            $programs_result = $conn->query($programs_query);
            
            if ($programs_result->num_rows > 0) {
                $color_classes = ['bg-primary', 'bg-success', 'bg-warning'];
                $i = 0;
                while ($program = $programs_result->fetch_assoc()) {
                    $color_class = $color_classes[$i % count($color_classes)];
                    echo '<div class="col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden transition-all">
                                <div class="position-relative overflow-hidden">
                                    <img src="' . (!empty($program['image']) ? BASE_URL . $program['image'] : 'assets/images/IYEF_logo.jpg') . '" 
                                         class="card-img-top program-image" 
                                         alt="' . htmlspecialchars($program['title']) . '" 
                                         style="height: 250px; object-fit: cover;">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge ' . $color_class . ' rounded-pill px-3 py-2">Featured</span>
                                    </div>
                                    <div class="image-overlay position-absolute top-0 start-0 w-100 h-100 bg-gradient-dark opacity-0 transition-all"></div>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-3">' . htmlspecialchars($program['title']) . '</h5>
                                    <p class="card-text text-muted mb-4">' . substr(strip_tags($program['description']), 0, 120) . '...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="program.php?slug=' . $program['slug'] . '" class="btn btn-outline-primary">
                                            Learn More <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                        <div class="text-muted small">
                                            <i class="fas fa-users me-1"></i> Open to All
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                    $i++;
                }
            } else {
                echo '<div class="col-12 text-center py-5">
                        <div class="empty-state">
                            <i class="fas fa-calendar-alt fa-4x text-muted mb-4 opacity-50"></i>
                            <h4 class="fw-bold mb-3">Programs Coming Soon</h4>
                            <p class="text-muted mb-4">We are preparing exciting new programs for youth empowerment.</p>
                            <a href="contact.php" class="btn btn-primary">Get Notified</a>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- CTA Banner -->
<section class="py-5">
    <div class="container">
        <div class="bg-gradient-primary rounded-4 p-5 text-white position-relative overflow-hidden">
            <!-- Background pattern -->
            <div class="row align-items-center position-relative" style="z-index: 2;">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <h3 class="display-6 fw-bold mb-3">Ready to Make a Difference?</h3>
                    <p class="lead mb-0 opacity-90">Join our community of changemakers and help transform young lives today.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="donate.php" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-heart me-2"></i> Donate Now
                        </a>
                        <a href="volunteer.php" class="btn btn-outline-light btn-lg px-4">
                            Volunteer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Events Section - Modern Timeline -->
<section class="py-5 bg-light">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">UPCOMING EVENTS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">Join Our Transformative Events</h2>
                <p class="lead text-muted">Experience life-changing workshops, seminars, and community gatherings.</p>
            </div>
            <div class="col-lg-4 text-lg-end align-self-end">
                <a href="events.php" class="btn btn-primary btn-lg px-4">
                    View Calendar <i class="fas fa-calendar-alt ms-2"></i>
                </a>
            </div>
        </div>
        
        <div class="row g-4">
            <?php
            // Fetch upcoming events using the function
            require_once 'includes/functions/events.php';
            $eventsData = getAllEvents(1, 3, true); // Get first page, 3 events, upcoming only
            
            if (!empty($eventsData['events'])) {
                foreach ($eventsData['events'] as $event) {
                    $event_date = new DateTime($event['event_date']);
                    $event_day = $event_date->format('d');
                    $event_month = $event_date->format('M');
                    $event_time = $event_date->format('h:i A');
                    
                    echo '<div class="col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden transition-all hover-lift">
                                <div class="position-relative">
                                    <img src="' . BASE_URL . (!empty($event['image']) ? $event['image'] : 'assets/images/event-default.jpg') . '" 
                                         class="card-img-top" 
                                         alt="' . htmlspecialchars($event['title']) . '" 
                                         style="height: 220px; object-fit: cover;">
                                    <div class="date-badge position-absolute top-0 start-0 m-3 bg-white text-dark rounded-3 p-3 text-center shadow">
                                        <div class="fw-bold fs-4">' . $event_day . '</div>
                                        <div class="small text-uppercase text-muted">' . $event_month . '</div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                                <i class="fas fa-calendar-alt"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="card-title fw-bold mb-1">' . htmlspecialchars($event['title']) . '</h5>
                                            <div class="d-flex flex-wrap gap-3 small text-muted">
                                                <span><i class="fas fa-clock me-1"></i> ' . $event_time . '</span>
                                                <span><i class="fas fa-map-marker-alt me-1"></i> ' . htmlspecialchars($event['location'] ?? 'TBD') . '</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="card-text text-muted mb-4">' . substr(strip_tags($event['description']), 0, 100) . '...</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="event.php?slug=' . $event['slug'] . '" class="btn btn-sm btn-outline-primary">
                                            Details
                                        </a>
                                        <a href="event-register.php?event_id=' . $event['id'] . '" class="btn btn-sm btn-primary">
                                            <i class="fas fa-user-plus me-1"></i> Register
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>';
                }
            } else {
                echo '<div class="col-12">
                        <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-calendar-plus fa-4x text-muted mb-4 opacity-50"></i>
                                <h4 class="fw-bold mb-3">Events Coming Soon</h4>
                                <p class="text-muted mb-4">Stay tuned for upcoming youth empowerment events and workshops.</p>
                                <a href="contact.php" class="btn btn-outline-primary">Notify Me</a>
                            </div>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="py-5">
    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-lg-8">
                <div class="d-inline-flex align-items-center bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 mb-3">
                    <span class="small fw-bold">LATEST INSIGHTS</span>
                </div>
                <h2 class="display-5 fw-bold mb-3">From Our Blog</h2>
                <p class="lead text-muted">Stay updated with inspiring stories, expert insights, and youth empowerment resources.</p>
            </div>
            <div class="col-lg-4 text-lg-end align-self-end">
                <a href="blog.php" class="btn btn-outline-primary btn-lg px-4">
                    View All Posts <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
        
        <div class="row g-4">
            <?php
            // Fetch latest blog posts using the function
            require_once 'includes/functions/blog.php';
            $blogData = getBlogPosts(1, 3, true); // Get first page, 3 posts, published only
            
            if (!empty($blogData['posts'])) {
                foreach ($blogData['posts'] as $post) {
                    $post_date = new DateTime($post['published_at']);
                    echo '<div class="col-lg-4 col-md-6">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden transition-all hover-lift">
                                <div class="position-relative overflow-hidden">
                                    <img src="' . BASE_URL . (!empty($post['featured_image']) ? $post['featured_image'] : 'assets/images/blog-default.jpg') . '" 
                                         class="card-img-top" 
                                         alt="' . htmlspecialchars($post['title']) . '" 
                                         style="height: 220px; object-fit: cover;">
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3 text-white" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle p-2 me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <small class="fw-medium">' . htmlspecialchars($post['author_name'] ?? 'IYEF Team') . '</small>
                                                <br>
                                                <small><i class="far fa-calendar me-1"></i>' . $post_date->format('M j, Y') . '</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <h5 class="card-title fw-bold mb-3">' . htmlspecialchars($post['title']) . '</h5>
                                    <p class="card-text text-muted mb-4">' . (!empty($post['excerpt']) ? $post['excerpt'] : (isset($post['content']) ? substr(strip_tags($post['content']), 0, 150) . '...' : 'Read more...')) . '</p>
                                    <a href="blog-post.php?slug=' . $post['slug'] . '" class="btn btn-sm btn-outline-primary">
                                        Read Article <i class="fas fa-arrow-right ms-2"></i>
                                    </a>
                                </div>
                            </div>
                        </div>';
                }
            } else {
                echo '<div class="col-12">
                        <div class="card border-dashed border-2 border-muted rounded-4 text-center py-5">
                            <div class="card-body">
                                <i class="fas fa-newspaper fa-4x text-muted mb-4 opacity-50"></i>
                                <h4 class="fw-bold mb-3">Blog Coming Soon</h4>
                                <p class="text-muted mb-4">Our blog with inspiring stories and resources will be available soon.</p>
                                <a href="contact.php" class="btn btn-outline-primary">Subscribe</a>
                            </div>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Final CTA Section -->
<section class="py-5 position-relative overflow-hidden">
    <!-- Background pattern -->
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary opacity-95"></div>
    <div class="position-absolute top-0 start-0 w-100 h-100"
     style="
        background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220%200%20100%20100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22none%22 stroke=%22white%22 stroke-width=%220.5%22 stroke-opacity=%220.1%22/></svg>');
        background-size: 100px 100px;
        ">
    </div>
    
    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-4 fw-bold text-white mb-4">Together, We Can Empower a Generation</h2>
                <p class="lead text-white opacity-75 mb-5">
                    Your support transforms lives. Join us in building a future where every youth has the opportunity to thrive.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="donate.php" class="btn btn-light btn-lg px-5 py-3">
                        <i class="fas fa-donate me-2"></i> Support Our Mission
                    </a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg px-5 py-3">
                        <i class="fas fa-handshake me-2"></i> Partner With Us
                    </a>
                </div>
                
                <div class="mt-5 pt-4 border-top border-white border-opacity-25">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="text-white">
                                <div class="display-6 fw-bold mb-2">24/7</div>
                                <div class="small opacity-75">Support Available</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-white">
                                <div class="display-6 fw-bold mb-2">100%</div>
                                <div class="small opacity-75">Transparent</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-white">
                                <div class="display-6 fw-bold mb-2">∞</div>
                                <div class="small opacity-75">Impact Potential</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for enhanced interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Typing animation for hero section
    const typedText = document.getElementById('typed-text');
    const words = ['Potentials', 'Futures', 'Communities', 'Dreams', 'Lives'];
    let wordIndex = 0;
    let charIndex = 0;
    let isDeleting = false;
    let isEnd = false;
    
    function typeEffect() {
        const currentWord = words[wordIndex];
        
        if (isDeleting) {
            typedText.textContent = currentWord.substring(0, charIndex - 1);
            charIndex--;
        } else {
            typedText.textContent = currentWord.substring(0, charIndex + 1);
            charIndex++;
        }
        
        if (!isDeleting && charIndex === currentWord.length) {
            isEnd = true;
            isDeleting = true;
            setTimeout(typeEffect, 1500);
        } else if (isDeleting && charIndex === 0) {
            isDeleting = false;
            wordIndex = (wordIndex + 1) % words.length;
            setTimeout(typeEffect, 500);
        } else {
            const speed = isDeleting ? 50 : 100;
            setTimeout(typeEffect, speed);
        }
    }
    
    // Start typing animation after a delay
    setTimeout(typeEffect, 1000);
    
    // Counter animation
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // The lower the faster
    
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
        
        // Start counter when element is in viewport
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
    
    // Smooth scroll for scroll indicator
    const scrollLinks = document.querySelectorAll('.scroll-indicator-link');
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
    
    // Add hover effects to cards
    const cards = document.querySelectorAll('.hover-lift');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
            this.style.transition = 'transform 0.3s ease';
            
            const gradient = this.querySelector('.card-gradient');
            if (gradient) {
                gradient.style.opacity = '0.1';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            
            const gradient = this.querySelector('.card-gradient');
            if (gradient) {
                gradient.style.opacity = '0';
            }
        });
    });
    
    // Program image hover effect
    const programImages = document.querySelectorAll('.program-image');
    programImages.forEach(img => {
        const parent = img.closest('.position-relative');
        const overlay = parent.querySelector('.image-overlay');
        
        parent.addEventListener('mouseenter', function() {
            img.style.transform = 'scale(1.05)';
            img.style.transition = 'transform 0.5s ease';
            overlay.style.opacity = '0.3';
        });
        
        parent.addEventListener('mouseleave', function() {
            img.style.transform = 'scale(1)';
            overlay.style.opacity = '0';
        });
    });
});
</script>

<!-- Add custom CSS for animations -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
    --success-gradient: linear-gradient(135deg, #28a745, #1e7e34);
    --warning-gradient: linear-gradient(135deg, #ffc107, #e0a800);
    --purple: #6f42c1;
    --bgpurple:rgb(239, 232, 255);
}

.bg-gradient-dark {
    background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.9));
}

.bg-gradient-light {
    background: linear-gradient(180deg, #f8f9fa 0%, #ffffff 100%);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.bg-purple {
    background-color: var(--bgpurple) !important;
}
.before-typed-text{
    visibility: hidden;
}

.text-purple {
    color: var(--purple) !important;
}

.border-dashed {
    border-style: dashed !important;
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
}

.transition-all {
    transition: all 0.3s ease;
}

.animate-fade-in {
    animation: fadeIn 1s ease-out;
}

.animate-slide-up {
    animation: slideUp 1s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
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
    0% {
        box-shadow: 0 0 0 0 rgba(76, 217, 100, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(76, 217, 100, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(76, 217, 100, 0);
    }
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
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

.hero-section {
    background-attachment: fixed;
}

.min-vh-100 {
    min-height: 100vh;
}

.dot {
    animation: pulse 2s infinite;
}

/* Button hover effects */
.btn-light {
    transition: all 0.3s ease;
}

.btn-light:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2) !important;
}

.btn-outline-light:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.card-gradient {
    transition: opacity 0.3s ease;
}

.image-overlay {
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
    transition: opacity 0.5s ease;
}

.date-badge {
    min-width: 60px;
}

.empty-state {
    padding: 4rem 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .display-5 {
        font-size: 1.75rem;
    }
    
    .display-6 {
        font-size: 1.5rem;
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
    
    .hero-section {
        background-attachment: scroll;
    }
}
</style>

<?php
require_once 'includes/footer.php';
?>