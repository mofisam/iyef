<?php
require_once 'config/db.php';
require_once 'includes/functions/programs.php';

$page_title = "Our Programs";
require_once 'includes/header.php';

// Get all active programs
$activePrograms = getActivePrograms();
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Our Empowerment Programs</h1>
                <p class="lead mb-4">Discover how IYEF is transforming lives through our comprehensive youth development initiatives.</p>
                <a href="#program-list" class="btn btn-light btn-lg">Explore Programs</a>
            </div>
            <div class="col-lg-6">
                <img src="/assets/images/programs-hero.jpg" alt="IYEF Programs" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Programs List -->
<section id="program-list" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Current Programs</h2>
            <p class="lead text-muted">Browse our active programs and find one that fits your needs</p>
        </div>
        
        <div class="row g-4">
            <?php if (!empty($activePrograms)): ?>
                <?php foreach ($activePrograms as $program): 
                    $startDate = $program['start_date'] ? date('M j, Y', strtotime($program['start_date'])) : 'Ongoing';
                    $endDate = $program['end_date'] ? date('M j, Y', strtotime($program['end_date'])) : 'Present';
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm overflow-hidden">
                        <?php if (!empty($program['image'])): ?>
                            <img src="<?= $program['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($program['title']) ?>" style="height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-secondary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-project-diagram fa-4x text-white"></i>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="h5 card-title"><?= htmlspecialchars($program['title']) ?></h3>
                            <div class="d-flex align-items-center text-muted mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <small><?= "$startDate - $endDate" ?></small>
                            </div>
                            <p class="card-text">
                                <?= strlen($program['description']) > 150 ? 
                                    substr(strip_tags($program['description']), 0, 150) . '...' : 
                                    strip_tags($program['description']) ?>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent border-top-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="program.php?slug=<?= $program['slug'] ?>" class="btn btn-sm btn-primary">Learn More</a>
                                <a href="program-register.php?program_id=<?= $program['id'] ?>" class="btn btn-sm btn-outline-primary">Register</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-project-diagram fa-4x text-muted mb-4"></i>
                            <h3 class="h4">No Active Programs at the Moment</h3>
                            <p class="text-muted">Check back later for upcoming programs or view our past initiatives below.</p>
                            <a href="#past-programs" class="btn btn-outline-primary">View Past Programs</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Past Programs -->
<section id="past-programs" class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Past Programs</h2>
            <p class="lead text-muted">See the impact of our previous initiatives</p>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>Program</th>
                                        <th>Duration</th>
                                        <th>Participants</th>
                                        <th>Details</th>
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
                                    
                                    foreach ($pastPrograms as $program):
                                        $startDate = $program['start_date'] ? date('M j, Y', strtotime($program['start_date'])) : 'N/A';
                                        $endDate = $program['end_date'] ? date('M j, Y', strtotime($program['end_date'])) : 'N/A';
                                    ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($program['title']) ?></strong>
                                            <div class="text-muted small"><?= substr(strip_tags($program['description']), 0, 100) ?>...</div>
                                        </td>
                                        <td><?= "$startDate - $endDate" ?></td>
                                        <td><?= $program['participants'] ?></td>
                                        <td>
                                            <a href="program.php?slug=<?= $program['slug'] ?>" class="btn btn-sm btn-outline-primary">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($pastPrograms)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No past programs to display</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Program Benefits -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="/assets/images/program-benefits.jpg" alt="Program Benefits" class="img-fluid rounded shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Why Join Our Programs?</h2>
                <div class="d-flex mb-3">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5">Skill Development</h4>
                        <p>Gain practical skills that prepare you for the workforce and entrepreneurship.</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5">Mentorship</h4>
                        <p>Connect with experienced mentors who guide your personal and professional growth.</p>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5">Community</h4>
                        <p>Join a supportive network of like-minded youth working toward similar goals.</p>
                    </div>
                </div>
                <div class="d-flex">
                    <div class="me-3 text-primary">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <h4 class="h5">Certification</h4>
                        <p>Receive certificates that validate your participation and achievements.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">What Participants Say</h2>
            <p class="lead text-muted">Hear from youth who have benefited from our programs</p>
        </div>
        
        <div class="row g-4">
            <?php
            $testimonials = fetchAll("
            SELECT t.* 
            FROM testimonials t
            WHERE t.is_approved = 1
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
            
            foreach ($testimonials as $testimonial):
                $initials = '';
                $parts = explode(' ', $testimonial['author_name']);
                foreach ($parts as $part) {
                    $initials .= strtoupper(substr($part, 0, 1));
                }
            ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <span class="fw-bold"><?= $initials ?></span>
                            </div>
                            <div>
                                <h5 class="mb-0"><?= htmlspecialchars($testimonial['author_name']) ?></h5>
                                <?php if (!empty($testimonial['program_title'])): ?>
                                    <small class="text-muted"><?= htmlspecialchars($testimonial['program_title']) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                        <p class="card-text">"<?= htmlspecialchars($testimonial['content']) ?>"</p>
                        <div class="text-primary">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <i class="fas fa-star<?= $i < $testimonial['rating'] ? '' : '-empty' ?>"></i>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($testimonials)): ?>
                <div class="col-12 text-center py-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5">
                            <i class="fas fa-comments fa-4x text-muted mb-4"></i>
                            <h3 class="h4">No Testimonials Yet</h3>
                            <p class="text-muted">Check back later to hear from our program participants.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>