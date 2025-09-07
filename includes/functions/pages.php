<?php
require_once '../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');

/**
 * Create or update a page
 * 
 * @param array $pageData Page data
 * @return bool True on success, false on failure
 */
function savePage($pageData) {
    // Validate input
    if (empty($pageData['title']) || empty($pageData['content']) || empty($pageData['slug'])) {
        return false;
    }
    
    // Check if page exists
    $existingPage = getPageBySlug($pageData['slug']);
    
    if ($existingPage) {
        // Update existing page
        $result = updateRecord('pages', [
            'title' => $pageData['title'],
            'content' => $pageData['content'],
            'meta_title' => $pageData['meta_title'] ?? null,
            'meta_description' => $pageData['meta_description'] ?? null
        ], 'slug = ?', [$pageData['slug']]);
    } else {
        // Create new page
        $result = insertRecord('pages', [
            'slug' => $pageData['slug'],
            'title' => $pageData['title'],
            'content' => $pageData['content'],
            'meta_title' => $pageData['meta_title'] ?? null,
            'meta_description' => $pageData['meta_description'] ?? null
        ]);
    }
    
    return $result !== false;
}

/**
 * Get page by slug
 * 
 * @param string $slug Page slug
 * @return array|false Page data or false if not found
 */
function getPageBySlug($slug) {
    return fetchSingle("SELECT * FROM pages WHERE slug = ?", [$slug]);
}

function getPageById($id) {
    return fetchSingle("SELECT * FROM pages WHERE id = ?", [$id]);
}

/**
 * Get all pages
 * 
 * @return array List of pages
 */
function getAllPages() {
    return fetchAll("SELECT * FROM pages ORDER BY title");
}

/**
 * Delete a page
 * 
 * @param string $slug Page slug
 * @return bool True on success, false on failure
 */
function deletePage($slug) {
    $result = deleteRecord('pages', 'slug = ?', [$slug]);
    return $result !== false;
}
