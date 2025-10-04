<?php
header('Content-Type: application/json');
require_once '../config.php';

$input = json_decode(file_get_contents('php://input'), true);

$transactionId = $input['transaction_id'] ?? '';
$amount = floatval($input['amount'] ?? 0);
$reason = $input['reason'] ?? '';

if (empty($transactionId) || $amount <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid refund request']);
    exit;
}

// Get transaction
$stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ? AND status = 'success'");
$stmt->bind_param('s', $transactionId);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if (!$transaction) {
    http_response_code(404);
    echo json_encode(['error' => 'Transaction not found or not eligible for refund']);
    exit;
}

if ($amount > $transaction['amount']) {
    http_response_code(400);
    echo json_encode(['error' => 'Refund amount exceeds transaction amount']);
    exit;
}

// Generate refund ID
$refundId = 'RFD' . time() . rand(1000, 9999);

// Insert refund
$stmt = $conn->prepare("
    INSERT INTO refunds (refund_id, transaction_id, merchant_id, amount, reason, status)
    VALUES (?, ?, ?, ?, ?, 'processing')
");

$stmt->bind_param('sssds', $refundId, $transactionId, $transaction['merchant_id'], $amount, $reason);

if ($stmt->execute()) {
    // Update transaction
    $stmt = $conn->prepare("
        UPDATE transactions 
        SET status = 'refunded', refund_amount = ?, refund_reason = ?
        WHERE transaction_id = ?
    ");
    $stmt->bind_param('dss', $amount, $reason, $transactionId);
    $stmt->execute();
    
    echo json_encode([
        'success' => true,
        'refund_id' => $refundId,
        'transaction_id' => $transactionId,
        'amount' => $amount,
        'status' => 'processing'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to process refund']);
}

$stmt->close();
$conn->close();
?>
