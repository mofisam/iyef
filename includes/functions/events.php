<?php
require_once 'config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');

/**
 * Create a new event
 * 
 * @param array $eventData Event data
 * @return array Result with status and message/event ID
 */
function createEvent($eventData) {
    // Validate input
    if (empty($eventData['title']) || empty($eventData['description']) || empty($eventData['event_date'])) {
        return ['status' => 'error', 'message' => 'Title, description and date are required'];
    }
    
    // Generate slug
    $slug = generateEventSlug($eventData['title']);
    
    // Prepare event data
    $data = [
        'title' => $eventData['title'],
        'slug' => $slug,
        'description' => $eventData['description'],
        'location' => $eventData['location'] ?? null,
        'event_date' => $eventData['event_date'],
        'image' => $eventData['image'] ?? null
    ];
    
    // Insert event
    $eventId = insertRecord('events', $data);
    
    if ($eventId) {
        return ['status' => 'success', 'event_id' => $eventId];
    } else {
        return ['status' => 'error', 'message' => 'Failed to create event'];
    }
}

/**
 * Update an event
 * 
 * @param int $eventId Event ID
 * @param array $eventData Updated event data
 * @return bool True on success, false on failure
 */
function updateEvent($eventId, $eventData) {
    // Generate new slug if title changed
    if (!empty($eventData['title'])) {
        $eventData['slug'] = generateEventSlug($eventData['title']);
    }
    
    $result = updateRecord('events', $eventData, 'id = ?', [$eventId]);
    return $result !== false;
}

/**
 * Delete an event
 * 
 * @param int $eventId Event ID
 * @return bool True on success, false on failure
 */
function deleteEvent($eventId) {
    // First delete all registrations for this event
    deleteRecord('event_registrations', 'event_id = ?', [$eventId]);
    
    // Then delete the event
    $result = deleteRecord('events', 'id = ?', [$eventId]);
    return $result !== false;
}

/**
 * Get event by ID
 * 
 * @param int $eventId Event ID
 * @return array|false Event data or false if not found
 */
function getEventById($eventId) {
    return fetchSingle("SELECT * FROM events WHERE id = ?", [$eventId]);
}

/**
 * Get event by slug
 * 
 * @param string $slug Event slug
 * @return array|false Event data or false if not found
 */
function getEventBySlug($slug) {
    return fetchSingle("SELECT * FROM events WHERE slug = ?", [$slug]);
}

/**
 * Get all events (paginated)
 * 
 * @param int $page Current page
 * @param int $perPage Events per page
 * @param bool $upcomingOnly Only get upcoming events
 * @return array Events and pagination info
 */
function getAllEvents($page = 1, $perPage = 10, $upcomingOnly = true) {
    $offset = ($page - 1) * $perPage;
    
    $where = $upcomingOnly ? "WHERE event_date >= CURDATE()" : "";
    
    $events = fetchAll("
        SELECT * FROM events
        $where
        ORDER BY event_date ASC
        LIMIT ? OFFSET ?
    ", [$perPage, $offset]);
    
    $total = fetchSingle("
        SELECT COUNT(*) as total FROM events
        $where
    ")['total'];
    
    $totalPages = ceil($total / $perPage);
    
    return [
        'events' => $events,
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
 * Register a participant for an event
 * 
 * @param int $eventId Event ID
 * @param array $participantData Participant data
 * @param int|null $userId Logged-in user ID (optional)
 * @return bool True on success, false on failure
 */
function registerForEvent($eventId, $participantData, $userId = null) {
    // Validate input
    if (empty($participantData['full_name']) || empty($participantData['email'])) {
        return false;
    }
    
    // Prepare registration data
    $data = [
        'event_id' => $eventId,
        'user_id' => $userId,
        'full_name' => $participantData['full_name'],
        'email' => $participantData['email'],
        'phone' => $participantData['phone'] ?? null
    ];
    
    // Insert registration
    $registrationId = insertRecord('event_registrations', $data);
    return $registrationId !== false;
}

/**
 * Get event registrations
 * 
 * @param int $eventId Event ID
 * @return array List of registrations
 */
function getEventRegistrations($eventId) {
    return fetchAll("
        SELECT er.*, full_name as user_name, email as user_email
        FROM event_registrations er
        WHERE er.event_id = ?
        ORDER BY er.registration_date DESC
    ", [$eventId]);
}

/**
 * Generate a URL-friendly slug for events
 * 
 * @param string $string The string to convert
 * @return string The generated slug
 */
function generateEventSlug($string) {
    $slug = strtolower(trim($string));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Ensure slug is unique
    $originalSlug = $slug;
    $counter = 1;
    
    while (recordExists('events', 'slug = ?', [$slug])) {
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
    
    return $slug;
}