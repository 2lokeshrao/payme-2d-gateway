<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$sql = "SELECT * FROM resellers WHERE email = ? AND status = 'active'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password_hash'])) {
        session_start();
        $_SESSION['reseller_id'] = $row['id'];
        $_SESSION['reseller_name'] = $row['name'];
        $_SESSION['reseller_email'] = $row['email'];
        
        unset($row['password_hash']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'reseller' => $row
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid password']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Reseller not found or inactive']);
}

$conn->close();
?>
