<?php
function getEventGallery($eventId) {
    try {
        return fetchAll("SELECT * FROM event_gallery WHERE event_id = ? ORDER BY created_at DESC", [$eventId]);
    } catch (mysqli_sql_exception $e) {
        error_log("Failed to get gallery: " . $e->getMessage());
        return [];
    }
}

function addGalleryPhoto($eventId, $photoData) {
    global $conn;
    
    try {
        $imageUrl = $photoData['image_url'] ?? '';
        $thumbnailUrl = $photoData['thumbnail_url'] ?? $imageUrl;
        $caption = $photoData['caption'] ?? '';

        $stmt = $conn->prepare("
            INSERT INTO event_gallery 
            (event_id, image_url, thumbnail_url, caption, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        $stmt->bind_param(
            "isss",
            $eventId,
            $imageUrl,
            $thumbnailUrl,
            $caption
        );

        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Error adding gallery photo: " . $e->getMessage());
        return false;
    }
}

function deleteGalleryPhoto($photoId) {
    global $conn;
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // First get photo to delete file
        $photo = fetchSingle("SELECT image_url, thumbnail_url FROM event_gallery WHERE id = ?", [$photoId]);
        
        if ($photo) {
            // Convert URLs to server paths
            $basePath = $_SERVER['DOCUMENT_ROOT'];
            
            // Delete main image file if exists
            if (!empty($photo['image_url'])) {
                $imagePath = $basePath . parse_url($photo['image_url'], PHP_URL_PATH);
                if (file_exists($imagePath)) {
                    if (!unlink($imagePath)) {
                        throw new Exception("Failed to delete image file: $imagePath");
                    }
                }
            }
            
            // Delete thumbnail file if exists and different from main image
            if (!empty($photo['thumbnail_url']) && $photo['thumbnail_url'] !== $photo['image_url']) {
                $thumbPath = $basePath . parse_url($photo['thumbnail_url'], PHP_URL_PATH);
                if (file_exists($thumbPath)) {
                    if (!unlink($thumbPath)) {
                        throw new Exception("Failed to delete thumbnail file: $thumbPath");
                    }
                }
            }
        }
        
        // Delete database record
        $stmt = $conn->prepare("DELETE FROM event_gallery WHERE id = ?");
        $stmt->bind_param("i", $photoId);
        $success = $stmt->execute();
        
        if (!$success) {
            throw new Exception("Failed to delete gallery record from database");
        }
        
        // Commit transaction if all operations succeeded
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        error_log("Error deleting gallery photo ID $photoId: " . $e->getMessage());
        return false;
    }
}

?>