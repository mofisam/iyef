<?php
// Start session and output buffering at the very top
ob_start();
require_once '../config/db.php';
require_once '../includes/functions/pages.php';

// Check admin access


$action = $_GET['action'] ?? 'list';
$pageId = $_GET['id'] ?? 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $pageData = [
            'slug' => trim($_POST['slug']),
            'title' => trim($_POST['title']),
            'content' => trim($_POST['content']),
            'meta_title' => trim($_POST['meta_title']),
            'meta_description' => trim($_POST['meta_description'])
        ];

        // Generate slug if empty
        if (empty($pageData['slug'])) {
            $pageData['slug'] = strtolower(str_replace(' ', '-', $pageData['title']));
        }

        // Sanitize slug
        $pageData['slug'] = preg_replace('/[^a-z0-9\-]/', '', $pageData['slug']);

        if ($action === 'add') {
            if (savePage($pageData)) {
                $_SESSION['success_message'] = 'Page created successfully!';
                header('Location: pages.php');
                exit;
            } else {
                $error = 'Failed to create page';
            }
        } elseif ($action === 'edit') {
            if (savePage($pageData)) {
                $_SESSION['success_message'] = 'Page updated successfully!';
                header('Location: pages.php');
                exit;
            } else {
                $error = 'Failed to update page';
            }
        }
    }
}

// Handle delete action
if ($action === 'delete') {
    if (deletePage($pageId)) {
        $_SESSION['success_message'] = 'Page deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete page';
    }
    header('Location: pages.php');
    exit;
}

// Set page title and breadcrumb
$page_title = "Manage Pages";
$breadcrumb = [
    ['title' => 'Pages', 'active' => $action === 'list'],
    ['title' => 'Add New', 'active' => $action === 'add'],
    ['title' => 'Edit', 'active' => $action === 'edit']
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <?php if ($action === 'list'): ?>
        <!-- Pages List -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">All Pages</h5>
                <a href="pages.php?action=add" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i> Add New
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 data-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">ID</th>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Last Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pages = getAllPages();
                            foreach ($pages as $page):
                                $updatedAt = new DateTime($page['updated_at']);
                            ?>
                            <tr>
                                <td><?= $page['id'] ?></td>
                                <td>
                                    <a href="pages.php?action=edit&id=<?= $page['id'] ?>" class="text-decoration-none">
                                        <?= htmlspecialchars($page['title']) ?>
                                    </a>
                                </td>
                                <td>/<?= $page['slug'] ?></td>
                                <td><?= $updatedAt->format('M j, Y') ?></td>
                                <td class="table-actions">
                                    <a href="../page.php?slug=<?= $page['slug'] ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="View">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="pages.php?action=edit&id=<?= $page['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="pages.php?action=delete&id=<?= $page['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($pages)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">No pages found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- Page Form -->
        <?php
        $page = [];
        if ($action === 'edit') {
            $page = getPageById($pageId);
            if (!$page) {
                $_SESSION['error_message'] = 'Page not found';
                header('Location: pages.php');
                exit;
            }
        }
        
        if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card admin-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><?= $action === 'add' ? 'Add New' : 'Edit' ?> Page</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                    
                        <!-- Title -->
                        <div class="col-md-12">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= htmlspecialchars($page['title'] ?? '') ?>" required>
                        </div>
                        
                        <!-- Slug -->
                        <div class="col-md-12">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">/</span>
                                <input type="text" class="form-control" id="slug" name="slug" 
                                       value="<?= htmlspecialchars($page['slug'] ?? '') ?>" required>
                            </div>
                            <div class="form-text">URL-friendly identifier (lowercase letters, numbers, and hyphens only)</div>
                        </div>
                        
                        <!-- Content -->
                        <div class="col-md-12">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <textarea class="form-control summernote" id="content" name="content" rows="10" required><?= htmlspecialchars($page['content'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- SEO Meta -->
                        <div class="col-md-12 mt-4">
                            <h6 class="border-bottom pb-2">SEO Settings</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                   value="<?= htmlspecialchars($page['meta_title'] ?? '') ?>">
                            <div class="form-text">Title for search engines (leave blank to use page title)</div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control" id="meta_description" name="meta_description" rows="2"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
                            <div class="form-text">Brief description for search engines (optimal length: 150-160 characters)</div>
                        </div>
                        
                        <!-- Submit -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Create Page' : 'Update Page' ?></button>
                            <a href="pages.php" class="btn btn-outline-secondary">Cancel</a>
                            
                            <?php if ($action === 'edit'): ?>
                                <a href="../page.php?slug=<?= $page['slug'] ?>" target="_blank" class="btn btn-outline-info float-end">
                                    <i class="fas fa-external-link-alt me-1"></i> View Page
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php'; 
ob_end_flush();
?>
<script>
    document.getElementById('title').addEventListener('input', function() {
    if (!document.getElementById('slug').value) {
        const slug = this.value.toLowerCase()
            .replace(/ /g, '-')
            .replace(/[^a-z0-9-]/g, '');
        document.getElementById('slug').value = slug;
    }
});
</script>