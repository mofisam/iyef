<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root'); // Replace with your database username
define('DB_PASSWORD', '1234'); // Replace with your database password
define('DB_NAME', 'iyef_db');
define('BASE_URL', 'http://localhost/web/IYEF/');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");
?>