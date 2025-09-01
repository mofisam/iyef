<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/donations.php';
require_once 'config/paystack.php';

// Handle donation form submission BEFORE any output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donationData = [
        'amount' => floatval($_POST['amount']),
        'currency' => $_POST['currency'] ?? 'NGN',
        'payment_method' => $_POST['payment_method'],
        'is_recurring' => isset($_POST['is_recurring']) ? 1 : 0,
    ];
    
    $errors = [];
    
    // Validate inputs
    if ($donationData['amount'] <= 0) {
        $errors['amount'] = 'Please enter a valid donation amount';
    }
    
    // Only validate donor info for online payments
    if ($donationData['payment_method'] === 'paystack') {
        $donorData = [
            'full_name' => trim($_POST['full_name']),
            'email' => trim($_POST['email']),
            'phone' => trim($_POST['phone'] ?? ''),
        ];
        
        if (empty($donorData['full_name'])) {
            $errors['full_name'] = 'Please enter your full name';
        }
        if (empty($donorData['email']) || !filter_var($donorData['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }
    }
    
    if (empty($errors)) {
        if ($donationData['payment_method'] === 'bank_transfer') {
            // Store bank transfer details in session
            $_SESSION['bank_transfer_details'] = [
                'amount' => $donationData['amount'],
                'currency' => $donationData['currency'],
            ];
            
            // Set flag to show bank details
            $showBankDetails = true;
            
        } else {
            // Handle Paystack payment
            $metadata = [
                'donor_name' => $donorData['full_name'],
                'donor_phone' => $donorData['phone'],
                'custom_fields' => [
                    [
                        'display_name' => 'Donor Name',
                        'variable_name' => 'donor_name',
                        'value' => $donorData['full_name']
                    ]
                ]
            ];
            
            $redirectUrl = initializePaystackTransaction(
                $donorData['email'],
                $donationData['amount'],
                $donationData['currency'],
                $metadata
            );
            
            if ($redirectUrl) {
                $_SESSION['donation_data'] = [
                    'amount' => $donationData['amount'],
                    'currency' => $donationData['currency'],
                    'donor_email' => $donorData['email'],
                    'donor_name' => $donorData['full_name'],
                    'donor_phone' => $donorData['phone'],
                    'is_recurring' => $donationData['is_recurring'],
                    'payment_method' => $donationData['payment_method']
                ];
                
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                // Store error in session to display after headers are sent
                $_SESSION['form_errors'] = ['general' => 'Unable to initialize payment. Please try again or use bank transfer.'];
                $_SESSION['form_data'] = $_POST;
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    } else {
        // Store errors and form data in session
        $_SESSION['form_errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Check for stored errors and form data from redirects
$errors = $_SESSION['form_errors'] ?? [];
$formData = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);

$page_title = "Support Our Cause - IYEF";
require_once 'includes/header.php';

// Get statistics
$totalDonations = getTotalDonations();
$donorsCount = getDonorsCount();
$programsSupported = fetchSingle("SELECT COUNT(*) as count FROM programs WHERE end_date >= CURDATE() OR end_date IS NULL")['count'] ?? 8;
?>

<!-- Donation Form Section -->
<section id="donation-form" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg">
                    <div class="card-body p-0">
                        <div class="row g-0">
                            <!-- Donation Options (left side) -->
                            <div class="col-lg-5 bg-primary text-white p-5">
                                <h3 class="mb-4">Choose Your Impact</h3>
                                
                                <div class="mb-4">
                                    <h4 class="h5 mb-3">Quick Donation Amounts</h4>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button type="button" class="btn btn-outline-light amount-btn" data-amount="1000">₦1,000</button>
                                        <button type="button" class="btn btn-outline-light amount-btn" data-amount="5000">₦5,000</button>
                                        <button type="button" class="btn btn-outline-light amount-btn" data-amount="10000">₦10,000</button>
                                        <button type="button" class="btn btn-outline-light amount-btn" data-amount="50000">₦50,000</button>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <h4 class="h5 mb-3">Recurring Donations</h4>
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="recurring-toggle" name="is_recurring" <?= isset($formData['is_recurring']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="recurring-toggle">
                                            Make this a monthly donation
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h4 class="h5 mb-3">Why Give Monthly?</h4>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">✓ Sustainable impact</li>
                                        <li class="mb-2">✓ Continuous support</li>
                                        <li class="mb-2">✓ Easy to manage</li>
                                        <li class="mb-2">✓ Greater long-term impact</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Donation Form (right side) -->
                            <div class="col-lg-7 p-5">
                                <h3 class="mb-4">Make a Donation</h3>
                                
                                <?php if (isset($errors['general'])): ?>
                                    <div class="alert alert-danger"><?= $errors['general'] ?></div>
                                <?php endif; ?>
                                
                                <form method="POST" id="donationForm">
                                    <!-- Donation Amount -->
                                    <div class="mb-4">
                                        <label for="amount" class="form-label fw-bold">Donation Amount (NGN) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₦</span>
                                            <input type="number" class="form-control form-control-lg <?= isset($errors['amount']) ? 'is-invalid' : '' ?>" 
                                                   id="amount" name="amount" min="100" step="100" 
                                                   value="<?= htmlspecialchars($formData['amount'] ?? '5000') ?>" required>
                                            <?php if (isset($errors['amount'])): ?>
                                                <div class="invalid-feedback"><?= $errors['amount'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <input type="hidden" name="currency" value="NGN">
                                        <div class="form-text">Minimum donation: ₦100</div>
                                    </div>
                                    
                                    <!-- Payment Method -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="radio" class="btn-check" name="payment_method" id="paystack" value="paystack" <?= (!isset($formData['payment_method']) || $formData['payment_method'] === 'paystack') ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-primary w-100" for="paystack">
                                                    <i class="fas fa-credit-card me-1"></i> Online Payment
                                                </label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" class="btn-check" name="payment_method" id="bank" value="bank_transfer" <?= isset($formData['payment_method']) && $formData['payment_method'] === 'bank_transfer' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-primary w-100" for="bank">
                                                    <i class="fas fa-university me-1"></i> Bank Transfer
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment Method Details -->
                                    <div id="paystack-details" class="payment-method mb-4" style="<?= (isset($formData['payment_method']) && $formData['payment_method'] === 'bank_transfer') ? 'display: none;' : '' ?>">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Secure online payment via Paystack (Cards, Bank Transfer, USSD, etc.)
                                        </div>
                                        
                                        <!-- Donor Information (Only for Online Payments) -->
                                        <div class="mb-4">
                                            <h5 class="mb-3">Your Information</h5>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input type="text" class="form-control <?= isset($errors['full_name']) ? 'is-invalid' : '' ?>" 
                                                           name="full_name" placeholder="Full Name *" 
                                                           value="<?= htmlspecialchars($formData['full_name'] ?? '') ?>" required>
                                                    <?php if (isset($errors['full_name'])): ?>
                                                        <div class="invalid-feedback"><?= $errors['full_name'] ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                                           name="email" placeholder="Email Address *" 
                                                           value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                                                    <?php if (isset($errors['email'])): ?>
                                                        <div class="invalid-feedback"><?= $errors['email'] ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-6">
                                                    <input type="tel" class="form-control" name="phone" 
                                                           placeholder="Phone Number" 
                                                           value="<?= htmlspecialchars($formData['phone'] ?? '') ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="bank_transfer-details" class="payment-method mb-4" style="<?= (isset($formData['payment_method']) && $formData['payment_method'] === 'bank_transfer') ? 'display: block;' : 'display: none;' ?>">
                                        <div class="alert alert-success">
                                            <h5 class="alert-heading">Bank Transfer Details</h5>
                                            <div class="bank-details mt-3">
                                                <div class="d-flex justify-content-between border-bottom py-2">
                                                    <strong>Bank Name:</strong>
                                                    <span>Guaranty Trust Bank (GTB)</span>
                                                </div>
                                                <div class="d-flex justify-content-between border-bottom py-2">
                                                    <strong>Account Name:</strong>
                                                    <span>INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION</span>
                                                </div>
                                                <div class="d-flex justify-content-between border-bottom py-2">
                                                    <strong>Account Number:</strong>
                                                    <span>0123456789</span>
                                                </div>
                                            </div>
                                            <hr>
                                            <p class="mb-0">Please use your name as the payment reference when transferring.</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Submit Button -->
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="fas fa-lock me-2"></i> 
                                        <span id="submit-text"><?= (isset($formData['payment_method']) && $formData['payment_method'] === 'bank_transfer') ? 'Confirm Donation' : 'Proceed to Payment' ?></span>
                                    </button>
                                    
                                    <div class="text-center mt-3">
                                        <img src="assets/images/paystack.svg" alt="Paystack Secure Payments" height="100" class="me-2">
                                        <img src="assets/images/ssl-secure.png" alt="SSL Secure" height="40">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick amount buttons
    document.querySelectorAll('.amount-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('amount').value = this.getAttribute('data-amount');
        });
    });
    
    // Payment method selection
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const paymentSections = document.querySelectorAll('.payment-method');
    const submitText = document.getElementById('submit-text');
    
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Hide all payment sections
            paymentSections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Show selected payment section
            const selectedSection = document.getElementById(this.value + '-details');
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }
            
            // Update submit button text
            if (this.value === 'bank_transfer') {
                submitText.textContent = 'Confirm Donation';
            } else {
                submitText.textContent = 'Proceed to Payment';
            }
            
            // Toggle required attribute on donor info fields
            const donorFields = document.querySelectorAll('#paystack-details input[name="full_name"], #paystack-details input[name="email"]');
            if (this.value === 'paystack') {
                donorFields.forEach(field => field.setAttribute('required', 'required'));
            } else {
                donorFields.forEach(field => field.removeAttribute('required'));
            }
        });
    });
    
    // Form validation
    const form = document.getElementById('donationForm');
    form.addEventListener('submit', function(e) {
        const amount = parseFloat(document.getElementById('amount').value);
        if (amount < 100) {
            e.preventDefault();
            alert('Minimum donation amount is ₦100');
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>