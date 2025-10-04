<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config.php';

session_start();
if (!isset($_SESSION['reseller_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$resellerId = $_SESSION['reseller_id'];

// Total sales
$totalSales = $conn->query("
    SELECT COUNT(*) as count 
    FROM activation_codes 
    WHERE reseller_id = $resellerId AND status = 'used'
")->fetch_assoc()['count'];

// Active codes
$activeCodes = $conn->query("
    SELECT COUNT(*) as count 
    FROM activation_codes 
    WHERE reseller_id = $resellerId AND status = 'unused' AND expires_at > NOW()
")->fetch_assoc()['count'];

// Total earnings
$totalEarnings = $conn->query("
    SELECT IFNULL(SUM(commission_amount), 0) as total 
    FROM reseller_earnings 
    WHERE reseller_id = $resellerId
")->fetch_assoc()['total'];

// Pending payout
$pendingPayout = $conn->query("
    SELECT IFNULL(SUM(commission_amount), 0) as total 
    FROM reseller_earnings 
    WHERE reseller_id = $resellerId AND status = 'pending'
")->fetch_assoc()['total'];

echo json_encode([
    'success' => true,
    'stats' => [
        'total_sales' => $totalSales,
        'active_codes' => $activeCodes,
        'total_earnings' => number_format($totalEarnings, 2),
        'pending_payout' => number_format($pendingPayout, 2)
    ]
]);

$conn->close();
?>
