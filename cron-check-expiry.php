<?php
/**
 * Daily Cron Job - Check License Expiry
 * Run this daily: 0 9 * * * /usr/bin/php /path/to/cron-check-expiry.php
 */

include_once 'config.php';
include_once 'verify-license.php';

echo "=== License Expiry Check Started at " . date('Y-m-d H:i:s') . " ===\n\n";

// Get all active subscriptions (not lifetime)
$sql = "SELECT * FROM clients WHERE license_type != 'lifetime' AND status IN ('active', 'grace_period')";
$result = $conn->query($sql);

$total_checked = 0;
$reminders_sent = 0;
$expired_count = 0;
$grace_period_count = 0;

while ($client = $result->fetch_assoc()) {
    $total_checked++;
    
    $today = new DateTime();
    $expiry = new DateTime($client['expiry_date']);
    
    // Calculate days difference
    if ($today > $expiry) {
        // Already expired
        $days_expired = $today->diff($expiry)->days;
        
        if ($days_expired <= 3) {
            // Grace period
            echo "⏰ Client #{$client['id']} ({$client['name']}) - Grace Period (Day $days_expired/3)\n";
            updateClientStatus($client['id'], 'grace_period');
            
            // Send grace period reminder
            sendGracePeriodReminder($client['email'], $client['name'], (3 - $days_expired));
            $grace_period_count++;
            
        } else {
            // Expired - disable gateway
            echo "❌ Client #{$client['id']} ({$client['name']}) - EXPIRED - Disabling gateway\n";
            disableGateway($client['id']);
            sendExpiryNotification($client['email'], $client['name']);
            $expired_count++;
        }
        
    } else {
        // Still active
        $days_left = $today->diff($expiry)->days;
        
        // Send reminders at specific intervals
        if ($days_left == 7) {
            echo "🔔 Client #{$client['id']} ({$client['name']}) - 7 days reminder sent\n";
            sendRenewalReminder($client['email'], $client['name'], 7, $client['license_type']);
            $reminders_sent++;
            
        } elseif ($days_left == 3) {
            echo "⚠️ Client #{$client['id']} ({$client['name']}) - 3 days reminder sent\n";
            sendRenewalReminder($client['email'], $client['name'], 3, $client['license_type']);
            $reminders_sent++;
            
        } elseif ($days_left == 1) {
            echo "🚨 Client #{$client['id']} ({$client['name']}) - 1 day reminder sent\n";
            sendRenewalReminder($client['email'], $client['name'], 1, $client['license_type']);
            $reminders_sent++;
            
        } else {
            echo "✅ Client #{$client['id']} ({$client['name']}) - Active ($days_left days left)\n";
        }
    }
}

echo "\n=== Summary ===\n";
echo "Total Checked: $total_checked\n";
echo "Reminders Sent: $reminders_sent\n";
echo "Grace Period: $grace_period_count\n";
echo "Expired & Disabled: $expired_count\n";
echo "\n=== Cron Job Completed at " . date('Y-m-d H:i:s') . " ===\n";

/**
 * Send grace period reminder
 */
function sendGracePeriodReminder($email, $name, $days_left) {
    $subject = "🚨 URGENT: Grace Period - $days_left Days Left";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #dc3545; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #fff; padding: 30px; border: 3px solid #dc3545; }
            .urgent { background: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; margin: 20px 0; }
            .btn { display: inline-block; background: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🚨 URGENT: Grace Period Active</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>$name</strong>,</p>
                
                <div class='urgent'>
                    <h2>⚠️ Your license has EXPIRED</h2>
                    <p>You have <strong>$days_left days</strong> left in grace period before your gateway is completely disabled.</p>
                </div>
                
                <h3>What happens next?</h3>
                <ul>
                    <li>🔴 <strong>After $days_left days:</strong> Gateway will be COMPLETELY DISABLED</li>
                    <li>🚫 <strong>No Transactions:</strong> Your merchants won't be able to process any payments</li>
                    <li>💰 <strong>Lost Revenue:</strong> You'll lose all commission income</li>
                </ul>
                
                <h3>Renew NOW to avoid service interruption!</h3>
                
                <a href='https://payme-gateway-3.lindy.site/renew-license.php' class='btn'>RENEW NOW</a>
                
                <p style='margin-top: 20px; color: #dc3545;'><strong>This is your final warning. Act now!</strong></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: PayMe 2D Gateway <noreply@payme2d.com>" . "\r\n";
    
    mail($email, $subject, $message, $headers);
}

/**
 * Send final expiry notification
 */
function sendExpiryNotification($email, $name) {
    $subject = "❌ Gateway Disabled - License Expired";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1a1a1a; color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #fff; padding: 30px; border: 3px solid #dc3545; }
            .disabled { background: #f8d7da; border-left: 5px solid #dc3545; padding: 15px; margin: 20px 0; }
            .btn { display: inline-block; background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin-top: 20px; font-weight: bold; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>❌ Gateway Disabled</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>$name</strong>,</p>
                
                <div class='disabled'>
                    <h2>🚫 Your gateway has been DISABLED</h2>
                    <p>Your license expired and grace period has ended.</p>
                </div>
                
                <h3>Current Status:</h3>
                <ul>
                    <li>❌ Gateway: <strong>DISABLED</strong></li>
                    <li>🚫 Transactions: <strong>BLOCKED</strong></li>
                    <li>💰 Commission: <strong>STOPPED</strong></li>
                </ul>
                
                <h3>Reactivate Your Gateway:</h3>
                <p>Renew your license now to restore full access immediately.</p>
                
                <a href='https://payme-gateway-3.lindy.site/renew-license.php' class='btn'>RENEW & REACTIVATE</a>
                
                <p style='margin-top: 20px;'><small>Your data is safe. Renew anytime to restore access.</small></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: PayMe 2D Gateway <noreply@payme2d.com>" . "\r\n";
    
    mail($email, $subject, $message, $headers);
}
?>
