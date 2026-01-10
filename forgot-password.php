<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/password_reset.php';

// Redirect logged-in users
if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$message = '';
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } else {
        $result = createPasswordResetRequest($email);
        
        if ($result['status'] === 'success') {
            $message = $result['message'];
            $email = ''; // Clear email field
        } else {
            $error = $result['message'];
        }
    }
}

$page_title = "Forgot Password";
require_once 'includes/header.php';
?>

<!-- Forgot Password Form -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Reset Your Password</h2>
                        <p class="text-center text-muted mb-4">
                            Enter your email address and we'll send you a link to reset your password.
                        </p>
                        
                        <!-- Success Message -->
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?= $message ?>
                                <div class="mt-3">
                                    <a href="login.php" class="btn btn-sm btn-outline-primary">Back to Login</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Error Message -->
                        <?php if ($error && !$message): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <!-- Form (only show if no success message) -->
                        <?php if (!$message): ?>
                            <form method="POST">
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= $error ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" value="<?= htmlspecialchars($email) ?>" 
                                           required autofocus>
                                    <div class="form-text">
                                        Enter the email address associated with your account.
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary btn-lg">Send Reset Link</button>
                                </div>
                                
                                <div class="text-center">
                                    <a href="login.php" class="text-decoration-none">
                                        <i class="fas fa-arrow-left"></i> Back to Login
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                        
                        <!-- Help Information -->
                        <div class="mt-4 pt-4 border-top">
                            <h6 class="mb-2"><i class="fas fa-question-circle me-2"></i>Need Help?</h6>
                            <p class="small text-muted mb-0">
                                If you're having trouble resetting your password, please contact our support team.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>