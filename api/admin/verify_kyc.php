<?php
/**
 * Admin KYC Verification API
 * Allows admin to verify or reject KYC documents
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/security.php';

// Start session
session_start();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin access required']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data['document_id']) || empty($data['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Document ID and action are required']);
    exit;
}

$document_id = intval($data['document_id']);
$action = Security::sanitizeInput($data['action']);
$rejection_reason = Security::sanitizeInput($data['rejection_reason'] ?? '');

// Validate action
if (!in_array($action, ['verify', 'reject'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid action. Use "verify" or "reject"']);
    exit;
}

// If rejecting, reason is required
if ($action === 'reject' && empty($rejection_reason)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
    exit;
}

try {
    // Database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    // Get document details
    $doc_query = "SELECT kd.*, m.id as merchant_id, m.user_id, m.merchant_id as merchant_code 
                  FROM kyc_documents kd 
                  JOIN merchants m ON kd.merchant_id = m.id 
                  WHERE kd.id = :document_id";
    
    $doc_stmt = $db->prepare($doc_query);
    $doc_stmt->bindParam(':document_id', $document_id);
    $doc_stmt->execute();
    
    $document = $doc_stmt->fetch();
    
    if (!$document) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Document not found']);
        exit;
    }
    
    // Check if already processed
    if ($document['verification_status'] !== 'pending') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Document already processed']);
        exit;
    }
    
    // Start transaction
    $db->beginTransaction();
    
    // Update document status
    $verification_status = ($action === 'verify') ? 'verified' : 'rejected';
    
    $update_query = "UPDATE kyc_documents 
                     SET verification_status = :status,
                         verified_by = :admin_id,
                         verified_at = CURRENT_TIMESTAMP,
                         rejection_reason = :rejection_reason
                     WHERE id = :document_id";
    
    $update_stmt = $db->prepare($update_query);
    $update_stmt->bindParam(':status', $verification_status);
    $update_stmt->bindParam(':admin_id', $_SESSION['user_id']);
    $update_stmt->bindParam(':rejection_reason', $rejection_reason);
    $update_stmt->bindParam(':document_id', $document_id);
    $update_stmt->execute();
    
    // Check if all merchant documents are verified
    $check_query = "SELECT COUNT(*) as total,
                           SUM(CASE WHEN verification_status = 'verified' THEN 1 ELSE 0 END) as verified
                    FROM kyc_documents 
                    WHERE merchant_id = :merchant_id";
    
    $check_stmt = $db->prepare($check_query);
    $check_stmt->bindParam(':merchant_id', $document['merchant_id']);
    $check_stmt->execute();
    
    $kyc_status = $check_stmt->fetch();
    
    // Update merchant KYC status
    $merchant_kyc_status = 'submitted';
    
    if ($action === 'verify' && $kyc_status['total'] > 0 && $kyc_status['verified'] >= 3) {
        // If at least 3 documents verified, mark merchant as verified
        $merchant_kyc_status = 'verified';
        
        $merchant_update = "UPDATE merchants 
                           SET kyc_status = 'verified', kyc_verified_at = CURRENT_TIMESTAMP 
                           WHERE id = :merchant_id";
        
        $merchant_stmt = $db->prepare($merchant_update);
        $merchant_stmt->bindParam(':merchant_id', $document['merchant_id']);
        $merchant_stmt->execute();
        
        // Update user status to active
        $user_update = "UPDATE users SET status = 'active' WHERE id = :user_id";
        $user_stmt = $db->prepare($user_update);
        $user_stmt->bindParam(':user_id', $document['user_id']);
        $user_stmt->execute();
    } elseif ($action === 'reject') {
        $merchant_kyc_status = 'rejected';
        
        $merchant_update = "UPDATE merchants SET kyc_status = 'rejected' WHERE id = :merchant_id";
        $merchant_stmt = $db->prepare($merchant_update);
        $merchant_stmt->bindParam(':merchant_id', $document['merchant_id']);
        $merchant_stmt->execute();
    }
    
    // Create notification for merchant
    $notif_query = "INSERT INTO notifications (user_id, title, message, type) 
                    VALUES (:user_id, :title, :message, :type)";
    
    $notif_stmt = $db->prepare($notif_query);
    $notif_stmt->bindParam(':user_id', $document['user_id']);
    
    if ($action === 'verify') {
        $title = 'KYC Document Verified';
        $message = 'Your ' . str_replace('_', ' ', $document['document_type']) . ' has been verified successfully.';
        $type = 'success';
    } else {
        $title = 'KYC Document Rejected';
        $message = 'Your ' . str_replace('_', ' ', $document['document_type']) . ' was rejected. Reason: ' . $rejection_reason;
        $type = 'error';
    }
    
    $notif_stmt->bindParam(':title', $title);
    $notif_stmt->bindParam(':message', $message);
    $notif_stmt->bindParam(':type', $type);
    $notif_stmt->execute();
    
    // Commit transaction
    $db->commit();
    
    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'KYC document ' . ($action === 'verify' ? 'verified' : 'rejected') . ' successfully',
        'data' => [
            'document_id' => $document_id,
            'merchant_id' => $document['merchant_code'],
            'verification_status' => $verification_status,
            'merchant_kyc_status' => $merchant_kyc_status,
            'verified_at' => date('Y-m-d H:i:s')
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    
    error_log("KYC Verification Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'KYC verification failed']);
}
?>
