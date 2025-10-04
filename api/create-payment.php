<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once '../config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate API Key
$apiKey = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (empty($apiKey)) {
    http_response_code(401);
    echo json_encode(['error' => 'API key required']);
    exit;
}

// Validate required fields
$required = ['amount', 'currency', 'customer_email'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
}

// Generate transaction ID
$transactionId = 'TXN' . time() . rand(1000, 9999);

// Get merchant ID from API key (simplified)
$merchantId = 'MERCH-12345';

// Prepare transaction data
$amount = floatval($input['amount']);
$currency = $input['currency'];
$customerEmail = $input['customer_email'];
$customerPhone = $input['customer_phone'] ?? null;
$description = $input['description'] ?? '';
$paymentMethod = $input['payment_method'] ?? 'card';
$redirectUrl = $input['redirect_url'] ?? '';
$webhookUrl = $input['webhook_url'] ?? '';
$metadata = json_encode($input['metadata'] ?? []);

// Calculate fees (2.5% + ₹3)
$feeAmount = ($amount * 0.025) + 3;
$taxAmount = $feeAmount * 0.18; // 18% GST
$netAmount = $amount - $feeAmount - $taxAmount;

// Insert transaction
$stmt = $conn->prepare("
    INSERT INTO transactions (
        transaction_id, merchant_id, customer_email, customer_phone,
        amount, currency, payment_method, status, description,
        redirect_url, webhook_url, metadata, fee_amount, tax_amount, net_amount,
        ip_address, user_agent
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$ipAddress = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];

$stmt->bind_param(
    'ssssdssssssddss',
    $transactionId, $merchantId, $customerEmail, $customerPhone,
    $amount, $currency, $paymentMethod, $description,
    $redirectUrl, $webhookUrl, $metadata,
    $feeAmount, $taxAmount, $netAmount,
    $ipAddress, $userAgent
);

if ($stmt->execute()) {
    // Generate checkout URL
    $checkoutUrl = "https://payme-gateway.lindy.site/checkout.html?txn=$transactionId";
    
    // Return response
    echo json_encode([
        'success' => true,
        'transaction_id' => $transactionId,
        'checkout_url' => $checkoutUrl,
        'amount' => $amount,
        'currency' => $currency,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create transaction']);
}

$stmt->close();
$conn->close();
?>
