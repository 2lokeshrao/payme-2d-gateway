<?php
/**
 * Client Dashboard - Shows license status and renewal options
 */

session_start();
include_once 'config.php';
include_once 'verify-license.php';

// Check if client is logged in
if (!isset($_SESSION['client_id'])) {
    header("Location: client-login.php");
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

// Get license history
$sql = "SELECT * FROM license_history WHERE client_id = ? ORDER BY payment_date DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$history = $stmt->get_result();

// Calculate total spent
$sql = "SELECT SUM(amount) as total_spent FROM license_history WHERE client_id = ? AND status = 'completed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);
$stmt->execute();
$total_spent = $stmt->get_result()->fetch_assoc()['total_spent'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - PayMe 2D Gateway</title>
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
            background: #f5f7fa;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .navbar-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h1 {
            font-size: 24px;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
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

        .alert-success {
            background: #d4edda;
            border-left: 5px solid #28a745;
            color: #155724;
        }

        .icon {
            font-size: 32px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .card-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .icon-purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .icon-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .icon-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .icon-orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }

        .card-title {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-value {
            font-size: 32px;
            font-weight: 900;
            color: #1a1a1a;
            margin-top: 10px;
        }

        .card-subtitle {
            font-size: 14px;
            color: #999;
            margin-top: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-active {
            background: #d4edda;
            color: #155724;
        }

        .badge-expired {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-grace {
            background: #fff3cd;
            color: #856404;
        }

        .badge-lifetime {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102,126,234,0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102,126,234,0.6);
        }

        .btn-success {
            background: #10b981;
            color: white;
            box-shadow: 0 4px 15px rgba(16,185,129,0.4);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16,185,129,0.6);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8f9fa;
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: #666;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 10px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            transition: width 0.5s ease;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .navbar-content {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="navbar-content">
            <h1><i class="fas fa-tachometer-alt"></i> Client Dashboard</h1>
            <div class="user-info">
                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($client['name']); ?></span>
                <button class="btn-logout" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Status Alerts -->
        <?php if ($license_status['status'] == 'expired'): ?>
            <div class="alert alert-danger">
                <div class="icon">❌</div>
                <div>
                    <strong>Gateway Disabled!</strong><br>
                    Your license has expired. Renew now to restore access immediately.
                    <a href="renew-license.php" style="color: #721c24; text-decoration: underline; font-weight: bold;">Renew Now</a>
                </div>
            </div>
        <?php elseif ($license_status['status'] == 'grace_period'): ?>
            <div class="alert alert-warning">
                <div class="icon">⏰</div>
                <div>
                    <strong>Grace Period Active!</strong><br>
                    You have <?php echo $license_status['days_left']; ?> days left before gateway is permanently disabled.
                    <a href="renew-license.php" style="color: #856404; text-decoration: underline; font-weight: bold;">Renew Now</a>
                </div>
            </div>
        <?php elseif ($license_status['warning']): ?>
            <div class="alert alert-info">
                <div class="icon">🔔</div>
                <div>
                    <strong>Renewal Reminder</strong><br>
                    Your license expires in <?php echo $license_status['days_left']; ?> days. Renew early to avoid service interruption.
                </div>
            </div>
        <?php elseif ($license_status['status'] == 'active' && $client['license_type'] == 'lifetime'): ?>
            <div class="alert alert-success">
                <div class="icon">♾️</div>
                <div>
                    <strong>Lifetime Access Active!</strong><br>
                    You have unlimited access to the gateway. No renewal needed.
                </div>
            </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="grid">
            <!-- License Status -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon icon-purple">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div>
                        <div class="card-title">License Status</div>
                    </div>
                </div>
                <div class="card-value">
                    <span class="status-badge badge-<?php echo $license_status['status'] == 'expired' ? 'expired' : ($license_status['status'] == 'grace_period' ? 'grace' : ($client['license_type'] == 'lifetime' ? 'lifetime' : 'active')); ?>">
                        <?php echo $client['license_type'] == 'lifetime' ? 'Lifetime' : strtoupper($license_status['status']); ?>
                    </span>
                </div>
                <div class="card-subtitle">
                    <?php echo ucfirst($client['license_type']); ?> License
                </div>
            </div>

            <!-- Days Remaining -->
            <?php if ($client['license_type'] != 'lifetime'): ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-icon icon-orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div>
                        <div class="card-title">Days Remaining</div>
                    </div>
                </div>
                <div class="card-value">
                    <?php 
                    if ($license_status['status'] == 'expired') {
                        echo '<span style="color: #dc3545;">Expired</span>';
                    } elseif ($license_status['status'] == 'grace_period') {
                        echo '<span style="color: #ffc107;">' . $license_status['days_left'] . '</span>';
                    } else {
                        echo $license_status['days_left'];
                    }
                    ?>
                </div>
                <div class="card-subtitle">
                    Expires: <?php echo date('d M Y', strtotime($client['expiry_date'])); ?>
                </div>
                <?php if ($license_status['status'] == 'active'): ?>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo min(100, ($license_status['days_left'] / 30) * 100); ?>%"></div>
                </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="card">
                <div class="card-header">
                    <div class="card-icon icon-green">
                        <i class="fas fa-infinity"></i>
                    </div>
                    <div>
                        <div class="card-title">Access Type</div>
                    </div>
                </div>
                <div class="card-value">
                    ♾️ Unlimited
                </div>
                <div class="card-subtitle">
                    Lifetime Access - No Expiry
                </div>
            </div>
            <?php endif; ?>

            <!-- Gateway Status -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon icon-<?php echo $client['gateway_enabled'] ? 'green' : 'orange'; ?>">
                        <i class="fas fa-<?php echo $client['gateway_enabled'] ? 'check-circle' : 'times-circle'; ?>"></i>
                    </div>
                    <div>
                        <div class="card-title">Gateway Status</div>
                    </div>
                </div>
                <div class="card-value">
                    <?php echo $client['gateway_enabled'] ? '✅ Active' : '❌ Disabled'; ?>
                </div>
                <div class="card-subtitle">
                    <?php echo $client['gateway_enabled'] ? 'Processing payments' : 'Not accepting payments'; ?>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="card">
                <div class="card-header">
                    <div class="card-icon icon-blue">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div>
                        <div class="card-title">Total Spent</div>
                    </div>
                </div>
                <div class="card-value">
                    ₹<?php echo number_format($total_spent); ?>
                </div>
                <div class="card-subtitle">
                    All-time investment
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <?php if ($client['license_type'] != 'lifetime'): ?>
        <div style="text-align: center; margin: 30px 0;">
            <a href="renew-license.php" class="btn btn-success">
                <i class="fas fa-sync-alt"></i> Renew License Now
            </a>
            <a href="pricing.html" class="btn btn-primary" style="margin-left: 15px;">
                <i class="fas fa-crown"></i> Upgrade to Lifetime
            </a>
        </div>
        <?php endif; ?>

        <!-- License History -->
        <div class="table-container">
            <h2 style="margin-bottom: 20px;"><i class="fas fa-history"></i> License History</h2>
            <?php if ($history->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>License Type</th>
                        <th>Amount</th>
                        <th>Expiry Date</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $history->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($row['payment_date'])); ?></td>
                        <td><?php echo ucfirst($row['license_type']); ?></td>
                        <td><strong>₹<?php echo number_format($row['amount']); ?></strong></td>
                        <td><?php echo date('d M Y', strtotime($row['expiry_date'])); ?></td>
                        <td><code><?php echo $row['transaction_id'] ?? 'N/A'; ?></code></td>
                        <td>
                            <span class="status-badge badge-<?php echo $row['status'] == 'completed' ? 'active' : 'expired'; ?>">
                                <?php echo ucfirst($row['status']); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="text-align: center; color: #999; padding: 40px;">No payment history available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
