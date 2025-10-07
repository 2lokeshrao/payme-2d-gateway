<?php
/**
 * User Registration API
 * Handles registration for merchants and resellers
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

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
$required_fields = ['user_type', 'email', 'password', 'full_name', 'phone'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required']);
        exit;
    }
}

// Sanitize inputs
$user_type = Security::sanitizeInput($data['user_type']);
$email = Security::sanitizeInput($data['email']);
$password = $data['password'];
$full_name = Security::sanitizeInput($data['full_name']);
$phone = Security::sanitizeInput($data['phone']);

// Validate user type
if (!in_array($user_type, ['merchant', 'reseller'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user type']);
    exit;
}

// Validate email
if (!Security::validateEmail($email)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Validate phone
if (!Security::validatePhone($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
    exit;
}

// Validate password strength
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

try {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Check if email already exists
    $check_query = "SELECT id FROM users WHERE email = :email";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':email', $email);
    $check_stmt->execute();
    
    if ($check_stmt->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'Email already registered']);
        exit;
    }
    
    // Hash password
    $password_hash = Security::hashPassword($password);
    
    // Start transaction
    $db->beginTransaction();
    
    // Insert user
    $insert_query = "INSERT INTO users (user_type, email, username, password_hash, full_name, phone, status, email_verified) 
                     VALUES (:user_type, :email, :username, :password_hash, :full_name, :phone, 'pending', FALSE) 
                     RETURNING id";
    
    $stmt = $db->prepare($insert_query);
    $stmt->bindParam(':user_type', $user_type);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':phone', $phone);
    $stmt->execute();
    
    $user_id = $stmt->fetchColumn();
    
    // If merchant, create merchant record
    if ($user_type === 'merchant' && !empty($data['business_name'])) {
        $business_name = Security::sanitizeInput($data['business_name']);
        $business_type = Security::sanitizeInput($data['business_type'] ?? '');
        $merchant_id = 'MERCH' . str_pad($user_id, 6, '0', STR_PAD_LEFT);
        
        // Generate API credentials
        $api_key = Security::generateAPIKey();
        $api_secret = Security::generateAPISecret();
        
        $merchant_query = "INSERT INTO merchants (user_id, merchant_id, business_name, business_type, api_key, api_secret, kyc_status) 
                          VALUES (:user_id, :merchant_id, :business_name, :business_type, :api_key, :api_secret, 'pending')";
        
        $merchant_stmt = $db->prepare($merchant_query);
        $merchant_stmt->bindParam(':user_id', $user_id);
        $merchant_stmt->bindParam(':merchant_id', $merchant_id);
        $merchant_stmt->bindParam(':business_name', $business_name);
        $merchant_stmt->bindParam(':business_type', $business_type);
        $merchant_stmt->bindParam(':api_key', $api_key);
        $merchant_stmt->bindParam(':api_secret', $api_secret);
        $merchant_stmt->execute();
    }
    
    // If reseller, create reseller record
    if ($user_type === 'reseller') {
        $reseller_code = 'RES' . str_pad($user_id, 6, '0', STR_PAD_LEFT);
        
        $reseller_query = "INSERT INTO resellers (user_id, reseller_code, commission_rate, status) 
                          VALUES (:user_id, :reseller_code, 30.00, 'active')";
        
        $reseller_stmt = $db->prepare($reseller_query);
        $reseller_stmt->bindParam(':user_id', $user_id);
        $reseller_stmt->bindParam(':reseller_code', $reseller_code);
        $reseller_stmt->execute();
    }
    
    // Create welcome notification
    $notif_query = "INSERT INTO notifications (user_id, title, message, type) 
                    VALUES (:user_id, :title, :message, 'success')";
    
    $notif_stmt = $db->prepare($notif_query);
    $notif_stmt->bindParam(':user_id', $user_id);
    
    $title = 'Welcome to PayMe Gateway!';
    $message = 'Your account has been created successfully. Please verify your email to get started.';
    
    $notif_stmt->bindParam(':title', $title);
    $notif_stmt->bindParam(':message', $message);
    $notif_stmt->execute();
    
    // Commit transaction
    $db->commit();
    
    // Success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'Registration successful! Please check your email for verification.',
        'data' => [
            'user_id' => $user_id,
            'email' => $email,
            'user_type' => $user_type
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("Registration Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
}
?>
