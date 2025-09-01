<?php
require_once '.env.db'; // Load environment variables

// Error reporting - only display errors in development environment
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 1 for development, 0 for production
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Establish database connection with improved security
try {
    $conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset to ensure proper encoding
    if (!$conn->set_charset("utf8mb4")) {
        throw new Exception("Error loading character set utf8mb4: " . $conn->error);
    }
    
    // Set timezone if needed
    $conn->query("SET time_zone = '+00:00'");
    
} catch (Exception $e) {
    // Log the detailed error
    error_log($e->getMessage());
    
    // Display user-friendly message
    header('HTTP/1.1 503 Service Unavailable');
    die("<h2>Service Temporarily Unavailable</h2>
        <p>We're experiencing technical difficulties. Please try again later.</p>
        <p>If the problem persists, contact support@fandvagroservices.com</p>");
}

// Optional: Create a function to safely close the connection
function closeDatabaseConnection($conn) {
    if ($conn instanceof mysqli) {
        $conn->close();
    }
}

// Register shutdown function to ensure connection is closed
register_shutdown_function(function() use ($conn) {
    closeDatabaseConnection($conn);
});
?>