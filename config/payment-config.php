<?php
/**
 * Payment Configuration for Code Purchase
 * Admin apne payment details yahan configure karenge
 */

// Admin Payment Details
define('ADMIN_PAYMENT_ENABLED', true);

// Bank Account Details (Admin ka account jahan payment aayegi)
define('ADMIN_BANK_NAME', 'HDFC Bank'); // Change to your bank
define('ADMIN_ACCOUNT_HOLDER', 'Your Name'); // Change to your name
define('ADMIN_ACCOUNT_NUMBER', '1234567890'); // Change to your account number
define('ADMIN_IFSC_CODE', 'HDFC0001234'); // Change to your IFSC code
define('ADMIN_ACCOUNT_TYPE', 'Savings'); // Savings or Current

// UPI Details (Admin ki UPI ID)
define('ADMIN_UPI_ID', 'yourname@okhdfc'); // Change to your UPI ID
define('ADMIN_UPI_NAME', 'Your Name'); // Change to your name
define('ADMIN_UPI_PROVIDER', 'Google Pay'); // Google Pay, PhonePe, Paytm, etc.

// Crypto Wallet Details (Optional)
define('ADMIN_BTC_WALLET', ''); // Bitcoin wallet address
define('ADMIN_ETH_WALLET', ''); // Ethereum wallet address
define('ADMIN_USDT_WALLET', ''); // USDT wallet address
define('ADMIN_BNB_WALLET', ''); // BNB wallet address

// Payment Gateway Integration (Optional - for card payments)
define('RAZORPAY_ENABLED', false);
define('RAZORPAY_KEY_ID', ''); // Your Razorpay Key ID
define('RAZORPAY_KEY_SECRET', ''); // Your Razorpay Key Secret

define('STRIPE_ENABLED', false);
define('STRIPE_PUBLIC_KEY', ''); // Your Stripe Public Key
define('STRIPE_SECRET_KEY', ''); // Your Stripe Secret Key

// Email Configuration for sending activation codes
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Change to your email
define('SMTP_PASSWORD', 'your-app-password'); // Gmail App Password
define('SMTP_FROM_EMAIL', 'noreply@payme-gateway.com');
define('SMTP_FROM_NAME', 'PayMe 2D Gateway');

// Automatic Code Generation Settings
define('AUTO_GENERATE_CODE', true); // Automatically generate code after payment
define('CODE_VALIDITY_DAYS', 365); // Default validity for purchased codes

// Payment Notification Settings
define('ADMIN_NOTIFICATION_EMAIL', 'admin@payme-gateway.com'); // Admin ko notification
define('SEND_ADMIN_NOTIFICATION', true);

// Website URL
define('WEBSITE_URL', 'https://payme-gateway.lindy.site');

// Currency
define('DEFAULT_CURRENCY', 'INR');
define('CURRENCY_SYMBOL', '₹');

?>
