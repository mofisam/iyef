<?php
session_start();
require_once 'config/db.php';
require_once 'includes/functions/donations.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the verification process
error_log("Donation verification started. Reference: " . ($_GET['reference'] ?? 'N/A'));

if (!isset($_GET['reference']) || !isset($_SESSION['donation_data'])) {
    error_log("Missing reference or donation data in session");
    header('Location: donate.php');
    exit;
}

$reference = $_GET['reference'];
$donationData = $_SESSION['donation_data'];

// Log the donation data
error_log("Donation data: " . print_r($donationData, true));

// Verify payment with Paystack
$verification = verifyPaystackTransaction($reference);

// Log the verification result
error_log("Verification result: " . print_r($verification, true));

if ($verification['status'] === 'success') {
    // Payment successful
    $transactionData = $verification['data'];
    
    // Record donation
    $donationRecord = [
        'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
        'donor_email' => $donationData['donor_email'],
        'donor_name' => $donationData['donor_name'],
        'amount' => $donationData['amount'],
        'currency' => $donationData['currency'],
        'transaction_id' => $transactionData->id,
        'paystack_reference' => $reference,
        'is_recurring' => $donationData['is_recurring'],
        'frequency' => 'one-time',
        'status' => 'completed'
    ];
    
    if (recordDonation($donationRecord)) {
        // Send receipt
        sendDonationReceipt($donationData['donor_email'], $donationRecord, $reference);
        
        // Clear session
        unset($_SESSION['donation_data']);
        
        // Redirect to success page
        $_SESSION['donation_success'] = true;
        $_SESSION['donation_amount'] = $donationData['amount'];
        $_SESSION['donation_currency'] = $donationData['currency'];
        $_SESSION['donor_email'] = $donationData['donor_email'];
        
        header('Location: donate-thank-you.php');
        exit;
    } else {
        $error = 'Error recording donation. Please contact support.';
        error_log("Error recording donation for reference: $reference");
    }
} else {
    $error = 'Payment verification failed: ' . ($verification['message'] ?? 'Unknown error');
    error_log("Payment verification failed for reference: $reference - " . ($verification['message'] ?? 'Unknown error'));
}

// If we get here, there was an error
$_SESSION['donation_error'] = $error;
error_log("Redirecting to error page with error: $error");
header('Location: donate-error.php');
exit;