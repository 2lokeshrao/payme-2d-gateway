# Quick Start Guide - PayMe 2D Gateway

**Get your payment gateway up and running in 10 minutes!**

---

## Prerequisites

Before you begin, ensure you have:

- ✅ PHP 7.4 or higher installed
- ✅ MySQL 5.7 or higher installed
- ✅ Web server (Apache/Nginx) configured
- ✅ Domain name (optional but recommended)
- ✅ Gmail account for email delivery

---

## Installation (5 Steps)

### Step 1: Download and Setup

```bash
# Clone the repository
git clone https://github.com/2lokeshrao/payme-2d-gateway.git
cd payme-2d-gateway

# Create database
mysql -u root -p -e "CREATE DATABASE payme_gateway;"

# Import database schema
mysql -u root -p payme_gateway < database.sql
```

### Step 2: Configure Database

Edit `config/database.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'payme_gateway');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
?>
```

### Step 3: Set File Permissions

```bash
chmod 755 -R /path/to/payme-2d-gateway
chmod 777 -R /path/to/payme-2d-gateway/uploads
```

### Step 4: Configure Web Server

**For Apache:**

Create virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName payme-gateway.com
    DocumentRoot /var/www/payme-2d-gateway
    
    <Directory /var/www/payme-2d-gateway>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**For Nginx:**

```nginx
server {
    listen 80;
    server_name payme-gateway.com;
    root /var/www/payme-2d-gateway;
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### Step 5: Access the System

Open your browser and navigate to:

- **Homepage:** `http://your-domain.com`
- **Admin Panel:** `http://your-domain.com/admin/login.html`

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **Change this password immediately after first login!**

---

## Initial Configuration (5 Minutes)

### 1. Login to Admin Panel

1. Go to: `http://your-domain.com/admin/login.html`
2. Enter username: `admin`
3. Enter password: `admin123`
4. Click "Login"

### 2. Change Admin Password

1. Click on "Settings" in the menu
2. Click "Change Password"
3. Enter current password: `admin123`
4. Enter new strong password
5. Confirm new password
6. Click "Update Password"

### 3. Configure Payment Methods

**Option A: Bank Account**

1. Go to "Payment Configuration"
2. Click "Bank Transfer" tab
3. Enter your bank details:
   - Bank Name: Select from dropdown
   - Account Type: Savings/Current
   - Account Holder Name: Your name
   - Account Number: Your account number
   - IFSC Code: Your bank's IFSC code
4. Click "Save Configuration"

**Option B: UPI Payment**

1. Go to "Payment Configuration"
2. Click "UPI Payment" tab
3. Enter your UPI details:
   - UPI ID: yourname@okhdfc
   - UPI Name: Your name
   - UPI Provider: Google Pay/PhonePe/etc.
4. Click "Save Configuration"

**Option C: Cryptocurrency (Optional)**

1. Go to "Payment Configuration"
2. Click "Cryptocurrency" tab
3. Enter wallet addresses:
   - Bitcoin (BTC) wallet address
   - Ethereum (ETH) wallet address
   - USDT wallet address
   - BNB wallet address
4. Click "Save Configuration"

### 4. Configure Email Settings

1. Go to "Payment Configuration"
2. Click "Email Settings" tab
3. Enter SMTP details:
   - SMTP Host: `smtp.gmail.com`
   - SMTP Port: `587`
   - SMTP Username: `your-email@gmail.com`
   - SMTP Password: Your Gmail App Password
   - From Email: `noreply@your-domain.com`
   - From Name: `PayMe 2D Gateway`
   - Admin Email: `admin@your-domain.com`
4. Click "Send Test Email" to verify
5. Click "Save Configuration"

**How to Get Gmail App Password:**

1. Go to: https://myaccount.google.com/security
2. Enable "2-Step Verification"
3. Click "App Passwords"
4. Select "Mail" and "Other (Custom name)"
5. Enter "PayMe Gateway"
6. Click "Generate"
7. Copy the 16-character password
8. Use this in SMTP Password field

### 5. Test the System

1. Go to: `http://your-domain.com/purchase-code.html`
2. Select "Monthly Plan"
3. Enter test details:
   - Name: Test User
   - Email: your-email@gmail.com
   - Phone: 9876543210
4. Select payment method (UPI recommended)
5. Make a small test payment
6. Enter transaction ID
7. Click "Verify Payment"
8. Check email for activation code

---

## Usage

### For Customers

**Purchase Activation Code:**

1. Visit: `http://your-domain.com/purchase-code.html`
2. Select subscription plan
3. Enter details (name, email, phone)
4. Choose payment method
5. Complete payment
6. Enter transaction ID
7. Receive activation code via email

**Activate Subscription:**

