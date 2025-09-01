<?php
require_once '../config/db_functions.php';

/**
 * Apply as a volunteer
 * 
 * @param array $volunteerData Volunteer data
 * @return bool True on success, false on failure
 */
function applyAsVolunteer($volunteerData) {
    // Validate input
    if (empty($volunteerData['full_name']) || empty($volunteerData['email'])) {
        return false;
    }
    
    // Prepare volunteer data
    $data = [
        'full_name' => $volunteerData['full_name'],
        'email' => $volunteerData['email'],
        'phone' => $volunteerData['phone'] ?? null,
        'skills' => $volunteerData['skills'] ?? null,
        'availability' => $volunteerData['availability'] ?? null,
        'motivation' => $volunteerData['motivation'] ?? null
    ];
    
    // Insert volunteer application
    $volunteerId = insertRecord('volunteers', $data);
    return $volunteerId !== false;
}

/**
 * Get all volunteer applications
 * 
 * @param int $page Current page
 * @param int $perPage Applications per page
 * @return array Volunteers and pagination info
 */
function getAllVolunteers($page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    
    $volunteers = fetchAll("
        SELECT * FROM volunteers
        ORDER BY applied_at DESC
        LIMIT ? OFFSET ?
    ", [$perPage, $offset]);
    
    $total = fetchSingle("SELECT COUNT(*) as total FROM volunteers")['total'];
    
    $totalPages = ceil($total / $perPage);
    
    return [
        'volunteers' => $volunteers,
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
 * Get volunteer by ID
 * 
 * @param int $volunteerId Volunteer ID
 * @return array|false Volunteer data or false if not found
 */
function getVolunteerById($volunteerId) {
    return fetchSingle("SELECT * FROM volunteers WHERE id = ?", [$volunteerId]);
}