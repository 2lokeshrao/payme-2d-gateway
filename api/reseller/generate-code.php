<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

require_once '../config.php';

session_start();
if (!isset($_SESSION['reseller_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$resellerId = $_SESSION['reseller_id'];
$data = json_decode(file_get_contents('php://input'), true);

$planId = $data['plan_id'] ?? 0;
$customerEmail = $data['customer_email'] ?? null;

// Verify reseller can sell this plan
$sql = "SELECT allowed_plans FROM resellers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resellerId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$allowedPlans = json_decode($row['allowed_plans'], true) ?? [];

if (!in_array($planId, $allowedPlans)) {
    echo json_encode(['success' => false, 'message' => 'You are not authorized to sell this plan']);
    exit;
}

// Generate unique code
function generateUniqueCode($conn) {
    do {
        $code = sprintf('PM2D-%04d-%04d-%04d', 
            rand(1000, 9999), 
            rand(1000, 9999), 
            rand(1000, 9999)
        );
        
        $checkSql = "SELECT id FROM activation_codes WHERE code = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $code);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->num_rows > 0;
    } while ($exists);
    
    return $code;
}

$code = generateUniqueCode($conn);
$validityDays = 365;
$expiresAt = date('Y-m-d H:i:s', strtotime("+$validityDays days"));
$notes = !empty($customerEmail) ? "Generated for: $customerEmail" : null;

$sql = "INSERT INTO activation_codes 
        (code, plan_id, created_by_type, created_by_id, reseller_id, validity_days, expires_at, notes) 
        VALUES (?, ?, 'reseller', ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("siiiiis", $code, $planId, $resellerId, $resellerId, $validityDays, $expiresAt, $notes);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Code generated successfully',
        'code' => $code,
        'expires_at' => $expiresAt
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to generate code']);
}

$conn->close();
?>
