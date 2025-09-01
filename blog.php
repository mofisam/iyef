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

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">IYEF Blog</h1>
                <p class="lead mb-4">Insights, stories, and updates on youth empowerment and development.</p>
                
                <!-- Search Form -->
                <form method="GET" action="blog.php" class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search articles..." 
                               value="<?= isset($search_query) ? htmlspecialchars($search_query) : '' ?>">
                        <button class="btn btn-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <?php if (isset($category)): ?>
                    <div class="d-flex align-items-center">
                        <a href="blog.php" class="btn btn-sm btn-outline-light me-2">
                            <i class="fas fa-arrow-left me-1"></i> All Articles
                        </a>
                        <span class="badge bg-light text-primary">Category: <?= htmlspecialchars($category['name']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-6">
                <img src="assets/images/blog.png" alt="IYEF Blog" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Blog Posts Column -->
            <div class="col-lg-8">
                <?php if (!empty($blog_data['posts'])): ?>
                    <div class="row g-4">
                        <?php foreach ($blog_data['posts'] as $post): 
                            $post_date = isset($post['published_at']) ? new DateTime($post['published_at']) : null;
                            $excerpt = isset($post['excerpt']) ? $post['excerpt'] : '';
                            $content = isset($post['content']) ? $post['content'] : '';
                        ?>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm">
                                <?php if (!empty($post['featured_image'])): ?>
                                    <img src="<?= BASE_URL . htmlspecialchars($post['featured_image']) ?>" class="card-img-top" alt="<?= isset($post['title']) ? htmlspecialchars($post['title']) : '' ?>" style="height: 200px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-secondary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-newspaper fa-4x text-white"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body">
                                    <?php if (!empty($post['category_name'])): ?>
                                        <span class="badge bg-primary mb-2"><?= htmlspecialchars($post['category_name']) ?></span>
                                    <?php endif; ?>
                                    <h3 class="h5 card-title">
                                        <a href="blog-post.php?slug=<?= isset($post['slug']) ? htmlspecialchars($post['slug']) : '' ?>" class="text-decoration-none">
                                            <?= isset($post['title']) ? htmlspecialchars($post['title']) : 'Untitled Post' ?>
                                        </a>
                                    </h3>
                                    <p class="card-text">
                                        <?= !empty($excerpt) ? htmlspecialchars($excerpt) : 
                                            (isset($content) ? substr(strip_tags($content), 0, 150) . '...' : 'No content available') ?>
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i> <?= isset($post['author_name']) ? htmlspecialchars($post['author_name']) : 'Unknown Author' ?>
                                        </small>
                                        <small class="text-muted">
                                            <?php if ($post_date): ?>
                                                <i class="fas fa-calendar-alt me-1"></i> <?= $post_date->format('M j, Y') ?>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav class="mt-5">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" 
                                           href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
                                           aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" 
                                           href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
                                           aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-5 text-center">
                            <i class="fas fa-newspaper fa-4x text-muted mb-4"></i>
                            <h3 class="h4">No Articles Found</h3>
                            <p class="text-muted">
                                <?php if ($search_query): ?>
                                    No articles match your search criteria. Try different keywords.
                                <?php else: ?>
                                    There are no published articles yet. Check back soon!
                                <?php endif; ?>
                            </p>
                            <a href="blog.php" class="btn btn-outline-primary">View All Articles</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar Column -->
            <div class="col-lg-4 mt-5 mt-lg-0">
                <div class="card border-0 shadow-sm " style="top: 20px;">
                    <div class="card-body">
                        <!-- Categories -->
                        <div class="mb-5">
                            <h4 class="fw-bold mb-3">Categories</h4>
                            <ul class="list-unstyled">
                                <?php
                                $categories = getBlogCategories();
                                foreach ($categories as $cat):
                                    $count = fetchSingle(
                                        "SELECT COUNT(*) as count FROM blog_posts WHERE category_id = ? AND is_published = 1",
                                        [$cat['id']]
                                    )['count'] ?? 0;
                                ?>
                                <li class="mb-2">
                                    <a href="blog.php?category=<?= $cat['id'] ?>" 
                                       class="d-flex justify-content-between align-items-center text-decoration-none">
                                        <span><?= htmlspecialchars($cat['name']) ?></span>
                                        <span class="badge bg-primary rounded-pill"><?= $count ?></span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <!-- Popular Posts -->
                        <div class="mb-5">
                            <h4 class="fw-bold mb-3">Popular Articles</h4>
                            <?php
                            $popular_posts = fetchAll("
                                SELECT bp.id, bp.title, bp.slug, bp.published_at
                                FROM blog_posts bp
                                LEFT JOIN (
                                    SELECT post_id, COUNT(*) as view_count
                                    FROM post_views
                                    WHERE view_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                    GROUP BY post_id
                                ) pv ON bp.id = pv.post_id
                                WHERE bp.is_published = 1
                                ORDER BY pv.view_count DESC, bp.published_at DESC
                                LIMIT 3
                            ") ?: [];
                            
                            foreach ($popular_posts as $post):
                                $post_date = isset($post['published_at']) ? new DateTime($post['published_at']) : null;
                            ?>
                            <div class="mb-3">
                                <a href="blog-post.php?slug=<?= htmlspecialchars($post['slug']) ?>" class="text-decoration-none">
                                    <h5 class="h6 mb-1"><?= htmlspecialchars($post['title']) ?></h5>
                                    <?php if ($post_date): ?>
                                        <small class="text-muted"><?= $post_date->format('M j, Y') ?></small>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if (empty($popular_posts)): ?>
                                <p class="text-muted small">No popular articles yet.</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Newsletter Signup -->
                        <div class="bg-light p-4 rounded">
                            <h4 class="fw-bold mb-3">Stay Updated</h4>
                            <p class="small text-muted">Subscribe to our newsletter for the latest articles and updates.</p>
                            <form action="/subscribe.php" method="POST">
                                <div class="mb-3">
                                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Your email" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="bg-light py-5">
    <div class="container text-center py-4">
        <h2 class="fw-bold mb-4">Want to contribute to our blog?</h2>
        <p class="lead mb-4">We welcome guest articles from youth development experts and community leaders.</p>
        <a href="contact.php" class="btn btn-primary btn-lg px-4">Contact Us</a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>