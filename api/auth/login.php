<?php
/**
 * User Login API
 * Handles authentication for all user types
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/security.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting
$client_ip = Security::getClientIP();
if (!Security::checkRateLimit($client_ip, 10, 300)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many login attempts. Please try again later.']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$email = Security::sanitizeInput($data['email']);
$password = $data['password'];

// Validate email format
if (!Security::validateEmail($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Query user
    $query = "SELECT u.*, m.merchant_id, m.business_name, m.kyc_status, m.api_key 
              FROM users u 
              LEFT JOIN merchants m ON u.id = m.user_id 
              WHERE u.email = :email AND u.status = 'active'";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch();
    
    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Verify password
    if (!Security::verifyPassword($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
    
    // Check email verification
    if (!$user['email_verified']) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Please verify your email address']);
        exit;
    }
    
    // Update last login
    $update_query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id";
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':id', $user['id']);
    $update_stmt->execute();
    
    // Start session
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['full_name'] = $user['full_name'];
    
    // Generate CSRF token
    $csrf_token = Security::generateCSRFToken();
    
    // Prepare response
    $response = [
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'user_id' => $user['id'],
            'user_type' => $user['user_type'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'csrf_token' => $csrf_token
        ]
    ];
    
    // Add merchant-specific data
    if ($user['user_type'] === 'merchant' && !empty($user['merchant_id'])) {
        $response['data']['merchant'] = [
            'merchant_id' => $user['merchant_id'],
            'business_name' => $user['business_name'],
            'kyc_status' => $user['kyc_status'],
            'api_key' => $user['api_key']
        ];
    }
    
    http_response_code(200);
    echo json_encode($response);
    
} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred during login']);
}
?>
