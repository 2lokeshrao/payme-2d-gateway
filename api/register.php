<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method');
}

$fullName = sanitizeInput($_POST['fullName'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($fullName) || empty($email) || empty($phone) || empty($password)) {
    jsonResponse(false, 'All fields are required');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, 'Invalid email address');
}

if (strlen($password) < 6) {
    jsonResponse(false, 'Password must be at least 6 characters');
}

$conn = getDBConnection();
if (!$conn) {
    jsonResponse(false, 'Database connection failed');
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    jsonResponse(false, 'Email already registered');
}

// Hash password
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $fullName, $email, $phone, $passwordHash);

if ($stmt->execute()) {
    jsonResponse(true, 'Registration successful', ['user_id' => $conn->insert_id]);
} else {
    jsonResponse(false, 'Registration failed. Please try again.');
}

$stmt->close();
$conn->close();
?>
