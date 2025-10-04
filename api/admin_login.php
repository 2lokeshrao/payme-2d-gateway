<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

// Hardcoded credentials for testing (works without database)
$validCredentials = [
    'admin' => 'admin123',
    'admin@payme2d.com' => 'admin123'
];

// Check credentials
if (isset($validCredentials[$username]) && $validCredentials[$username] === $password) {
    // Login successful
    $_SESSION['admin_id'] = 1;
    $_SESSION['admin_email'] = 'admin@payme2d.com';
    $_SESSION['admin_name'] = 'Admin User';
    $_SESSION['admin_role'] = 'admin';
    
    // Generate token
    $token = bin2hex(random_bytes(32));
    $_SESSION['admin_token'] = $token;
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'data' => [
            'admin_id' => 1,
            'username' => 'Admin User',
            'email' => 'admin@payme2d.com',
            'token' => $token
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid username or password. Use: admin / admin123'
    ]);
}
?>
