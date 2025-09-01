<?php
require_once 'config/db.php';
require_once 'includes/functions/pages.php';

$slug = $_GET['slug'] ?? 'home'; // Default to 'home' if no slug provided

$page = getPageBySlug($slug);

if (!$page) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

$page_title = $page['meta_title'] ?: $page['title'];
require_once 'includes/header.php';
?>

<div class="container my-5">
    <article class="page-content">
        <h1><?= htmlspecialchars($page['title']) ?></h1>
        <div class="page-body">
            <?= $page['content'] ?>
        </div>
    </article>
</div>

<?php require_once 'includes/footer.php'; ?>