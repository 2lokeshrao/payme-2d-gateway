<?php
/**
 * Create Payment Link API
 * Generates payment link for merchants
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

require_once '../config/database.php';
require_once '../config/security.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get API Key from header
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';

if (empty($api_key)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'API Key is required']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
$required_fields = ['amount', 'customer_email', 'payment_method'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
        exit;
    }
}

// Sanitize inputs
$amount = floatval($data['amount']);
$customer_name = Security::sanitizeInput($data['customer_name'] ?? '');
$customer_email = Security::sanitizeInput($data['customer_email']);
$customer_phone = Security::sanitizeInput($data['customer_phone'] ?? '');
$payment_method = Security::sanitizeInput($data['payment_method']);
$description = Security::sanitizeInput($data['description'] ?? '');
$callback_url = Security::sanitizeInput($data['callback_url'] ?? '');
$redirect_url = Security::sanitizeInput($data['redirect_url'] ?? '');

// Validate amount
if ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Amount must be greater than 0']);
    exit;
}

// Validate email
if (!Security::validateEmail($customer_email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid customer email']);
    exit;
}

// Validate payment method
$valid_methods = ['upi', 'crypto', 'bank_transfer', 'card', 'wallet'];
if (!in_array($payment_method, $valid_methods)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
    exit;
}

try {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Verify API Key and get merchant
    $merchant_query = "SELECT m.id, m.merchant_id, m.business_name, m.kyc_status, u.status 
                       FROM merchants m 
                       JOIN users u ON m.user_id = u.id 
                       WHERE m.api_key = :api_key";
    
    $merchant_stmt = $db->prepare($merchant_query);
    $merchant_stmt->bindParam(':api_key', $api_key);
    $merchant_stmt->execute();
    
    $merchant = $merchant_stmt->fetch();
    
    if (!$merchant) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid API Key']);
        exit;
    }
    
    // Check merchant status
    if ($merchant['status'] !== 'active') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Merchant account is not active']);
        exit;
    }
    
    // Check KYC status
    if ($merchant['kyc_status'] !== 'verified') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'KYC verification required']);
        exit;
    }
    
    // Generate transaction ID
    $transaction_id = 'TXN' . strtoupper(bin2hex(random_bytes(8)));
    
    // Get client IP
    $ip_address = Security::getClientIP();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Insert transaction
    $insert_query = "INSERT INTO transactions 
                     (transaction_id, merchant_id, payment_method, amount, customer_name, 
                      customer_email, customer_phone, description, callback_url, redirect_url, 
                      ip_address, user_agent, payment_status) 
                     VALUES 
                     (:transaction_id, :merchant_id, :payment_method, :amount, :customer_name, 
                      :customer_email, :customer_phone, :description, :callback_url, :redirect_url, 
                      :ip_address, :user_agent, 'pending') 
                     RETURNING id";
    
    $stmt = $db->prepare($insert_query);
    $stmt->bindParam(':transaction_id', $transaction_id);
    $stmt->bindParam(':merchant_id', $merchant['id']);
    $stmt->bindParam(':payment_method', $payment_method);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':customer_name', $customer_name);
    $stmt->bindParam(':customer_email', $customer_email);
    $stmt->bindParam(':customer_phone', $customer_phone);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':callback_url', $callback_url);
    $stmt->bindParam(':redirect_url', $redirect_url);
    $stmt->bindParam(':ip_address', $ip_address);
    $stmt->bindParam(':user_agent', $user_agent);
    $stmt->execute();
    
    $db_transaction_id = $stmt->fetchColumn();
    
    // Generate payment link
    $payment_link = "https://hungry-nights-cough.lindy.site/payment.html?txn=" . $transaction_id;
    
    // Log API request
    $log_query = "INSERT INTO api_logs (merchant_id, endpoint, method, request_data, status_code, ip_address) 
                  VALUES (:merchant_id, :endpoint, :method, :request_data, :status_code, :ip_address)";
    
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(':merchant_id', $merchant['id']);
    
    $endpoint = '/api/payment/create_payment_link';
    $method = 'POST';
    $request_data = json_encode($data);
    $status_code = 201;
    
    $log_stmt->bindParam(':endpoint', $endpoint);
    $log_stmt->bindParam(':method', $method);
    $log_stmt->bindParam(':request_data', $request_data);
    $log_stmt->bindParam(':status_code', $status_code);
    $log_stmt->bindParam(':ip_address', $ip_address);
    $log_stmt->execute();
    
    // Success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Payment link created successfully',
        'data' => [
            'transaction_id' => $transaction_id,
            'payment_link' => $payment_link,
            'amount' => $amount,
            'currency' => 'INR',
            'payment_method' => $payment_method,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Payment Link Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create payment link']);
}
?>
