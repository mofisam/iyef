<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/password_reset.php';

// Redirect logged-in users
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';
$message = '';
$valid_token = false;

// Validate token
if ($token) {
    $reset_data = validateResetToken($token);
    if ($reset_data) {
        $valid_token = true;
    } else {
        $error = 'Invalid or expired password reset link. Please request a new reset link.';
    }
} else {
    $error = 'No reset token provided.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in both password fields';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $result = resetPasswordWithToken($token, $new_password);
        
        if ($result['status'] === 'success') {
            $message = $result['message'];
            $valid_token = false; // Token is now used
        } else {
            $error = $result['message'];
        }
    }
}

$page_title = "Reset Password";
require_once 'includes/header.php';
?>

<!-- Reset Password Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="IYEF" height="60" class="mb-3">
                            <h2>Set New Password</h2>
                            <p class="text-muted">Create a new password for your account</p>
                        </div>
                        
                        <!-- Error Message -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                                <?php if (strpos($error, 'expired') !== false || strpos($error, 'Invalid') !== false): ?>
                                    <div class="mt-2">
                                        <a href="forgot-password.php" class="btn btn-sm btn-outline-primary">
                                            Request New Reset Link
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Success Message -->
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?= $message ?>
                                <div class="mt-3">
                                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Reset Form (only show if token is valid and no success) -->
                        <?php if ($valid_token && !$message): ?>
                            <form method="POST">
                                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">
                                        Must be at least 8 characters long. Use a mix of letters, numbers, and symbols for better security.
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="confirm_password" class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <!-- Password Strength Indicator -->
                                <div class="mb-4">
                                    <div class="progress" style="height: 5px;">
                                        <div id="password-strength-bar" class="progress-bar" role="progressbar"></div>
                                    </div>
                                    <small id="password-strength-text" class="form-text"></small>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">Reset Password</button>
                                </div>
                                
                                <div class="text-center">
                                    <a href="forgot-password.php" class="text-decoration-none">
                                        <i class="fas fa-arrow-left"></i> Back to Forgot Password
                                    </a>
                                </div>
                            </form>
                            
                            <!-- Password Requirements -->
                            <div class="mt-4 p-3 border rounded bg-light">
                                <h6 class="mb-2"><i class="fas fa-shield-alt me-2"></i>Password Requirements:</h6>
                                <ul class="small mb-0">
                                    <li>At least 8 characters long</li>
                                    <li>Use uppercase and lowercase letters</li>
                                    <li>Include at least one number</li>
                                    <li>Consider using special characters (@, #, $, etc.)</li>
                                </ul>
                            </div>
                        <?php elseif (!$valid_token && !$message && !$error): ?>
                            <!-- Loading state -->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Validating reset token...</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Password Strength Checker Script -->
<?php if ($valid_token && !$message): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthBar = document.getElementById('password-strength-bar');
    const strengthText = document.getElementById('password-strength-text');
    const submitBtn = document.getElementById('submit-btn');
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        let strength = 0;
        
        // Length check
        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 10;
        
        // Character type checks
        if (/[a-z]/.test(password)) strength += 20; // lowercase
        if (/[A-Z]/.test(password)) strength += 20; // uppercase
        if (/[0-9]/.test(password)) strength += 20; // numbers
        if (/[^a-zA-Z0-9]/.test(password)) strength += 15; // special chars
        
        // Update progress bar
        strength = Math.min(strength, 100);
        strengthBar.style.width = strength + '%';
        
        // Update colors and text
        if (strength < 40) {
            strengthBar.className = 'progress-bar bg-danger';
            strengthText.textContent = 'Weak password';
            strengthText.className = 'form-text text-danger';
        } else if (strength < 70) {
            strengthBar.className = 'progress-bar bg-warning';
            strengthText.textContent = 'Moderate password';
            strengthText.className = 'form-text text-warning';
        } else {
            strengthBar.className = 'progress-bar bg-success';
            strengthText.textContent = 'Strong password';
            strengthText.className = 'form-text text-success';
        }
        
        // Enable/disable submit button based on minimum strength
        if (strength >= 40) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    });
    
    // Trigger initial check
    passwordInput.dispatchEvent(new Event('input'));
});
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>