<?php
require_once '../config/db.php';
require_once '../config/db_functions.php';

// Check admin access


$action = $_GET['action'] ?? 'list';
$faqId = $_GET['id'] ?? 0;
$currentCategory = $_GET['category'] ?? null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'question' => trim($_POST['question']),
        'answer' => trim($_POST['answer']),
        'category' => trim($_POST['category'] ?? null),
        'display_order' => (int)$_POST['display_order'],
        'is_active' => isset($_POST['is_active']) ? 1 : 0
    ];

    if ($action === 'add') {
        $result = insertRecord('faqs', $data);
        if ($result) {
            $_SESSION['success_message'] = 'FAQ added successfully!';
            header('Location: faqs.php'.($currentCategory ? '?category='.urlencode($currentCategory) : ''));
            exit;
        } else {
            $error = 'Failed to add FAQ';
        }
    } elseif ($action === 'edit') {
        $result = updateRecord('faqs', $data, 'id = ?', [$faqId]);
        if ($result) {
            $_SESSION['success_message'] = 'FAQ updated successfully!';
            header('Location: faqs.php'.($currentCategory ? '?category='.urlencode($currentCategory) : ''));
            exit;
        } else {
            $error = 'Failed to update FAQ';
        }
    }
}

// Handle delete action
if ($action === 'delete') {
    $result = deleteRecord('faqs', 'id = ?', [$faqId]);
    if ($result) {
        $_SESSION['success_message'] = 'FAQ deleted successfully!';
    } else {
        $_SESSION['error_message'] = 'Failed to delete FAQ';
    }
    header('Location: faqs.php'.($currentCategory ? '?category='.urlencode($currentCategory) : ''));
    exit;
}

// Get all distinct categories
$categories = fetchAll("SELECT DISTINCT category FROM faqs WHERE category IS NOT NULL AND category != '' ORDER BY category");

// Get FAQs based on current category filter
$whereClause = $currentCategory ? "WHERE category = ?" : "";
$params = $currentCategory ? [$currentCategory] : [];
$faqs = fetchAll("SELECT * FROM faqs $whereClause ORDER BY display_order ASC, id DESC", $params);

// Set page title and breadcrumb
$page_title = "Manage FAQs";
$breadcrumb = [
    ['title' => 'FAQs', 'active' => $action === 'list'],
    ['title' => 'Add New', 'active' => $action === 'add'],
    ['title' => 'Edit', 'active' => $action === 'edit']
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <!-- FAQs List -->
        <div class="card admin-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Frequently Asked Questions</h5>
                <div class="d-flex gap-2">
                    <!-- Category Filter Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoryDropdown" 
                                data-bs-toggle="dropdown" aria-expanded="false">
                            <?= $currentCategory ? htmlspecialchars($currentCategory) : 'All Categories' ?>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="categoryDropdown">
                            <li><a class="dropdown-item" href="faqs.php">All Categories</a></li>
                            <?php foreach ($categories as $cat): ?>
                                <li><a class="dropdown-item" href="faqs.php?category=<?= urlencode($cat['category']) ?>">
                                    <?= htmlspecialchars($cat['category']) ?>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <a href="faqs.php?action=add<?= $currentCategory ? '&category='.urlencode($currentCategory) : '' ?>" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Add New
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 data-table">
                        <thead class="bg-light">
                            <tr>
                                <th width="50">ID</th>
                                <th>Question</th>
                                <th>Category</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faqs as $faq): ?>
                            <tr>
                                <td><?= $faq['id'] ?></td>
                                <td><?= htmlspecialchars($faq['question']) ?></td>
                                <td><?= !empty($faq['category']) ? htmlspecialchars($faq['category']) : 'General' ?></td>
                                <td><?= $faq['display_order'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $faq['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $faq['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <a href="faqs.php?action=edit&id=<?= $faq['id'] ?><?= $currentCategory ? '&category='.urlencode($currentCategory) : '' ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="faqs.php?action=delete&id=<?= $faq['id'] ?><?= $currentCategory ? '&category='.urlencode($currentCategory) : '' ?>" 
                                       class="btn btn-sm btn-outline-danger confirm-delete" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($faqs)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        No FAQs found<?= $currentCategory ? ' in this category' : '' ?>.
                                        <a href="faqs.php?action=add<?= $currentCategory ? '&category='.urlencode($currentCategory) : '' ?>">
                                            Add one now
                                        </a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <!-- FAQ Form -->
        <?php
        $faq = [];
        if ($action === 'edit') {
            $faq = fetchSingle("SELECT * FROM faqs WHERE id = ?", [$faqId]);
            if (!$faq) {
                $_SESSION['error_message'] = 'FAQ not found';
                header('Location: faqs.php');
                exit;
            }
        }
        
        // Set default category from URL if adding new FAQ
        if ($action === 'add' && $currentCategory && empty($faq)) {
            $faq['category'] = $currentCategory;
        }
        ?>
        
        <div class="card admin-card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0"><?= $action === 'add' ? 'Add New' : 'Edit' ?> FAQ</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row g-3">
                        <!-- Question -->
                        <div class="col-md-12">
                            <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="question" name="question" 
                                   value="<?= htmlspecialchars($faq['question'] ?? '') ?>" required>
                        </div>
                        
                        <!-- Answer -->
                        <div class="col-md-12">
                            <label for="answer" class="form-label">Answer <span class="text-danger">*</span></label>
                            <textarea class="form-control summernote" id="answer" name="answer" 
                                      rows="5" required><?= htmlspecialchars($faq['answer'] ?? '') ?></textarea>
                        </div>
                        
                        <!-- Category -->
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="category" name="category" 
                                   value="<?= htmlspecialchars($faq['category'] ?? '') ?>"
                                   list="category-list">
                            <datalist id="category-list">
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= htmlspecialchars($cat['category']) ?>">
                                <?php endforeach; ?>
                            </datalist>
                            <div class="form-text">Leave blank for "General" category</div>
                        </div>
                        
                        <!-- Display Order -->
                        <div class="col-md-3">
                            <label for="display_order" class="form-label">Display Order</label>
                            <input type="number" class="form-control" id="display_order" name="display_order" 
                                   value="<?= $faq['display_order'] ?? 0 ?>" min="0">
                        </div>
                        
                        <!-- Status -->
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?= ($faq['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                        
                        <!-- Submit -->
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary"><?= $action === 'add' ? 'Add FAQ' : 'Update FAQ' ?></button>
                            <a href="faqs.php<?= $currentCategory ? '?category='.urlencode($currentCategory) : '' ?>" 
                               class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Initialize Summernote
$(document).ready(function() {
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });
    
    // Confirm before delete
    $('.confirm-delete').on('click', function() {
        return confirm('Are you sure you want to delete this FAQ?');
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>