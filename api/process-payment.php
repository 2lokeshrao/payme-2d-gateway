<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';
require_once 'payment-gateway-integration.php';

$input = json_decode(file_get_contents('php://input'), true);

$transactionId = $input['transaction_id'] ?? '';
$paymentMethod = $input['payment_method'] ?? '';
$paymentDetails = $input['payment_details'] ?? [];

if (empty($transactionId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Transaction ID required']);
    exit;
}

// Get transaction from database
$stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
$stmt->bind_param('s', $transactionId);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if (!$transaction) {
    http_response_code(404);
    echo json_encode(['error' => 'Transaction not found']);
    exit;
}

// Initialize payment gateway
$gateway = new PaymentGateway('payme2d'); // Can be 'razorpay', 'stripe', 'payu'

$paymentResult = null;

// Process based on payment method
switch($paymentMethod) {
    case 'card':
        // Process card payment
        $cardData = [
            'number' => preg_replace('/\s+/', '', $paymentDetails['card_number']),
            'name' => $paymentDetails['card_name'],
            'expiry_month' => $paymentDetails['expiry_month'],
            'expiry_year' => $paymentDetails['expiry_year'],
            'cvv' => $paymentDetails['cvv']
        ];
        
        $paymentResult = $gateway->processCardPayment(
            $cardData, 
            $transaction['amount'], 
            $transaction['currency']
        );
        break;
        
    case 'upi':
        // Process UPI payment
        $upiId = $paymentDetails['upi_id'];
        $paymentResult = $gateway->processUPIPayment(
            $upiId,
            $transaction['amount'],
            $transaction['currency']
        );
        break;
        
    case 'netbanking':
        // Process net banking
        $bank = $paymentDetails['bank'];
        $paymentResult = [
            'success' => true,
            'transaction_id' => 'NB' . time() . rand(1000, 9999),
            'status' => 'success',
            'payment_method' => 'netbanking',
            'bank' => $bank
        ];
        break;
        
    case 'wallet':
        // Process wallet payment
        $wallet = $paymentDetails['wallet'];
        $paymentResult = [
            'success' => true,
            'transaction_id' => 'WLT' . time() . rand(1000, 9999),
            'status' => 'success',
            'payment_method' => 'wallet',
            'wallet' => $wallet
        ];
        break;
        
    case 'crypto':
        // Process crypto payment
        $crypto = $paymentDetails['crypto'];
        $paymentResult = [
            'success' => true,
            'transaction_id' => 'CRY' . time() . rand(1000, 9999),
            'status' => 'success',
            'payment_method' => 'crypto',
            'cryptocurrency' => $crypto
        ];
        break;
        
    case 'emi':
        // Process EMI payment
        $tenure = $paymentDetails['tenure'];
        $paymentResult = [
            'success' => true,
            'transaction_id' => 'EMI' . time() . rand(1000, 9999),
            'status' => 'success',
            'payment_method' => 'emi',
            'tenure' => $tenure
        ];
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid payment method']);
        exit;
}

// Update transaction based on result
if ($paymentResult['success']) {
    // Payment successful
    $stmt = $conn->prepare("
        UPDATE transactions 
        SET status = 'success', 
            completed_at = NOW(),
            payment_provider = ?,
            gateway_transaction_id = ?,
            gateway_response = ?
        WHERE transaction_id = ?
    ");
    
    $provider = $paymentResult['gateway'] ?? 'payme2d';
    $gatewayTxnId = $paymentResult['transaction_id'];
    $response = json_encode($paymentResult);
    
    $stmt->bind_param('ssss', $provider, $gatewayTxnId, $response, $transactionId);
    $stmt->execute();
    
    // Send webhook if configured
    if (!empty($transaction['webhook_url'])) {
        sendWebhook($transaction['webhook_url'], [
            'event' => 'payment.success',
            'transaction_id' => $transactionId,
            'gateway_transaction_id' => $gatewayTxnId,
            'amount' => $transaction['amount'],
            'currency' => $transaction['currency'],
            'status' => 'success',
            'payment_method' => $paymentMethod,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'transaction_id' => $transactionId,
        'gateway_transaction_id' => $gatewayTxnId,
        'status' => 'success',
        'amount' => $transaction['amount'],
        'currency' => $transaction['currency'],
        'payment_method' => $paymentMethod,
        'redirect_url' => $transaction['redirect_url'] ?: 'payment-success.html?txn=' . $transactionId,
        'message' => 'Payment processed successfully'
    ]);
    
} else {
    // Payment failed
    $stmt = $conn->prepare("
        UPDATE transactions 
        SET status = 'failed',
            error_message = ?,
            gateway_response = ?
        WHERE transaction_id = ?
    ");
    
    $errorMsg = $paymentResult['error'] ?? 'Payment failed';
    $response = json_encode($paymentResult);
    
    $stmt->bind_param('sss', $errorMsg, $response, $transactionId);
    $stmt->execute();
    
    // Send webhook if configured
    if (!empty($transaction['webhook_url'])) {
        sendWebhook($transaction['webhook_url'], [
            'event' => 'payment.failed',
            'transaction_id' => $transactionId,
            'amount' => $transaction['amount'],
            'currency' => $transaction['currency'],
            'status' => 'failed',
            'error' => $errorMsg,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'transaction_id' => $transactionId,
        'status' => 'failed',
        'error' => $errorMsg,
        'message' => 'Payment processing failed'
    ]);
}

function sendWebhook($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-PayMe2D-Signature: ' . hash_hmac('sha256', json_encode($data), 'webhook_secret')
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Log webhook delivery
    // You can add webhook logging here
    
    return $httpCode >= 200 && $httpCode < 300;
}

$stmt->close();
$conn->close();
?>
