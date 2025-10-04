<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

// Check admin authentication
session_start();
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Admin access required']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        listCodes();
        break;
    
    case 'generate':
        generateCode();
        break;
    
    case 'generate-bulk':
        generateBulkCodes();
        break;
    
    case 'delete':
        deleteCode();
        break;
    
    case 'stats':
        getStats();
        break;
    
    case 'activate':
        activateCode();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function generateUniqueCode() {
    global $conn;
    
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

function listCodes() {
    global $conn;
    
    $status = $_GET['status'] ?? '';
    $resellerId = $_GET['reseller_id'] ?? '';
    $planId = $_GET['plan_id'] ?? '';
    $search = $_GET['search'] ?? '';
    
    $sql = "SELECT 
                ac.*,
                sp.plan_name,
                sp.price as plan_price,
                r.name as reseller_name,
                r.email as reseller_email
            FROM activation_codes ac
            JOIN subscription_plans sp ON ac.plan_id = sp.id
            LEFT JOIN resellers r ON ac.reseller_id = r.id
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if (!empty($status)) {
        $sql .= " AND ac.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if (!empty($resellerId)) {
        $sql .= " AND ac.reseller_id = ?";
        $params[] = $resellerId;
        $types .= 'i';
    }
    
    if (!empty($planId)) {
        $sql .= " AND ac.plan_id = ?";
        $params[] = $planId;
        $types .= 'i';
    }
    
    if (!empty($search)) {
        $sql .= " AND (ac.code LIKE ? OR ac.used_by_email LIKE ?)";
        $searchParam = "%$search%";
        $params[] = $searchParam;
        $params[] = $searchParam;
        $types .= 'ss';
    }
    
    $sql .= " ORDER BY ac.created_at DESC LIMIT 100";
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    $codes = [];
    while ($row = $result->fetch_assoc()) {
        $codes[] = $row;
    }
    
    echo json_encode(['success' => true, 'codes' => $codes]);
}

function generateCode() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $planId = $data['plan_id'] ?? 0;
    $resellerId = $data['reseller_id'] ?? null;
    $validityDays = $data['validity_days'] ?? 365;
    $customerEmail = $data['customer_email'] ?? null;
    
    if ($planId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid plan ID']);
        return;
    }
    
    // Generate unique code
    $code = generateUniqueCode();
    
    // Calculate expiry date
    $expiresAt = date('Y-m-d H:i:s', strtotime("+$validityDays days"));
    
    $sql = "INSERT INTO activation_codes 
            (code, plan_id, created_by_type, created_by_id, reseller_id, validity_days, expires_at, notes) 
            VALUES (?, ?, 'admin', ?, ?, ?, ?, ?)";
    
    $adminId = $_SESSION['admin_id'];
    $notes = !empty($customerEmail) ? "Generated for: $customerEmail" : null;
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("siiiiis", $code, $planId, $adminId, $resellerId, $validityDays, $expiresAt, $notes);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Activation code generated successfully',
            'code' => $code,
            'expires_at' => $expiresAt
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to generate code']);
    }
}

function generateBulkCodes() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $planId = $data['plan_id'] ?? 0;
    $resellerId = $data['reseller_id'] ?? null;
    $quantity = $data['quantity'] ?? 1;
    $validityDays = $data['validity_days'] ?? 365;
    
    if ($planId <= 0 || $quantity <= 0 || $quantity > 100) {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters (max 100 codes)']);
        return;
    }
    
    $codes = [];
    $expiresAt = date('Y-m-d H:i:s', strtotime("+$validityDays days"));
    $adminId = $_SESSION['admin_id'];
    
    $conn->begin_transaction();
    
    try {
        $sql = "INSERT INTO activation_codes 
                (code, plan_id, created_by_type, created_by_id, reseller_id, validity_days, expires_at) 
                VALUES (?, ?, 'admin', ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        
        for ($i = 0; $i < $quantity; $i++) {
            $code = generateUniqueCode();
            $stmt->bind_param("siiiss", $code, $planId, $adminId, $resellerId, $validityDays, $expiresAt);
            $stmt->execute();
            $codes[] = $code;
        }
        
        $conn->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => "$quantity codes generated successfully",
            'codes' => $codes,
            'expires_at' => $expiresAt
        ]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to generate bulk codes']);
    }
}

function deleteCode() {
    global $conn;
    
    $codeId = $_GET['id'] ?? 0;
    
    // Check if code is unused
    $checkSql = "SELECT status FROM activation_codes WHERE id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $codeId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['status'] != 'unused') {
            echo json_encode(['success' => false, 'message' => 'Cannot delete used or expired codes']);
            return;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Code not found']);
        return;
    }
    
    $sql = "DELETE FROM activation_codes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $codeId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Code deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete code']);
    }
}

function getStats() {
    global $conn;
    
    // Total codes
    $totalCodes = $conn->query("SELECT COUNT(*) as count FROM activation_codes")->fetch_assoc()['count'];
    
    // Unused codes
    $unusedCodes = $conn->query("SELECT COUNT(*) as count FROM activation_codes WHERE status = 'unused'")->fetch_assoc()['count'];
    
    // Used codes
    $usedCodes = $conn->query("SELECT COUNT(*) as count FROM activation_codes WHERE status = 'used'")->fetch_assoc()['count'];
    
    // Expired codes
    $expiredCodes = $conn->query("SELECT COUNT(*) as count FROM activation_codes WHERE status = 'expired' OR (status = 'unused' AND expires_at < NOW())")->fetch_assoc()['count'];
    
    // Total revenue from codes
    $totalRevenue = $conn->query("
        SELECT IFNULL(SUM(sp.price), 0) as total 
        FROM activation_codes ac
        JOIN subscription_plans sp ON ac.plan_id = sp.id
        WHERE ac.status = 'used'
    ")->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_codes' => $totalCodes,
            'unused_codes' => $unusedCodes,
            'used_codes' => $usedCodes,
            'expired_codes' => $expiredCodes,
            'total_revenue' => number_format($totalRevenue, 2)
        ]
    ]);
}

function activateCode() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $code = $data['code'] ?? '';
    $userId = $data['user_id'] ?? 0;
    $email = $data['email'] ?? '';
    
    if (empty($code) || $userId <= 0 || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Invalid activation data']);
        return;
    }
    
    // Call stored procedure
    $sql = "CALL activate_code(?, ?, ?, @success, @message)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sis", $code, $userId, $email);
    $stmt->execute();
    
    // Get output parameters
    $result = $conn->query("SELECT @success as success, @message as message");
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'success' => (bool)$row['success'],
        'message' => $row['message']
    ]);
}

$conn->close();
?>
