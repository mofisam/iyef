<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/testimonials.php';

$page = max(1, $_GET['page'] ?? 1);
$perPage = 9;

$testimonialsData = getAllTestimonials($page, $perPage, true);
$testimonials = $testimonialsData['testimonials'];
$pagination = $testimonialsData['pagination'];

$page_title = "Testimonials";
require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold text-primary mb-3">What People Say About IYEF</h1>
            <p class="lead">Real stories from our community members and program participants</p>
            <a href="/testimonial.php" class="btn btn-primary btn-lg mt-3">
                <i class="fas fa-plus me-2"></i>Share Your Story
            </a>
        </div>

        <?php if (empty($testimonials)): ?>
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h3 class="text-muted">No testimonials yet</h3>
                <p class="text-muted">Be the first to share your experience with IYEF!</p>
                <a href="/testimonial.php" class="btn btn-primary">Share Your Story</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 testimonial-card">
                        <div class="card-body text-center p-4">
                            <div class="testimonial-rating text-warning mb-3">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star<?= $i <= $testimonial['rating'] ? '' : '-half-alt' ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <div class="testimonial-content mb-4">
                                <p class="fst-italic">"<?= htmlspecialchars($testimonial['content']) ?>"</p>
                            </div>
                            <div class="testimonial-author">
                                <?php if (!empty($testimonial['author_image'])): ?>
                                    <img src="<?= $testimonial['author_image'] ?>" alt="<?= htmlspecialchars($testimonial['author_name']) ?>" 
                                         class="rounded-circle mb-2" width="60" height="60">
                                <?php endif; ?>
                                <h6 class="mb-1"><?= htmlspecialchars($testimonial['author_name']) ?></h6>
                                <?php if ($testimonial['author_title']): ?>
                                    <p class="text-muted small mb-0"><?= htmlspecialchars($testimonial['author_title']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pagination['total_pages'] > 1): ?>
            <nav aria-label="Testimonials pagination" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php if ($pagination['has_prev']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['has_next']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>