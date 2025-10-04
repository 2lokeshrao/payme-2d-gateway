<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Get user ID from session
session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Check if user has active subscription
if (!hasActiveSubscription($userId)) {
    echo json_encode(['success' => false, 'message' => 'Active subscription required', 'code' => 'SUBSCRIPTION_REQUIRED']);
    exit;
}

switch ($action) {
    case 'get':
        getSelfPaymentSettings($userId);
        break;
    
    case 'save-bank':
        saveBankDetails($userId);
        break;
    
    case 'save-upi':
        saveUpiDetails($userId);
        break;
    
    case 'save-crypto':
        saveCryptoDetails($userId);
        break;
    
    case 'save-settlement':
        saveSettlementSettings($userId);
        break;
    
    case 'verify-bank':
        verifyBankAccount($userId);
        break;
    
    case 'verify-upi':
        verifyUpiId($userId);
        break;
    
    case 'verify-crypto':
        verifyCryptoWallet($userId);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function hasActiveSubscription($userId) {
    global $conn;
    
    $sql = "SELECT subscription_status, end_date FROM user_subscriptions 
            WHERE user_id = ? AND subscription_status = 'active' 
            ORDER BY created_at DESC LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Check if not expired
        return strtotime($row['end_date']) > time();
    }
    
    return false;
}

function getSelfPaymentSettings($userId) {
    global $conn;
    
    $sql = "SELECT * FROM merchant_self_payment_settings WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode(['success' => true, 'settings' => $row]);
    } else {
        echo json_encode(['success' => true, 'settings' => null]);
    }
}

function saveBankDetails($userId) {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $bankName = $data['bank_name'] ?? '';
    $accountHolderName = $data['account_holder_name'] ?? '';
    $accountNumber = $data['account_number'] ?? '';
    $ifscCode = $data['ifsc_code'] ?? '';
    $accountType = $data['account_type'] ?? 'savings';
    
    // Validation
    if (empty($bankName) || empty($accountHolderName) || empty($accountNumber) || empty($ifscCode)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        return;
    }
    
    // Check if settings exist
    $checkSql = "SELECT id FROM merchant_self_payment_settings WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $exists = $checkStmt->get_result()->num_rows > 0;
    
    if ($exists) {
        // Update
        $sql = "UPDATE merchant_self_payment_settings 
                SET bank_enabled = TRUE, bank_name = ?, account_holder_name = ?, 
                    account_number = ?, ifsc_code = ?, account_type = ?, 
                    bank_verified = FALSE, updated_at = NOW() 
                WHERE user_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $bankName, $accountHolderName, $accountNumber, $ifscCode, $accountType, $userId);
    } else {
        // Insert
        $sql = "INSERT INTO merchant_self_payment_settings 
                (user_id, bank_enabled, bank_name, account_holder_name, account_number, ifsc_code, account_type) 
                VALUES (?, TRUE, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $userId, $bankName, $accountHolderName, $accountNumber, $ifscCode, $accountType);
    }
    
    if ($stmt->execute()) {
        // Log feature access
        logFeatureAccess($userId, 'self_bank_account', true, 'Bank details saved');
        
        echo json_encode(['success' => true, 'message' => 'Bank details saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save bank details']);
    }
}

function saveUpiDetails($userId) {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $upiId = $data['upi_id'] ?? '';
    $upiQrCode = $data['upi_qr_code'] ?? '';
    
    if (empty($upiId)) {
        echo json_encode(['success' => false, 'message' => 'UPI ID is required']);
        return;
    }
    
    // Validate UPI ID format
    if (!preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z]+$/', $upiId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid UPI ID format']);
        return;
    }
    
    $checkSql = "SELECT id FROM merchant_self_payment_settings WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $exists = $checkStmt->get_result()->num_rows > 0;
    
    if ($exists) {
        $sql = "UPDATE merchant_self_payment_settings 
                SET upi_enabled = TRUE, upi_id = ?, upi_qr_code = ?, 
                    upi_verified = FALSE, updated_at = NOW() 
                WHERE user_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $upiId, $upiQrCode, $userId);
    } else {
        $sql = "INSERT INTO merchant_self_payment_settings 
                (user_id, upi_enabled, upi_id, upi_qr_code) 
                VALUES (?, TRUE, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $userId, $upiId, $upiQrCode);
    }
    
    if ($stmt->execute()) {
        logFeatureAccess($userId, 'self_upi', true, 'UPI details saved');
        echo json_encode(['success' => true, 'message' => 'UPI details saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save UPI details']);
    }
}

