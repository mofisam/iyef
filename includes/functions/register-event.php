<?php
session_start();
require_once '../../config/db.php';
require_once 'events.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: events.php');
    exit;
}

// Validate required fields
$required = ['event_id', 'full_name', 'email'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// Sanitize input data
$event_id = (int)$_POST['event_id'];
$full_name = trim(htmlspecialchars($_POST['full_name']));
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$phone = !empty($_POST['phone']) ? trim(htmlspecialchars($_POST['phone'])) : null;
$organization = !empty($_POST['organization']) ? trim(htmlspecialchars($_POST['organization'])) : null;
$special_requirements = !empty($_POST['special_requirements']) ? trim(htmlspecialchars($_POST['special_requirements'])) : null;
$hear_about = !empty($_POST['hear_about']) ? trim(htmlspecialchars($_POST['hear_about'])) : null;

// Handle dietary preferences
$dietary_preferences = [];
if (!empty($_POST['dietary']) && is_array($_POST['dietary'])) {
    $dietary_preferences = array_map('trim', $_POST['dietary']);
    $dietary_preferences = array_map('htmlspecialchars', $dietary_preferences);
}
$other_dietary = null;
if (in_array('other', $dietary_preferences) && !empty($_POST['other_dietary_text'])) {
    $other_dietary = trim(htmlspecialchars($_POST['other_dietary_text']));
}

// Check if user is already registered for this event
$existing_registration = fetchSingle(
    "SELECT id FROM event_registrations WHERE event_id = ? AND email = ?", 
    [$event_id, $email]
);

if ($existing_registration) {
    $_SESSION['error_message'] = "You are already registered for this event.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Get event capacity to check availability
$event = fetchSingle("SELECT capacity FROM events WHERE id = ?", [$event_id]);
if (!$event) {
    $_SESSION['error_message'] = "Event not found.";
    header('Location: /events.php');
    exit;
}

// Check if event is full
$registrations_count = fetchSingle(
    "SELECT COUNT(*) as count FROM event_registrations WHERE event_id = ?", 
    [$event_id]
)['count'];

if ($event['capacity'] > 0 && $registrations_count >= $event['capacity']) {
    $_SESSION['error_message'] = "This event is already full.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Insert registration into database
try {
    $stmt = $conn->prepare("
        INSERT INTO event_registrations (
            event_id, 
            user_id, 
            full_name, 
            email, 
            phone, 
            organization, 
            dietary_preferences, 
            other_dietary, 
            special_requirements, 
            hear_about,
            registration_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $dietary_json = !empty($dietary_preferences) ? json_encode($dietary_preferences) : null;

    $stmt->bind_param(
        "isssssssss",
        $event_id,
        $user_id,
        $full_name,
        $email,
        $phone,
        $organization,
        $dietary_json,
        $other_dietary,
        $special_requirements,
        $hear_about
    );

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Thank you for registering!";
        
        // Send confirmation email if needed
        // sendConfirmationEmail($email, $full_name, $event_id);
        
        header('Location: /event.php?slug=' . urlencode(fetchSingle("SELECT slug FROM events WHERE id = ?", [$event_id])['slug']));
        exit;
    } else {
        throw new Exception("Database error: " . $stmt->error);
    }
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    $_SESSION['error_message'] = "An error occurred during registration. Please try again.";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}