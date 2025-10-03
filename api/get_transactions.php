<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, 'Invalid request method');
}

$userId = intval($_GET['user_id'] ?? 0);

if ($userId <= 0) {
    jsonResponse(false, 'Invalid user ID');
}

$conn = getDBConnection();
if (!$conn) {
    jsonResponse(false, 'Database connection failed');
}

// Get all transactions for user
$stmt = $conn->prepare("
    SELECT 
        transaction_id, 
        card_number_last4, 
        card_holder_name,
        card_type,
        amount, 
        currency, 
        status, 
        created_at 
    FROM transactions 
    WHERE user_id = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

jsonResponse(true, 'Transactions retrieved successfully', [
    'transactions' => $transactions
]);

$stmt->close();
$conn->close();
?>
