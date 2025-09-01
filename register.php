<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/users.php';

// Redirect logged-in users to their profile
if (isset($_SESSION['user_id'])) {
    header('Location: /profile.php');
    exit;
}

$errors = [];
$formData = [
    'full_name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $formData = [
        'full_name' => trim($_POST['full_name']),
        'email' => trim($_POST['email']),
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password']
    ];

    // Validate full name
    if (empty($formData['full_name'])) {
        $errors['full_name'] = 'Full name is required';
    } elseif (strlen($formData['full_name']) < 3) {
        $errors['full_name'] = 'Full name must be at least 3 characters';
    }

    // Validate email
    if (empty($formData['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    // Validate password
    if (empty($formData['password'])) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($formData['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    // Validate password confirmation
    if ($formData['password'] !== $formData['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $registrationData = [
            'full_name' => $formData['full_name'],
            'email' => $formData['email'],
            'password' => $formData['password'],
            'role_id' => 2 // Default role for regular users
        ];

        $result = registerUser($registrationData);

        if ($result['status'] === 'success') {
            // Registration successful - log the user in
            $loginResult = loginUser($formData['email'], $formData['password']);
            
            if ($loginResult['status'] === 'success') {
                $_SESSION['user_id'] = $loginResult['user']['id'];
                $_SESSION['user_role'] = getUserRoleName($loginResult['user']['role_id']);
                
                // Set success message and redirect
                $_SESSION['success_message'] = 'Registration successful! Welcome to IYEF.';
                header('Location: /profile.php');
                exit;
            } else {
                $errors['general'] = 'Registration successful but automatic login failed. Please try logging in.';
            }
        } else {
            $errors['general'] = $result['message'];
        }
    }
}

$page_title = "Create an Account";
require_once 'includes/header.php';
?>

<!-- Registration Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="text-center mb-4">Join INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION</h2>
                        <p class="text-center text-muted mb-4">Create your account to access programs, events, and more</p>
                        
                        <?php if (isset($errors['general'])): ?>
                            <div class="alert alert-danger"><?= $errors['general'] ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" novalidate>
                            <div class="row g-3">
                                <!-- Full Name -->
                                <div class="col-12">
                                    <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                           id="full_name" name="full_name" value="<?= htmlspecialchars($formData['full_name']) ?>" required>
                                    <?php if (isset($errors['full_name'])): ?>
                                        <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Email -->
                                <div class="col-12">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                           id="email" name="email" value="<?= htmlspecialchars($formData['email']) ?>" required>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Password -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
                                           id="password" name="password" required>
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                    <?php endif; ?>
                                    <div class="form-text">Must be at least 8 characters</div>
                                </div>
                                
                                <!-- Confirm Password -->
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" 
                                           id="confirm_password" name="confirm_password" required>
                                    <?php if (isset($errors['confirm_password'])): ?>
                                        <div class="invalid-feedback"><?= $errors['confirm_password'] ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Terms Checkbox -->
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>" 
                                               type="checkbox" id="terms" name="terms" required>
                                        <label class="form-check-label" for="terms">
                                            I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and <a href="privacy.php" target="_blank">Privacy Policy</a>
                                        </label>
                                        <?php if (isset($errors['terms'])): ?>
                                            <div class="invalid-feedback"><?= $errors['terms'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Create Account</button>
                                </div>
                                
                                <!-- Login Link -->
                                <div class="col-12 text-center mt-3">
                                    <p class="mb-0">Already have an account? <a href="login.php">Log in here</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="bg-light py-5">
    <div class="container">
        <h3 class="text-center mb-4">Why Register With IYEF?</h3>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h5>Program Registration</h5>
                        <p class="mb-0">Sign up for our youth empowerment programs and training sessions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-ticket-alt fa-2x"></i>
                        </div>
                        <h5>Event Access</h5>
                        <p class="mb-0">Register for workshops, seminars, and special events</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 mb-3 mx-auto" style="width: 70px; height: 70px;">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                        <h5>Stay Updated</h5>
                        <p class="mb-0">Get notifications about new opportunities and resources</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>