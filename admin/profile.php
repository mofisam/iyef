<?php
require_once '../config/db.php';
require_once '../includes/functions/users.php';

// Check admin access

$userId = $_GET['user_id'] ?? 0;
$action = $_GET['action'] ?? 'edit';

// Get user data
$user = getUserById($userId);
if (!$user) {
    $_SESSION['error_message'] = 'User not found';
    header('Location: users.php');
    exit;
}

// Get existing profile data
$profile = fetchSingle("SELECT * FROM profiles WHERE user_id = ?", [$userId]);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'user_id' => $userId,
        'position' => trim($_POST['position']),
        'bio' => trim($_POST['bio']),
        'phone' => trim($_POST['phone']),
        'social_links' => json_encode([
            'facebook' => trim($_POST['facebook']),
            'twitter' => trim($_POST['twitter']),
            'instagram' => trim($_POST['instagram']),
            'linkedin' => trim($_POST['linkedin'])
        ]),
        'photo' => $profile['photo'] ?? null // Default to existing photo
    ];

    // Handle photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/profiles/';
        $fileName = uniqid() . '_' . basename($_FILES['photo']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
            $data['photo'] = '/assets/uploads/profiles/' . $fileName;
            
            // Delete old photo if it exists
            if (!empty($profile['photo']) && $profile['photo'] !== $data['photo']) {
                $oldPhoto = '../' . ltrim($profile['photo'], '/');
                if (file_exists($oldPhoto)) {
                    unlink($oldPhoto);
                }
            }
        }
    }

    // Check if profile exists
    if ($profile) {
        // Update existing profile
        $result = updateRecord('profiles', $data, 'user_id = ?', [$userId]);
    } else {
        // Create new profile
        $result = insertRecord('profiles', $data);
    }

    if ($result) {
        $_SESSION['success_message'] = 'Profile updated successfully!';
        header("Location: profile.php?user_id=$userId");
        exit;
    } else {
        $error = 'Failed to update profile';
    }
}

$page_title = "Manage Profile: " . $user['full_name'];
$breadcrumb = [
    ['title' => 'Users', 'url' => 'users.php'],
    ['title' => 'Manage Profile', 'active' => true]
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <div class="card admin-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0"><?= $user['full_name'] ?>'s Profile</h5>
            <a href="users.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Users
            </a>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-4">
                    <!-- Left Column - Photo and Basic Info -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-body text-center">
                                <div class="mb-3 position-relative">
                                    <img id="profile-preview" src="<?= !empty($profile['photo']) ? $profile['photo'] : '/assets/images/avatar-default.png' ?>" 
                                         class="rounded-circle shadow" style="width: 200px; height: 200px; object-fit: cover;">
                                    <label for="photo" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" 
                                           style="width: 40px; height: 40px; line-height: 40px;">
                                        <i class="fas fa-camera"></i>
                                        <input type="file" id="photo" name="photo" class="d-none" accept="image/*">
                                    </label>
                                </div>
                                <h4 class="mb-1"><?= $user['full_name'] ?></h4>
                                <p class="text-muted mb-3"><?= $user['role_name'] ?></p>
                                
                                <div class="text-start">
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control" value="<?= $user['email'] ?>" readonly>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position/Title</label>
                                        <input type="text" class="form-control" id="position" name="position" 
                                               value="<?= htmlspecialchars($profile['position'] ?? '') ?>">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" 
                                               value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Bio and Social Links -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Biography</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Profile Bio</label>
                                    <textarea class="form-control summernote" id="bio" name="bio" rows="8"><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Social Media Links</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $socialLinks = [];
                                if (!empty($profile['social_links'])) {
                                    $socialLinks = json_decode($profile['social_links'], true);
                                }
                                ?>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-primary text-white"><i class="fab fa-facebook-f"></i></span>
                                            <input type="url" class="form-control" placeholder="Facebook URL" 
                                                   name="facebook" value="<?= htmlspecialchars($socialLinks['facebook'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-info text-white"><i class="fab fa-twitter"></i></span>
                                            <input type="url" class="form-control" placeholder="Twitter URL" 
                                                   name="twitter" value="<?= htmlspecialchars($socialLinks['twitter'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-danger text-white"><i class="fab fa-instagram"></i></span>
                                            <input type="url" class="form-control" placeholder="Instagram URL" 
                                                   name="instagram" value="<?= htmlspecialchars($socialLinks['instagram'] ?? '') ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text bg-primary text-white"><i class="fab fa-linkedin-in"></i></span>
                                            <input type="url" class="form-control" placeholder="LinkedIn URL" 
                                                   name="linkedin" value="<?= htmlspecialchars($socialLinks['linkedin'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Save Profile
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Preview profile photo before upload
document.getElementById('photo').addEventListener('change', function(e) {
    const preview = document.getElementById('profile-preview');
    const file = e.target.files[0];
    const reader = new FileReader();
    
    reader.onload = function(e) {
        preview.src = e.target.result;
    }
    
    if (file) {
        reader.readAsDataURL(file);
    }
});

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
});
</script>

<?php require_once 'includes/footer.php'; ?>