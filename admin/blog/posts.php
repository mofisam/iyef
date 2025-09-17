<?php
require_once '../../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'config/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'includes/functions/blog.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'includes/functions/users.php');

// Handle post deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (deleteBlogPost($_GET['id'])) {
        $_SESSION['success_message'] = 'Blog post deleted successfully';
        header('Location: posts.php');
        exit;
    } else {
        $_SESSION['error_message'] = 'Failed to delete blog post';
    }
}

// Get all blog posts
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$postsPerPage = 10;
$postsData = getBlogPosts($page, $postsPerPage, false);
$posts = $postsData['posts'];
$pagination = $postsData['pagination'];

$page_title = "Manage Blog Posts";
$breadcrumb = [
    ['title' => 'Categories', 'active' => false, 'url' => 'categoies.php'],
    ['title' => 'Posts', 'active' => true]
];
require_once '../includes/header.php';
?>

<div class="card admin-card">
    <div class="card-header bg-white border-bottom-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Blog Posts</h5>
            <a href="add-post.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add New
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="50">ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td><?= $post['id'] ?></td>
                            <td>
                                <a href="../../blog-post.php?slug=<?= $post['slug'] ?>" target="_blank">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($post['author_name']) ?></td>
                            <td><?= $post['category_name'] ?? 'Uncategorized' ?></td>
                            <td>
                                <?php if (isset($post['is_published']) && $post['is_published']): ?>
                                    <span class="badge bg-success">Published</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('M j, Y', strtotime($post['published_at'])) ?></td>
                            <td class="text-end table-actions">
                                <a href="edit-post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="posts.php?action=delete&id=<?= $post['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">No blog posts found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer bg-white border-top-0 py-3">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($pagination['has_prev']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $pagination['current_page'] - 1 ?>">Previous</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Previous</span>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <li class="page-item <?= $i == $pagination['current_page'] ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagination['has_next']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $pagination['current_page'] + 1 ?>">Next</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Next</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>