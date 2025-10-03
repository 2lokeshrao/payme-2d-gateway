<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, 'Invalid request method');
}

$conn = getDBConnection();
if (!$conn) {
    jsonResponse(false, 'Database connection failed');
}

// Get all transactions with user details
$result = $conn->query("
    SELECT 
        t.transaction_id,
        t.card_number_last4,
        t.card_holder_name,
        t.card_type,
        t.amount,
        t.currency,
        t.status,
        t.created_at,
        u.full_name as user_name,
        u.email as user_email
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
");

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

jsonResponse(true, 'Transactions retrieved successfully', [
    'transactions' => $transactions
]);

$conn->close();
?>
