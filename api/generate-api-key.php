<?php
header('Content-Type: application/json');
require_once '../config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$keyType = $_POST['key_type'] ?? 'test';

// Generate API key and secret
$apiKey = 'pk_' . $keyType . '_' . bin2hex(random_bytes(16));
$apiSecret = 'sk_' . $keyType . '_' . bin2hex(random_bytes(16));

// Hash the secret before storing
$hashedSecret = password_hash($apiSecret, PASSWORD_DEFAULT);

// Insert API key
$stmt = $conn->prepare("
    INSERT INTO api_keys (user_id, api_key, api_secret, key_type, is_active)
    VALUES (?, ?, ?, ?, 1)
");

$stmt->bind_param('ssss', $userId, $apiKey, $hashedSecret, $keyType);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'api_key' => $apiKey,
        'api_secret' => $apiSecret,
        'key_type' => $keyType,
        'message' => 'API key generated successfully. Save the secret - you won\'t see it again!'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to generate API key']);
}

$stmt->close();
$conn->close();
?>
