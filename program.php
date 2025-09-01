<?php
require_once 'config/db.php';
require_once 'includes/functions/programs.php';

$page_title = $program['title'] ?? 'Program Details';
require_once 'includes/header.php';

// Get program by slug
$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: programs.php');
    exit;
}

// Fetch program details
$program = getProgramBySlug($slug);
if (!$program) {
    header('Location: programs.php');
    exit;
}

// Check if program is active (for registration)
$isActive = isProgramActive($program['id']);

// Get related programs
$relatedPrograms = getRelatedPrograms($program['id']);

// Get program testimonials - FIXED: Added program_id condition
$testimonials = fetchAll("
    SELECT t.* 
    FROM testimonials t
    WHERE t.is_approved = 1
    ORDER BY t.created_at DESC
    LIMIT 5
");

?>

<!-- Program Hero Section -->
<section class="program-hero py-5 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="programs.php">Programs</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($program['title'] ?? 'Program') ?></li>
            </ol>
        </nav>
        
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-5 fw-bold mb-3"><?= htmlspecialchars($program['title'] ?? 'Program') ?></h1>
                
                <?php if (($program['start_date'] ?? null) || ($program['end_date'] ?? null)): ?>
                <div class="program-dates mb-3">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <span>
                            <?php if (($program['start_date'] ?? null) && ($program['end_date'] ?? null)): ?>
                                <?= date('F j, Y', strtotime($program['start_date'])) ?> - <?= date('F j, Y', strtotime($program['end_date'])) ?>
                            <?php elseif ($program['start_date'] ?? null): ?>
                                Starting <?= date('F j, Y', strtotime($program['start_date'])) ?>
                            <?php else: ?>
                                Ongoing Program
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="program-status mb-4">
                    <?php if ($isActive): ?>
                        <span class="badge bg-success">Currently Accepting Registrations</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Program Completed</span>
                    <?php endif; ?>
                </div>
                
                <?php if ($isActive): ?>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="program-register.php?program_id=<?= $program['id'] ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Register Now
                    </a>
                    <button type="button" class="btn btn-outline-secondary btn-lg" data-bs-toggle="modal" data-bs-target="#shareModal">
                        <i class="fas fa-share-alt me-2"></i>Share
                    </button>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="col-lg-6 mt-4 mt-lg-0">
                <?php if (!empty($program['image'])): ?>
                    <img src="<?=BASE_URL . $program['image'] ?>" alt="<?= htmlspecialchars($program['title'] ?? 'Program Image') ?>" class="img-fluid rounded shadow">
                <?php else: ?>
                    <div class="bg-secondary rounded shadow d-flex align-items-center justify-content-center" style="height: 300px;">
                        <i class="fas fa-project-diagram fa-5x text-white"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Program Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="program-content mb-5">
                    <h2 class="mb-4">About This Program</h2>
                    <div class="content">
                        <?= $program['description'] ?? 'No description available.' ?>
                    </div>
                </div>
                
                <!-- Program Highlights -->
                <div class="program-highlights mb-5">
                    <h3 class="mb-4">Program Highlights</h3>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <div>
                                    <h5>Community Building</h5>
                                    <p class="text-muted">Connect with like-minded individuals and build lasting relationships.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-certificate fa-2x"></i>
                                </div>
                                <div>
                                    <h5>Certification</h5>
                                    <p class="text-muted">Receive a certificate upon successful completion of the program.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                </div>
                                <div>
                                    <h5>Expert Guidance</h5>
                                    <p class="text-muted">Learn from industry professionals with years of experience.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <i class="fas fa-briefcase fa-2x"></i>
                                </div>
                                <div>
                                    <h5>Career Opportunities</h5>
                                    <p class="text-muted">Gain access to exclusive job and internship opportunities.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Testimonials -->
                <?php if (!empty($testimonials)): ?>
                <div class="program-testimonials mb-5">
                    <h3 class="mb-4">What Participants Say</h3>
                    <div class="row">
                        <?php foreach ($testimonials as $testimonial): 
                            $initials = '';
                            $parts = explode(' ', $testimonial['author_name'] ?? '');
                            foreach ($parts as $part) {
                                $initials .= strtoupper(substr($part, 0, 1));
                            }
                        ?>
                        <div class="col-12 mb-3">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <span class="fw-bold"><?= $initials ?></span>
                                        </div>
                                        <div>
                                            <h5 class="mb-0"><?= htmlspecialchars($testimonial['author_name'] ?? 'Anonymous') ?></h5>
                                            <div class="text-primary small">
                                                <?php for ($i = 0; $i < 5; $i++): ?>
                                                    <i class="fas fa-star<?= $i < ($testimonial['rating'] ?? 0) ? '' : '-empty' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="card-text">"<?= htmlspecialchars($testimonial['content'] ?? 'No content') ?>"</p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Registration CTA -->
                <?php if ($isActive): ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h4 class="card-title">Join This Program</h4>
                        <p class="card-text">Register now to secure your spot in this transformative program.</p>
                        <a href="program-register.php?program_id=<?= $program['id'] ?>" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-user-plus me-2"></i>Register Now
                        </a>
                        <small class="text-muted">Spaces are limited - don't miss out!</small>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Program Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Program Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php if ($program['start_date'] ?? null): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-play-circle me-2 text-primary"></i>Start Date</span>
                                <span><?= date('F j, Y', strtotime($program['start_date'])) ?></span>
                            </li>
                            <?php endif; ?>
                            
                            <?php if ($program['end_date'] ?? null): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-flag-checkered me-2 text-primary"></i>End Date</span>
                                <span><?= date('F j, Y', strtotime($program['end_date'])) ?></span>
                            </li>
                            <?php endif; ?>
                            
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-users me-2 text-primary"></i>Participants</span>
                                <span>
                                    <?php
                                    $participantCount = 0;
                                    $countResult = fetchSingle("
                                        SELECT COUNT(*) as count 
                                        FROM program_registrations 
                                        WHERE program_id = ?
                                    ", [$program['id']]);
                                    if ($countResult) {
                                        $participantCount = $countResult['count'];
                                    }
                                    echo $participantCount;
                                    ?>
                                </span>
                            </li>
                            
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-tag me-2 text-primary"></i>Status</span>
                                <span>
                                    <?php if ($isActive): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Completed</span>
                                    <?php endif; ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Share Program -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Share This Program</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-around">
                            <?php
                            $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                            $encodedUrl = urlencode($currentUrl);
                            $encodedTitle = urlencode($program['title'] ?? 'Check out this program');
                            ?>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedUrl ?>" 
                               target="_blank" class="text-decoration-none text-dark">
                                <i class="fab fa-facebook fa-2x"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text=Check out this program: <?= $encodedTitle ?>&url=<?= $encodedUrl ?>" 
                               target="_blank" class="text-decoration-none text-dark">
                                <i class="fab fa-twitter fa-2x"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encodedUrl ?>" 
                               target="_blank" class="text-decoration-none text-dark">
                                <i class="fab fa-linkedin fa-2x"></i>
                            </a>
                            <a href="mailto:?subject=Check out this program: <?= htmlspecialchars($program['title'] ?? 'Program') ?>&body=I thought you might be interested in this program: <?= $encodedUrl ?>" 
                               class="text-decoration-none text-dark">
                                <i class="fas fa-envelope fa-2x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Programs -->
<?php if (!empty($relatedPrograms)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">Related Programs</h2>
        <div class="row g-4">
            <?php foreach ($relatedPrograms as $related): 
                $startDate = $related['start_date'] ? date('M j, Y', strtotime($related['start_date'])) : 'Ongoing';
                $endDate = $related['end_date'] ? date('M j, Y', strtotime($related['end_date'])) : 'Present';
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <?php if (!empty($related['image'])): ?>
                        <img src="<?=BASE_URL . $related['image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($related['title'] ?? 'Program Image') ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-project-diagram fa-4x text-white"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <h3 class="h5 card-title"><?= htmlspecialchars($related['title'] ?? 'Program') ?></h3>
                        <div class="d-flex align-items-center text-muted mb-2">
                            <i class="fas fa-calendar-alt me-2"></i>
                            <small><?= "$startDate - $endDate" ?></small>
                        </div>
                        <p class="card-text">
                            <?= strlen($related['description'] ?? '') > 100 ? 
                                substr(strip_tags($related['description'] ?? ''), 0, 100) . '...' : 
                                strip_tags($related['description'] ?? 'No description available') ?>
                        </p>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="program.php?slug=<?= $related['slug'] ?? '' ?>" class="btn btn-sm btn-outline-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Share Modal -->
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareModalLabel">Share This Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-around mb-4">
                    <?php
                    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    $encodedUrl = urlencode($currentUrl);
                    $encodedTitle = urlencode($program['title'] ?? 'Check out this program');
                    ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedUrl ?>" 
                       target="_blank" class="text-decoration-none text-dark d-flex flex-column align-items-center">
                        <i class="fab fa-facebook fa-3x mb-2"></i>
                        <span>Facebook</span>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=Check out this program: <?= $encodedTitle ?>&url=<?= $encodedUrl ?>" 
                       target="_blank" class="text-decoration-none text-dark d-flex flex-column align-items-center">
                        <i class="fab fa-twitter fa-3x mb-2"></i>
                        <span>Twitter</span>
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encodedUrl ?>" 
                       target="_blank" class="text-decoration-none text-dark d-flex flex-column align-items-center">
                        <i class="fab fa-linkedin fa-3x mb-2"></i>
                        <span>LinkedIn</span>
                    </a>
                </div>
                <div class="input-group">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($currentUrl) ?>" readonly>
                    <button class="btn btn-outline-secondary" type="button" id="copyLinkBtn">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Copy link functionality
document.getElementById('copyLinkBtn').addEventListener('click', function() {
    const linkInput = document.querySelector('#shareModal input');
    linkInput.select();
    document.execCommand('copy');
    
    // Show feedback
    const originalText = this.innerHTML;
    this.innerHTML = '<i class="fas fa-check"></i> Copied!';
    this.classList.add('btn-success');
    
    setTimeout(() => {
        this.innerHTML = originalText;
        this.classList.remove('btn-success');
    }, 2000);
});
</script>

<?php require_once 'includes/footer.php'; ?>