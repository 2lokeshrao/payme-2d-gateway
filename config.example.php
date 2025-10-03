<?php
// Database Configuration
// Copy this file to config.php and update with your database credentials

define('DB_HOST', 'localhost');
define('DB_USER', 'your_database_username');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'payme_gateway');

// Create database connection
function getDBConnection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        $conn->set_charset("utf8mb4");
        return $conn;
    } catch (Exception $e) {
        error_log($e->getMessage());
        return null;
    }
}

// Security functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function generateTransactionId() {
    return 'TXN' . time() . rand(1000, 9999);
}

function generateSessionToken() {
    return bin2hex(random_bytes(32));
}

// Session management
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS
        session_start();
    }
}

function isUserLoggedIn() {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['session_token']);
}

function isAdminLoggedIn() {
    startSecureSession();
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_token']);
}

function requireLogin() {
    if (!isUserLoggedIn()) {
        header('Location: login.html');
        exit();
    }
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin/login.html');
        exit();
    }
}

// Response helper
function jsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}
?>
