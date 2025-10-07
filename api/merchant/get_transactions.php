<?php
/**
 * Get Merchant Transactions API
 * Retrieves transaction list for authenticated merchant
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

require_once '../config/database.php';
require_once '../config/security.php';

// Only allow GET requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get API Key from header
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';

if (empty($api_key)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'API Key is required']);
    exit;
}

// Get query parameters
$status = isset($_GET['status']) ? Security::sanitizeInput($_GET['status']) : '';
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 50;
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$from_date = isset($_GET['from_date']) ? Security::sanitizeInput($_GET['from_date']) : '';
$to_date = isset($_GET['to_date']) ? Security::sanitizeInput($_GET['to_date']) : '';

// Validate limit
if ($limit > 100) {
    $limit = 100;
}

try {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Verify API Key and get merchant
    $merchant_query = "SELECT m.id, m.merchant_id, m.business_name 
                       FROM merchants m 
                       WHERE m.api_key = :api_key";
    
    $merchant_stmt = $db->prepare($merchant_query);
    $merchant_stmt->bindParam(':api_key', $api_key);
    $merchant_stmt->execute();
    
    $merchant = $merchant_stmt->fetch();
    
    if (!$merchant) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid API Key']);
        exit;
    }
    
    // Build query
    $where_conditions = ["merchant_id = :merchant_id"];
    $params = [':merchant_id' => $merchant['id']];
    
    if (!empty($status)) {
        $where_conditions[] = "payment_status = :status";
        $params[':status'] = $status;
    }
    
    if (!empty($from_date)) {
        $where_conditions[] = "created_at >= :from_date";
        $params[':from_date'] = $from_date;
    }
    
    if (!empty($to_date)) {
        $where_conditions[] = "created_at <= :to_date";
        $params[':to_date'] = $to_date;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_query = "SELECT COUNT(*) as total FROM transactions WHERE " . $where_clause;
    $count_stmt = $db->prepare($count_query);
    foreach ($params as $key => $value) {
        $count_stmt->bindValue($key, $value);
    }
    $count_stmt->execute();
    $total_count = $count_stmt->fetch()['total'];
    
    // Get transactions
    $query = "SELECT transaction_id, payment_method, amount, currency, 
                     customer_name, customer_email, customer_phone, 
                     payment_status, utr_number, gateway_transaction_id,
                     description, created_at, completed_at
              FROM transactions 
              WHERE " . $where_clause . "
              ORDER BY created_at DESC 
              LIMIT :limit OFFSET :offset";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $transactions = $stmt->fetchAll();
    
    // Calculate statistics
    $stats_query = "SELECT 
                        COUNT(*) as total_transactions,
                        SUM(CASE WHEN payment_status = 'success' THEN amount ELSE 0 END) as total_revenue,
                        SUM(CASE WHEN payment_status = 'success' THEN 1 ELSE 0 END) as successful_transactions,
                        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_transactions,
                        SUM(CASE WHEN payment_status = 'failed' THEN 1 ELSE 0 END) as failed_transactions
                    FROM transactions 
                    WHERE merchant_id = :merchant_id";
    
    $stats_stmt = $db->prepare($stats_query);
    $stats_stmt->bindParam(':merchant_id', $merchant['id']);
    $stats_stmt->execute();
    $stats = $stats_stmt->fetch();
    
    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Transactions retrieved successfully',
        'data' => [
            'transactions' => $transactions,
            'pagination' => [
                'total' => intval($total_count),
                'limit' => $limit,
                'offset' => $offset,
                'has_more' => ($offset + $limit) < $total_count
            ],
            'statistics' => [
                'total_transactions' => intval($stats['total_transactions']),
                'total_revenue' => floatval($stats['total_revenue']),
                'successful_transactions' => intval($stats['successful_transactions']),
                'pending_transactions' => intval($stats['pending_transactions']),
                'failed_transactions' => intval($stats['failed_transactions']),
                'success_rate' => $stats['total_transactions'] > 0 
                    ? round(($stats['successful_transactions'] / $stats['total_transactions']) * 100, 2) 
                    : 0
            ]
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Get Transactions Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to retrieve transactions']);
}
?>
