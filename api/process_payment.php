<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$userId = intval($_POST['user_id'] ?? 0);
$amount = floatval($_POST['amount'] ?? 0);
$currency = sanitizeInput($_POST['currency'] ?? 'USD');
$cardHolderName = sanitizeInput($_POST['card_holder_name'] ?? '');
$cardNumber = sanitizeInput($_POST['card_number'] ?? '');
$expiryDate = sanitizeInput($_POST['expiry_date'] ?? '');
$cvv = sanitizeInput($_POST['cvv'] ?? '');
$billingAddress = sanitizeInput($_POST['billing_address'] ?? '');
$billingCity = sanitizeInput($_POST['billing_city'] ?? '');
$billingZip = sanitizeInput($_POST['billing_zip'] ?? '');
$billingCountry = sanitizeInput($_POST['billing_country'] ?? '');

// Validation
if ($userId <= 0) {
    jsonResponse(false, 'Invalid user');
}

if ($amount <= 0) {
    jsonResponse(false, 'Invalid amount');
}

if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
    jsonResponse(false, 'Invalid card number');
}

if (!preg_match('/^\d{2}\/\d{2}$/', $expiryDate)) {
    jsonResponse(false, 'Invalid expiry date');
}

if (strlen($cvv) < 3 || strlen($cvv) > 4) {
    jsonResponse(false, 'Invalid CVV');
}

$conn = getDBConnection();
if (!$conn) {
    jsonResponse(false, 'Database connection failed');
}

// Verify user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, 'User not found');
}

// Detect card type
$cardType = detectCardType($cardNumber);

// Get last 4 digits of card
$cardLast4 = substr($cardNumber, -4);

// Generate transaction ID
$transactionId = generateTransactionId();

// Get IP address and user agent
$ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// Simulate payment processing
// In a real system, you would integrate with a payment processor here
$paymentStatus = simulatePaymentProcessing($cardNumber, $expiryDate, $cvv, $amount);

// Insert transaction
$stmt = $conn->prepare("INSERT INTO transactions (user_id, transaction_id, card_number_last4, card_holder_name, card_type, amount, currency, status, payment_method, billing_address, billing_city, billing_country, billing_zip, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'card', ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("issssdssssssss", 
    $userId, 
    $transactionId, 
    $cardLast4, 
    $cardHolderName, 
    $cardType, 
    $amount, 
    $currency, 
    $paymentStatus,
    $billingAddress,
    $billingCity,
    $billingCountry,
    $billingZip,
    $ipAddress,
    $userAgent
);

if ($stmt->execute()) {
    jsonResponse(true, 'Payment processed successfully', [
        'transaction_id' => $transactionId,
        'status' => $paymentStatus,
        'amount' => $amount,
        'currency' => $currency
    ]);
} else {
    jsonResponse(false, 'Payment processing failed');
}

$stmt->close();
$conn->close();

// Helper functions
function detectCardType($cardNumber) {
    $firstDigit = substr($cardNumber, 0, 1);
    $firstTwoDigits = substr($cardNumber, 0, 2);
    
    if ($firstDigit == '4') {
        return 'Visa';
    } elseif ($firstTwoDigits >= 51 && $firstTwoDigits <= 55) {
        return 'Mastercard';
    } elseif ($firstTwoDigits == 34 || $firstTwoDigits == 37) {
        return 'American Express';
    } elseif ($firstTwoDigits == 60 || $firstTwoDigits == 65) {
        return 'Discover';
    } else {
        return 'Unknown';
    }
}

function simulatePaymentProcessing($cardNumber, $expiryDate, $cvv, $amount) {
    // Simulate payment processing
    // In production, integrate with real payment gateway
    
    // For demo: 90% success rate
    $random = rand(1, 100);
    
    if ($random <= 90) {
        return 'success';
    } elseif ($random <= 95) {
        return 'pending';
    } else {
        return 'failed';
    }
}
?>
