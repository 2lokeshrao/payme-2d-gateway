<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$email = sanitizeInput($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($email) || empty($password)) {
    jsonResponse(false, 'Email and password are required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, 'Invalid email address');
}

$conn = getDBConnection();
if (!$conn) {
    jsonResponse(false, 'Database connection failed');
}

// Get user
$stmt = $conn->prepare("SELECT id, full_name, email, password_hash, status FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    jsonResponse(false, 'Invalid email or password');
}

$user = $result->fetch_assoc();

// Verify password
if (!password_verify($password, $user['password_hash'])) {
    jsonResponse(false, 'Invalid email or password');
}

// Check account status
if ($user['status'] !== 'active') {
    jsonResponse(false, 'Account is inactive or suspended');
}

// Create session
startSecureSession();
$sessionToken = generateSessionToken();
$expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));

$stmt = $conn->prepare("INSERT INTO sessions (user_id, session_token, user_type, expires_at) VALUES (?, ?, 'user', ?)");
$stmt->bind_param("iss", $user['id'], $sessionToken, $expiresAt);
$stmt->execute();

$_SESSION['user_id'] = $user['id'];
$_SESSION['session_token'] = $sessionToken;

jsonResponse(true, 'Login successful', [
    'user_id' => $user['id'],
    'name' => $user['full_name'],
    'email' => $user['email'],
    'token' => $sessionToken
]);

$stmt->close();
$conn->close();
?>
