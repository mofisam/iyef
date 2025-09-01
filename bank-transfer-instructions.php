<?php
session_start();
if (!isset($_SESSION['bank_transfer_details'])) {
    header('Location: donate.php');
    exit;
}

$details = $_SESSION['bank_transfer_details'];
$page_title = "Bank Transfer Instructions";
require_once 'includes/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg">
                    <div class="card-header bg-primary text-white py-3">
                        <h3 class="mb-0 text-center">Bank Transfer Instructions</h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Thank you for choosing bank transfer!</h4>
                            <p class="mb-0">Please use the following bank account details to complete your donation.</p>
                        </div>
                        
                        <!-- Bank Account Details -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-university me-2"></i> Our Bank Account Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Bank Name:</th>
                                                <td>Guaranty Trust Bank (GTB)</td>
                                            </tr>
                                            <tr>
                                                <th>Account Name:</th>
                                                <td><strong>INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION</strong></td>
                                            </tr>
                                            <tr>
                                                <th>Account Number:</th>
                                                <td><strong>0123456789</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>Account Type:</th>
                                                <td>Current Account</td>
                                            </tr>
                                            <tr>
                                                <th>Sort Code:</th>
                                                <td>058</td>
                                            </tr>
                                            <tr>
                                                <th>Bank Branch:</th>
                                                <td>Victoria Island, Lagos</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Donation Details -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Your Donation Details</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <th width="30%">Donor Name:</th>
                                        <td><?= htmlspecialchars($details['donor_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td><?= htmlspecialchars($details['donor_email']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td><?= htmlspecialchars($details['donor_phone'] ?? 'Not provided') ?></td>
                                    </tr>
                                    <tr>
                                        <th>Amount:</th>
                                        <td><strong>₦<?= number_format($details['amount'], 2) ?></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Instructions -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-circle me-2"></i> Important Instructions:</h6>
                            <ol class="mb-0">
                                <li>Transfer <strong>exactly ₦<?= number_format($details['amount'], 2) ?></strong> to our bank account</li>
                                <li>Use your name <strong>"<?= htmlspecialchars($details['donor_name']) ?>"</strong> as the transfer reference</li>
                                <li>Keep your transfer receipt for your records</li>
                                <li>Send your transfer receipt to <a href="mailto:finance@iyef.org">finance@iyef.org</a> for confirmation</li>
                                <li>You will receive a donation receipt via email within 24-48 hours</li>
                            </ol>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="/donate.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Donation Page
                            </a>
                            <a href="mailto:finance@iyef.org" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i> Email Transfer Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
// Clear session data after showing
unset($_SESSION['bank_transfer_details']);
require_once 'includes/footer.php'; 
?>