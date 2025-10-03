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

// Get total users
$result = $conn->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $result->fetch_assoc()['total'];

// Get active users
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE status = 'active'");
$activeUsers = $result->fetch_assoc()['total'];

// Get new users today
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE DATE(created_at) = CURDATE()");
$newUsersToday = $result->fetch_assoc()['total'];

// Get transaction stats
$result = $conn->query("
    SELECT 
        COUNT(*) as total_transactions,
        SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful_transactions,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_transactions,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_transactions,
        SUM(CASE WHEN status = 'success' THEN amount ELSE 0 END) as total_revenue
    FROM transactions
");
$transactionStats = $result->fetch_assoc();

// Get recent transactions with user names
$result = $conn->query("
    SELECT 
        t.transaction_id,
        t.amount,
        t.currency,
        t.status,
        t.created_at,
        u.full_name as user_name
    FROM transactions t
    JOIN users u ON t.user_id = u.id
    ORDER BY t.created_at DESC
    LIMIT 10
");

$recentTransactions = [];
while ($row = $result->fetch_assoc()) {
    $recentTransactions[] = $row;
}

jsonResponse(true, 'Stats retrieved successfully', [
    'stats' => [
        'total_users' => intval($totalUsers),
        'active_users' => intval($activeUsers),
        'new_users_today' => intval($newUsersToday),
        'total_transactions' => intval($transactionStats['total_transactions']),
        'successful_transactions' => intval($transactionStats['successful_transactions']),
        'pending_transactions' => intval($transactionStats['pending_transactions']),
        'failed_transactions' => intval($transactionStats['failed_transactions']),
        'total_revenue' => floatval($transactionStats['total_revenue'])
    ],
    'recent_transactions' => $recentTransactions
]);

$conn->close();
?>
