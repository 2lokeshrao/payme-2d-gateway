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
$action = $_GET['action'] ?? 'list';

if ($action == 'recent') {
    $sql = "SELECT 
                ac.*,
                sp.plan_name,
                sp.price as plan_price,
                CASE 
                    WHEN ac.status = 'used' THEN 
                        (sp.price * (SELECT commission_rate FROM resellers WHERE id = ac.reseller_id) / 100)
                    ELSE 0
                END as commission
            FROM activation_codes ac
            JOIN subscription_plans sp ON ac.plan_id = sp.id
            WHERE ac.reseller_id = ?
            ORDER BY ac.created_at DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resellerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $codes = [];
    while ($row = $result->fetch_assoc()) {
        $codes[] = $row;
    }
    
    echo json_encode(['success' => true, 'codes' => $codes]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

$conn->close();
?>
