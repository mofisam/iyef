<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/users.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $result = loginUser($email, $password);
        
        if ($result['status'] === 'success') {
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['user_role'] = getUserRoleName($result['user']['role_id']);
            
            // Redirect to admin if user is admin
            if ($_SESSION['user_role'] === 'admin') {
                header('Location: index.php');
                exit;
            } else {
                // Regular users are redirected to their profile
                header('Location: profile.php');
                exit;
            }
        } else {
            $error = $result['message'];
        }
    }
}

$page_title = "Admin Login";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    
    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card admin-card shadow-sm">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="IYEF" height="60">
                            <h2 class="mt-3 mb-0">Admin Login</h2>
                            <p class="text-muted">Access your admin dashboard</p>
                        </div>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">Login</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <a href="forgot-password.php" class="text-decoration-none">Forgot Password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>