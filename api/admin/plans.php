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
        listPlans();
        break;
    
    case 'get':
        getPlan();
        break;
    
    case 'create':
        createPlan();
        break;
    
    case 'update':
        updatePlan();
        break;
    
    case 'delete':
        deletePlan();
        break;
    
    case 'stats':
        getStats();
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function listPlans() {
    global $conn;
    
    $sql = "SELECT 
                sp.*,
                COUNT(DISTINCT us.id) as subscribers
            FROM subscription_plans sp
            LEFT JOIN user_subscriptions us ON sp.id = us.plan_id AND us.subscription_status = 'active'
            GROUP BY sp.id
            ORDER BY sp.price ASC";
    
    $result = $conn->query($sql);
    $plans = [];
    
    while ($row = $result->fetch_assoc()) {
        $row['features'] = json_decode($row['features'], true);
        $plans[] = $row;
    }
    
    echo json_encode(['success' => true, 'plans' => $plans]);
}

function getPlan() {
    global $conn;
    
    $planId = $_GET['id'] ?? 0;
    
    $sql = "SELECT * FROM subscription_plans WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $planId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $row['features'] = json_decode($row['features'], true);
        echo json_encode(['success' => true, 'plan' => $row]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Plan not found']);
    }
}

function createPlan() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $name = $data['name'] ?? '';
    $type = $data['type'] ?? '';
    $price = $data['price'] ?? 0;
    $duration = $data['duration'] ?? 30;
    $features = json_encode($data['features'] ?? []);
    $status = $data['status'] ?? 'active';
    
    if (empty($name) || empty($type) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid plan data']);
        return;
    }
    
    $sql = "INSERT INTO subscription_plans (plan_name, plan_type, price, duration_days, features, status) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiss", $name, $type, $price, $duration, $features, $status);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Plan created successfully',
            'plan_id' => $conn->insert_id
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create plan']);
    }
}

function updatePlan() {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $planId = $data['id'] ?? 0;
    $name = $data['name'] ?? '';
    $price = $data['price'] ?? 0;
    $duration = $data['duration'] ?? 30;
    $features = json_encode($data['features'] ?? []);
    $status = $data['status'] ?? 'active';
    
    if ($planId <= 0 || empty($name) || $price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid plan data']);
        return;
    }
    
    $sql = "UPDATE subscription_plans 
            SET plan_name = ?, price = ?, duration_days = ?, features = ?, status = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdissi", $name, $price, $duration, $features, $status, $planId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Plan updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update plan']);
    }
}

function deletePlan() {
    global $conn;
    
    $planId = $_GET['id'] ?? 0;
    
    // Check if plan has active subscriptions
    $checkSql = "SELECT COUNT(*) as count FROM user_subscriptions 
                 WHERE plan_id = ? AND subscription_status = 'active'";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $planId);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row['count'] > 0) {
        echo json_encode([
            'success' => false, 
            'message' => 'Cannot delete plan with active subscriptions'
        ]);
        return;
    }
    
    $sql = "DELETE FROM subscription_plans WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $planId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Plan deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete plan']);
    }
}

function getStats() {
    global $conn;
    
    // Total plans
    $totalPlans = $conn->query("SELECT COUNT(*) as count FROM subscription_plans")->fetch_assoc()['count'];
    
    // Active plans
    $activePlans = $conn->query("SELECT COUNT(*) as count FROM subscription_plans WHERE status = 'active'")->fetch_assoc()['count'];
    
    // Total revenue
    $totalRevenue = $conn->query("SELECT IFNULL(SUM(amount), 0) as total FROM subscription_payments WHERE payment_status = 'completed'")->fetch_assoc()['total'];
    
    // Active subscriptions
    $activeSubscriptions = $conn->query("SELECT COUNT(*) as count FROM user_subscriptions WHERE subscription_status = 'active'")->fetch_assoc()['count'];
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_plans' => $totalPlans,
            'active_plans' => $activePlans,
            'total_revenue' => number_format($totalRevenue, 2),
            'active_subscriptions' => $activeSubscriptions
        ]
    ]);
}

$conn->close();
?>
