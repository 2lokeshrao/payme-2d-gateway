# PayMe 2D Gateway

**Complete Payment Gateway Solution for Direct Payment Collection**

[![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/2lokeshrao/payme-2d-gateway)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-orange.svg)](https://mysql.com)

---

## Overview

PayMe 2D Gateway is a comprehensive payment gateway solution that enables businesses and individuals to accept payments directly into their bank accounts, UPI IDs, or cryptocurrency wallets without any middleman or commission fees.

### Key Features

✅ **Direct Payment Collection** - Payments go directly to your account  
✅ **Multiple Payment Methods** - Bank Transfer, UPI, Cryptocurrency  
✅ **Automated Code Generation** - Instant activation code delivery  
✅ **Email Automation** - Professional email templates  
✅ **Reseller System** - Build your own reseller network  
✅ **Complete API** - Integrate with any platform  
✅ **Real-time Analytics** - Track all transactions  
✅ **Secure & Reliable** - Bank-grade security

---

## Quick Start

### Installation

**Step 1: Clone the Repository**

```bash
git clone https://github.com/2lokeshrao/payme-2d-gateway.git
cd payme-2d-gateway
```

**Step 2: Setup Database**

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE payme_gateway;"

# Import schema
mysql -u root -p payme_gateway < database.sql
```

**Step 3: Configure Database Connection**

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'payme_gateway');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

**Step 4: Access the System**

- **Homepage:** `http://your-domain.com`
- **Admin Panel:** `http://your-domain.com/admin/login.html`
- **User Panel:** `http://your-domain.com/user/login.html`

**Default Admin Credentials:**
- Username: `admin`
- Password: `admin123`

⚠️ **Important:** Change the default password immediately after first login!

---

## System Requirements

### Minimum Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- 1 GB RAM
- 10 GB Storage

### Recommended Requirements

- PHP 8.0 or higher
- MySQL 8.0 or higher
- Apache/Nginx with SSL
- 2 GB RAM
- 20 GB Storage

---

## Features

### For Businesses

**Self Payment Gateway**
- Configure your own bank account
- Configure your own UPI ID
- Configure crypto wallets
- Payments come directly to you
- No commission, no middleman

**Payment Methods Supported**
- 🏦 Bank Transfer (NEFT/RTGS/IMPS)
- 📱 UPI Payments (Google Pay, PhonePe, Paytm, BHIM)
- 💎 Cryptocurrency (BTC, ETH, USDT, BNB)

**Transaction Management**
- Real-time transaction tracking
- Detailed analytics and reports
- Export transaction data
- Download invoices
- Payment verification

### For Admins

**Complete Control Panel**
- Dashboard with analytics
- User management
- Subscription plan management
- Reseller management
- Payment configuration
- Email configuration
- Transaction monitoring

**Reseller System**
- Create reseller accounts
- Set commission rates
- Track reseller sales
- Manage payouts
- Generate activation codes
- Revenue sharing

### For Customers

**Easy Purchase Process**
- Select subscription plan
- Enter details
- Choose payment method
- Complete payment
- Receive activation code via email
- Activate subscription instantly

**Subscription Plans**
- Monthly Plan: ₹60/month
- Quarterly Plan: ₹150/3 months (Save 17%)
- Yearly Plan: ₹500/year (Save 30%)
- Lifetime Plan: ₹2,999 one-time (Best Value)

---

## Documentation

### Complete Documentation

For detailed documentation, please refer to:

📚 **[DOCUMENTATION.md](DOCUMENTATION.md)** - Complete system documentation

This includes:
- Installation guide
- Configuration guide
- Admin setup guide
- Payment configuration
- User guide
- Reseller system
- API documentation
- Security features
- Troubleshooting

### Quick Start Guide

📖 **[QUICKSTART.md](QUICKSTART.md)** - Quick setup guide for getting started

---

## Configuration

### Payment Configuration

**Bank Account Setup**

1. Login to Admin Panel
2. Go to "Payment Configuration"
3. Click "Bank Transfer" tab
4. Enter bank details:
   - Bank Name
   - Account Holder Name
   - Account Number
   - IFSC Code
5. Save configuration

**UPI Payment Setup**

1. Go to "Payment Configuration"
2. Click "UPI Payment" tab
3. Enter UPI details:
   - UPI ID (e.g., yourname@okhdfc)
   - UPI Name
   - UPI Provider
4. Save configuration

**Cryptocurrency Setup**

1. Go to "Payment Configuration"
2. Click "Cryptocurrency" tab
3. Enter wallet addresses:
   - Bitcoin (BTC) wallet
   - Ethereum (ETH) wallet
   - Tether (USDT) wallet
   - Binance Coin (BNB) wallet
4. Save configuration

### Email Configuration

**Gmail Setup (Recommended)**

1. Go to "Payment Configuration"
2. Click "Email Settings" tab
3. Enter SMTP details:
   - SMTP Host: `smtp.gmail.com`
   - SMTP Port: `587`
   - SMTP Username: Your Gmail address
   - SMTP Password: Gmail App Password
4. Test email delivery
5. Save configuration

**How to Get Gmail App Password:**
1. Go to Google Account Settings
2. Security → 2-Step Verification
3. App Passwords
4. Generate password for "Mail"
5. Use this password in SMTP settings

---

## API Integration

### Authentication

All API requests require authentication using API key.

```bash
Authorization: Bearer YOUR_API_KEY
```

### Create Payment Link

```bash
POST /api/create-payment-link.php
Content-Type: application/json

{
  "amount": 1000,
  "description": "Product Purchase",
  "customer_email": "customer@example.com",
  "payment_methods": ["upi", "bank", "crypto"]
}
```

### Verify Payment

```bash
POST /api/verify-payment.php
Content-Type: application/json

{
  "payment_id": "PAY123456",
  "transaction_id": "UPI123456789"
}
```

For complete API documentation, see [DOCUMENTATION.md](DOCUMENTATION.md#api-documentation)

---

## Security

### Security Features

- ✅ SSL/TLS encryption
- ✅ Password hashing (Bcrypt)
- ✅ SQL injection protection
- ✅ XSS protection
- ✅ CSRF protection
- ✅ Two-factor authentication (2FA)
- ✅ Session management
- ✅ Activity logging

### Best Practices

1. Use strong passwords
2. Enable 2FA for admin accounts
3. Regular database backups
4. Keep software updated
5. Monitor transactions regularly
6. Use HTTPS (SSL certificate)
7. Restrict admin access by IP

---

## Support

### Getting Help

**Documentation:**
- [Complete Documentation](DOCUMENTATION.md)
- [Quick Start Guide](QUICKSTART.md)

**Support Channels:**
- Email: support@payme-gateway.com
- GitHub Issues: [Create Issue](https://github.com/2lokeshrao/payme-2d-gateway/issues)

**Before Contacting Support:**
1. Check documentation
2. Review error messages
3. Check browser console
4. Try troubleshooting steps

---

## Contributing

We welcome contributions! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## Changelog

### Version 1.0.0 (October 4, 2025)

**Initial Release**
- Complete payment gateway system
- Admin, user, and reseller panels
- Multiple payment methods (Bank, UPI, Crypto)
- Automated code generation and email delivery
- API integration
- Complete documentation

---

## Screenshots

### Homepage
![Homepage](https://payme-gateway.lindy.site/assets/images/screenshot-home.png)

### Purchase Page
![Purchase Page](https://payme-gateway.lindy.site/assets/images/screenshot-purchase.png)

### Admin Dashboard
![Admin Dashboard](https://payme-gateway.lindy.site/assets/images/screenshot-admin.png)

---

## Links

- **Live Demo:** https://payme-gateway.lindy.site
- **Documentation:** [DOCUMENTATION.md](DOCUMENTATION.md)
- **Quick Start:** [QUICKSTART.md](QUICKSTART.md)
- **GitHub:** https://github.com/2lokeshrao/payme-2d-gateway

---

## Contact

**Developer:** Lokesh Rao  
**Email:** 2lokeshrao@gmail.com  
**Website:** https://payme-gateway.lindy.site

---

**© 2025 PayMe 2D Gateway. All rights reserved.**

Made with ❤️ in India
