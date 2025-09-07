<?php
require_once '../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');

/**
 * Create a new program
 * 
 * @param array $programData Program data
 * @return array Result with status and message/program ID
 */
function createProgram($programData) {
    // Validate input
    if (empty($programData['title']) || empty($programData['description'])) {
        return ['status' => 'error', 'message' => 'Title and description are required'];
    }
    
    // Generate slug
    $slug = generateProgramSlug($programData['title']);
    
    // Prepare program data
    $data = [
        'title' => $programData['title'],
        'slug' => $slug,
        'description' => $programData['description'],
        'start_date' => $programData['start_date'] ?? null,
        'end_date' => $programData['end_date'] ?? null,
        'image' => $programData['image'] ?? null
    ];
    
    // Insert program
    $programId = insertRecord('programs', $data);
    
    if ($programId) {
        return ['status' => 'success', 'program_id' => $programId];
    } else {
        return ['status' => 'error', 'message' => 'Failed to create program'];
    }
}

/**
 * Update a program
 * 
 * @param int $programId Program ID
 * @param array $programData Updated program data
 * @return bool True on success, false on failure
 */
function updateProgram($programId, $programData) {
    // Generate new slug if title changed
    if (!empty($programData['title'])) {
        $programData['slug'] = generateProgramSlug($programData['title']);
    }
    
    $result = updateRecord('programs', $programData, 'id = ?', [$programId]);
    return $result !== false;
}

/**
 * Delete a program
 * 
 * @param int $programId Program ID
 * @return bool True on success, false on failure
 */
function deleteProgram($programId) {
    // First delete all registrations for this program
    deleteRecord('program_registrations', 'program_id = ?', [$programId]);
    
    // Then delete the program
    $result = deleteRecord('programs', 'id = ?', [$programId]);
    return $result !== false;
}

/**
 * Get program by ID
 * 
 * @param int $programId Program ID
 * @return array|false Program data or false if not found
 */
function getProgramById($programId) {
    return fetchSingle("SELECT * FROM programs WHERE id = ?", [$programId]);
}

/**
 * Get program by slug
 * 
 * @param string $slug Program slug
 * @return array|false Program data or false if not found
 */
function getProgramBySlug($slug) {
    return fetchSingle("SELECT * FROM programs WHERE slug = ?", [$slug]);
}

/**
 * Get all programs (paginated)
 * 
 * @param int $page Current page
 * @param int $perPage Programs per page
 * @return array Programs and pagination info
 */
function getAllPrograms($page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    
    $programs = fetchAll("
        SELECT * FROM programs
        ORDER BY created_at DESC
        LIMIT ? OFFSET ?
    ", [$perPage, $offset]);
    
    $total = fetchSingle("SELECT COUNT(*) as total FROM programs")['total'];
    
    $totalPages = ceil($total / $perPage);
    
    return [
        'programs' => $programs,
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
 * Get active programs (current or upcoming)
 * 
 * @return array List of active programs
 */
function getActivePrograms() {
    return fetchAll("
        SELECT * FROM programs
        WHERE end_date >= CURDATE() OR end_date IS NULL
        ORDER BY start_date ASC
    ");
}

function isProgramActive($programId) {
    $program = fetchSingle("SELECT * FROM programs WHERE id = ?", [$programId]);
    
    if (!$program) {
        return false;
    }
    
    // If no dates set, consider it always active
    if (!$program['start_date'] && !$program['end_date']) {
        return true;
    }
    
    $currentDate = date('Y-m-d');
    
    // If only start date is set
    if ($program['start_date'] && !$program['end_date']) {
        return $currentDate >= $program['start_date'];
    }
    
    // If both dates are set
    return $currentDate >= $program['start_date'] && $currentDate <= $program['end_date'];
}

/**
 * Get related programs (excluding current program)
 */
function getRelatedPrograms($programId, $limit = 3) {
    return fetchAll("
        SELECT * FROM programs 
        WHERE id != ? 
        ORDER BY created_at DESC 
        LIMIT ?
    ", [$programId, $limit]);
}

/**
 * Register a participant for a program
 * 
 * @param int $programId Program ID
 * @param array $participantData Participant data
 * @return bool True on success, false on failure
 */
function registerForProgram($programId, $participantData) {
    // Validate input
    if (empty($participantData['full_name']) || empty($participantData['email'])) {
        return false;
    }
    
    // Prepare registration data
    $data = [
        'program_id' => $programId,
        'full_name' => $participantData['full_name'],
        'age' => $participantData['age'] ?? null,
        'gender' => $participantData['gender'] ?? null,
        'phone' => $participantData['phone'] ?? null,
        'email' => $participantData['email'],
        'address' => $participantData['address'] ?? null,
        'emergency_contact' => $participantData['emergency_contact'] ?? null
    ];
    
    // Insert registration
    $registrationId = insertRecord('program_registrations', $data);
    return $registrationId !== false;
}

/**
 * Get program registrations
 * 
 * @param int $programId Program ID
 * @return array List of registrations
 */
function getProgramRegistrations($programId) {
    return fetchAll("
        SELECT * FROM program_registrations
        WHERE program_id = ?
        ORDER BY registered_at DESC
    ", [$programId]);
}

/**
 * Generate a URL-friendly slug for programs
 * 
 * @param string $string The string to convert
 * @return string The generated slug
 */
function generateProgramSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Ensure slug is unique
    $originalSlug = $slug;
    $counter = 1;
    
    while (recordExists('programs', 'slug = ?', [$slug])) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}