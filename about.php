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
        SELECT u.full_name, u.email, p.position 
        FROM users u
        JOIN profiles p ON u.id = p.id
        WHERE p.position IN ('admin','director', 'manager', 'coordinator')
        AND u.is_active = 1
        ORDER BY u.full_name
        LIMIT 12
    ");
} catch (mysqli_sql_exception $e) {
    error_log("Error fetching team members: " . $e->getMessage());
}

// Get partners data
$partners = [
    ['image' => 'partner-1.png', 'name' => 'Partner 1'],
    ['image' => 'partner-2.png', 'name' => 'Partner 2'],
    ['image' => 'partner-3.png', 'name' => 'Partner 3'],
    ['image' => 'partner-4.png', 'name' => 'Partner 4']
];
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">About INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION</h1>
                <p class="lead mb-4">Empowering youth through education, skills training, and mentorship to help them reach their full potential.</p>
                <a href="#mission" class="btn btn-light btn-lg me-2">Our Mission</a>
                <a href="#team" class="btn btn-outline-light btn-lg">Our Team</a>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/IYEF_gt_z.jpg" alt="IYEF Team" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section id="mission" class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-bullseye fa-2x"></i>
                        </div>
                        <h3 class="h4">Our Mission</h3>
                        <p class="mb-0">To reform, mentor and empower weak and vulnerable adolescents from diverse backgrounds to become indefatigable breeds of kingdom youths leading sound and balanced lives, being self-reliant and making a positive impact in their communities. Additionally, we aim to train and develop impactful youth leaders and parents.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-eye fa-2x"></i>
                        </div>
                        <h3 class="h4">Our Vision</h3>
                        <p class="mb-0">Our vision is to raise kingdom youths who can weather the storms of life, making positive impact with their lives.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Content Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <?php if (!empty($aboutContent['content'])): ?>
                            <?= $aboutContent['content'] ?>
                        <?php else: ?>
                            <h2 class="mb-4">Who We Are</h2>
                            <p>
                                INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION (IYEF) is a non-profit, non-governmental, and faith-based organization (NGO) dedicated to empowering and uplifting weak and vulnerable adolescents from diverse backgrounds globally through reformation initiatives. We are committed to providing educational opportunities, skills training, holistic mentorship, and resources to help youth overcome challenges and reach their full potential. Additionally, we advocate for United Nation Sustainable Development Goal SDG- goal 4, which focuses on quality education.
                                Our vision is anchored in Isaiah 58 verse 12, "...to raise the foundations of many generations.” We aim to raise kingdom youths who can weather the storms of life and make a positive impact with their lives.
                                To the glory of God, IYEF has been positively impacting many youths through its youth mentorship and empowerment programs, summits, campaigns, as well as transformative content shared on its social media platforms.
                            </p>
                            
                            <h3 class="mt-5 mb-3">Our History</h3>
                            <p>Founded in 2022, IYEF began as a small community initiative and has grown into a recognized organization impacting thousands of youth annually. Our journey has been marked by consistent commitment to youth development and empowerment.</p>
                            
                            <h3 class="mt-5 mb-3">Our Core Values</h3>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3 text-primary">
                                            <i class="fas fa-heart fa-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="h5">Compassion</h4>
                                            <p>We approach every youth with empathy and understanding, recognizing their unique challenges and potential.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3 text-primary">
                                            <i class="fas fa-shield-alt fa-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="h5">Integrity</h4>
                                            <p>We maintain the highest ethical standards in all our programs and interactions.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3 text-primary">
                                            <i class="fas fa-hands-helping fa-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="h5">Empowerment</h4>
                                            <p>We believe in equipping youth with the tools they need to transform their own lives.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex">
                                        <div class="me-3 text-primary">
                                            <i class="fas fa-globe-africa fa-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="h5">Inclusivity</h4>
                                            <p>We serve youth from all backgrounds without discrimination.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h3 class="mt-5 mb-3">Our Approach</h3>
                            <p>We implement a holistic approach to youth development through:</p>
                            <ul>
                                <li><strong>Educational Support:</strong> Providing access to quality education and learning resources</li>
                                <li><strong>Skills Training:</strong> Offering vocational and technical skills acquisition programs</li>
                                <li><strong>Mentorship:</strong> Connecting youth with positive role models and mentors</li>
                                <li><strong>Spiritual Guidance:</strong> Nurturing faith-based values and ethical principles</li>
                                <li><strong>Community Building:</strong> Creating supportive networks for youth development</li>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section id="team" class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Leadership Team</h2>
        <?php if (!empty($teamMembers)): ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($teamMembers as $member): ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-sm h-100">
                        <img src="/assets/images/avatar-default.png" class="card-img-top" alt="<?= htmlspecialchars($member['full_name']) ?>">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-1"><?= htmlspecialchars($member['full_name']) ?></h5>
                            <p class="text-muted mb-2"><?= htmlspecialchars(ucfirst($member['position'])) ?></p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="mailto:<?= htmlspecialchars($member['email']) ?>" class="text-primary" title="Email">
                                    <i class="fas fa-envelope"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <p>Our team information is currently being updated. Please check back soon to meet our leadership team.</p>
                <div class="mt-3">
                    <a href="contact.php" class="btn btn-primary">Contact Our Team</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Impact Stats Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Impact</h2>
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3">
                <div class="display-4 fw-bold">5,000+</div>
                <p class="mb-0">Youth Empowered</p>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-4 fw-bold">50+</div>
                <p class="mb-0">Programs Conducted</p>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-4 fw-bold">15+</div>
                <p class="mb-0">Communities Reached</p>
            </div>
            <div class="col-6 col-md-3">
                <div class="display-4 fw-bold">100+</div>
                <p class="mb-0">Volunteers Engaged</p>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Our Partners & Supporters</h2>
        <div class="row g-4 align-items-center justify-content-center">
            <?php foreach ($partners as $partner): ?>
            <div class="col-6 col-md-3 col-lg-2">
                <img src="assets/images/<?= $partner['image'] ?>" alt="<?= $partner['name'] ?>" class="img-fluid">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="mb-4">Join Our Mission</h2>
        <p class="lead mb-4">Become part of the movement to empower the next generation of leaders.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="donate.php" class="btn btn-primary btn-lg px-4">Donate Now</a>
            <a href="volunteer.php" class="btn btn-outline-primary btn-lg px-4">Volunteer</a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>