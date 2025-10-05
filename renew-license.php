<?php
/**
 * License Renewal Page
 * Allows clients to renew their subscription
 */

session_start();
include_once 'config.php';
include_once 'verify-license.php';

// Check if client is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: client-login.php?redirect=renew-license.php");
    exit();
}

$client_id = $_SESSION['client_id'];

// Get client data
$sql = "SELECT * FROM clients WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$client = $stmt->get_result()->fetch_assoc();

// Check license status
$license_status = checkLicenseStatus($client_id);

// Calculate renewal amount and new expiry
if ($client['license_type'] == 'monthly') {
    $renewal_amount = 5999;
    $new_expiry = date('Y-m-d H:i:s', strtotime($client['expiry_date'] . ' +1 month'));
    $plan_name = "Monthly License";
} elseif ($client['license_type'] == 'yearly') {
    $renewal_amount = 49999;
    $new_expiry = date('Y-m-d H:i:s', strtotime($client['expiry_date'] . ' +1 year'));
    $plan_name = "Yearly License";
} else {
    // Lifetime - no renewal needed
    header("Location: client-dashboard.php?message=lifetime_no_renewal");
    exit();
}

// Handle renewal payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_renewal'])) {
    
    // Process payment via your gateway
    $payment_data = [
        'amount' => $renewal_amount,
        'email' => $client['email'],
        'name' => $client['name'],
        'purpose' => 'License Renewal - ' . $plan_name,
        'client_id' => $client_id
    ];
    
    // Redirect to payment gateway
    $_SESSION['renewal_data'] = $payment_data;
    header("Location: payment-gateway.php?type=renewal&amount=$renewal_amount&client_id=$client_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renew License - PayMe 2D Gateway</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            margin-bottom: 25px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 32px;
            color: #1a1a1a;
            margin-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            margin-top: 10px;
        }

        .status-expired {
            background: #fee;
            color: #dc3545;
        }

        .status-grace {
            background: #fff3cd;
            color: #856404;
        }

        .status-warning {
            background: #d1ecf1;
            color: #0c5460;
        }

        .info-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            font-weight: 700;
            color: #1a1a1a;
        }

        .renewal-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 25px;
        }

        .renewal-section h2 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .price {
            font-size: 48px;
            font-weight: 900;
            margin: 20px 0;
        }

        .benefits {
            text-align: left;
            margin: 25px 0;
        }

        .benefits li {
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .benefits li:before {
            content: "✓";
            font-weight: bold;
            font-size: 20px;
        }

        .btn {
            width: 100%;
            padding: 18px;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 800;
            cursor: pointer;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #10b981;
            color: white;
            box-shadow: 0 8px 20px rgba(16,185,129,0.5);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(16,185,129,0.7);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-top: 15px;
        }

        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .alert-danger {
            background: #f8d7da;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border-left: 5px solid #ffc107;
            color: #856404;
        }

        .alert-info {
            background: #d1ecf1;
            border-left: 5px solid #17a2b8;
            color: #0c5460;
        }

        .icon {
            font-size: 32px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Status Alert -->
        <?php if ($license_status['status'] == 'expired'): ?>
            <div class="alert alert-danger">
                <div class="icon">❌</div>
                <div>
                    <strong>Gateway Disabled!</strong><br>
                    Your license has expired. Renew now to restore access immediately.
                </div>
            </div>
        <?php elseif ($license_status['status'] == 'grace_period'): ?>
            <div class="alert alert-warning">
                <div class="icon">⏰</div>
                <div>
                    <strong>Grace Period Active!</strong><br>
                    You have <?php echo $license_status['days_left']; ?> days left before gateway is disabled.
                </div>
            </div>
        <?php elseif ($license_status['warning']): ?>
            <div class="alert alert-info">
                <div class="icon">🔔</div>
                <div>
                    <strong>Renewal Reminder</strong><br>
                    Your license expires in <?php echo $license_status['days_left']; ?> days.
                </div>
            </div>
        <?php endif; ?>

        <!-- Current License Info -->
        <div class="card">
            <div class="header">
                <h1><i class="fas fa-sync-alt"></i> Renew License</h1>
                <span class="status-badge status-<?php echo $license_status['status'] == 'expired' ? 'expired' : ($license_status['status'] == 'grace_period' ? 'grace' : 'warning'); ?>">
                    <?php echo strtoupper($license_status['status']); ?>
                </span>
            </div>

            <div class="info-section">
                <h3 style="margin-bottom: 15px;">Current License Details</h3>
                <div class="info-row">
                    <span class="info-label">Client Name:</span>
                    <span class="info-value"><?php echo htmlspecialchars($client['name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">License Type:</span>
                    <span class="info-value"><?php echo ucfirst($client['license_type']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Purchase Date:</span>
                    <span class="info-value"><?php echo date('d M Y', strtotime($client['purchase_date'])); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Expiry Date:</span>
                    <span class="info-value" style="color: #dc3545;"><?php echo date('d M Y', strtotime($client['expiry_date'])); ?></span>
                </div>
                <?php if ($license_status['status'] == 'active'): ?>
                <div class="info-row">
                    <span class="info-label">Days Remaining:</span>
                    <span class="info-value"><?php echo $license_status['days_left']; ?> days</span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Renewal Options -->
        <div class="card">
            <div class="renewal-section">
                <h2>🔄 Renew Your License</h2>
                <div class="price">₹<?php echo number_format($renewal_amount); ?></div>
                <p style="font-size: 16px; opacity: 0.9;">
                    Extends your license by <?php echo $client['license_type'] == 'monthly' ? '1 month' : '1 year'; ?>
                </p>

                <ul class="benefits">
                    <li>Instant Gateway Reactivation</li>
                    <li>All Features Restored</li>
                    <li>Unlimited Transactions</li>
                    <li>Continue Earning Commission</li>
                    <li>Priority Support</li>
                </ul>
            </div>

            <form method="POST">
                <button type="submit" name="confirm_renewal" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> Proceed to Payment
                </button>
            </form>

            <button onclick="window.location.href='client-dashboard.php'" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </button>
        </div>

        <!-- Help Section -->
        <div class="card">
            <h3 style="margin-bottom: 15px;">💡 Need Help?</h3>
            <p style="color: #666; line-height: 1.6;">
                If you have any questions about renewal or need assistance, please contact our support team at 
                <strong>support@payme2d.com</strong> or call <strong>+91-XXXXXXXXXX</strong>
            </p>
        </div>
    </div>
</body>
</html>
