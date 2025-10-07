<?php
/**
 * Verify Payment API
 * Verifies payment status and updates transaction
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
if (empty($data['transaction_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Transaction ID is required']);
    exit;
}

$transaction_id = Security::sanitizeInput($data['transaction_id']);
$utr_number = Security::sanitizeInput($data['utr_number'] ?? '');
$gateway_transaction_id = Security::sanitizeInput($data['gateway_transaction_id'] ?? '');
$payment_status = Security::sanitizeInput($data['payment_status'] ?? 'success');

// Validate payment status
$valid_statuses = ['success', 'failed', 'pending', 'processing'];
if (!in_array($payment_status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid payment status']);
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
    $merchant_query = "SELECT m.id, m.merchant_id, m.business_name, m.commission_rate 
                       FROM merchants m 
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
    
    // Get transaction
    $txn_query = "SELECT * FROM transactions 
                  WHERE transaction_id = :transaction_id AND merchant_id = :merchant_id";
    
    $txn_stmt = $db->prepare($txn_query);
    $txn_stmt->bindParam(':transaction_id', $transaction_id);
    $txn_stmt->bindParam(':merchant_id', $merchant['id']);
    $txn_stmt->execute();
    
    $transaction = $txn_stmt->fetch();
    
    if (!$transaction) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Transaction not found']);
        exit;
    }
    
    // Check if already processed
    if ($transaction['payment_status'] === 'success') {
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Transaction already verified',
            'data' => [
                'transaction_id' => $transaction['transaction_id'],
                'amount' => $transaction['amount'],
                'status' => $transaction['payment_status'],
                'completed_at' => $transaction['completed_at']
            ]
        ]);
        exit;
    }
    
    // Start transaction
    $db->beginTransaction();
    
    // Update transaction status
    $update_query = "UPDATE transactions 
                     SET payment_status = :payment_status,
                         utr_number = :utr_number,
                         gateway_transaction_id = :gateway_transaction_id,
                         completed_at = CASE WHEN :payment_status = 'success' THEN CURRENT_TIMESTAMP ELSE NULL END,
                         updated_at = CURRENT_TIMESTAMP
                     WHERE transaction_id = :transaction_id";
    
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':payment_status', $payment_status);
    $update_stmt->bindParam(':utr_number', $utr_number);
    $update_stmt->bindParam(':gateway_transaction_id', $gateway_transaction_id);
    $update_stmt->bindParam(':transaction_id', $transaction_id);
    $update_stmt->execute();
    
    // If payment successful, send callback
    if ($payment_status === 'success' && !empty($transaction['callback_url'])) {
        $callback_data = [
            'transaction_id' => $transaction_id,
            'merchant_id' => $merchant['merchant_id'],
            'amount' => $transaction['amount'],
            'currency' => $transaction['currency'],
            'status' => 'success',
            'utr_number' => $utr_number,
            'gateway_transaction_id' => $gateway_transaction_id,
            'customer_email' => $transaction['customer_email'],
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Send webhook (async in production)
        // For now, just log it
        error_log("Webhook to: " . $transaction['callback_url'] . " Data: " . json_encode($callback_data));
    }
    
    // Commit transaction
    $db->commit();
    
    // Log API request
    $log_query = "INSERT INTO api_logs (merchant_id, endpoint, method, request_data, status_code, ip_address) 
                  VALUES (:merchant_id, :endpoint, :method, :request_data, :status_code, :ip_address)";
    
    $log_stmt = $db->prepare($log_query);
    $log_stmt->bindParam(':merchant_id', $merchant['id']);
    
    $endpoint = '/api/payment/verify_payment';
    $method = 'POST';
    $request_data = json_encode($data);
    $status_code = 200;
    $ip_address = Security::getClientIP();
    
    $log_stmt->bindParam(':endpoint', $endpoint);
    $log_stmt->bindParam(':method', $method);
    $log_stmt->bindParam(':request_data', $request_data);
    $log_stmt->bindParam(':status_code', $status_code);
    $log_stmt->bindParam(':ip_address', $ip_address);
    $log_stmt->execute();
    
    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Payment verified successfully',
        'data' => [
            'transaction_id' => $transaction_id,
            'amount' => $transaction['amount'],
            'currency' => $transaction['currency'],
            'status' => $payment_status,
            'utr_number' => $utr_number,
            'gateway_transaction_id' => $gateway_transaction_id,
            'verified_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Payment Verification Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
}
?>
