<?php
header('Content-Type: application/json');
require_once '../config/payment-config.php';

// Return admin payment details for display on purchase page
$response = [
    'success' => true,
    'upi_id' => ADMIN_UPI_ID,
    'upi_name' => ADMIN_UPI_NAME,
    'upi_provider' => ADMIN_UPI_PROVIDER,
    'bank_name' => ADMIN_BANK_NAME,
    'account_holder' => ADMIN_ACCOUNT_HOLDER,
    'account_number' => ADMIN_ACCOUNT_NUMBER,
    'ifsc_code' => ADMIN_IFSC_CODE,
    'account_type' => ADMIN_ACCOUNT_TYPE,
    'btc_wallet' => ADMIN_BTC_WALLET,
    'eth_wallet' => ADMIN_ETH_WALLET,
    'usdt_wallet' => ADMIN_USDT_WALLET,
    'bnb_wallet' => ADMIN_BNB_WALLET
];

echo json_encode($response);
?>
