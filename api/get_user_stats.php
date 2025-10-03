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

// Get user stats
$stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful_payments,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_payments,
        SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as total_amount
    FROM transactions 
    WHERE user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$stats = $result->fetch_assoc();

// Get recent transactions (last 5)
$stmt = $conn->prepare("
    SELECT transaction_id, amount, currency, status, created_at 
    FROM transactions 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$recentTransactions = [];
while ($row = $result->fetch_assoc()) {
    $recentTransactions[] = $row;
}

jsonResponse(true, 'Stats retrieved successfully', [
    'stats' => [
        'total_transactions' => intval($stats['total_transactions']),
        'successful_payments' => intval($stats['successful_payments']),
        'pending_payments' => intval($stats['pending_payments']),
        'total_amount' => floatval($stats['total_amount'])
    ],
    'recent_transactions' => $recentTransactions
]);

$stmt->close();
$conn->close();
?>
