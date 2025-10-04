<?php
header('Content-Type: application/json');
require_once '../config/database.php';
require_once '../config/payment-config.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$plan = $data['plan'] ?? '';
$amount = $data['amount'] ?? 0;
$payment_method = $data['payment_method'] ?? '';
$transaction_id = $data['transaction_id'] ?? '';
$customer_name = $data['customer_name'] ?? '';
$customer_email = $data['customer_email'] ?? '';
$customer_phone = $data['customer_phone'] ?? '';

// Validate input
if (empty($plan) || empty($amount) || empty($payment_method) || empty($transaction_id) || empty($customer_email)) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required fields'
    ]);
    exit;
}

try {
    // Generate activation code
    $activation_code = generateActivationCode();
    
    // Calculate validity days based on plan
    $validity_days = [
        'monthly' => 30,
        'quarterly' => 90,
        'yearly' => 365,
        'lifetime' => 36500 // 100 years
    ];
    
    $validity = $validity_days[$plan] ?? 365;
    $expiry_date = date('Y-m-d H:i:s', strtotime("+{$validity} days"));
    
    // Get plan ID from database
    $stmt = $pdo->prepare("SELECT id, name FROM subscription_plans WHERE plan_type = ? LIMIT 1");
    $stmt->execute([$plan]);
    $plan_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$plan_data) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid plan selected'
        ]);
        exit;
    }
    
    // Insert activation code into database
    $stmt = $pdo->prepare("
        INSERT INTO activation_codes 
        (code, plan_id, status, validity_days, expiry_date, customer_email, customer_name, customer_phone, created_at) 
        VALUES (?, ?, 'unused', ?, ?, ?, ?, ?, NOW())
    ");
    
    $stmt->execute([
        $activation_code,
        $plan_data['id'],
        $validity,
        $expiry_date,
        $customer_email,
        $customer_name,
        $customer_phone
    ]);
    
    // Record payment transaction
    $stmt = $pdo->prepare("
        INSERT INTO code_purchases 
        (activation_code, plan_type, amount, payment_method, transaction_id, customer_name, customer_email, customer_phone, status, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'completed', NOW())
    ");
    
    $stmt->execute([
        $activation_code,
        $plan,
        $amount,
        $payment_method,
        $transaction_id,
        $customer_name,
        $customer_email,
        $customer_phone
    ]);
    
    // Send email with activation code
    sendActivationCodeEmail($customer_email, $customer_name, $activation_code, $plan_data['name'], $validity);
    
    // Send notification to admin
    if (SEND_ADMIN_NOTIFICATION) {
        sendAdminNotification($customer_name, $customer_email, $plan_data['name'], $amount, $payment_method, $transaction_id);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Payment verified and activation code generated',
        'activation_code' => $activation_code,
        'plan_name' => $plan_data['name'],
        'validity_days' => $validity,
        'expiry_date' => $expiry_date
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error processing payment: ' . $e->getMessage()
    ]);
}

function generateActivationCode() {
    // Generate format: PM2D-XXXX-XXXX-XXXX
    $segments = [];
    for ($i = 0; $i < 3; $i++) {
        $segments[] = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 4));
    }
    return 'PM2D-' . implode('-', $segments);
}

function sendActivationCodeEmail($email, $name, $code, $plan_name, $validity_days) {
    $subject = "Your PayMe 2D Gateway Activation Code";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .code-box { background: white; padding: 20px; border: 3px dashed #667eea; border-radius: 10px; text-align: center; margin: 20px 0; }
            .code { font-size: 24px; font-weight: bold; color: #667eea; letter-spacing: 2px; }
            .button { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
            .info-box { background: white; padding: 15px; border-left: 4px solid #667eea; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🎉 Welcome to PayMe 2D Gateway!</h1>
            </div>
            <div class='content'>
                <h2>Hi {$name},</h2>
                <p>Thank you for purchasing <strong>{$plan_name}</strong>! Your payment has been confirmed.</p>
                
                <div class='code-box'>
                    <p style='margin: 0; font-size: 14px; color: #666;'>Your Activation Code</p>
                    <div class='code'>{$code}</div>
                </div>
                
                <div class='info-box'>
                    <strong>📋 Plan Details:</strong><br>
                    Plan: {$plan_name}<br>
                    Validity: {$validity_days} days<br>
                    Status: Active
                </div>
                
                <h3>🚀 How to Activate:</h3>
                <ol>
                    <li>Go to <a href='" . WEBSITE_URL . "/activate-subscription.html'>Activation Page</a></li>
                    <li>Login to your account (or create one if you haven't)</li>
                    <li>Enter the activation code above</li>
                    <li>Click 'Activate Subscription'</li>
                    <li>Start accepting payments!</li>
                </ol>
                
                <div style='text-align: center;'>
                    <a href='" . WEBSITE_URL . "/activate-subscription.html' class='button'>Activate Now</a>
                </div>
                
                <div class='info-box'>
                    <strong>💡 Quick Start Guide:</strong><br>
                    1. Activate your subscription<br>
                    2. Add your bank account/UPI details<br>
                    3. Create payment links<br>
                    4. Start receiving payments directly!
                </div>
                
                <p>If you have any questions, feel free to contact our support team.</p>
                
                <p>Best regards,<br>
                <strong>PayMe 2D Gateway Team</strong></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">" . "\r\n";
    
    mail($email, $subject, $message, $headers);
}

function sendAdminNotification($customer_name, $customer_email, $plan_name, $amount, $payment_method, $transaction_id) {
    $subject = "New Code Purchase - PayMe 2D Gateway";
    
    $message = "
    <html>
    <body style='font-family: Arial, sans-serif;'>
        <h2>New Code Purchase Notification</h2>
        <p><strong>Customer Details:</strong></p>
        <ul>
            <li>Name: {$customer_name}</li>
            <li>Email: {$customer_email}</li>
        </ul>
        <p><strong>Purchase Details:</strong></p>
        <ul>
            <li>Plan: {$plan_name}</li>
            <li>Amount: ₹{$amount}</li>
            <li>Payment Method: {$payment_method}</li>
            <li>Transaction ID: {$transaction_id}</li>
            <li>Date: " . date('Y-m-d H:i:s') . "</li>
        </ul>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_EMAIL . "\r\n";
    
    mail(ADMIN_NOTIFICATION_EMAIL, $subject, $message, $headers);
}
?>
