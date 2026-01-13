<?php
require_once 'config/db.php';
require_once 'includes/functions/blog.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search_query = isset($_GET['search']) ? trim($_GET['search']) : null;

if ($category_id) {
    $blog_data = getPostsByCategory($category_id, $page, 6);
    $category = fetchSingle("SELECT name FROM categories WHERE id = ?", [$category_id]);
    $page_title = $category ? htmlspecialchars($category['name']) . " Articles" : "Blog";
} elseif ($search_query) {
    $search_param = "%$search_query%";
    $blog_data = [
        'posts' => fetchAll("
            SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, bp.published_at, 
                   u.full_name as author_name, c.name as category_name
            FROM blog_posts bp
            JOIN users u ON bp.author_id = u.id
            LEFT JOIN categories c ON bp.category_id = c.id
            WHERE bp.is_published = 1 
            AND (bp.title LIKE ? OR bp.content LIKE ? OR bp.excerpt LIKE ?)
            ORDER BY bp.published_at DESC
            LIMIT 6 OFFSET " . (($page - 1) * 6),
            [$search_param, $search_param, $search_param]
        ),
        'pagination' => [
            'current_page' => $page,
            'per_page' => 6,
            'total_items' => fetchSingle("
                SELECT COUNT(*) as total 
                FROM blog_posts 
                WHERE is_published = 1 
                AND (title LIKE ? OR content LIKE ? OR excerpt LIKE ?)",
                [$search_param, $search_param, $search_param]
            )['total'] ?? 0
        ]
    ];
    $page_title = "Search Results for: " . htmlspecialchars($search_query);
} else {
    $blog_data = getBlogPosts($page, 6);
    $page_title = "Our Blog";
}

// Calculate total pages for pagination
$total_pages = isset($blog_data['pagination']['total_items']) ? 
    ceil($blog_data['pagination']['total_items'] / $blog_data['pagination']['per_page']) : 1;

require_once 'includes/header.php';
?>

<!-- Hero Section - Modern Design -->
<section class="hero-section position-relative overflow-hidden min-vh-60 d-flex align-items-center">
    <!-- Background with gradient overlay -->
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(rgba(0, 86, 179, 0.9), rgba(0, 123, 255, 0.85)), url('assets/images/blog.png'); background-size: cover; background-position: center;"></div>
    
    <!-- Animated background elements -->
    <div class="position-absolute w-100 h-100" style="overflow: hidden; z-index: 1;">
        <div class="position-absolute rounded-circle" style="width: 300px; height: 300px; background: rgba(255, 255, 255, 0.1); top: -150px; right: -100px;"></div>
        <div class="position-absolute rounded-circle" style="width: 200px; height: 200px; background: rgba(255, 255, 255, 0.05); bottom: -100px; left: -50px;"></div>
    </div>
    
    <div class="container position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-10 col-xl-8">
                <!-- Badge -->
                <!-- Main heading -->
                <h1 class="display-2 fw-bold mb-4 text-white animate-slide-up" style="line-height: 1.2;">
                    IYEF <span class="text-warning">Blog</span>
                </h1>
                
                <!-- Description -->
                <p class="lead mb-5 text-white opacity-75 animate-fade-in" style="font-size: 1.35rem; animation-delay: 0.3s;">
                    Discover inspiring stories, expert insights, and transformative ideas on youth empowerment and development.
                </p>
                
                <!-- Search Form -->
                <div class="row justify-content-center animate-fade-in" style="animation-delay: 0.5s;">
                    <div class="col-lg-8 col-md-10">
                        <form method="GET" action="blog.php" class="search-form">
                            <div class="input-group input-group-lg shadow-lg rounded-pill overflow-hidden">
                                <span class="input-group-text bg-white border-0">
                                    <i class="fas fa-search text-primary"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-0" 
                                       placeholder="Search articles on youth empowerment..." 
                                       value="<?= isset($search_query) ? htmlspecialchars($search_query) : '' ?>">
                                <button class="btn btn-primary px-4" type="submit">
                                    Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Active Filters -->
                <div class="mt-4 animate-fade-in" style="animation-delay: 0.7s;">
                    <?php if (isset($category)): ?>
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                            <a href="blog.php" class="btn btn-sm btn-outline-light">
                                <i class="fas fa-times me-1"></i> Clear Filter
                            </a>
                            <span class="badge bg-light text-primary fs-6 px-3 py-2">
                                <i class="fas fa-folder me-1"></i> Category: <?= htmlspecialchars($category['name']) ?>
                            </span>
                        </div>
                    <?php elseif ($search_query): ?>
                        <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
                            <a href="blog.php" class="btn btn-sm btn-outline-light">
                                <i class="fas fa-times me-1"></i> Clear Search
                            </a>
                            <span class="badge bg-light text-primary fs-6 px-3 py-2">
                                <i class="fas fa-search me-1"></i> Search: "<?= htmlspecialchars($search_query) ?>"
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="position-absolute bottom-0 start-50 translate-middle-x text-center pb-4" style="z-index: 2;">
        <a href="#blog-posts" class="text-white text-decoration-none d-flex flex-column align-items-center scroll-to-section">
            <span class="small mb-2 opacity-75">Explore Articles</span>
            <div class="scroll-indicator">
                <i class="fas fa-chevron-down fs-5"></i>
            </div>
        </a>
    </div>
