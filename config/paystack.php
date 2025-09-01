<?php
// Paystack Configuration
define('PAYSTACK_SECRET_KEY', 'sk_test_your_secret_key_here');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_your_public_key_here');
define('PAYSTACK_BASE_URL', 'https://api.paystack.co');

// Set to true for production
define('PAYSTACK_LIVE_MODE', false);

// Supported currencies (Paystack supports NGN, USD, GHS, ZAR)
$supportedCurrencies = [
    'NGN' => 'Nigerian Naira',
    'USD' => 'US Dollar',
    'GHS' => 'Ghanaian Cedi',
    'ZAR' => 'South African Rand'
];

// Conversion rates (you might want to get these from an API)
$currencyRates = [
    'USD' => 1,
    'NGN' => 1500, // Example rate, use actual rates
    'GHS' => 12,
    'ZAR' => 18
];

// Get appropriate Paystack key
function getPaystackKey() {
    return PAYSTACK_LIVE_MODE ? PAYSTACK_SECRET_KEY : 'sk_test_41008269e1c6f30a68e89226ebe8bf9628c9e3ae';
}

function getPaystackPublicKey() {
    return PAYSTACK_LIVE_MODE ? PAYSTACK_PUBLIC_KEY : 'pk_test_3d8772ab51c1407f1302d2fffc114220b0b1d9ee';
}
?>