1. Visit: `http://your-domain.com/activate.html`
2. Enter activation code
3. Click "Activate"
4. Subscription is now active!

### For Admin

**View Dashboard:**

1. Login to admin panel
2. View statistics:
   - Total users
   - Total revenue
   - Active subscriptions
   - Recent transactions

**Manage Users:**

1. Go to "User Management"
2. View all users
3. Edit user details
4. Activate/deactivate users
5. View user transactions

**Manage Resellers:**

1. Go to "Reseller Management"
2. Click "Add New Reseller"
3. Enter reseller details
4. Set commission rate
5. Click "Create Reseller"

**Generate Activation Codes:**

1. Go to "Activation Codes"
2. Click "Generate Codes"
3. Select plan
4. Enter quantity
5. Click "Generate"
6. Download codes

---

## Troubleshooting

### Issue: Cannot Login to Admin Panel

**Solution:**
```bash
# Reset admin password via database
mysql -u root -p payme_gateway
UPDATE users SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE email = 'admin@payme-gateway.com';
# New password: admin123
```

### Issue: Emails Not Sending

**Solution:**
1. Verify SMTP credentials
2. Use Gmail App Password (not regular password)
3. Check spam folder
4. Try port 465 instead of 587
5. Enable "Less secure app access" in Gmail

### Issue: Database Connection Error

**Solution:**
1. Check database credentials in `config/database.php`
2. Verify MySQL is running: `sudo service mysql status`
3. Check database exists: `mysql -u root -p -e "SHOW DATABASES;"`
4. Grant permissions: `GRANT ALL ON payme_gateway.* TO 'user'@'localhost';`

### Issue: Payment Details Not Showing

**Solution:**
1. Clear browser cache
2. Check browser console for errors
3. Verify payment configuration is saved
4. Check database connection
5. Reload the page

---

## Next Steps

### Secure Your Installation

1. **Enable HTTPS:**
   ```bash
   # Install Let's Encrypt SSL
   sudo apt install certbot python3-certbot-apache
   sudo certbot --apache -d your-domain.com
   ```

2. **Change Default Credentials:**
   - Change admin password
   - Change database password
   - Update all default values

3. **Setup Backups:**
   ```bash
   # Create backup script
   #!/bin/bash
   mysqldump -u root -p payme_gateway > backup_$(date +%Y%m%d).sql
   tar -czf backup_$(date +%Y%m%d).tar.gz /var/www/payme-2d-gateway
   ```

4. **Enable Firewall:**
   ```bash
   sudo ufw allow 80/tcp
   sudo ufw allow 443/tcp
   sudo ufw enable
   ```

### Customize Your Gateway

1. **Update Branding:**
   - Replace logo in `assets/images/logo.png`
   - Update colors in `assets/css/style.css`
   - Modify homepage content in `index.html`

2. **Add Custom Plans:**
   - Login to admin panel
   - Go to "Subscription Plans"
   - Click "Add New Plan"
   - Enter plan details
   - Save plan

3. **Configure Reseller System:**
   - Create reseller accounts
   - Set commission rates
   - Generate activation codes
   - Track reseller sales

### Monitor Your System

1. **Check Logs:**
   ```bash
   # Apache logs
   tail -f /var/log/apache2/error.log
   
   # PHP logs
   tail -f /var/log/php7.4-fpm.log
   
   # MySQL logs
   tail -f /var/log/mysql/error.log
   ```

2. **Monitor Transactions:**
   - Login to admin panel
   - Go to "Transactions"
   - View recent transactions
   - Check for suspicious activity

3. **Regular Maintenance:**
   - Update PHP and MySQL regularly
   - Backup database daily
   - Monitor disk space
   - Check email delivery

---

## Support

### Need Help?

**Documentation:**
- [Complete Documentation](DOCUMENTATION.md)
- [README](README.md)

**Support Channels:**
- Email: support@payme-gateway.com
- GitHub: https://github.com/2lokeshrao/payme-2d-gateway/issues

**Common Resources:**
- [PHP Documentation](https://php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Apache Documentation](https://httpd.apache.org/docs/)

---

## Checklist

Before going live, ensure:

- [ ] Database configured and tested
- [ ] Admin password changed
- [ ] Payment methods configured
- [ ] Email settings configured and tested
- [ ] Test purchase completed successfully
- [ ] SSL certificate installed (HTTPS)
- [ ] Firewall configured
- [ ] Backup system setup
- [ ] Custom branding applied
- [ ] Terms and privacy policy updated

---

**Congratulations! Your PayMe 2D Gateway is now ready to accept payments!**

Share your purchase page: `http://your-domain.com/purchase-code.html`

---

**© 2025 PayMe 2D Gateway. All rights reserved.**