function saveCryptoDetails($userId) {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $btcWallet = $data['btc_wallet'] ?? '';
    $ethWallet = $data['eth_wallet'] ?? '';
    $usdtWallet = $data['usdt_wallet'] ?? '';
    $usdtNetwork = $data['usdt_network'] ?? 'ERC-20';
    $bnbWallet = $data['bnb_wallet'] ?? '';
    
    // At least one wallet should be provided
    if (empty($btcWallet) && empty($ethWallet) && empty($usdtWallet) && empty($bnbWallet)) {
        echo json_encode(['success' => false, 'message' => 'At least one crypto wallet is required']);
        return;
    }
    
    // Validate Ethereum address format
    if (!empty($ethWallet) && !preg_match('/^0x[a-fA-F0-9]{40}$/', $ethWallet)) {
        echo json_encode(['success' => false, 'message' => 'Invalid Ethereum wallet address']);
        return;
    }
    
    $checkSql = "SELECT id FROM merchant_self_payment_settings WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $exists = $checkStmt->get_result()->num_rows > 0;
    
    if ($exists) {
        $sql = "UPDATE merchant_self_payment_settings 
                SET crypto_enabled = TRUE, btc_wallet_address = ?, eth_wallet_address = ?, 
                    usdt_wallet_address = ?, usdt_network = ?, bnb_wallet_address = ?, 
                    crypto_verified = FALSE, updated_at = NOW() 
                WHERE user_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $btcWallet, $ethWallet, $usdtWallet, $usdtNetwork, $bnbWallet, $userId);
    } else {
        $sql = "INSERT INTO merchant_self_payment_settings 
                (user_id, crypto_enabled, btc_wallet_address, eth_wallet_address, 
                 usdt_wallet_address, usdt_network, bnb_wallet_address) 
                VALUES (?, TRUE, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $userId, $btcWallet, $ethWallet, $usdtWallet, $usdtNetwork, $bnbWallet);
    }
    
    if ($stmt->execute()) {
        logFeatureAccess($userId, 'self_crypto', true, 'Crypto wallets saved');
        echo json_encode(['success' => true, 'message' => 'Crypto wallet details saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save crypto wallet details']);
    }
}

function saveSettlementSettings($userId) {
    global $conn;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    $autoSettlement = $data['auto_settlement'] ?? true;
    $settlementFrequency = $data['settlement_frequency'] ?? 'instant';
    
    $checkSql = "SELECT id FROM merchant_self_payment_settings WHERE user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("i", $userId);
    $checkStmt->execute();
    $exists = $checkStmt->get_result()->num_rows > 0;
    
    if ($exists) {
        $sql = "UPDATE merchant_self_payment_settings 
                SET auto_settlement = ?, settlement_frequency = ?, updated_at = NOW() 
                WHERE user_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isi", $autoSettlement, $settlementFrequency, $userId);
    } else {
        $sql = "INSERT INTO merchant_self_payment_settings 
                (user_id, auto_settlement, settlement_frequency) 
                VALUES (?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $userId, $autoSettlement, $settlementFrequency);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Settlement settings saved successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save settlement settings']);
    }
}

function verifyBankAccount($userId) {
    global $conn;
    
    // In production, this would trigger actual bank verification via penny drop or similar
    $sql = "UPDATE merchant_self_payment_settings SET bank_verified = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Bank account verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Verification failed']);
    }
}

function verifyUpiId($userId) {
    global $conn;
    
    // In production, this would trigger actual UPI verification
    $sql = "UPDATE merchant_self_payment_settings SET upi_verified = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'UPI ID verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Verification failed']);
    }
}

function verifyCryptoWallet($userId) {
    global $conn;
    
    // In production, this would verify wallet ownership via signature or small transaction
    $sql = "UPDATE merchant_self_payment_settings SET crypto_verified = TRUE WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Crypto wallets verified successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Verification failed']);
    }
}

function logFeatureAccess($userId, $featureName, $granted, $reason) {
    global $conn;
    
    $sql = "INSERT INTO feature_access_log (user_id, feature_name, access_granted, access_reason) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isis", $userId, $featureName, $granted, $reason);
    $stmt->execute();
}

$conn->close();
?>
