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

// Get reseller's allowed plans
$sql = "SELECT allowed_plans FROM resellers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resellerId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$allowedPlans = json_decode($row['allowed_plans'], true) ?? [];

if (empty($allowedPlans)) {
    echo json_encode(['success' => true, 'plans' => []]);
    exit;
}

// Get plan details
$placeholders = implode(',', array_fill(0, count($allowedPlans), '?'));
$sql = "SELECT * FROM subscription_plans WHERE id IN ($placeholders) AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat('i', count($allowedPlans)), ...$allowedPlans);
$stmt->execute();
$result = $stmt->get_result();

$plans = [];
while ($row = $result->fetch_assoc()) {
    $row['features'] = json_decode($row['features'], true);
    $plans[] = $row;
}

echo json_encode(['success' => true, 'plans' => $plans]);

$conn->close();
?>
