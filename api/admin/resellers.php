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
        listResellers();
        break;
    
    case 'get':
        getReseller();
        break;
    
    case 'create':
        createReseller();
        break;
    
    case 'update':
        updateReseller();
        break;
    
    case 'delete':
        deleteReseller();
        break;
    
    case 'stats':
        getStats();
        break;
    
    case 'performance':
        getResellerPerformance();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function listResellers() {
    global $conn;
    
    $sql = "SELECT 
                r.*,
                COUNT(DISTINCT ac.id) as total_codes,
                COUNT(DISTINCT CASE WHEN ac.status = 'used' THEN ac.id END) as codes_used,
                COUNT(DISTINCT rc.id) as total_customers,
                IFNULL(SUM(re.commission_amount), 0) as total_earnings
            FROM resellers r
            LEFT JOIN activation_codes ac ON r.id = ac.reseller_id
            LEFT JOIN reseller_customers rc ON r.id = rc.reseller_id
            LEFT JOIN reseller_earnings re ON r.id = re.reseller_id
            GROUP BY r.id
            ORDER BY r.created_at DESC";
    
    $result = $conn->query($sql);
    $resellers = [];
    
    while ($row = $result->fetch_assoc()) {
        $row['allowed_plans'] = json_decode($row['allowed_plans'], true);
        $resellers[] = $row;
    }
    
    echo json_encode(['success' => true, 'resellers' => $resellers]);
}

function getReseller() {
    global $conn;
    
    $resellerId = $_GET['id'] ?? 0;
    
    $sql = "SELECT * FROM resellers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resellerId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $row['allowed_plans'] = json_decode($row['allowed_plans'], true);
        unset($row['password_hash']); // Don't send password
        echo json_encode(['success' => true, 'reseller' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Reseller not found']);
    }
}

function createReseller() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $phone = $data['phone'] ?? '';
    $password = $data['password'] ?? '';
    $commissionRate = $data['commission_rate'] ?? 20;
    $allowedPlans = json_encode($data['allowed_plans'] ?? []);
    $status = $data['status'] ?? 'active';
    
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Name, email, and password are required']);
        return;
    }
    
    // Check if email already exists
    $checkSql = "SELECT id FROM resellers WHERE email = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email already exists']);
        return;
    }
    
    // Hash password
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO resellers (name, email, phone, password_hash, commission_rate, allowed_plans, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdss", $name, $email, $phone, $passwordHash, $commissionRate, $allowedPlans, $status);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Reseller created successfully',
            'reseller_id' => $conn->insert_id,
            'login_url' => 'https://payme-gateway.lindy.site/reseller/login.html'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create reseller']);
    }
}

function updateReseller() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $resellerId = $data['id'] ?? 0;
    $name = $data['name'] ?? '';
    $phone = $data['phone'] ?? '';
    $commissionRate = $data['commission_rate'] ?? 20;
    $allowedPlans = json_encode($data['allowed_plans'] ?? []);
    $status = $data['status'] ?? 'active';
    
    if ($resellerId <= 0 || empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Invalid reseller data']);
        return;
    }
    
    $sql = "UPDATE resellers 
            SET name = ?, phone = ?, commission_rate = ?, allowed_plans = ?, status = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssi", $name, $phone, $commissionRate, $allowedPlans, $status, $resellerId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reseller updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update reseller']);
    }
}

function deleteReseller() {
    global $conn;
    
    $resellerId = $_GET['id'] ?? 0;
    
    // Check if reseller has active codes
    $checkSql = "SELECT COUNT(*) as count FROM activation_codes 
                 WHERE reseller_id = ? AND status = 'unused'";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $resellerId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Cannot delete reseller with unused activation codes'
        ]);
        return;
    }
    
    $sql = "DELETE FROM resellers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $resellerId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Reseller deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete reseller']);
    }
}

function getStats() {
    global $conn;
    
    // Total resellers
    $totalResellers = $conn->query("SELECT COUNT(*) as count FROM resellers")->fetch_assoc()['count'];
    
    // Active resellers
    $activeResellers = $conn->query("SELECT COUNT(*) as count FROM resellers WHERE status = 'active'")->fetch_assoc()['count'];
    
    // Total codes generated by resellers
    $totalCodes = $conn->query("SELECT COUNT(*) as count FROM activation_codes WHERE reseller_id IS NOT NULL")->fetch_assoc()['count'];
    
    // Total commission paid
    $totalCommission = $conn->query("SELECT IFNULL(SUM(commission_amount), 0) as total FROM reseller_earnings WHERE status = 'paid'")->fetch_assoc()['total'];
    
    // Pending commission
    $pendingCommission = $conn->query("SELECT IFNULL(SUM(commission_amount), 0) as total FROM reseller_earnings WHERE status = 'pending'")->fetch_assoc()['total'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_resellers' => $totalResellers,
            'active_resellers' => $activeResellers,
            'total_codes' => $totalCodes,
            'total_commission' => number_format($totalCommission, 2),
            'pending_commission' => number_format($pendingCommission, 2)
        ]
    ]);
}

function getResellerPerformance() {
    global $conn;
    
    $sql = "SELECT * FROM reseller_performance ORDER BY total_earnings DESC LIMIT 10";
    $result = $conn->query($sql);
    $performance = [];
    
    while ($row = $result->fetch_assoc()) {
        $performance[] = $row;
    }
    
    echo json_encode(['success' => true, 'performance' => $performance]);
}

$conn->close();
?>
