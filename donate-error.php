<?php
session_start();
$error = $_SESSION['donation_error'] ?? 'Unknown error occurred';
unset($_SESSION['donation_error']);

$page_title = "Donation Error";
require_once 'includes/header.php';
?>

<section class="min-vh-100 d-flex align-items-center bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="bg-danger bg-opacity-10 rounded-circle p-4 mb-4 mx-auto" style="width: 120px; height: 120px;">
                    <i class="fas fa-times fa-3x text-danger"></i>
                </div>
                
                <h1 class="display-4 fw-bold text-danger mb-4">Payment Failed</h1>
                <p class="lead mb-4">We encountered an issue with your donation.</p>
                
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="/donate.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i> Try Again
                    </a>
                    <a href="/contact.php" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-headset me-2"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>