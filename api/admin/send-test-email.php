<?php
header('Content-Type: application/json');
require_once '../../config/database.php';

$input = json_decode(file_get_contents('php://input'), true);
$testEmail = $input['email'] ?? '';

if (empty($testEmail)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email address required'
    ]);
    exit;
}

try {
    // Get email configuration
    $stmt = $pdo->query("SELECT config_key, config_value FROM payment_config WHERE config_key LIKE 'email_%'");
    $configs = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    $subject = "Test Email - PayMe 2D Gateway";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>✅ Email Configuration Test</h1>
            </div>
            <div class='content'>
                <h2>Congratulations!</h2>
                <p>Your email configuration is working correctly.</p>
                <p>This is a test email sent from PayMe 2D Gateway admin panel.</p>
                <p><strong>Test Details:</strong></p>
                <ul>
                    <li>Date: " . date('Y-m-d H:i:s') . "</li>
                    <li>Recipient: {$testEmail}</li>
                    <li>Status: Success</li>
                </ul>
                <p>You can now send activation codes to customers automatically!</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $fromEmail = $configs['email_from_email'] ?? 'noreply@payme-gateway.com';
    $fromName = $configs['email_from_name'] ?? 'PayMe 2D Gateway';
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: {$fromName} <{$fromEmail}>" . "\r\n";
    
    $sent = mail($testEmail, $subject, $message, $headers);
    
    if ($sent) {
        echo json_encode([
            'success' => true,
            'message' => 'Test email sent successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to send test email'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
?>
