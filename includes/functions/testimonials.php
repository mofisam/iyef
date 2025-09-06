<?php
require_once 'config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');

/**
 * Create a new testimonial
 * 
 * @param array $testimonialData Testimonial data
 * @return array Result with status and message/testimonial ID
 */
function createTestimonial($testimonialData) {
    // Validate input
    if (empty($testimonialData['author_name']) || empty($testimonialData['content'])) {
        return ['status' => 'error', 'message' => 'Author name and content are required'];
    }

    // Prepare testimonial data
    $data = [
        'author_name' => $testimonialData['author_name'],
        'author_title' => $testimonialData['author_title'] ?? null,
        'author_image' => $testimonialData['author_image'] ?? null,
        'content' => $testimonialData['content'],
        'rating' => $testimonialData['rating'] ?? 5,
        'is_approved' => $testimonialData['is_approved'] ?? 0,
        'is_featured' => $testimonialData['is_featured'] ?? 0
    ];

    // Insert testimonial
    $testimonialId = insertRecord('testimonials', $data);
    
    if ($testimonialId) {
        return ['status' => 'success', 'testimonial_id' => $testimonialId];
    } else {
        return ['status' => 'error', 'message' => 'Failed to create testimonial'];
    }
}

/**
 * Update a testimonial
 * 
 * @param int $testimonialId Testimonial ID
 * @param array $testimonialData Updated testimonial data
 * @return bool True on success, false on failure
 */
function updateTestimonial($testimonialId, $testimonialData) {
    $result = updateRecord('testimonials', $testimonialData, 'id = ?', [$testimonialId]);
    return $result !== false;
}

/**
 * Delete a testimonial
 * 
 * @param int $testimonialId Testimonial ID
 * @return bool True on success, false on failure
 */
function deleteTestimonial($testimonialId) {
    $testimonial = getTestimonialById($testimonialId);
    if ($testimonial && !empty($testimonial['author_image'])) {
        $imagePath = '../' . ltrim($testimonial['author_image'], '/');
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
    
    $result = deleteRecord('testimonials', 'id = ?', [$testimonialId]);
    return $result !== false;
}

/**
 * Get testimonial by ID
 * 
 * @param int $testimonialId Testimonial ID
 * @return array|false Testimonial data or false if not found
 */
function getTestimonialById($testimonialId) {
    return fetchSingle("SELECT * FROM testimonials WHERE id = ?", [$testimonialId]);
}

/**
 * Get all testimonials (paginated)
 * 
 * @param int $page Current page
 * @param int $perPage Testimonials per page
 * @param bool $approvedOnly Only get approved testimonials
 * @return array Testimonials and pagination info
 */
function getAllTestimonials($page = 1, $perPage = 10, $approvedOnly = false) {
    $offset = ($page - 1) * $perPage;
    
    $where = $approvedOnly ? "WHERE is_approved = 1" : "";
    
    $testimonials = fetchAll("
        SELECT * FROM testimonials
        $where
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ", [$perPage, $offset]);
    
    $total = fetchSingle("
        SELECT COUNT(*) as total FROM testimonials
        $where
    ")['total'];
    
    $totalPages = ceil($total / $perPage);
    
    return [
        'testimonials' => $testimonials,
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
 * Get featured testimonials
 * 
 * @param int $limit Number of testimonials to return
 * @return array Featured testimonials
 */
function getFeaturedTestimonials($limit = 3) {
    return fetchAll("
        SELECT * FROM testimonials
        WHERE is_approved = 1 AND is_featured = 1
        ORDER BY created_at DESC
        LIMIT ?
    ", [$limit]);
}

/**
 * Get testimonials for approval
 * 
 * @return array Testimonials awaiting approval
 */
function getTestimonialsForApproval() {
    return fetchAll("
        SELECT * FROM testimonials
        WHERE is_approved = 0
        ORDER BY created_at DESC
    ");
}

/**
 * Approve a testimonial
 * 
 * @param int $testimonialId Testimonial ID
 * @return bool True on success, false on failure
 */
function approveTestimonial($testimonialId) {
    return updateTestimonial($testimonialId, ['is_approved' => 1]);
}

/**
 * Toggle featured status
 * 
 * @param int $testimonialId Testimonial ID
 * @return bool True on success, false on failure
 */
function toggleFeaturedTestimonial($testimonialId) {
    $testimonial = getTestimonialById($testimonialId);
    if ($testimonial) {
        $newStatus = $testimonial['is_featured'] ? 0 : 1;
        return updateTestimonial($testimonialId, ['is_featured' => $newStatus]);
    }
    return false;
}

/**
 * Get testimonials statistics
 * 
 * @return array Statistics
 */
function getTestimonialsStats() {
    return fetchSingle("
        SELECT 
            COUNT(*) as total,
            SUM(is_approved) as approved,
            SUM(is_featured) as featured,
            SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending
        FROM testimonials
    ");
}


/**
 * Send email notification for new testimonial submission
 * 
 * @param array $testimonialData Testimonial data
 * @return bool True on success, false on failure
 */
function sendTestimonialNotification($testimonialData) {
    $settings = getSettings();
    if (!$settings || empty($settings['contact_email'])) {
        return false;
    }
    
    $to = $settings['contact_email'];
    $subject = 'New Testimonial Submission - IYEF Website';
    
    $message = "
    <html>
    <head>
        <title>New Testimonial Submission</title>
    </head>
    <body>
        <h2>New Testimonial Received</h2>
        <p><strong>Author:</strong> {$testimonialData['author_name']}</p>
        <p><strong>Title:</strong> {$testimonialData['author_title']}</p>
        <p><strong>Rating:</strong> {$testimonialData['rating']}/5 stars</p>
        <p><strong>Content:</strong></p>
        <blockquote>{$testimonialData['content']}</blockquote>
        <br>
        <p>Please review and approve this testimonial in the admin panel.</p>
        <p><a href=\"" . getAdminUrl() . "testimonials.php\">View in Admin Panel</a></p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: IYEF Website <noreply@iyef.org>" . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Get admin URL
 * 
 * @return string Admin URL
 */
function getAdminUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . '/admin/';
}
?>