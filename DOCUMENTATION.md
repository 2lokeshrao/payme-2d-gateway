# PayMe 2D Gateway - Complete Documentation

**Version:** 1.0.0  
**Last Updated:** October 4, 2025  
**Website:** https://payme-gateway.lindy.site  
**GitHub:** https://github.com/2lokeshrao/payme-2d-gateway

---

## Table of Contents

1. [Introduction](#introduction)
2. [System Overview](#system-overview)
3. [Getting Started](#getting-started)
4. [Admin Setup Guide](#admin-setup-guide)
5. [Payment Configuration](#payment-configuration)
6. [User Guide](#user-guide)
7. [Reseller System](#reseller-system)
8. [API Documentation](#api-documentation)
9. [Security Features](#security-features)
10. [Troubleshooting](#troubleshooting)

---

## Introduction

PayMe 2D Gateway is a comprehensive payment gateway solution that allows businesses and individuals to accept payments directly into their bank accounts, UPI IDs, or cryptocurrency wallets without any middleman or commission.

### Key Features

- **Direct Payment Collection** - Payments go directly to your account
- **Multiple Payment Methods** - Bank Transfer, UPI, Cryptocurrency
- **Automated Code Generation** - Instant activation code delivery
- **Email Automation** - Professional email templates
- **Reseller System** - Build your own reseller network
- **Complete API** - Integrate with any platform
- **Real-time Analytics** - Track all transactions
- **Secure & Reliable** - Bank-grade security

### Two Business Models

**Model 1: Self Payment Gateway**
- Users configure their own payment accounts
- Payments go directly to user's account
- No commission, no middleman
- Perfect for individual businesses

**Model 2: Central Gateway with Resellers**
- Admin controls the entire system
- Resellers generate activation codes
- Revenue sharing between admin and resellers
- Perfect for building a payment gateway business

---

## System Overview

### Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Email:** SMTP (Gmail, SendGrid, etc.)
- **Hosting:** Any PHP hosting with MySQL

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     PayMe 2D Gateway                        │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐    │
│  │   Customer   │  │     Admin    │  │   Reseller   │    │
│  │   Portal     │  │    Panel     │  │    Panel     │    │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘    │
│         │                  │                  │             │
│         └──────────────────┼──────────────────┘             │
│                            │                                │
│                    ┌───────▼────────┐                      │
│                    │   API Layer    │                      │
│                    └───────┬────────┘                      │
│                            │                                │
│                    ┌───────▼────────┐                      │
│                    │    Database    │                      │
│                    └────────────────┘                      │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  Payment Methods: Bank Transfer | UPI | Cryptocurrency     │
└─────────────────────────────────────────────────────────────┘
```

### Database Schema

**Main Tables:**
- `users` - User accounts and authentication
- `subscription_plans` - Available subscription plans
- `activation_codes` - Generated activation codes
- `code_purchases` - Purchase transactions
- `resellers` - Reseller accounts
- `payment_config` - Admin payment configuration
- `transactions` - All payment transactions

---

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- SMTP email account (Gmail recommended)
- Domain name (optional but recommended)

### Installation Steps

**Step 1: Download the Code**

```bash
git clone https://github.com/2lokeshrao/payme-2d-gateway.git
cd payme-2d-gateway
```

**Step 2: Configure Database**

1. Create a new MySQL database:
```sql
CREATE DATABASE payme_gateway;
```

2. Import the database schema:
```bash
mysql -u username -p payme_gateway < database.sql
```

3. Update database credentials in `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'payme_gateway');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

**Step 3: Configure Web Server**

Point your web server document root to the project directory.

**Apache Example:**
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

**Step 4: Set File Permissions**

```bash
chmod 755 -R /var/www/payme-2d-gateway
chmod 777 -R /var/www/payme-2d-gateway/uploads
```

**Step 5: Access the System**

- Homepage: `http://your-domain.com`
- Admin Panel: `http://your-domain.com/admin/login.html`
- User Panel: `http://your-domain.com/user/login.html`

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

**⚠️ Important:** Change the default admin password immediately after first login!

---

## Admin Setup Guide

### First Time Setup

**Step 1: Login to Admin Panel**

1. Go to: `http://your-domain.com/admin/login.html`
2. Enter default credentials
3. Click "Login"

**Step 2: Change Admin Password**

1. Go to "Settings" → "Change Password"
2. Enter current password: `admin123`
3. Enter new strong password
4. Confirm new password
5. Click "Update Password"

**Step 3: Configure Payment Methods**

1. Go to "Payment Configuration"
2. Configure at least one payment method:
   - Bank Account Details
   - UPI ID
   - Cryptocurrency Wallets (optional)

**Step 4: Configure Email Settings**

1. Go to "Email Configuration"
2. Enter SMTP details
3. Test email delivery
4. Save configuration

**Step 5: Create Subscription Plans**

Default plans are already created:
- Monthly Plan: ₹60
- Quarterly Plan: ₹150
- Yearly Plan: ₹500
- Lifetime Plan: ₹2,999

You can modify these or create new plans.

---

## Payment Configuration

### Bank Account Setup

**Required Information:**

| Field | Description | Example |
|-------|-------------|---------|
| Bank Name | Your bank name | HDFC Bank |
| Account Type | Savings or Current | Savings |
| Account Holder | Name as per bank | Rajesh Kumar |
| Account Number | Bank account number | 50100123456789 |
| IFSC Code | Branch IFSC code | HDFC0001234 |

**How to Configure:**

1. Login to Admin Panel
2. Go to "Payment Configuration"
3. Click on "Bank Transfer" tab
4. Fill in all bank details
5. Click "Save Configuration"

**Customer Payment Process:**

1. Customer selects "Bank Transfer" payment method
2. System displays your bank details
3. Customer transfers money via:
   - Net Banking
   - NEFT/RTGS/IMPS
   - Mobile Banking App
4. Customer enters transaction reference number
5. System verifies and generates activation code

---

### UPI Payment Setup

**Required Information:**

| Field | Description | Example |
|-------|-------------|---------|
| UPI ID | Your UPI ID | rajesh@okhdfc |
| UPI Name | Name on UPI | Rajesh Kumar |
| UPI Provider | Payment app | Google Pay |

**Supported UPI Providers:**

- Google Pay (`@okaxis`, `@okhdfc`, `@okicici`)
- PhonePe (`@ybl`)
- Paytm (`@paytm`)
- BHIM (`@upi`)
- Other UPI apps

**How to Configure:**

1. Login to Admin Panel
2. Go to "Payment Configuration"
3. Click on "UPI Payment" tab
4. Enter your UPI ID
5. Enter UPI name
6. Select UPI provider
7. Click "Save Configuration"

**Customer Payment Process:**

1. Customer selects "UPI Payment" method
2. System displays your UPI ID and QR code
3. Customer scans QR code or copies UPI ID
4. Customer makes payment via any UPI app
5. Customer enters UPI transaction ID
6. System verifies and generates activation code

**UPI Benefits:**

- ✅ Instant payment (24/7)
- ✅ No transaction fees
- ✅ Easy QR code scanning
- ✅ Works with all UPI apps
- ✅ Real-time confirmation

---

### Cryptocurrency Setup

**Supported Cryptocurrencies:**

| Crypto | Symbol | Network |
|--------|--------|---------|
| Bitcoin | BTC | Bitcoin Network |
| Ethereum | ETH | Ethereum Network |
| Tether | USDT | ERC-20/TRC-20/BEP-20 |
| Binance Coin | BNB | Binance Smart Chain |

**Required Information:**

For each cryptocurrency you want to accept, you need the wallet address.

**How to Configure:**

1. Login to Admin Panel
2. Go to "Payment Configuration"
3. Click on "Cryptocurrency" tab
4. Enter wallet addresses for desired cryptocurrencies
5. Click "Save Configuration"

**⚠️ Important Security Notes:**

- Double-check wallet addresses before saving
- Wrong address = lost funds (irreversible)
- Use hardware wallet for large amounts
- Test with small amount first
- Keep private keys secure

**Customer Payment Process:**

1. Customer selects "Cryptocurrency" payment method
2. Customer selects cryptocurrency (BTC/ETH/USDT/BNB)
3. System displays wallet address and QR code
4. Customer sends crypto to the address
5. Customer enters transaction hash
6. System verifies and generates activation code

---

### Email Configuration

**Why Email Configuration is Important:**

- Activation codes are sent via email
- Customers receive purchase confirmations
- Admin receives purchase notifications
- Password reset emails
- Transaction receipts

**Gmail Configuration (Recommended):**

| Field | Value |
|-------|-------|
| SMTP Host | smtp.gmail.com |
| SMTP Port | 587 |
| SMTP Username | your-email@gmail.com |
| SMTP Password | App Password (16 characters) |
| From Email | noreply@your-domain.com |
| From Name | PayMe 2D Gateway |
| Admin Email | admin@your-domain.com |

**How to Get Gmail App Password:**

1. Go to Google Account Settings
2. Click "Security"
3. Enable "2-Step Verification" (if not enabled)
4. Click "App Passwords"
5. Select "Mail" and "Other (Custom name)"
6. Enter "PayMe Gateway"
7. Click "Generate"
8. Copy the 16-character password
9. Use this password in SMTP settings

**How to Configure:**

1. Login to Admin Panel
2. Go to "Payment Configuration"
3. Click on "Email Settings" tab
4. Enter SMTP details
5. Enter Gmail App Password
6. Click "Save Configuration"
7. Click "Send Test Email"
8. Enter your email address
9. Check inbox (and spam folder)

**Other Email Providers:**

**SendGrid:**
- SMTP Host: smtp.sendgrid.net
- SMTP Port: 587
- Username: apikey
- Password: Your SendGrid API Key

**Mailgun:**
- SMTP Host: smtp.mailgun.org
- SMTP Port: 587
- Username: Your Mailgun SMTP username
- Password: Your Mailgun SMTP password

**AWS SES:**
- SMTP Host: email-smtp.region.amazonaws.com
- SMTP Port: 587
- Username: Your SES SMTP username
- Password: Your SES SMTP password

---

## User Guide

### How to Purchase Activation Code

**Step 1: Visit Purchase Page**

Go to: `http://your-domain.com/purchase-code.html`

**Step 2: Select Plan**

Choose from available plans:
- Monthly Plan (₹60/month)
- Quarterly Plan (₹150/3 months) - Save 17%
- Yearly Plan (₹500/year) - Save 30%
- Lifetime Plan (₹2,999 one-time) - Best Value

**Step 3: Enter Your Details**

- Full Name
- Email Address (activation code will be sent here)
- Phone Number

**Step 4: Select Payment Method**

Choose your preferred payment method:
- UPI Payment (instant)
- Bank Transfer (NEFT/RTGS/IMPS)
- Cryptocurrency (BTC/ETH/USDT/BNB)

**Step 5: Complete Payment**

**For UPI:**
1. Scan QR code or copy UPI ID
2. Open any UPI app (Google Pay, PhonePe, etc.)
3. Make payment
4. Copy transaction ID

**For Bank Transfer:**
1. Note down bank details
2. Transfer money via net banking or bank app
3. Copy transaction reference number

**For Cryptocurrency:**
1. Select cryptocurrency
2. Copy wallet address or scan QR code
3. Send crypto from your wallet
4. Copy transaction hash

**Step 6: Verify Payment**

1. Enter transaction ID/reference number/hash
2. Click "Verify Payment & Get Activation Code"
3. System will verify payment
4. Activation code will be generated
5. Code will be sent to your email

**Step 7: Activate Subscription**

1. Check your email for activation code
2. Click on activation link in email
3. Or go to: `http://your-domain.com/activate.html`
4. Enter activation code
5. Click "Activate"
6. Your subscription is now active!

---

### How to Use the Gateway

**Step 1: Login to User Panel**

1. Go to: `http://your-domain.com/user/login.html`
2. Enter email and password
3. Click "Login"

**Step 2: Configure Payment Settings**

1. Go to "Payment Settings"
2. Add your bank account details
3. Add your UPI ID
4. Add crypto wallets (optional)
5. Save configuration

**Step 3: Create Payment Link**

1. Go to "Create Payment Link"
2. Enter amount
3. Enter description
4. Select payment methods to accept
5. Click "Generate Link"
6. Share link with customers

**Step 4: Receive Payments**

1. Customer clicks on payment link
2. Customer selects payment method
3. Customer makes payment to YOUR account
4. You receive payment directly
5. Transaction is recorded in dashboard

**Step 5: Track Transactions**

1. Go to "Transactions"
2. View all payments received
3. Filter by date, amount, status
4. Export reports
5. Download invoices

---

## Reseller System

### How Reseller System Works

**Overview:**

1. Admin creates reseller accounts
2. Resellers generate activation codes with commission
3. Resellers sell codes to customers
4. Revenue is split between admin and reseller
5. Resellers track sales and earnings

**Revenue Split Example:**

- Yearly Plan Price: ₹500
- Reseller Commission: 20%
- Reseller Earns: ₹100
- Admin Earns: ₹400

### For Admin: How to Create Resellers

**Step 1: Login to Admin Panel**

Go to: `http://your-domain.com/admin/login.html`

**Step 2: Create Reseller Account**

1. Go to "Reseller Management"
2. Click "Add New Reseller"
3. Enter reseller details:
   - Name
   - Email
   - Phone
   - Commission Rate (e.g., 20%)
4. Click "Create Reseller"
5. Reseller receives login credentials via email

**Step 3: Manage Resellers**

- View all resellers
- Edit reseller details
- Change commission rates
- Activate/deactivate resellers
- View reseller sales
- Track commission payouts

### For Resellers: How to Sell Codes

**Step 1: Login to Reseller Panel**

Go to: `http://your-domain.com/reseller/login.html`

**Step 2: Generate Activation Codes**

1. Go to "Generate Codes"
2. Select plan (Monthly/Quarterly/Yearly/Lifetime)
3. Enter quantity
4. Click "Generate Codes"
5. Codes are generated instantly

**Step 3: Sell Codes to Customers**

1. Share activation codes with customers
2. Customers activate codes
3. You earn commission
4. Track sales in dashboard

**Step 4: Track Earnings**

1. Go to "Dashboard"
2. View total sales
3. View total earnings
4. View commission breakdown
5. Download sales reports

**Step 5: Request Payout**

1. Go to "Payouts"
2. View available balance
3. Click "Request Payout"
4. Enter bank details
5. Submit request
6. Admin processes payout

---

## API Documentation

### Authentication

All API requests require authentication using API key.

**Get API Key:**

1. Login to User Panel
2. Go to "API Settings"
3. Click "Generate API Key"
4. Copy API key

**Authentication Header:**

```
Authorization: Bearer YOUR_API_KEY
```

### Create Payment Link

**Endpoint:** `POST /api/create-payment-link.php`

**Request Body:**

```json
{
  "amount": 1000,
  "description": "Product Purchase",
  "customer_email": "customer@example.com",
  "customer_name": "John Doe",
  "payment_methods": ["upi", "bank", "crypto"]
}
```

**Response:**

```json
{
  "success": true,
  "payment_link": "https://your-domain.com/pay/abc123",
  "payment_id": "PAY123456",
  "expires_at": "2025-10-05 10:00:00"
}
```

### Verify Payment

**Endpoint:** `POST /api/verify-payment.php`

**Request Body:**

```json
{
  "payment_id": "PAY123456",
  "transaction_id": "UPI123456789"
}
```

**Response:**

```json
{
  "success": true,
  "status": "completed",
  "amount": 1000,
  "transaction_id": "UPI123456789",
  "verified_at": "2025-10-04 10:30:00"
}
```

### Get Transaction Details

**Endpoint:** `GET /api/get-transaction.php?id=TXN123456`

**Response:**

```json
{
  "success": true,
  "transaction": {
    "id": "TXN123456",
    "amount": 1000,
    "status": "completed",
    "payment_method": "upi",
    "customer_email": "customer@example.com",
    "created_at": "2025-10-04 10:00:00",
    "completed_at": "2025-10-04 10:30:00"
  }
}
```

---

## Security Features

### Data Security

- **Encryption:** All sensitive data encrypted in database
- **HTTPS:** SSL/TLS encryption for all communications
- **Password Hashing:** Bcrypt hashing for passwords
- **SQL Injection Protection:** Prepared statements
- **XSS Protection:** Input sanitization and output encoding

### Payment Security

- **Direct Transfer:** No payment gateway, direct bank transfer
- **No Card Storage:** No credit card details stored
- **Transaction Verification:** Manual verification of transactions
- **Fraud Detection:** Suspicious transaction monitoring
- **Secure Wallets:** Cryptocurrency wallet security

### Account Security

- **Two-Factor Authentication (2FA):** Optional 2FA for accounts
- **Session Management:** Secure session handling
- **Login Attempts:** Limit failed login attempts
- **IP Whitelisting:** Restrict access by IP
- **Activity Logs:** Track all user activities

### Best Security Practices

1. **Use Strong Passwords:**
   - Minimum 8 characters
   - Mix of uppercase, lowercase, numbers, symbols
   - Change regularly

2. **Enable 2FA:**
   - Use Google Authenticator
   - Backup codes stored securely

3. **Regular Backups:**
   - Daily database backups
   - Store backups securely
   - Test backup restoration

4. **Keep Software Updated:**
   - Update PHP regularly
   - Update MySQL regularly
   - Update dependencies

5. **Monitor Transactions:**
   - Check transactions daily
   - Verify suspicious activities
   - Report fraud immediately

---

## Troubleshooting

### Common Issues and Solutions

#### Issue: Cannot Login to Admin Panel

**Possible Causes:**
- Wrong username or password
- Account locked due to failed attempts
- Database connection error

**Solutions:**
1. Verify credentials (default: admin/admin123)
2. Reset password via database
3. Check database connection in config file
4. Clear browser cache and cookies

#### Issue: Emails Not Sending

**Possible Causes:**
- Wrong SMTP credentials
- Gmail blocking less secure apps
- Firewall blocking SMTP port
- Email in spam folder

**Solutions:**
1. Verify SMTP settings
2. Use Gmail App Password (not regular password)
3. Check firewall settings
4. Check spam folder
5. Try different SMTP port (465 instead of 587)
6. Test with different email provider

#### Issue: Payment Details Not Showing

**Possible Causes:**
- Payment configuration not saved
- Database connection error
- Browser cache issue
- JavaScript error

**Solutions:**
1. Re-save payment configuration
2. Check database connection
3. Clear browser cache
4. Check browser console for errors
5. Try different browser

#### Issue: Activation Code Not Generated

**Possible Causes:**
- Database error
- Plan not found
- API endpoint error
- Server error

**Solutions:**
1. Check database connection
2. Verify plan exists in database
3. Check API endpoint is accessible
4. Check server error logs
5. Verify PHP version compatibility

#### Issue: Customer Not Receiving Email

**Possible Causes:**
- Wrong email address
- Email in spam folder
- SMTP not configured
- Email delivery failed

**Solutions:**
1. Verify customer email address
2. Check spam folder
3. Test email configuration
4. Check email logs
5. Try resending email

### Getting Help

**Documentation:**
- Read this documentation thoroughly
- Check QUICKSTART.md for quick setup
- Review API documentation

**Support Channels:**
- Email: support@payme-gateway.com
- GitHub Issues: https://github.com/2lokeshrao/payme-2d-gateway/issues
- Documentation: https://payme-gateway.lindy.site/docs

**Before Contacting Support:**
1. Check this documentation
2. Review error messages
3. Check browser console
4. Check server logs
5. Try troubleshooting steps

**When Contacting Support, Provide:**
- Detailed description of issue
- Steps to reproduce
- Error messages
- Screenshots
- Browser and OS information
- Server configuration

---

## Appendix

### System Requirements

**Minimum Requirements:**
- PHP 7.4+
- MySQL 5.7+
- 1 GB RAM
- 10 GB Storage
- Apache/Nginx

**Recommended Requirements:**
- PHP 8.0+
- MySQL 8.0+
- 2 GB RAM
- 20 GB Storage
- Apache/Nginx with SSL

### File Structure

```
payme-2d-gateway/
├── admin/                  # Admin panel files
├── user/                   # User panel files
├── reseller/              # Reseller panel files
├── api/                   # API endpoints
├── config/                # Configuration files
├── assets/                # CSS, JS, images
├── uploads/               # User uploads
├── database.sql           # Database schema
├── index.html            # Homepage
├── purchase-code.html    # Purchase page
├── activate.html         # Activation page
└── DOCUMENTATION.md      # This file
```

### Database Tables

**users**
- id, email, password, name, phone, status, created_at

**subscription_plans**
- id, name, price, duration, features, status

**activation_codes**
- id, code, plan_id, user_id, status, expires_at, created_at

**code_purchases**
- id, customer_name, customer_email, plan_id, amount, payment_method, transaction_id, status, created_at

**resellers**
- id, name, email, commission_rate, total_sales, total_earnings, status, created_at

**payment_config**
- id, config_key, config_value, updated_at

**transactions**
- id, user_id, amount, type, status, payment_method, transaction_id, created_at

### Changelog

**Version 1.0.0 (October 4, 2025)**
- Initial release
- Complete payment gateway system
- Admin, user, and reseller panels
- Multiple payment methods
- Automated code generation
- Email automation
- API integration
- Complete documentation

---

**© 2025 PayMe 2D Gateway. All rights reserved.**

For more information, visit: https://payme-gateway.lindy.site
