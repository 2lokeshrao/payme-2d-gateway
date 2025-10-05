<?php
/**
 * License Verification System
 * Checks license status on login and API calls
 */

include_once 'config.php';

/**
 * Check license status for a client
 * @param int $client_id
 * @return array Status information
 */
function checkLicenseStatus($client_id) {
    global $conn;
    
    // Get client data
    $sql = "SELECT * FROM clients WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return ['status' => 'invalid', 'message' => 'Client not found'];
    }
    
    $client = $result->fetch_assoc();
    
    // Lifetime license - always active
    if ($client['license_type'] == 'lifetime') {
        return [
            'status' => 'active',
            'message' => 'Lifetime Access',
            'license_type' => 'lifetime',
            'unlimited' => true
        ];
    }
    
    // Check expiry for subscription licenses
    $today = new DateTime();
    $expiry = new DateTime($client['expiry_date']);
    $interval = $today->diff($expiry);
    
    // Already expired
    if ($today > $expiry) {
        $days_expired = $interval->days;
        
        // Grace period (3 days)
        if ($days_expired <= 3) {
            updateClientStatus($client_id, 'grace_period');
            return [
                'status' => 'grace_period',
                'message' => 'Your license expired. Grace period active.',
                'days_left' => (3 - $days_expired),
                'expiry_date' => $client['expiry_date'],
                'license_type' => $client['license_type'],
                'warning' => true
            ];
        } else {
            // Expired - disable gateway
            updateClientStatus($client_id, 'expired');
            disableGateway($client_id);
            return [
                'status' => 'expired',
                'message' => 'License expired. Gateway disabled. Please renew.',
                'expiry_date' => $client['expiry_date'],
                'license_type' => $client['license_type'],
                'blocked' => true
            ];
        }
    }
    
    // Active license
    $days_left = $interval->days;
    
    // Send reminder if < 7 days and not sent yet
    if ($days_left <= 7 && !$client['renewal_reminder_sent']) {
        sendRenewalReminder($client['email'], $client['name'], $days_left, $client['license_type']);
        markReminderSent($client_id);
    }
    
    return [
        'status' => 'active',
        'message' => 'License active',
        'days_left' => $days_left,
        'expiry_date' => $client['expiry_date'],
        'license_type' => $client['license_type'],
        'warning' => ($days_left <= 7)
    ];
}

/**
 * Update client status
 */
function updateClientStatus($client_id, $status) {
    global $conn;
    $sql = "UPDATE clients SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $client_id);
    $stmt->execute();
}

/**
 * Disable gateway for expired client
 */
function disableGateway($client_id) {
    global $conn;
    $sql = "UPDATE clients SET gateway_enabled = 0, status = 'expired' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    
    // Log the action
    logLicenseAction($client_id, 'gateway_disabled', 'License expired - gateway disabled');
}

/**
 * Enable gateway after renewal
 */
function enableGateway($client_id) {
    global $conn;
    $sql = "UPDATE clients SET gateway_enabled = 1, status = 'active' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
    
    // Log the action
    logLicenseAction($client_id, 'gateway_enabled', 'License renewed - gateway enabled');
}

/**
 * Send renewal reminder email
 */
function sendRenewalReminder($email, $name, $days_left, $license_type) {
    $subject = "⚠️ License Expiring in $days_left Days - PayMe 2D Gateway";
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; }
            .warning { background: #fff3cd; border-left: 5px solid #ffc107; padding: 15px; margin: 20px 0; }
            .btn { display: inline-block; background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; margin-top: 20px; }
            .footer { background: #1a1a1a; color: white; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🔔 License Renewal Reminder</h1>
            </div>
            <div class='content'>
                <p>Dear <strong>$name</strong>,</p>
                
                <div class='warning'>
                    <h3>⚠️ Your license expires in <strong>$days_left days</strong></h3>
                    <p>License Type: <strong>" . ucfirst($license_type) . "</strong></p>
                </div>
                
                <p>To avoid service interruption, please renew your license before it expires.</p>
                
                <h3>What happens if you don't renew?</h3>
                <ul>
                    <li>✅ <strong>Grace Period:</strong> 3 days after expiry with limited access</li>
                    <li>❌ <strong>After Grace Period:</strong> Gateway will be completely disabled</li>
                    <li>🚫 <strong>No Transactions:</strong> Your merchants won't be able to process payments</li>
                </ul>
                
                <a href='https://payme-gateway-3.lindy.site/renew-license.php' class='btn'>Renew Now</a>
                
                <p style='margin-top: 20px;'><small>If you have already renewed, please ignore this email.</small></p>
            </div>
            <div class='footer'>
                <p>PayMe 2D Gateway - Complete Payment Solution</p>
                <p>© 2025 All rights reserved</p>
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
 * Mark reminder as sent
 */
function markReminderSent($client_id) {
    global $conn;
    $sql = "UPDATE clients SET renewal_reminder_sent = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $client_id);
    $stmt->execute();
}

/**
 * Log license actions
 */
function logLicenseAction($client_id, $action, $description) {
    global $conn;
    $sql = "INSERT INTO license_logs (client_id, action, description, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $client_id, $action, $description);
    $stmt->execute();
}

/**
 * Verify API access (use this on every API call)
 */
function verifyAPIAccess($api_key) {
    global $conn;
    
    // Get client by API key
    $sql = "SELECT id FROM clients WHERE api_key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $api_key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return [
            'success' => false,
            'error' => 'Invalid API key'
        ];
    }
    
    $client = $result->fetch_assoc();
    $license_status = checkLicenseStatus($client['id']);
    
    if ($license_status['status'] == 'expired') {
        return [
            'success' => false,
            'error' => 'Gateway access disabled. License expired. Please renew at https://payme-gateway-3.lindy.site/renew-license.php'
        ];
    }
    
    if ($license_status['status'] == 'grace_period') {
        return [
            'success' => true,
            'warning' => 'License expired. Grace period: ' . $license_status['days_left'] . ' days left. Please renew soon.',
            'client_id' => $client['id']
        ];
    }
    
    return [
        'success' => true,
        'client_id' => $client['id'],
        'license_status' => $license_status
    ];
}
?>
