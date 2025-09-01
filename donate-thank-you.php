<?php
session_start();
if (!isset($_SESSION['donation_success'])) {
    header('Location: donate.php');
    exit;
}

$amount = $_SESSION['donation_amount'];
$currency = $_SESSION['donation_currency'];

// Clear session variables
unset($_SESSION['donation_success']);
unset($_SESSION['donation_amount']);
unset($_SESSION['donation_currency']);

$page_title = "Thank You for Your Donation";
require_once 'includes/header.php';
?>

<section class="hero-section bg-success text-white py-5">
    <div class="container py-4">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="bg-white bg-opacity-10 rounded-circle p-4 mb-4 mx-auto" style="width: 100px; height: 100px;">
                    <i class="fas fa-check fa-3x"></i>
                </div>
                <h1 class="display-4 fw-bold mb-4">Thank You!</h1>
                <p class="lead mb-4">Your donation of <?= $currency ?> <?= number_format($amount, 2) ?> has been successfully processed.</p>
                <p class="mb-4">A confirmation email has been sent to your email address with your donation receipt.</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="/" class="btn btn-light btn-lg">Return Home</a>
                    <a href="/programs.php" class="btn btn-outline-light btn-lg">See Our Programs</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <h3 class="mb-4">What Happens Next?</h3>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="bg-light rounded-circle p-3 mb-3 mx-auto" style="width: 80px; height: 80px;">
                                    <i class="fas fa-envelope-open-text fa-2x text-primary"></i>
                                </div>
                                <h5>Email Receipt</h5>
                                <p class="small">You'll receive a donation receipt within 24 hours for tax purposes</p>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light rounded-circle p-3 mb-3 mx-auto" style="width: 80px; height: 80px;">
                                    <i class="fas fa-newspaper fa-2x text-primary"></i>
                                </div>
                                <h5>Impact Report</h5>
                                <p class="small">Receive quarterly updates on how your donation is making a difference</p>
                            </div>
                            <div class="col-md-4">
                                <div class="bg-light rounded-circle p-3 mb-3 mx-auto" style="width: 80px; height: 80px;">
                                    <i class="fas fa-hands-helping fa-2x text-primary"></i>
                                </div>
                                <h5>Continued Support</h5>
                                <p class="small">Join our community of supporters making a lasting impact</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h3 class="mb-4">Share Your Support</h3>
                <p class="mb-4">Help us reach more supporters by sharing your donation experience</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="#" class="btn btn-outline-primary">
                        <i class="fab fa-facebook-f me-2"></i> Share on Facebook
                    </a>
                    <a href="#" class="btn btn-outline-info">
                        <i class="fab fa-twitter me-2"></i> Share on Twitter
                    </a>
                    <a href="#" class="btn btn-outline-success">
                        <i class="fab fa-whatsapp me-2"></i> Share on WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>