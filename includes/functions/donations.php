<?php
require_once '../config/base_link.php';
require_once($_SERVER['DOCUMENT_ROOT'] .  BASE_FILE .'config/db_functions.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'config/paystack.php');
require_once($_SERVER['DOCUMENT_ROOT'] . BASE_FILE . 'vendor/autoload.php'); // If using composer

/**
 * Initialize Paystack transaction
 */
function initializePaystackTransaction($email, $amount, $currency = 'NGN', $metadata = []) {
    try {
        // Use cURL instead of the Paystack library for better compatibility
        $url = PAYSTACK_BASE_URL . '/transaction/initialize';
        
        $fields = [
            'email' => $email,
            'amount' => $amount * 100, // Convert to kobo
            'currency' => $currency,
            'metadata' => $metadata,
            'callback_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/donate-verify.php'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . getPaystackKey(),
            'Content-Type: application/json',
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("Paystack cURL Error: " . $error);
            return false;
        }
        
        $result = json_decode($response);
        
        if ($result->status && isset($result->data->authorization_url)) {
            return $result->data->authorization_url;
        } else {
            error_log("Paystack Error: " . $response);
            return false;
        }
        
    } catch (Exception $e) {
        error_log("Paystack initialization error: " . $e->getMessage());
        return false;
    }
}

/**
 * Verify Paystack transaction
 */
function verifyPaystackTransaction($reference) {
    try {
        // Check if the constant is defined
        if (!defined('PAYSTACK_BASE_URL')) {
            error_log("PAYSTACK_BASE_URL constant is not defined");
            return ['status' => 'error', 'message' => 'Configuration error'];
        }
        
        $url = PAYSTACK_BASE_URL . '/transaction/verify/' . $reference;
        
        error_log("Verifying transaction with URL: $url");
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . getPaystackKey(),
        ]);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        error_log("Paystack verification response - HTTP Code: $httpCode, Response: $response");
        
        if ($error) {
            error_log("cURL Error: " . $error);
            return ['status' => 'error', 'message' => $error];
        }
        
        $result = json_decode($response);
        
        if (!$result) {
            error_log("Failed to decode JSON response: " . $response);
            return ['status' => 'error', 'message' => 'Invalid response from payment gateway'];
        }
        
        // Check if the response has the expected structure
        if (isset($result->status) && $result->status === true) {
            if (isset($result->data->status) && $result->data->status === 'success') {
                return ['status' => 'success', 'data' => $result->data];
            } else {
                error_log("Transaction not successful. Status: " . ($result->data->status ?? 'unknown'));
                return ['status' => 'failed', 'message' => 'Payment not successful. Status: ' . ($result->data->status ?? 'unknown')];
            }
        } else {
            error_log("Paystack API returned false status. Message: " . ($result->message ?? 'No message'));
            return ['status' => 'failed', 'message' => $result->message ?? 'Payment verification failed'];
        }
        
    } catch (Exception $e) {
        error_log("Paystack verification error: " . $e->getMessage());
        return ['status' => 'error', 'message' => $e->getMessage()];
    }
}

/**
 * Record donation in database
 */
function recordDonation($donationData) {
    try {
        return insertRecord('donations', $donationData) !== false;
    } catch (Exception $e) {
        error_log("Donation recording error: " . $e->getMessage());
        return false;
    }
}
/**
 * Send donation receipt email
 */
function sendDonationReceipt($email, $donationData, $transactionId) {
    try {
        $subject = "Thank you for your donation to IYEF";
        $message = "
            Dear Donor,
            
            Thank you for your generous donation of {$donationData['currency']} " . 
            number_format($donationData['amount'], 2) . ".
            
            Transaction Reference: {$donationData['paystack_reference']}
            Date: " . date('F j, Y') . "
            
            Your support helps us empower youth through education and mentorship programs.
            
            INDEFATIGABLE YOUTH EMPOWERMENT FOUNDATION
            EIN: 12-3456789 | Registered Charity: #CH12345
            
            This email serves as your official receipt for tax purposes.
        ";

        // Simulated email sending
        error_log("Receipt email to: {$email} - Amount: {$donationData['amount']} {$donationData['currency']}");
        return true;
    } catch (Exception $e) {
        error_log("Email sending error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get total donations amount
 */
function getTotalDonations() {
    $result = fetchSingle("SELECT SUM(amount) as total FROM donations WHERE status = 'completed' OR receipt_sent = 1");
    return $result ? (float)$result['total'] : 0;
}

/**
 * Get donors count
 */
function getDonorsCount() {
    $result = fetchSingle("
        SELECT COUNT(DISTINCT COALESCE(user_id, donor_email)) as count 
        FROM donations 
        WHERE status = 'completed'
    ");
    return $result ? $result['count'] : 0;
}

/**
 * Get donations by user
 */
function getUserDonations($userId) {
    return fetchAll("
        SELECT * FROM donations
        WHERE user_id = ?
        ORDER BY donated_at DESC
    ", [$userId]);
}

/**
 * Process donation payment (simulated)
 */
function processDonationPayment($donationData, $donorData) {
    try {
        usleep(500000); // Simulate delay
        $transactionId = 'TXN_' . strtoupper(uniqid()) . '_' . time();

        if (rand(1, 100) <= 95) {
            return $transactionId;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log("Payment processing error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all donations (for admin)
 */
function getAllDonations($page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;

    $donations = fetchAll("
        SELECT d.*, u.full_name, u.email
        FROM donations d
        LEFT JOIN users u ON d.user_id = u.id
        ORDER BY d.donated_at DESC
        LIMIT ? OFFSET ?
    ", [$perPage, $offset]);

    $total = fetchSingle("SELECT COUNT(*) as total FROM donations")['total'];
    $totalPages = ceil($total / $perPage);

    return [
        'donations' => $donations,
        'pagination' => [
            'current_page' => $page,
            'per_page' => $perPage,
            'total_items' => $total,
            'total_pages' => $totalPages,
            'has_prev' => $page > 1,
            'has_next' => $page < $totalPages
        ]
    ];
}
