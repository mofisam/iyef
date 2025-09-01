<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/blog.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: blog.php');
    exit;
}

// Validate required fields
$errors = [];
$post_id = $_POST['post_id'] ?? 0;
$content = trim($_POST['content'] ?? '');

// Check if post exists
$post = fetchSingle("SELECT id FROM blog_posts WHERE id = ?", [$post_id]);
if (!$post) {
    $_SESSION['error_message'] = 'The blog post you\'re commenting on doesn\'t exist.';
    header('Location: blog.php');
    exit;
}

// Validate comment content
if (empty($content)) {
    $errors['content'] = 'Please enter your comment.';
} elseif (strlen($content) > 1000) {
    $errors['content'] = 'Comment cannot exceed 1000 characters.';
}

// Handle logged-in vs guest users
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user = fetchSingle("SELECT full_name, email FROM users WHERE id = ?", [$user_id]);
    $name = $user['full_name'];
    $email = $user['email'];
} else {
    // Validate guest user fields
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name)) {
        $errors['name'] = 'Please enter your name.';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name cannot exceed 100 characters.';
    }

    if (empty($email)) {
        $errors['email'] = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (strlen($email) > 255) {
        $errors['email'] = 'Email cannot exceed 255 characters.';
    }
}

// If there are errors, redirect back with error messages
if (!empty($errors)) {
    $_SESSION['comment_errors'] = $errors;
    $_SESSION['comment_form_data'] = [
        'name' => $name,
        'email' => $email,
        'content' => $content
    ];
    header("Location: blog-post.php?slug=" . urlencode($_GET['slug'] ?? '') . "#comment-form");
    exit;
}

// Prepare comment data
$comment_data = [
    'post_id' => $post_id,
    'content' => $content,
    'ip_address' => $_SERVER['REMOTE_ADDR'],
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
    'is_approved' => 1 // Set to 0 if you want to moderate comments first
];

// Add user information
if (isset($_SESSION['user_id'])) {
    $comment_data['user_id'] = $user_id;
} else {
    $comment_data['guest_name'] = $name;
    $comment_data['guest_email'] = $email;
}

// Insert comment into database
$comment_id = insertRecord('comments', $comment_data);

if ($comment_id) {
    // Update comment count in blog post
    executeQuery("UPDATE blog_posts SET comment_count = comment_count + 1 WHERE id = ?", [$post_id]);
    
    // Send notification email to admin (optional)
    // sendCommentNotification($comment_id);
    
    $_SESSION['success_message'] = 'Your comment has been submitted!' . 
        ($comment_data['is_approved'] ? '' : ' It will appear after approval.');
} else {
    $_SESSION['error_message'] = 'There was an error submitting your comment. Please try again.';
}

// Redirect back to the blog post
header("Location: blog-post.php?slug=" . urlencode($_GET['slug'] ?? '') . "#comments");
exit;