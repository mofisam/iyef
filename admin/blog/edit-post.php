<?php
// This is essentially the same as add-post.php but with ID parameter
// We can just redirect to add-post.php with the ID
if (!isset($_GET['id'])) {
    header('Location: posts.php');
    exit;
}

$_GET['id'] = intval($_GET['id']);
require 'add-post.php';
?>