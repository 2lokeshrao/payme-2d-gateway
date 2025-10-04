<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Get user ID from session or token
session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId && $action !== 'plans') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

switch ($action) {
    case 'plans':
        getSubscriptionPlans();
        break;
    
    case 'check':
        checkSubscription($userId);
        break;
    
    case 'subscribe':
        createSubscription($userId);
        break;
    
    case 'verify-payment':
        verifyPayment($userId);
        break;
    
    case 'cancel':
        cancelSubscription($userId);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function getSubscriptionPlans() {
    global $conn;
    
    $sql = "SELECT * FROM subscription_plans WHERE is_active = TRUE ORDER BY price_usd ASC";
    $result = $conn->query($sql);
    
    $plans = [];
    while ($row = $result->fetch_assoc()) {
        $row['features'] = json_decode($row['features'], true);
        $plans[] = $row;
    }
    
    echo json_encode(['success' => true, 'plans' => $plans]);
}

function checkSubscription($userId) {
    global $conn;
    
    $sql = "SELECT us.*, sp.plan_name, sp.plan_type, sp.price_usd, sp.features 
            FROM user_subscriptions us 
            JOIN subscription_plans sp ON us.plan_id = sp.id 
            WHERE us.user_id = ? 
            ORDER BY us.created_at DESC 
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $row['features'] = json_decode($row['features'], true);
        
        // Check if subscription is expired
        if ($row['subscription_status'] === 'active' && strtotime($row['end_date']) < time()) {
            // Update status to expired
            $updateSql = "UPDATE user_subscriptions SET subscription_status = 'expired' WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("i", $row['id']);
            $updateStmt->execute();
            $row['subscription_status'] = 'expired';
        }
        
        echo json_encode(['success' => true, 'subscription' => $row]);
    } else {
        echo json_encode(['success' => true, 'subscription' => null]);
    }
}

function createSubscription($userId) {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $planId = $data['plan_id'] ?? null;
    $paymentMethod = $data['payment_method'] ?? null;
    
    if (!$planId || !$paymentMethod) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        return;
    }
    
    // Get plan details
    $planSql = "SELECT * FROM subscription_plans WHERE id = ?";
    $planStmt = $conn->prepare($planSql);
    $planStmt->bind_param("i", $planId);
    $planStmt->execute();
    $plan = $planStmt->get_result()->fetch_assoc();
    
    if (!$plan) {
        echo json_encode(['success' => false, 'message' => 'Invalid plan']);
        return;
    }
    
    // Calculate end date
    $startDate = date('Y-m-d H:i:s');
    $endDate = date('Y-m-d H:i:s', strtotime($startDate . ' +' . getPlanDuration($plan['plan_type'])));
    $nextBillingDate = $endDate;
    
    // Create subscription
    $sql = "INSERT INTO user_subscriptions (user_id, plan_id, subscription_status, start_date, end_date, next_billing_date, payment_method) 
            VALUES (?, ?, 'pending', ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissss", $userId, $planId, $startDate, $endDate, $nextBillingDate, $paymentMethod);
    
    if ($stmt->execute()) {
        $subscriptionId = $conn->insert_id;
        
        // Create payment record
        $paymentSql = "INSERT INTO subscription_payments (subscription_id, user_id, amount_usd, payment_method, payment_status) 
                       VALUES (?, ?, ?, ?, 'pending')";
        $paymentStmt = $conn->prepare($paymentSql);
        $paymentStmt->bind_param("iids", $subscriptionId, $userId, $plan['price_usd'], $paymentMethod);
        $paymentStmt->execute();
        
        // Generate license key
        $licenseKey = generateLicenseKey($userId, $planId);
        $licenseSql = "INSERT INTO license_keys (user_id, license_key, plan_id, expiry_date) 
                       VALUES (?, ?, ?, ?)";
        $licenseStmt = $conn->prepare($licenseSql);
        $licenseStmt->bind_param("isis", $userId, $licenseKey, $planId, $endDate);
        $licenseStmt->execute();
        
        echo json_encode([
            'success' => true, 
            'subscription_id' => $subscriptionId,
            'license_key' => $licenseKey,
            'message' => 'Subscription created successfully'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create subscription']);
    }
}

function verifyPayment($userId) {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $subscriptionId = $data['subscription_id'] ?? null;
    $transactionId = $data['transaction_id'] ?? null;
    
    if (!$subscriptionId) {
        echo json_encode(['success' => false, 'message' => 'Missing subscription ID']);
        return;
    }
    
    // Update subscription status to active
    $sql = "UPDATE user_subscriptions SET subscription_status = 'active', last_payment_date = NOW() WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $subscriptionId, $userId);
    
    if ($stmt->execute()) {
        // Update payment status
        $paymentSql = "UPDATE subscription_payments SET payment_status = 'completed', transaction_id = ?, payment_date = NOW() 
                       WHERE subscription_id = ?";
        $paymentStmt = $conn->prepare($paymentSql);
        $paymentStmt->bind_param("si", $transactionId, $subscriptionId);
        $paymentStmt->execute();
        
        // Update license key status
        $licenseSql = "UPDATE license_keys SET status = 'active', last_verified = NOW() WHERE user_id = ?";
        $licenseStmt = $conn->prepare($licenseSql);
        $licenseStmt->bind_param("i", $userId);
        $licenseStmt->execute();
        
        echo json_encode(['success' => true, 'message' => 'Payment verified and subscription activated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to verify payment']);
    }
}

function cancelSubscription($userId) {
    global $conn;
    
    $sql = "UPDATE user_subscriptions SET subscription_status = 'cancelled', auto_renew = FALSE 
            WHERE user_id = ? AND subscription_status = 'active'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Subscription cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel subscription']);
    }
}

function getPlanDuration($planType) {
    switch ($planType) {
        case 'monthly':
            return '1 month';
        case 'quarterly':
            return '3 months';
        case 'yearly':
            return '1 year';
        default:
            return '1 month';
    }
}

function generateLicenseKey($userId, $planId) {
    $prefix = 'PM2D';
    $random = strtoupper(bin2hex(random_bytes(8)));
    $checksum = substr(md5($userId . $planId . time()), 0, 4);
    return $prefix . '-' . substr($random, 0, 4) . '-' . substr($random, 4, 4) . '-' . substr($random, 8, 4) . '-' . strtoupper($checksum);
}

$conn->close();
?>