</section>

<!-- Main Content -->
<section id="blog-posts" class="py-5">
    <div class="container py-5">
        <div class="row">
            <!-- Blog Posts Column -->
            <div class="col-lg-8">
                <?php if (!empty($blog_data['posts'])): ?>
                    <!-- Results Summary -->
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <div>
                            <h2 class="h3 fw-bold mb-0">
                                <?php if ($category_id): ?>
                                    <?= htmlspecialchars($category['name']) ?> Articles
                                <?php elseif ($search_query): ?>
                                    Search Results
                                <?php else: ?>
                                    Latest Articles
                                <?php endif; ?>
                            </h2>
                            <p class="text-muted mb-0">
                                <?= $blog_data['pagination']['total_items'] ?? 0 ?> article(s) found
                            </p>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted small">Page <?= $page ?> of <?= $total_pages ?></span>
                        </div>
                    </div>
                    
                    <!-- Blog Posts Grid -->
                    <div class="row g-4">
                        <?php foreach ($blog_data['posts'] as $post): 
                            $post_date = isset($post['published_at']) ? new DateTime($post['published_at']) : null;
                            $excerpt = isset($post['excerpt']) ? $post['excerpt'] : '';
                            $content = isset($post['content']) ? $post['content'] : '';
                        ?>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-lg hover-lift overflow-hidden">
                                <!-- Featured Image -->
                                <div class="position-relative overflow-hidden" style="height: 220px;">
                                    <?php if (!empty($post['featured_image'])): ?>
                                        <img src="<?= BASE_URL . htmlspecialchars($post['featured_image']) ?>" 
                                             class="card-img-top h-100 w-100" 
                                             alt="<?= isset($post['title']) ? htmlspecialchars($post['title']) : '' ?>" 
                                             style="object-fit: cover; transition: transform 0.5s ease;">
                                        <div class="image-overlay position-absolute top-0 start-0 w-100 h-100"></div>
                                    <?php else: ?>
                                        <div class="bg-gradient-primary h-100 w-100 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-newspaper fa-4x text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Category Badge -->
                                    <?php if (!empty($post['category_name'])): ?>
                                        <div class="position-absolute top-0 start-0 m-3">
                                            <span class="badge bg-primary bg-opacity-90 text-white px-3 py-2 rounded-pill">
                                                <?= htmlspecialchars($post['category_name']) ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Date Badge -->
                                    <?php if ($post_date): ?>
                                        <div class="position-absolute bottom-0 end-0 m-3">
                                            <div class="bg-white text-dark rounded-3 p-2 text-center shadow-sm">
                                                <div class="fw-bold fs-5"><?= $post_date->format('d') ?></div>
                                                <div class="small text-uppercase"><?= $post_date->format('M') ?></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Card Body -->
                                <div class="card-body p-4">
                                    <h3 class="h4 card-title fw-bold mb-3">
                                        <a href="blog-post.php?slug=<?= isset($post['slug']) ? htmlspecialchars($post['slug']) : '' ?>" 
                                           class="text-decoration-none text-dark hover-primary">
                                            <?= isset($post['title']) ? htmlspecialchars($post['title']) : 'Untitled Post' ?>
                                        </a>
                                    </h3>
                                    
                                    <p class="card-text text-muted mb-4">
                                        <?= !empty($excerpt) ? htmlspecialchars($excerpt) : 
                                            (isset($content) ? substr(strip_tags($content), 0, 150) . '...' : 'No content available') ?>
                                    </p>
                                    
                                    <!-- Author & Meta -->
                                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-2">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <small class="fw-bold d-block"><?= isset($post['author_name']) ? htmlspecialchars($post['author_name']) : 'IYEF Team' ?></small>
                                                <small class="text-muted">Author</small>
                                            </div>
                                        </div>
                                        
                                        <a href="blog-post.php?slug=<?= isset($post['slug']) ? htmlspecialchars($post['slug']) : '' ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            Read More <i class="fas fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination - Modern Design -->
                    <?php if ($total_pages > 1): ?>
                        <div class="mt-5 pt-4">
                            <nav aria-label="Blog pagination">
                                <ul class="pagination justify-content-center pagination-lg">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                                               aria-label="Previous">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    // Show limited pagination for better UX
                                    $start_page = max(1, $page - 2);
                                    $end_page = min($total_pages, $page + 2);
                                    
                                    if ($start_page > 1): ?>
                                        <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a></li>
                                        <?php if ($start_page > 2): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($end_page < $total_pages): ?>
                                        <?php if ($end_page < $total_pages - 1): ?>
                                            <li class="page-item disabled"><span class="page-link">...</span></li>
                                        <?php endif; ?>
                                        <li class="page-item"><a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $total_pages])) ?>"><?= $total_pages ?></a></li>
                                    <?php endif; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" 
                                               href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                                               aria-label="Next">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- No Results State -->
                    <div class="card border-dashed border-3 border-muted rounded-4 text-center py-5">
                        <div class="card-body">
                            <div class="empty-state-icon mb-4">
                                <i class="fas fa-newspaper fa-4x text-muted opacity-50"></i>
                            </div>
                            <h3 class="h2 fw-bold mb-3">No Articles Found</h3>
                            <p class="lead text-muted mb-4">
                                <?php if ($search_query): ?>
                                    We couldn't find any articles matching "<?= htmlspecialchars($search_query) ?>".<br>
                                    Try different keywords or browse our categories.
                                <?php elseif ($category_id): ?>
                                    There are no articles in this category yet.<br>
                                    Check back soon or explore other categories.
                                <?php else: ?>
                                    Our blog is currently being updated with fresh content.<br>
                                    New articles will be published soon!
                                <?php endif; ?>
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-3">
                                <a href="blog.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-home me-2"></i> Back to Blog
                                </a>
                                <a href="#sidebar-categories" class="btn btn-outline-primary btn-lg scroll-to-section">
                                    <i class="fas fa-folder-open me-2"></i> Browse Categories
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar Column -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="sticky-sidebar">
                    <!-- Categories Widget -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-primary bg-opacity-10 border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-primary text-white rounded-3 p-2 me-3">
                                    <i class="fas fa-folder fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="h5 fw-bold mb-0">Categories</h3>
                                    <small class="text-muted">Browse by topic</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4" id="sidebar-categories">
                            <div class="category-list">
                                <?php
                                $categories = getBlogCategories();
                                foreach ($categories as $cat):
                                    $count = fetchSingle(
                                        "SELECT COUNT(*) as count FROM blog_posts WHERE category_id = ? AND is_published = 1",
                                        [$cat['id']]
                                    )['count'] ?? 0;
                                    
                                    $is_active = ($category_id == $cat['id']);
                                ?>
                                <a href="blog.php?category=<?= $cat['id'] ?>" 
                                   class="category-item d-flex justify-content-between align-items-center p-3 rounded-3 mb-2 text-decoration-none <?= $is_active ? 'active-category' : '' ?>">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-wrapper bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                            <i class="fas fa-folder"></i>
                                        </div>
                                        <span class="fw-medium"><?= htmlspecialchars($cat['name']) ?></span>
                                    </div>
                                    <span class="badge bg-primary rounded-pill px-3 py-2"><?= $count ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 text-center py-3">
                            <a href="blog.php" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-list me-1"></i> All Categories
                            </a>
                        </div>
                    </div>
                    
                    <!-- Popular Posts Widget -->
                    <div class="card border-0 shadow-lg mb-4">
                        <div class="card-header bg-warning bg-opacity-10 border-0 py-4">
                            <div class="d-flex align-items-center">
                                <div class="icon-wrapper bg-warning text-dark rounded-3 p-2 me-3">
                                    <i class="fas fa-fire fa-lg"></i>
                                </div>
                                <div>
                                    <h3 class="h5 fw-bold mb-0">Popular Now</h3>
                                    <small class="text-muted">Trending articles</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <?php
                            $popular_posts = fetchAll("
                                SELECT bp.id, bp.title, bp.slug, bp.published_at, bp.featured_image
                                FROM blog_posts bp
                                LEFT JOIN (
                                    SELECT post_id, COUNT(*) as view_count
                                    FROM post_views
                                    WHERE view_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                    GROUP BY post_id
                                ) pv ON bp.id = pv.post_id
                                WHERE bp.is_published = 1
                                ORDER BY pv.view_count DESC, bp.published_at DESC
                                LIMIT 5
                            ") ?: [];
                            
                            if (!empty($popular_posts)): ?>
                                <div class="popular-posts-list">
                                    <?php foreach ($popular_posts as $index => $post):
                                        $post_date = isset($post['published_at']) ? new DateTime($post['published_at']) : null;
                                    ?>
                                    <div class="popular-post-item d-flex align-items-start mb-3 pb-3 border-bottom">
                                        <div class="position-relative" style="width: 60px; height: 60px; flex-shrink: 0;">
                                            <?php if (!empty($post['featured_image'])): ?>
                                                <img src="<?= BASE_URL . htmlspecialchars($post['featured_image']) ?>" 
                                                     class="rounded-2 w-100 h-100" 
                                                     alt="<?= htmlspecialchars($post['title']) ?>" 
                                                     style="object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-2 w-100 h-100 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-newspaper"></i>
                                                </div>
                                            <?php endif; ?>
                                            <span class="position-absolute top-0 start-0 translate-middle badge bg-danger rounded-circle" style="width: 20px; height: 20px;">
                                                <?= $index + 1 ?>
                                            </span>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <a href="blog-post.php?slug=<?= htmlspecialchars($post['slug']) ?>" 
                                               class="text-decoration-none">
                                                <h6 class="fw-bold mb-1 line-clamp-2"><?= htmlspecialchars($post['title']) ?></h6>
                                            </a>
                                            <?php if ($post_date): ?>
                                                <small class="text-muted">
                                                    <i class="far fa-calendar me-1"></i><?= $post_date->format('M j, Y') ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-chart-line fa-3x text-muted opacity-50 mb-3"></i>
                                    <p class="text-muted">No popular articles yet. Be the first to read!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Newsletter Widget -->
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4 bg-gradient-primary text-white rounded-3">
                            <div class="text-center mb-4">
                                <div class="icon-wrapper bg-white bg-opacity-5 text-primary rounded-3 p-1 mb-3 mx-auto" style="width: 70px; height: 70px;">
                                    <i class="fas fa-envelope-open-text fa-2x"></i>
                                </div>
                                <h3 class="h4 fw-bold mb-2">Stay Informed</h3>
                                <p class="mb-0 opacity-75">Get youth empowerment insights delivered to your inbox</p>
                            </div>
                            
                            <form action="/subscribe.php" method="POST" class="newsletter-form">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="email" name="email" 
                                               class="form-control rounded-pill border-0" 
                                               placeholder="Enter your email" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-light btn-lg w-100 rounded-pill">
                                    <i class="fas fa-paper-plane me-2"></i> Subscribe
                                </button>
                                <p class="small text-center mt-3 opacity-75 mb-0">
                                    We respect your privacy. Unsubscribe at any time.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action - Modern Design -->
<section class="py-5 position-relative overflow-hidden">
    <div class="position-absolute top-0 start-0 w-100 h-100 bg-gradient-primary opacity-95"></div>
    <div
  class="position-absolute top-0 start-0 w-100 h-100"
  style="
    pointer-events: none;
    background-image: url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2240%22 fill=%22none%22 stroke=%22white%22 stroke-width=%220.5%22 stroke-opacity=%220.1%22/></svg>');
    background-size: 100px 100px;
  "
></div>

    <div class="container py-5 position-relative" style="z-index: 2;">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold text-white mb-4">Share Your Youth Empowerment Story</h2>
                <p class="lead text-white opacity-75 mb-5">
                    We welcome contributions from youth development experts, community leaders, and empowered youth. 
                    Share your insights and inspire the next generation.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="contact.php?subject=Guest%20Article%20Submission" class="btn btn-light btn-lg px-5 py-3">
                        <i class="fas fa-edit me-2"></i> Submit Article
                    </a>
                    <a href="contact.php" class="btn btn-outline-light btn-lg px-5 py-3">
                        <i class="fas fa-question-circle me-2"></i> Learn More
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- JavaScript for enhanced interactivity -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Category hover effects
    const categoryItems = document.querySelectorAll('.category-item');
    categoryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            if (!this.classList.contains('active-category')) {
                this.style.backgroundColor = 'rgba(0, 123, 255, 0.05)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active-category')) {
                this.style.backgroundColor = '';
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
    
    // Sticky sidebar
    const sidebar = document.querySelector('.sticky-sidebar');
    if (sidebar) {
        const offsetTop = sidebar.offsetTop;
        
        window.addEventListener('scroll', function() {
            if (window.scrollY > offsetTop) {
                sidebar.style.position = 'sticky';
                sidebar.style.top = '20px';
            } else {
                sidebar.style.position = 'relative';
                sidebar.style.top = '0';
            }
        });
    }
});
</script>

<!-- Add custom CSS for animations -->
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #007bff, #0056b3);
    --success-gradient: linear-gradient(135deg, #28a745, #1e7e34);
    --warning-gradient: linear-gradient(135deg, #ffc107, #e0a800);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
}

.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1) !important;
}

.hover-primary:hover {
    color: #007bff !important;
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


.border-dashed {
    border-style: dashed !important;
}

.image-overlay {
    background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.hover-lift:hover .image-overlay {
    opacity: 1;
}

.active-category {
    background-color: rgba(0, 123, 255, 0.1) !important;
    border-left: 3px solid #007bff !important;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.sticky-sidebar {
    transition: all 0.3s ease;
}

.empty-state-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.category-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.category-item:hover {
    border-color: rgba(0, 123, 255, 0.2);
    transform: translateX(5px);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .display-2 {
        font-size: 2.5rem;
    }
    
    .display-5 {
        font-size: 1.75rem;
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
    
    .sticky-sidebar {
        position: relative !important;
        top: 0 !important;
    }
}

@media (max-width: 576px) {
    .display-2 {
        font-size: 2rem;
    }
    
    .min-vh-60 {
        min-height: 50vh;
    }
    
    .pagination-lg .page-link {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>