<?php
require_once 'config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');
/**
 * Functions for managing event speakers
 */

/**
 * Get all speakers for an event
 * 
 * @param int $eventId The event ID
 * @return array Array of speaker data
 */
function getEventSpeakers($eventId) {
    try {
        return fetchAll("
            SELECT * FROM event_speakers 
            WHERE event_id = ? 
            ORDER BY display_order ASC
        ", [$eventId]);
    } catch (Exception $e) {
        error_log("Error getting speakers: " . $e->getMessage());
        return [];
    }
}

/**
 * Get a single speaker by ID
 * 
 * @param int $speakerId The speaker ID
 * @return array|null Speaker data or null if not found
 */
function getSpeakerById($speakerId) {
    try {
        return fetchSingle("
            SELECT * FROM event_speakers 
            WHERE id = ?
        ", [$speakerId]);
    } catch (Exception $e) {
        error_log("Error getting speaker: " . $e->getMessage());
        return null;
    }
}

/**
 * Add a new speaker
 * 
 * @param array $data Speaker data (name, title, bio, etc.)
 * @return bool True on success
 */
function addSpeaker($data) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO event_speakers 
            (event_id, name, title, bio, photo, twitter, linkedin, display_order, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->bind_param(
            "issssssi",
            $data['event_id'],
            $data['name'],
            $data['title'],
            $data['bio'],
            $data['photo'],
            $data['twitter'],
            $data['linkedin'],
            $data['display_order']
        );
        
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Error adding speaker: " . $e->getMessage());
        return false;
    }
}

/**
 * Update an existing speaker
 * 
 * @param int $speakerId The speaker ID
 * @param array $data Updated speaker data
 * @return bool True on success
 */
function updateSpeaker($speakerId, $data) {
    global $conn;
    
    try {
        // Check if we're updating the photo
        $photoUpdate = !empty($data['photo']) ? ", photo = ?" : "";
        
        $stmt = $conn->prepare("
            UPDATE event_speakers SET
            name = ?,
            title = ?,
            bio = ?,
            twitter = ?,
            linkedin = ?,
            display_order = ?
            $photoUpdate
            WHERE id = ?
        ");
        
        if (!empty($data['photo'])) {
            $stmt->bind_param(
                "sssssssi",
                $data['name'],
                $data['title'],
                $data['bio'],
                $data['twitter'],
                $data['linkedin'],
                $data['display_order'],
                $data['photo'],
                $speakerId
            );
        } else {
            $stmt->bind_param(
                "sssssii",
                $data['name'],
                $data['title'],
                $data['bio'],
                $data['twitter'],
                $data['linkedin'],
                $data['display_order'],
                $speakerId
            );
        }
        
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Error updating speaker: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a speaker
 * 
 * @param int $speakerId The speaker ID
 * @return bool True on success
 */
function deleteSpeaker($speakerId) {
    global $conn;
    
    try {
        // First get speaker to delete photo
        $speaker = getSpeakerById($speakerId);
        if ($speaker && !empty($speaker['photo'])) {
            $photoPath = '../assets/uploads/speakers/' . $speaker['photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
        }
        
        $stmt = $conn->prepare("DELETE FROM event_speakers WHERE id = ?");
        $stmt->bind_param("i", $speakerId);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Error deleting speaker: " . $e->getMessage());
        return false;
    }
}

/**
 * Handle speaker photo upload
 * 
 * @param array $file The $_FILES array element
 * @return string|false The filename on success, false on failure
 */
function handleSpeakerPhotoUpload($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }
    
    // Validate file size (max 2MB)
    if ($file['size'] > 2000000) {
        return false;
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../assets/uploads/speakers/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . strtolower($extension);
    $targetPath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $filename;
    }
    
    return false;
}