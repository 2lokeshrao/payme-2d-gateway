<?php
header('Content-Type: application/json');
require_once '../config.php';

$transactionId = $_GET['transaction_id'] ?? '';

if (empty($transactionId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Transaction ID required']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
$stmt->bind_param('s', $transactionId);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();

if ($transaction) {
    // Remove sensitive data
    unset($transaction['id']);
    echo json_encode($transaction);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Transaction not found']);
}

$stmt->close();
$conn->close();
?>
