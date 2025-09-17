<?php
require_once '../../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'config/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'includes/functions/blog.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'includes/functions/users.php');

// Check if user is admin


// Handle form submission
$errors = [];
$postData = [
    'title' => '',
    'excerpt' => '',
    'content' => '',
    'category_id' => '',
    'is_published' => 1,
    'featured_image' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postData = [
        'title' => trim($_POST['title']),
        'excerpt' => trim($_POST['excerpt']),
        'content' => trim($_POST['content']),
        'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
        'is_published' => isset($_POST['is_published']) ? 1 : 0,
        'featured_image' => trim($_POST['featured_image'] ?? '')
    ];
    
    // Validate input
    if (empty($postData['title'])) {
        $errors['title'] = 'Title is required';
    }
    
    if (empty($postData['content'])) {
        $errors['content'] = 'Content is required';
    }
    
    // Handle featured image upload
    if (!empty($_FILES['featured_image_file']['name'])) {
        $uploadDir = '../../assets/uploads/blog/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $_FILES['featured_image_file']['tmp_name']);
        
        if (in_array($mimeType, $allowedTypes)) {
            $extension = pathinfo($_FILES['featured_image_file']['name'], PATHINFO_EXTENSION);
            $fileName = 'post-' . time() . '.' . $extension;
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['featured_image_file']['tmp_name'], $filePath)) {
                $postData['featured_image'] = '/assets/uploads/blog/' . $fileName;
            } else {
                $errors['featured_image_file'] = 'Failed to upload image';
            }
        } else {
            $errors['featured_image_file'] = 'Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.';
        }
    }
    
    // If editing, get the post ID
    $postId = isset($_GET['id']) ? $_GET['id'] : null;
    
    // If no errors, save the post
    if (empty($errors)) {
        if ($postId) {
            // Update existing post
            if (updateBlogPost($postId, $postData)) {
                $_SESSION['success_message'] = 'Post updated successfully';
                header('Location: posts.php');
                exit;
            } else {
                $errors['general'] = 'Failed to update post';
            }
        } else {
            // Create new post
            $result = createBlogPost($postData, $_SESSION['user_id']);
            
            if ($result['status'] === 'success') {
                $_SESSION['success_message'] = 'Post created successfully';
                header('Location: edit-post.php?id=' . $result['post_id']);
                exit;
            } else {
                $errors['general'] = $result['message'];
            }
        }
    }
} elseif (isset($_GET['id'])) {
    // Editing existing post - load the data
    $post = getBlogPostById($_GET['id']);
    
    if ($post) {
        $postData = [
            'title' => $post['title'],
            'excerpt' => $post['excerpt'],
            'content' => $post['content'],
            'category_id' => $post['category_id'],
            'is_published' => $post['is_published'],
            'featured_image' => $post['featured_image']
        ];
    } else {
        $_SESSION['error_message'] = 'Post not found';
        header('Location: posts.php');
        exit;
    }
}

// Get all categories for dropdown
$categories = getBlogCategories();

$page_title = isset($_GET['id']) ? "Edit Blog Post" : "Add New Blog Post";
$breadcrumb = [
    ['title' => 'Blog', 'active' => false, 'url' => 'admin/blog/posts.php'],
    ['title' => 'Posts', 'active' => false, 'url' => 'admin/blog/posts.php'],
    ['title' => isset($_GET['id']) ? 'Edit' : 'Add', 'active' => true]
];
require_once '../includes/header.php';
?>

<div class="card admin-card">
    <div class="card-header bg-white border-bottom-0 py-3">
        <h5 class="mb-0"><?= $page_title ?></h5>
    </div>
    <div class="card-body">
        <?php if (isset($errors['general'])): ?>
            <div class="alert alert-danger"><?= $errors['general'] ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <!-- Title -->
                <div class="col-12">
                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" 
                           id="title" name="title" value="<?= htmlspecialchars($postData['title']) ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= $errors['title'] ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Excerpt -->
                <div class="col-12">
                    <label for="excerpt" class="form-label">Excerpt</label>
                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?= 
                        htmlspecialchars($postData['excerpt']) 
                    ?></textarea>
                    <div class="form-text">A short summary of your post (optional)</div>
                </div>
                
                <!-- Content -->
                <div class="col-12">
                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control summernote <?= isset($errors['content']) ? 'is-invalid' : '' ?>" 
                              id="content" name="content" rows="10" required><?= 
                        htmlspecialchars($postData['content']) 
                    ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <div class="invalid-feedback"><?= $errors['content'] ?></div>
                    <?php endif; ?>
                </div>
                
                <!-- Category & Status -->
                <div class="col-md-6">
                    <label for="category_id" class="form-label">Category</label>
                    <select class="form-select select2" id="category_id" name="category_id">
                        <option value="">Uncategorized</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['id'] ?>" <?= 
                                $postData['category_id'] == $category['id'] ? 'selected' : '' 
                            ?>>
                                <?= htmlspecialchars($category['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_published" name="is_published" 
                               <?= $postData['is_published'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_published">Published</label>
                    </div>
                </div>
                
                <!-- Featured Image -->
                <div class="col-12">
                    <label class="form-label">Featured Image</label>
                    
                    <?php if (!empty($postData['featured_image'])): ?>
                        <div class="mb-3">
                            <img src="<?= BASE_URL . $postData['featured_image'] ?>" class="img-thumbnail" style="max-height: 200px;">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="remove_featured_image" name="remove_featured_image">
                                <label class="form-check-label" for="remove_featured_image">Remove featured image</label>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" class="form-control <?= isset($errors['featured_image_file']) ? 'is-invalid' : '' ?>" 
                           id="featured_image_file" name="featured_image_file" accept="image/*">
                    <?php if (isset($errors['featured_image_file'])): ?>
                        <div class="invalid-feedback"><?= $errors['featured_image_file'] ?></div>
                    <?php endif; ?>
                    <input type="hidden" name="featured_image" value="<?= $postData['featured_image'] ?>">
                    <div class="form-text">Recommended size: 1200x630 pixels</div>
                </div>
                
                <!-- Submit Button -->
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <?= isset($_GET['id']) ? 'Update' : 'Publish' ?> Post
                    </button>
                    <a href="posts.php" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>