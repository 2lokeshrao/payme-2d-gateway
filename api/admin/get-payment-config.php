<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

try {
    // Get all configuration from database
    $stmt = $pdo->query("SELECT config_key, config_value FROM payment_config");
    $configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $response = [
        'success' => true,
        // Bank details
        'bank_name' => $configs['bank_bank_name'] ?? '',
        'account_type' => $configs['bank_account_type'] ?? 'Savings',
        'account_holder' => $configs['bank_account_holder'] ?? '',
        'account_number' => $configs['bank_account_number'] ?? '',
        'ifsc_code' => $configs['bank_ifsc_code'] ?? '',
        
        // UPI details
        'upi_id' => $configs['upi_upi_id'] ?? '',
        'upi_name' => $configs['upi_upi_name'] ?? '',
        'upi_provider' => $configs['upi_upi_provider'] ?? '',
        
        // Crypto wallets
        'btc_wallet' => $configs['crypto_btc_wallet'] ?? '',
        'eth_wallet' => $configs['crypto_eth_wallet'] ?? '',
        'usdt_wallet' => $configs['crypto_usdt_wallet'] ?? '',
        'bnb_wallet' => $configs['crypto_bnb_wallet'] ?? '',
        
        // Email config
        'smtp_host' => $configs['email_smtp_host'] ?? 'smtp.gmail.com',
        'smtp_port' => $configs['email_smtp_port'] ?? '587',
        'smtp_username' => $configs['email_smtp_username'] ?? '',
        'from_email' => $configs['email_from_email'] ?? 'noreply@payme-gateway.com',
        'from_name' => $configs['email_from_name'] ?? 'PayMe 2D Gateway',
        'admin_notification_email' => $configs['email_admin_notification_email'] ?? ''
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error loading configuration: ' . $e->getMessage()
    ]);
}
?>
