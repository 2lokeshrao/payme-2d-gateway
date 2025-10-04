<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$type = $input['type'] ?? '';
$data = $input['data'] ?? [];

if (empty($type) || empty($data)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required data'
    ]);
    exit;
}

try {
    // Create config table if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS payment_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            config_key VARCHAR(100) UNIQUE,
            config_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    
    // Save configuration based on type
    foreach ($data as $key => $value) {
        $configKey = $type . '_' . $key;
        
        $stmt = $pdo->prepare("
            INSERT INTO payment_config (config_key, config_value) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE config_value = ?
        ");
        
        $stmt->execute([$configKey, $value, $value]);
    }
    
    // Also update the config file
    updateConfigFile($type, $data);
    
    echo json_encode([
        'success' => true,
        'message' => 'Configuration saved successfully'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error saving configuration: ' . $e->getMessage()
    ]);
}

function updateConfigFile($type, $data) {
    $configFile = '../../config/payment-config.php';
    
    if (!file_exists($configFile)) {
        return;
    }
    
    $content = file_get_contents($configFile);
    
    // Update values in config file
    if ($type === 'bank') {
        $content = preg_replace("/define\('ADMIN_BANK_NAME', '.*?'\);/", "define('ADMIN_BANK_NAME', '{$data['bank_name']}');", $content);
        $content = preg_replace("/define\('ADMIN_ACCOUNT_HOLDER', '.*?'\);/", "define('ADMIN_ACCOUNT_HOLDER', '{$data['account_holder']}');", $content);
        $content = preg_replace("/define\('ADMIN_ACCOUNT_NUMBER', '.*?'\);/", "define('ADMIN_ACCOUNT_NUMBER', '{$data['account_number']}');", $content);
        $content = preg_replace("/define\('ADMIN_IFSC_CODE', '.*?'\);/", "define('ADMIN_IFSC_CODE', '{$data['ifsc_code']}');", $content);
        $content = preg_replace("/define\('ADMIN_ACCOUNT_TYPE', '.*?'\);/", "define('ADMIN_ACCOUNT_TYPE', '{$data['account_type']}');", $content);
    } elseif ($type === 'upi') {
        $content = preg_replace("/define\('ADMIN_UPI_ID', '.*?'\);/", "define('ADMIN_UPI_ID', '{$data['upi_id']}');", $content);
        $content = preg_replace("/define\('ADMIN_UPI_NAME', '.*?'\);/", "define('ADMIN_UPI_NAME', '{$data['upi_name']}');", $content);
        $content = preg_replace("/define\('ADMIN_UPI_PROVIDER', '.*?'\);/", "define('ADMIN_UPI_PROVIDER', '{$data['upi_provider']}');", $content);
    } elseif ($type === 'crypto') {
        $content = preg_replace("/define\('ADMIN_BTC_WALLET', '.*?'\);/", "define('ADMIN_BTC_WALLET', '{$data['btc_wallet']}');", $content);
        $content = preg_replace("/define\('ADMIN_ETH_WALLET', '.*?'\);/", "define('ADMIN_ETH_WALLET', '{$data['eth_wallet']}');", $content);
        $content = preg_replace("/define\('ADMIN_USDT_WALLET', '.*?'\);/", "define('ADMIN_USDT_WALLET', '{$data['usdt_wallet']}');", $content);
        $content = preg_replace("/define\('ADMIN_BNB_WALLET', '.*?'\);/", "define('ADMIN_BNB_WALLET', '{$data['bnb_wallet']}');", $content);
    } elseif ($type === 'email') {
        $content = preg_replace("/define\('SMTP_HOST', '.*?'\);/", "define('SMTP_HOST', '{$data['smtp_host']}');", $content);
        $content = preg_replace("/define\('SMTP_PORT', .*?\);/", "define('SMTP_PORT', {$data['smtp_port']});", $content);
        $content = preg_replace("/define\('SMTP_USERNAME', '.*?'\);/", "define('SMTP_USERNAME', '{$data['smtp_username']}');", $content);
        $content = preg_replace("/define\('SMTP_PASSWORD', '.*?'\);/", "define('SMTP_PASSWORD', '{$data['smtp_password']}');", $content);
        $content = preg_replace("/define\('SMTP_FROM_EMAIL', '.*?'\);/", "define('SMTP_FROM_EMAIL', '{$data['from_email']}');", $content);
        $content = preg_replace("/define\('SMTP_FROM_NAME', '.*?'\);/", "define('SMTP_FROM_NAME', '{$data['from_name']}');", $content);
        $content = preg_replace("/define\('ADMIN_NOTIFICATION_EMAIL', '.*?'\);/", "define('ADMIN_NOTIFICATION_EMAIL', '{$data['admin_notification_email']}');", $content);
    }
    
    file_put_contents($configFile, $content);
}
?>
