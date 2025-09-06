<?php
$page_title = "Empowering Youth for a Better Future";
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Empowering Youth for a Better Future</h1>
                <p class="lead mb-4">We are committed to providing educational opportunities, skills training, and holistic mentorship to help youth overcome challenges and reach their full potential.</p>
                <div class="d-flex gap-3">
                    <a href="programs.php" class="btn btn-light btn-lg px-4">Our Programs</a>
                    <a href="donate.php" class="btn btn-outline-light btn-lg px-4">Donate Now</a>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/IYEF_hero1.jpg" alt="Empowered Youth" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="assets/images/IYEF_gt1.jpg" alt="About IYEF" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">About IYEF</h2>
                <p class="lead">Indefatigable Youth Empowerment Foundation (IYEF) is a registered non-profit, non-governmental, and faith-based organization.</p>
                <p>As a beacon of hope, we are dedicated to empowering and uplifting weak and vulnerable adolescents from diverse backgrounds globally through reformation initiatives.</p>
                <p>We advocate for the United Nation Sustainable Development Goal 4 (SDG-4), which focuses on quality education for all.</p>
                <a href="about.php" class="btn btn-primary mt-3">Learn More About Us</a>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Mission</h2>
            <p class="lead">To raise and empower weak and vulnerable adolescents to become sound, resilient kingdom youths</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-graduation-cap fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Education</h5>
                        <p>Providing quality education opportunities to underprivileged youth to help them break the cycle of poverty.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-hands-helping fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Skills Training</h5>
                        <p>Equipping youth with practical skills that make them self-reliant and productive members of society.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-heart fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Mentorship</h5>
                        <p>Providing holistic mentorship to help youth develop strong character and ethical principles.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold mb-0">Our Programs</h2>
            <a href="programs.php" class="btn btn-outline-primary">View All Programs</a>
        </div>
        <div class="row g-4">
            <?php
            // Fetch featured programs.git 
            $programs_query = "SELECT id, title, slug, description, image FROM programs ORDER BY created_at DESC LIMIT 3";
            $programs_result = $conn->query($programs_query);
            
            if ($programs_result->num_rows > 0) {
                while ($program = $programs_result->fetch_assoc()) {
                    echo '<div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <img src="' . (!empty($program['image']) ? BASE_URL . $program['image'] : 'assets/images/IYEF_logo.jpg') . '" class="card-img-top" alt="' . htmlspecialchars($program['title']) . '" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">' . htmlspecialchars($program['title']) . '</h5>
                                    <p class="card-text">' . substr(strip_tags($program['description']), 0, 100) . '...</p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <a href="program.php?slug=' . $program['slug'] . '" class="btn btn-primary">Learn More</a>
                                </div>
                            </div>
                        </div>';
                }
            } else {
                echo '<div class="col-12 text-center py-4">
                        <p class="text-muted">No programs available at the moment. Please check back later.</p>
                    </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Events Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold mb-0">Upcoming Events</h2>
            <a href="events.php" class="btn btn-outline-primary">View All Events</a>
        </div>
        <div class="row g-4">
            <?php
            // Fetch upcoming events
            $events_query = "SELECT id, title, slug, description, location, event_date, image FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 3";
            $events_result = $conn->query($events_query);
            
            if ($events_result->num_rows > 0) {
                while ($event = $events_result->fetch_assoc()) {
                    $event_date = new DateTime($event['event_date']);
                    echo '<div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <img src="' . BASE_URL . (!empty($event['image']) ? $event['image'] : '<?= BASE_URL ?>assets/images/event-default.jpg') . '" class="card-img-top" alt="' . htmlspecialchars($event['title']) . '" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-primary text-white text-center p-2 rounded me-3" style="width: 60px;">
                                            <div class="fw-bold">' . $event_date->format('M') . '</div>
                                            <div class="fs-5">' . $event_date->format('d') . '</div>
                                        </div>
                                        <div>
                                            <h5 class="card-title fw-bold mb-0">' . htmlspecialchars($event['title']) . '</h5>
                                            <small class="text-muted"><i class="fas fa-clock me-1"></i> ' . $event_date->format('h:i A') . '</small>
                                        </div>
                                    </div>
                                    <p class="card-text"><i class="fas fa-map-marker-alt me-1 text-primary"></i> ' . htmlspecialchars($event['location']) . '</p>
                                    <p class="card-text">' . substr(strip_tags($event['description']), 0, 100) . '...</p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                                    <a href="event.php?slug=' . $event['slug'] . '" class="btn btn-sm btn-outline-primary">Details</a>
                                    <a href="event-register.php?event_id=' . $event['id'] . '" class="btn btn-sm btn-primary">Register</a>
                                </div>
                            </div>
                        </div>';
                }
            } else {
                echo '<div class="col-12 text-center py-4">
                        <p class="text-muted">No upcoming events at the moment. Please check back later.</p>
                    </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h2 class="fw-bold mb-0">Latest Blog Posts</h2>
            <a href="blog.php" class="btn btn-outline-primary">View All Posts</a>
        </div>
        <div class="row g-4">
            <?php
            // Fetch latest blog posts
            $blog_query = "SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, u.full_name AS author, bp.published_at 
                          FROM blog_posts bp 
                          JOIN users u 
                          WHERE bp.is_published = 1 
                          ORDER BY bp.published_at DESC 
                          LIMIT 3";
            $blog_result = $conn->query($blog_query);
            
            if ($blog_result->num_rows > 0) {
                while ($post = $blog_result->fetch_assoc()) {
                    $post_date = new DateTime($post['published_at']);
                    echo '<div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <img src="' . BASE_URL . (!empty($post['featured_image']) ? $post['featured_image'] : '<?= BASE_URL ?>assets/images/blog-default.jpg') . '" class="card-img-top" alt="' . htmlspecialchars($post['title']) . '" style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted"><i class="fas fa-user me-1"></i> ' . htmlspecialchars($post['author']) . '</small>
                                        <small class="text-muted"><i class="fas fa-calendar-alt me-1"></i> ' . $post_date->format('M j, Y') . '</small>
                                    </div>
                                    <h5 class="card-title fw-bold">' . htmlspecialchars($post['title']) . '</h5>
                                    <p class="card-text">' . (!empty($post['excerpt']) ? $post['excerpt'] : substr(strip_tags($post['content']), 0, 150) . '...') . '</p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <a href="blog-post.php?slug=' . $post['slug'] . '" class="btn btn-sm btn-primary">Read More</a>
                                </div>
                            </div>
                        </div>';
                }
            } else {
                echo '<div class="col-12 text-center py-4">
                        <p class="text-muted">No blog posts available at the moment. Please check back later.</p>
                    </div>';
            }
            ?>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-primary text-white py-5">
    <div class="container text-center py-4">
        <h2 class="fw-bold mb-4">Join Us in Empowering Youth</h2>
        <p class="lead mb-5">Your support can make a difference in the lives of vulnerable adolescents. Whether through donations, volunteering, or partnerships, you can help us achieve our mission.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="donate.php" class="btn btn-light btn-lg px-4">Donate Now</a>
            <a href="volunteer.php" class="btn btn-outline-light btn-lg px-4">Become a Volunteer</a>
        </div>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>