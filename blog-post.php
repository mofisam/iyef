<?php
require_once 'config/db.php';
require_once 'includes/functions/blog.php';

// Get the post slug from URL
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: blog.php');
    exit;
}

// Get the post by slug
$post = getBlogPostBySlug($slug);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    $page_title = "Post Not Found";
    require_once 'includes/header.php';
    ?>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-exclamation-circle fa-4x text-muted mb-4"></i>
                            <h1>Post Not Found</h1>
                            <p class="lead">The article you're looking for doesn't exist or may have been moved.</p>
                            <a href="blog.php" class="btn btn-primary">Back to Blog</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    require_once 'includes/footer.php';
    exit;
}

// Increment view count
executeQuery("UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]);

// Get related posts (from same category)
$related_posts = fetchAll("
    SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, bp.published_at,
           u.full_name as author_name, c.name as category_name
    FROM blog_posts bp
    JOIN users u ON bp.author_id = u.id
    LEFT JOIN categories c ON bp.category_id = c.id
    WHERE bp.category_id = ? AND bp.id != ? AND bp.is_published = 1
    ORDER BY bp.published_at DESC
    LIMIT 3
", [$post['category_id'], $post['id']]);

// Get comments for this post
$comments = fetchAll("
    SELECT c.*, u.full_name, u.email 
    FROM comments c
    LEFT JOIN users u ON c.user_id = u.id
    WHERE c.post_id = ? AND c.is_approved = 1
    ORDER BY c.created_at DESC
", [$post['id']]);

$page_title = $post['title'];
require_once 'includes/header.php';

// Format publish date
$publish_date = new DateTime($post['published_at']);
?>

<!-- Blog Post Content -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <article class="blog-post">
                    <!-- Post Header -->
                    <header class="mb-5">
                        <div class="d-flex align-items-center mb-3">
                            <?php if (!empty($post['category_name'])): ?>
                                <a href="blog.php?category=<?= $post['category_id'] ?>" class="badge bg-primary text-decoration-none me-2">
                                    <?= htmlspecialchars($post['category_name']) ?>
                                </a>
                            <?php endif; ?>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i> <?= $publish_date->format('F j, Y') ?>
                            </small>
                        </div>
                        
                        <h1 class="mb-3"><?= htmlspecialchars($post['title']) ?></h1>
                        
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3">
                                <?php if (!empty($post['author_avatar'])): ?>
                                    <img src="<?= BASE_URL . $post['author_avatar'] ?>" alt="<?= htmlspecialchars($post['author_name']) ?>" class="rounded-circle" width="50" height="50">
                                <?php else: ?>
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <?= substr($post['author_name'], 0, 1) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h5 class="mb-0"><?= htmlspecialchars($post['author_name']) ?></h5>
                                <small class="text-muted">Author</small>
                            </div>
                        </div>
                    </header>
                    
                    <!-- Featured Image -->
                    <?php if (!empty($post['featured_image'])): ?>
                        <figure class="mb-5">
                            <img src="<?= BASE_URL . $post['featured_image'] ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="img-fluid rounded shadow-sm w-100">
                            <?php if (!empty($post['featured_image_caption'])): ?>
                                <figcaption class="text-center mt-2 text-muted small">
                                    <?= htmlspecialchars($post['featured_image_caption']) ?>
                                </figcaption>
                            <?php endif; ?>
                        </figure>
                    <?php endif; ?>
                    
                    <!-- Post Content -->
                    <div class="post-content mb-5">
                        <?= $post['content'] ?>
                    </div>
                    
                    <!-- Post Footer -->
                    <footer class="border-top border-bottom py-4 mb-5">
                        <div class="row">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <h6 class="mb-2">Tags:</h6>
                                <?php
                                $tags = fetchAll("
                                    SELECT t.name 
                                    FROM post_tags pt
                                    JOIN tags t ON pt.tag_id = t.id
                                    WHERE pt.post_id = ?
                                ", [$post['id']]);
                                
                                if (!empty($tags)): ?>
                                    <div class="tag-cloud">
                                        <?php foreach ($tags as $tag): ?>
                                            <a href="blog.php?tag=<?= urlencode($tag['name']) ?>" class="badge bg-light text-dark text-decoration-none me-1 mb-1">
                                                #<?= htmlspecialchars($tag['name']) ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted small mb-0">No tags</p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6 class="mb-2">Share this post:</h6>
                                <div class="social-share">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&text=<?= urlencode($post['title']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&title=<?= urlencode($post['title']) ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                    <a href="mailto:?subject=<?= urlencode($post['title']) ?>&body=Check out this article: <?= urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                                       class="btn btn-sm btn-outline-primary me-1 mb-1">
                                        <i class="fas fa-envelope"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </footer>
                    
                    <!-- Author Bio -->
                    <div class="card border-0 shadow-sm mb-5">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-3 mb-md-0">
                                    <?php if (!empty($post['author_avatar'])): ?>
                                        <img src="<?= BASE_URL . $post['author_avatar'] ?>" alt="<?= htmlspecialchars($post['author_name']) ?>" class="rounded-circle img-fluid" width="150" height="150">
                                    <?php else: ?>
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 3rem;">
                                            <?= substr($post['author_name'], 0, 1) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-9">
                                    <h4 class="mb-2">About <?= htmlspecialchars($post['author_name']) ?></h4>
                                    <?php if (!empty($post['author_bio'])): ?>
                                        <p><?= htmlspecialchars($post['author_bio']) ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">No bio available</p>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($post['author_website']) || !empty($post['author_social'])): ?>
                                        <div class="social-links">
                                            <?php if (!empty($post['author_website'])): ?>
                                                <a href="<?= htmlspecialchars($post['author_website']) ?>" target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                                    <i class="fas fa-globe"></i> Website
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($post['author_social'])): 
                                                $social_links = json_decode($post['author_social'], true);
                                                foreach ($social_links as $platform => $url):
                                                    if (!empty($url)):
                                                        $icon = '';
                                                        $class = '';
                                                        switch (strtolower($platform)) {
                                                            case 'facebook': $icon = 'facebook-f'; break;
                                                            case 'twitter': $icon = 'twitter'; break;
                                                            case 'linkedin': $icon = 'linkedin-in'; break;
                                                            case 'instagram': $icon = 'instagram'; break;
                                                            default: $icon = 'share-alt'; break;
                                                        }
                                            ?>
                                                <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="btn btn-sm btn-outline-primary me-1 mb-1">
                                                    <i class="fab fa-<?= $icon ?>"></i> <?= htmlspecialchars($platform) ?>
                                                </a>
                                            <?php endif; endforeach; endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Comments Section -->
                    <div class="mb-5">
                        <h3 class="mb-4">
                            Comments
                            <span class="badge bg-primary rounded-pill ms-2"><?= count($comments) ?></span>
                        </h3>
                        
                        <?php if (!empty($comments)): ?>
                            <div class="comments-list mb-5">
                                <?php foreach ($comments as $comment): 
                                    $comment_date = new DateTime($comment['created_at']);
                                ?>
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <div class="d-flex mb-3">
                                            <div class="me-3">
                                                <?php if (!empty($comment['email'])): ?>
                                                    <img src="https://www.gravatar.com/avatar/<?= md5(strtolower(trim($comment['email']))) ?>?s=60&d=mp" 
                                                         alt="<?= htmlspecialchars($comment['full_name']) ?>" class="rounded-circle">
                                                <?php else: ?>
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <?= substr($comment['full_name'], 0, 1) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <h5 class="mb-1"><?= htmlspecialchars($comment['full_name']) ?></h5>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i> <?= $comment_date->format('F j, Y \a\t g:i a') ?>
                                                </small>
                                                <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-light">
                                No comments yet. Be the first to share your thoughts!
                            </div>
                        <?php endif; ?>
                        
                        <!-- Comment Form -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h4 class="mb-3">Leave a Comment</h4>
                                <form id="comment-form" method="POST" action="post-comment.php">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">
                                    <?php else: ?>
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="comment-name" class="form-label">Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="comment-name" name="name" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="comment-email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="comment-email" name="email" required>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="comment-content" class="form-label">Comment <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="comment-content" name="content" rows="4" required></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Post Comment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- Related Posts -->
<?php if (!empty($related_posts)): ?>
<section class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold mb-3">Related Articles</h2>
            <p class="lead text-muted">You might also like these posts</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($related_posts as $related): 
                $related_date = new DateTime($related['published_at']);
                $excerpt = !empty($related['excerpt']) ? $related['excerpt'] : substr(strip_tags($related['content']), 0, 100) . '...';
            ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <?php if (!empty($related['featured_image'])): ?>
                        <img src="<?= BASE_URL . $related['featured_image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($related['title']) ?>" style="height: 200px; object-fit: cover;">
                    <?php else: ?>
                        <div class="card-img-top bg-secondary" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-newspaper fa-4x text-white"></i>
                        </div>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">
                                <?= htmlspecialchars($related['category_name'] ?? 'Uncategorized') ?>
                            </span>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt me-1"></i> <?= $related_date->format('M j, Y') ?>
                            </small>
                        </div>
                        <h3 class="h5 card-title"><?= htmlspecialchars($related['title']) ?></h3>
                        <p class="card-text"><?= $excerpt ?></p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="blog-post.php?slug=<?= $related['slug'] ?>" class="btn btn-sm btn-primary">Read More</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>