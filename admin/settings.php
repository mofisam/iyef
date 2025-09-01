<?php
require_once '../config/db.php';
require_once '../includes/functions/settings.php';

// Check admin access

$settings = getSettings();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settingsData = [
        'site_name' => trim($_POST['site_name']),
        'contact_email' => trim($_POST['contact_email']),
        'contact_phone' => trim($_POST['contact_phone']),
        'address' => trim($_POST['address']),
        'facebook_url' => trim($_POST['facebook_url']),
        'twitter_url' => trim($_POST['twitter_url']),
        'instagram_url' => trim($_POST['instagram_url'])
    ];

    // Handle logo upload
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/uploads/';
        $fileName = uniqid() . '_' . basename($_FILES['site_logo']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $targetPath)) {
            $settingsData['site_logo'] = '/assets/uploads/' . $fileName;
            
            // Delete old logo if it exists
            if (!empty($settings['site_logo']) && $settings['site_logo'] !== $settingsData['site_logo']) {
                $oldLogo = '../' . ltrim($settings['site_logo'], '/');
                if (file_exists($oldLogo)) {
                    unlink($oldLogo);
                }
            }
        }
    } elseif (isset($_POST['remove_logo'])) {
        // Remove logo if checkbox is checked
        if (!empty($settings['site_logo'])) {
            $oldLogo = '../' . ltrim($settings['site_logo'], '/');
            if (file_exists($oldLogo)) {
                unlink($oldLogo);
            }
        }
        $settingsData['site_logo'] = '';
    } else {
        // Keep existing logo if no new one uploaded
        $settingsData['site_logo'] = $settings['site_logo'] ?? '';
    }

    if (updateSettings($settingsData)) {
        $_SESSION['success_message'] = 'Settings updated successfully!';
        header('Location: settings.php');
        exit;
    } else {
        $error = 'Failed to update settings';
    }
}

// Set page title and breadcrumb
$page_title = "Site Settings";
$breadcrumb = [
    ['title' => 'Settings', 'active' => true]
];

require_once 'includes/header.php';
?>

<div class="container-fluid">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="card admin-card">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Site Settings</h5>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <!-- Site Info -->
                    <div class="col-md-12">
                        <h6 class="border-bottom pb-2 mb-3">Site Information</h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="site_name" class="form-label">Site Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="site_name" name="site_name" 
                               value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="site_logo" class="form-label">Site Logo</label>
                        <?php if (!empty($settings['site_logo'])): ?>
                            <div class="mb-3">
                                <img src="<?= BASE_URL ?><?= $settings['site_logo'] ?>" alt="Current logo" class="img-thumbnail" style="max-height: 80px;">
                                <input type="hidden" name="existing_logo" value="<?= $settings['site_logo'] ?>">
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="remove_logo" name="remove_logo">
                                <label class="form-check-label" for="remove_logo">Remove current logo</label>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                        <div class="form-text">Recommended size: 300x80 pixels</div>
                    </div>
                    
                    <!-- Contact Info -->
                    <div class="col-md-12 mt-4">
                        <h6 class="border-bottom pb-2 mb-3">Contact Information</h6>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="contact_email" class="form-label">Contact Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                               value="<?= htmlspecialchars($settings['contact_email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="contact_phone" class="form-label">Contact Phone</label>
                        <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                               value="<?= htmlspecialchars($settings['contact_phone'] ?? '') ?>">
                    </div>
                    
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars($settings['address'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="col-md-12 mt-4">
                        <h6 class="border-bottom pb-2 mb-3">Social Media</h6>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="facebook_url" class="form-label">Facebook URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-facebook-f"></i></span>
                            <input type="url" class="form-control" id="facebook_url" name="facebook_url" 
                                   value="<?= htmlspecialchars($settings['facebook_url'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="twitter_url" class="form-label">Twitter URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                            <input type="url" class="form-control" id="twitter_url" name="twitter_url" 
                                   value="<?= htmlspecialchars($settings['twitter_url'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label for="instagram_url" class="form-label">Instagram URL</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fab fa-instagram"></i></span>
                            <input type="url" class="form-control" id="instagram_url" name="instagram_url" 
                                   value="<?= htmlspecialchars($settings['instagram_url'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <!-- Submit -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>