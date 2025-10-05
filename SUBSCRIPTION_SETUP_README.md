# PayMe 2D Gateway - Subscription Management System

## 🎯 Complete Setup Guide

This document explains how to implement the subscription management system for your PayMe 2D Gateway.

---

## 📋 Table of Contents

1. [Overview](#overview)
2. [Database Setup](#database-setup)
3. [File Structure](#file-structure)
4. [Installation Steps](#installation-steps)
5. [Cron Job Setup](#cron-job-setup)
6. [Testing](#testing)
7. [Usage Guide](#usage-guide)

---

## 🌟 Overview

### Pricing Model

**Subscription Plans:**
- **Monthly License:** ₹5,999/month
- **Yearly License:** ₹49,999/year (Save 30%)
- **Lifetime License:** ₹2,99,999 (One-time payment)

### Key Features

✅ **No Third-Party Dependency** - Uses your own payment gateway
✅ **Automatic Expiry Tracking** - Daily cron job checks
✅ **Email Reminders** - 7, 3, 1 day before expiry
✅ **Grace Period** - 3 days after expiry
✅ **Auto-Disable** - Gateway stops if not renewed
✅ **Easy Renewal** - One-click renewal process

---

## 💾 Database Setup

### Step 1: Run Database Schema

```bash
mysql -u your_username -p your_database < database-schema.sql
```

This will:
- Add subscription columns to `clients` table
- Create `license_history` table
- Create `license_logs` table
- Create `email_queue` table
- Add indexes for performance
- Create views and stored procedures

### Step 2: Verify Tables

```sql
SHOW TABLES;
-- Should show: clients, license_history, license_logs, email_queue

DESCRIBE clients;
-- Should show new columns: license_type, expiry_date, status, etc.
```

---

## 📁 File Structure

```
payme-2d-gateway/
├── index.html                      # Homepage with pricing preview
├── pricing.html                    # Full pricing page
├── verify-license.php              # License verification functions
├── cron-check-expiry.php          # Daily cron job
├── renew-license.php              # Renewal page
├── database-schema.sql            # Database setup
├── SUBSCRIPTION_SETUP_README.md   # This file
└── config.php                     # Database configuration (create this)
```

---

## 🚀 Installation Steps

### Step 1: Create config.php

```php
<?php
// config.php
$servername = "localhost";
$username = "your_db_username";
$password = "your_db_password";
$dbname = "your_database_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>
```

### Step 2: Upload Files

Upload all files to your server:
```bash
scp -r * user@your-server:/var/www/html/payme-gateway/
```

### Step 3: Set Permissions

```bash
chmod 644 *.php
chmod 644 *.html
chmod 600 config.php  # Secure config file
```

### Step 4: Test Database Connection

```bash
php -r "include 'config.php'; echo 'Connected successfully';"
```

---

## ⏰ Cron Job Setup

### Setup Daily Cron Job

```bash
crontab -e
```

Add this line (runs daily at 9 AM):
```bash
0 9 * * * /usr/bin/php /var/www/html/payme-gateway/cron-check-expiry.php >> /var/log/license-check.log 2>&1
```

### Test Cron Job Manually

```bash
php cron-check-expiry.php
```

Expected output:
```
=== License Expiry Check Started at 2025-10-05 09:00:00 ===

✅ Client #1 (Test Client) - Active (25 days left)
🔔 Client #2 (Another Client) - 7 days reminder sent

=== Summary ===
Total Checked: 2
Reminders Sent: 1
Grace Period: 0
Expired & Disabled: 0

=== Cron Job Completed at 2025-10-05 09:00:15 ===
```

---

## 🧪 Testing

### Test 1: License Verification

```php
<?php
include 'verify-license.php';

$status = checkLicenseStatus(1); // Replace 1 with actual client_id
print_r($status);
?>
```

### Test 2: API Access Verification

```php
<?php
include 'verify-license.php';

$result = verifyAPIAccess('your_api_key_here');
print_r($result);
?>
```

### Test 3: Manual Renewal

1. Login as client
2. Go to `renew-license.php`
3. Click "Proceed to Payment"
4. Complete payment
5. Verify expiry date extended

---

## 📖 Usage Guide

### For Clients

#### 1. Purchase License
- Visit: `https://your-domain.com/pricing.html`
- Choose plan (Monthly/Yearly/Lifetime)
- Click "Purchase License"
- Fill registration form
- Complete payment

#### 2. Check License Status
- Login to client dashboard
- View license details:
  - License type
  - Expiry date
  - Days remaining
  - Status (Active/Grace Period/Expired)

#### 3. Renew License
- Go to `renew-license.php`
- Review renewal details
- Click "Proceed to Payment"
- Complete payment
- Gateway reactivated instantly

### For Administrators

#### 1. Monitor Licenses

```sql
-- View all active subscriptions
SELECT * FROM active_subscriptions;

-- View expiring soon
SELECT * FROM clients 
WHERE license_type != 'lifetime' 
AND DATEDIFF(expiry_date, NOW()) <= 7
ORDER BY expiry_date ASC;

-- View expired licenses
SELECT * FROM clients 
WHERE status = 'expired';
```

#### 2. Manual License Extension

```sql
-- Extend license by 1 month
UPDATE clients 
SET expiry_date = DATE_ADD(expiry_date, INTERVAL 1 MONTH),
    status = 'active',
    gateway_enabled = true
WHERE id = 123;
```

#### 3. Revenue Reports

```sql
-- Monthly revenue
SELECT * FROM revenue_summary;

-- Total revenue
SELECT 
    SUM(amount) as total_revenue,
    COUNT(*) as total_transactions
FROM license_history 
WHERE status = 'completed';
```

---

## 🔧 Integration with Existing Code

### Add to client-login.php

```php
<?php
session_start();
include 'verify-license.php';

// After successful login
$client_id = $_SESSION['client_id'];
$license_status = checkLicenseStatus($client_id);

if ($license_status['status'] == 'expired') {
    header("Location: renew-license.php?expired=true");
    exit();
}

if ($license_status['status'] == 'grace_period') {
    $_SESSION['warning'] = "Your license expired. " . $license_status['days_left'] . " days left in grace period.";
}
?>
```

### Add to API Endpoints

```php
<?php
include 'verify-license.php';

// At the start of every API call
$api_key = $_POST['api_key'] ?? $_GET['api_key'];
$access = verifyAPIAccess($api_key);

if (!$access['success']) {
    echo json_encode([
        'success' => false,
        'error' => $access['error']
    ]);
    exit();
}

// Continue with API logic
$client_id = $access['client_id'];
?>
```

### Add to Client Dashboard

```php
<?php
$license_status = checkLicenseStatus($_SESSION['client_id']);
?>

<div class="license-status">
    <?php if ($license_status['status'] == 'expired'): ?>
        <div class="alert alert-danger">
            ❌ Gateway Disabled - <a href="renew-license.php">Renew Now</a>
        </div>
    <?php elseif ($license_status['status'] == 'grace_period'): ?>
        <div class="alert alert-warning">
            ⏰ Grace Period: <?php echo $license_status['days_left']; ?> days left
        </div>
    <?php elseif ($license_status['warning']): ?>
        <div class="alert alert-info">
            🔔 Expires in <?php echo $license_status['days_left']; ?> days
        </div>
    <?php endif; ?>
</div>
```

---

## 📧 Email Configuration

### Setup SMTP (Recommended)

```php
// In verify-license.php, replace mail() with PHPMailer

use PHPMailer\PHPMailer\PHPMailer;

function sendRenewalReminder($email, $name, $days_left, $license_type) {
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@gmail.com';
    $mail->Password = 'your-app-password';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    $mail->setFrom('noreply@payme2d.com', 'PayMe 2D Gateway');
    $mail->addAddress($email, $name);
    
    $mail->isHTML(true);
    $mail->Subject = "⚠️ License Expiring in $days_left Days";
    $mail->Body = "..."; // Your HTML email content
    
    $mail->send();
}
```

---

## 🔒 Security Best Practices

1. **Secure config.php**
   ```bash
   chmod 600 config.php
   ```

2. **Use prepared statements** (already implemented)

3. **Validate all inputs**
   ```php
   $client_id = filter_var($_POST['client_id'], FILTER_VALIDATE_INT);
   ```

4. **Use HTTPS** for all pages

5. **Implement rate limiting** on renewal page

---

## 🐛 Troubleshooting

### Issue: Cron job not running

**Solution:**
```bash
# Check cron logs
tail -f /var/log/cron

# Test manually
php cron-check-expiry.php

# Verify cron is installed
which cron
```

### Issue: Emails not sending

**Solution:**
```bash
# Check PHP mail configuration
php -i | grep mail

# Test mail function
php -r "mail('test@example.com', 'Test', 'Test message');"

# Use SMTP instead (see Email Configuration)
```

### Issue: Database connection failed

**Solution:**
```bash
# Verify credentials
mysql -u username -p database_name

# Check config.php permissions
ls -la config.php

# Test connection
php -r "include 'config.php'; echo 'Connected';"
```

---

## 📊 Monitoring & Analytics

### Dashboard Queries

```sql
-- Active licenses by type
SELECT license_type, COUNT(*) as count
FROM clients
WHERE status = 'active'
GROUP BY license_type;

-- Expiring this week
SELECT name, email, expiry_date, DATEDIFF(expiry_date, NOW()) as days_left
FROM clients
WHERE license_type != 'lifetime'
AND DATEDIFF(expiry_date, NOW()) BETWEEN 0 AND 7
ORDER BY expiry_date ASC;

-- Revenue this month
SELECT SUM(amount) as revenue
FROM license_history
WHERE MONTH(payment_date) = MONTH(NOW())
AND YEAR(payment_date) = YEAR(NOW())
AND status = 'completed';
```

---

## 🎉 Success Checklist

- [ ] Database schema created
- [ ] All PHP files uploaded
- [ ] config.php configured
- [ ] Cron job setup and tested
- [ ] Email sending tested
- [ ] License verification working
- [ ] Renewal process tested
- [ ] API access control implemented
- [ ] Client dashboard updated
- [ ] Monitoring queries working

---

## 📞 Support

For any issues or questions:
- Email: support@payme2d.com
- Documentation: https://docs.payme2d.com
- GitHub: https://github.com/2lokeshrao/payme-2d-gateway

---

## 📝 License

© 2025 PayMe 2D Gateway. All rights reserved.
