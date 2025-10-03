<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$username = sanitizeInput($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($username) || empty($password)) {
    jsonResponse(false, 'Username and password are required');
}

$conn = getDBConnection();
if (!$conn) {
    jsonResponse(false, 'Database connection failed');
}

// Get admin user
$stmt = $conn->prepare("SELECT id, username, email, password_hash, role FROM admin_users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, 'Invalid username or password');
}

$admin = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $admin['password_hash'])) {
    jsonResponse(false, 'Invalid username or password');
}

// Create session
startSecureSession();
$sessionToken = generateSessionToken();
$expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

$stmt = $conn->prepare("INSERT INTO sessions (user_id, session_token, user_type, expires_at) VALUES (?, ?, 'admin', ?)");
$stmt->bind_param("iss", $admin['id'], $sessionToken, $expiresAt);
$stmt->execute();

// Update last login
$stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
$stmt->bind_param("i", $admin['id']);
$stmt->execute();

$_SESSION['admin_id'] = $admin['id'];
$_SESSION['admin_token'] = $sessionToken;

jsonResponse(true, 'Login successful', [
    'admin_id' => $admin['id'],
    'username' => $admin['username'],
    'email' => $admin['email'],
    'role' => $admin['role'],
    'token' => $sessionToken
]);

$stmt->close();
$conn->close();
?>
