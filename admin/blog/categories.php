<?php
require_once '../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'config/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'includes/functions/blog.php');

// Check if user is admin


// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                if (!empty($_POST['name'])) {
                    $data = [
                        'name' => trim($_POST['name']),
                        'description' => trim($_POST['description'] ?? '')
                    ];
                    
                    if (insertRecord('categories', $data)) {
                        $_SESSION['success_message'] = 'Category added successfully';
                    } else {
                        $_SESSION['error_message'] = 'Failed to add category';
                    }
                }
                break;
                
            case 'edit':
                if (!empty($_POST['id']) && !empty($_POST['name'])) {
                    $data = [
                        'name' => trim($_POST['name']),
                        'description' => trim($_POST['description'] ?? '')
                    ];
                    
                    if (updateRecord('categories', $data, 'id = ?', [$_POST['id']])) {
                        $_SESSION['success_message'] = 'Category updated successfully';
                    } else {
                        $_SESSION['error_message'] = 'Failed to update category';
                    }
                }
                break;
                
            case 'delete':
                if (!empty($_POST['id'])) {
                    // Check if category is in use
                    $inUse = fetchSingle("SELECT COUNT(*) as count FROM blog_posts WHERE category_id = ?", [$_POST['id']])['count'];
                    
                    if ($inUse > 0) {
                        $_SESSION['error_message'] = 'Cannot delete category - it is being used by blog posts';
                    } elseif (deleteRecord('categories', 'id = ?', [$_POST['id']])) {
                        $_SESSION['success_message'] = 'Category deleted successfully';
                    } else {
                        $_SESSION['error_message'] = 'Failed to delete category';
                    }
                }
                break;
        }
        
        header('Location: categories.php');
        exit;
    }
}

// Get all categories
$categories = getBlogCategories();

$page_title = "Manage Blog Categories";
$breadcrumb = [
    ['title' => 'Blog', 'active' => false, 'url' => 'admin/blog/posts.php'],
    ['title' => 'Categories', 'active' => true]
];
require_once '../includes/header.php';
?>

<div class="row g-4">
    <!-- Add/Edit Category Form -->
    <div class="col-lg-4">
        <div class="card admin-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0"><?= isset($_GET['edit']) ? 'Edit' : 'Add' ?> Category</h5>
            </div>
            <div class="card-body">
                <?php
                $editingCategory = null;
                if (isset($_GET['edit'])) {
                    $editingCategory = fetchSingle("SELECT * FROM categories WHERE id = ?", [$_GET['edit']]);
                }
                ?>
                <form method="POST">
                    <input type="hidden" name="action" value="<?= $editingCategory ? 'edit' : 'add' ?>">
                    <?php if ($editingCategory): ?>
                        <input type="hidden" name="id" value="<?= $editingCategory['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" 
                               value="<?= $editingCategory ? htmlspecialchars($editingCategory['name']) : '' ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= 
                            $editingCategory ? htmlspecialchars($editingCategory['description']) : '' 
                        ?></textarea>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <?= $editingCategory ? 'Update' : 'Add' ?> Category
                        </button>
                        
                        <?php if ($editingCategory): ?>
                            <a href="categories.php" class="btn btn-outline-secondary mt-2">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Categories List -->
    <div class="col-lg-8">
        <div class="card admin-card">
            <div class="card-header bg-white border-bottom-0 py-3">
                <h5 class="mb-0">All Categories</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Posts</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= htmlspecialchars($category['name']) ?></td>
                                    <td><?= htmlspecialchars($category['description']) ?: '—' ?></td>
                                    <td>
                                        <?= fetchSingle("SELECT COUNT(*) as count FROM blog_posts WHERE category_id = ?", [$category['id']])['count'] ?>
                                    </td>
                                    <td class="text-end table-actions">
                                        <a href="categories.php?edit=<?= $category['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $category['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($categories)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">No categories found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>