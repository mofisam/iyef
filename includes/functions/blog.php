<?php
require_once '../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');

/**
 * Create a new blog post
 * 
 * @param array $postData Post data (title, content, etc.)
 * @param int $authorId Author user ID
 * @return array Result with status and message/post ID
 */
function createBlogPost($postData, $authorId) {
    // Validate input
    if (empty($postData['title']) || empty($postData['content'])) {
        return ['status' => 'error', 'message' => 'Title and content are required'];
    }
    
    // Generate slug
    $slug = generateSlug($postData['title']);
    
    // Prepare post data
    $data = [
        'title' => $postData['title'],
        'slug' => $slug,
        'excerpt' => $postData['excerpt'] ?? null,
        'content' => $postData['content'],
        'featured_image' => $postData['featured_image'] ?? null,
        'category_id' => $postData['category_id'] ?? null,
        'author_id' => $authorId,
        'is_published' => $postData['is_published'] ?? 1
    ];
    
    // Insert post
    $postId = insertRecord('blog_posts', $data);
    
    if ($postId) {
        return ['status' => 'success', 'post_id' => $postId];
    } else {
        return ['status' => 'error', 'message' => 'Failed to create post'];
    }
}

/**
 * Update a blog post
 * 
 * @param int $postId Post ID
 * @param array $postData Updated post data
 * @return bool True on success, false on failure
 */
function updateBlogPost($postId, $postData) {
    // Generate new slug if title changed
    if (!empty($postData['title'])) {
        $postData['slug'] = generateSlug($postData['title']);
    }
    
    $result = updateRecord('blog_posts', $postData, 'id = ?', [$postId]);
    return $result !== false;
}

/**
 * Delete a blog post
 * 
 * @param int $postId Post ID
 * @return bool True on success, false on failure
 */
function deleteBlogPost($postId) {
    $result = deleteRecord('blog_posts', 'id = ?', [$postId]);
    return $result !== false;
}

/**
 * Get blog post by ID
 * 
 * @param int $postId Post ID
 * @return array|false Post data or false if not found
 */
function getBlogPostById($postId) {
    return fetchSingle("
        SELECT bp.*, u.full_name as author_name, c.name as category_name
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN categories c ON bp.category_id = c.id
        WHERE bp.id = ?
    ", [$postId]);
}

/**
 * Get blog post by slug
 * 
 * @param string $slug Post slug
 * @return array|false Post data or false if not found
 */
function getBlogPostBySlug($slug) {
    return fetchSingle("
        SELECT bp.*, u.full_name as author_name, c.name as category_name
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN categories c ON bp.category_id = c.id
        WHERE bp.slug = ?
    ", [$slug]);
}

/**
 * Get all blog posts (paginated)
 * 
 * @param int $page Current page
 * @param int $perPage Posts per page
 * @param bool $publishedOnly Only get published posts
 * @return array Posts and pagination info
 */
function getBlogPosts($page = 1, $perPage = 10, $publishedOnly = true) {
    $offset = ($page - 1) * $perPage;
    
    $where = $publishedOnly ? "WHERE bp.is_published = 1" : "";
    
    $posts = fetchAll("
        SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, bp.published_at, 
               u.full_name as author_name, c.name as category_name
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN categories c ON bp.category_id = c.id
        $where
        ORDER BY bp.published_at DESC
        LIMIT ? OFFSET ?
    ", [$perPage, $offset]);
    
    $total = fetchSingle("
        SELECT COUNT(*) as total FROM blog_posts bp
        $where
    ")['total'];
    
    $totalPages = ceil($total / $perPage);
    
    return [
        'posts' => $posts,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_items' => $total,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages
        ]
    ];
}

/**
 * Get posts by category (paginated)
 * 
 * @param int $categoryId Category ID
 * @param int $page Current page
 * @param int $perPage Posts per page
 * @return array Posts and pagination info
 */
function getPostsByCategory($categoryId, $page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    
    $posts = fetchAll("
        SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, bp.published_at, 
               u.full_name as author_name, c.name as category_name
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN categories c ON bp.category_id = c.id
        WHERE bp.category_id = ? AND bp.is_published = 1
        ORDER BY bp.published_at DESC
        LIMIT ? OFFSET ?
    ", [$categoryId, $perPage, $offset]);
    
    $total = fetchSingle("
        SELECT COUNT(*) as total FROM blog_posts
        WHERE category_id = ? AND is_published = 1
    ", [$categoryId])['total'];
    
    $totalPages = ceil($total / $perPage);
    
    return [
        'posts' => $posts,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_items' => $total,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages
        ]
    ];
}

/**
 * Get all blog categories
 * 
 * @return array List of categories
 */
function getBlogCategories() {
    return fetchAll("SELECT * FROM categories ORDER BY name");
}

/**
 * Generate a URL-friendly slug from a string
 * 
 * @param string $string The string to convert
 * @return string The generated slug
 */
function generateSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Ensure slug is unique
    $originalSlug = $slug;
    $counter = 1;
    
    while (recordExists('blog_posts', 'slug = ?', [$slug])) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}