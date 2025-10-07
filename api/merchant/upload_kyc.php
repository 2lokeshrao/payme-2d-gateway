<?php
/**
 * Upload KYC Documents API
 * Handles KYC document uploads for merchants
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-API-Key');

require_once '../config/database.php';
require_once '../config/security.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get API Key from header
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';

if (empty($api_key)) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'API Key is required']);
    exit;
}

// Validate file upload
if (!isset($_FILES['document']) || $_FILES['document']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Document file is required']);
    exit;
}

// Get POST data
$document_type = Security::sanitizeInput($_POST['document_type'] ?? '');
$document_number = Security::sanitizeInput($_POST['document_number'] ?? '');

// Validate document type
$valid_types = ['pan_card', 'aadhar_card', 'gst_certificate', 'bank_statement', 'business_proof', 'other'];
if (!in_array($document_type, $valid_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid document type']);
    exit;
}

// Validate file
$file = $_FILES['document'];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];
$max_file_size = 5 * 1024 * 1024; // 5MB

$file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: JPG, PNG, PDF']);
    exit;
}

if ($file['size'] > $max_file_size) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'File size exceeds 5MB limit']);
    exit;
}

try {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Verify API Key and get merchant
    $merchant_query = "SELECT m.id, m.merchant_id, m.business_name, m.kyc_status 
                       FROM merchants m 
                       WHERE m.api_key = :api_key";
    
    $merchant_stmt = $db->prepare($merchant_query);
    $merchant_stmt->bindParam(':api_key', $api_key);
    $merchant_stmt->execute();
    
    $merchant = $merchant_stmt->fetch();
    
    if (!$merchant) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid API Key']);
        exit;
    }
    
    // Create upload directory if not exists
    $upload_dir = '../uploads/kyc/' . $merchant['merchant_id'] . '/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $filename = $document_type . '_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $file_extension;
    $file_path = $upload_dir . $filename;
    
    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        throw new Exception('Failed to upload file');
    }
    
    // Insert KYC document record
    $insert_query = "INSERT INTO kyc_documents 
                     (merchant_id, document_type, document_number, document_file_path, verification_status) 
                     VALUES (:merchant_id, :document_type, :document_number, :file_path, 'pending') 
                     RETURNING id";
    
    $stmt = $db->prepare($insert_query);
    $stmt->bindParam(':merchant_id', $merchant['id']);
    $stmt->bindParam(':document_type', $document_type);
    $stmt->bindParam(':document_number', $document_number);
    $stmt->bindParam(':file_path', $file_path);
    $stmt->execute();
    
    $document_id = $stmt->fetchColumn();
    
    // Update merchant KYC status to 'submitted' if it was 'pending'
    if ($merchant['kyc_status'] === 'pending') {
        $update_query = "UPDATE merchants SET kyc_status = 'submitted' WHERE id = :merchant_id";
        $update_stmt = $db->prepare($update_query);
        $update_stmt->bindParam(':merchant_id', $merchant['id']);
        $update_stmt->execute();
    }
    
    // Create notification
    $notif_query = "INSERT INTO notifications (user_id, title, message, type) 
                    SELECT user_id, :title, :message, 'info' 
                    FROM merchants WHERE id = :merchant_id";
    
    $notif_stmt = $db->prepare($notif_query);
    $notif_stmt->bindParam(':merchant_id', $merchant['id']);
    
    $title = 'KYC Document Uploaded';
    $message = 'Your ' . str_replace('_', ' ', $document_type) . ' has been uploaded successfully and is under review.';
    
    $notif_stmt->bindParam(':title', $title);
    $notif_stmt->bindParam(':message', $message);
    $notif_stmt->execute();
    
    // Success response
    http_response_code(201);
    echo json_encode([
        'success' => true,
        'message' => 'KYC document uploaded successfully',
        'data' => [
            'document_id' => $document_id,
            'document_type' => $document_type,
            'verification_status' => 'pending',
            'uploaded_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    error_log("KYC Upload Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to upload KYC document']);
}
?>
