<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(false, 'Invalid request method');
}

$conn = getDBConnection();
if (!$conn) {
    jsonResponse(false, 'Database connection failed');
}

// Get all users
$result = $conn->query("
    SELECT 
        id,
        full_name,
        email,
        phone,
        status,
        created_at
    FROM users
    ORDER BY created_at DESC
");

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

jsonResponse(true, 'Users retrieved successfully', [
    'users' => $users
]);

$conn->close();
?>
