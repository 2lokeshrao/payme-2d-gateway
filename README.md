# PayMe 2D Gateway - Complete Payment Gateway System

![PayMe Gateway](https://img.shields.io/badge/Version-1.0.0-blue)
![License](https://img.shields.io/badge/License-Proprietary-red)
![Status](https://img.shields.io/badge/Status-Production%20Ready-green)

## 🚀 Overview

PayMe 2D Gateway is a complete, production-ready payment gateway system supporting UPI, Cryptocurrency, Bank Transfers, Cards, and Digital Wallets. Built with PHP, PostgreSQL, and modern web technologies.

**Live Demo:** https://hungry-nights-cough.lindy.site

---

## ✨ Features

### 🔐 Multi-User System
- **Admin Panel** - Complete control over merchants, transactions, KYC verification
- **Merchant Dashboard** - Business analytics, transaction management, API integration
- **Client Portal** - Gateway owners can manage their business
- **Reseller System** - Affiliate program with commission tracking

### 💳 Payment Methods
- ✅ UPI Payments
- ✅ Cryptocurrency (Bitcoin, Ethereum, USDT)
- ✅ Bank Transfers
- ✅ Card Payments
- ✅ Digital Wallets

### 🛡️ Security Features
- ✅ CSRF Protection
- ✅ Password Hashing (bcrypt)
- ✅ Prepared Statements (SQL Injection Prevention)
- ✅ Rate Limiting
- ✅ Input Validation & Sanitization
- ✅ API Key Authentication
- ✅ Session Management

### 📊 Business Features
- Real-time transaction tracking
- Revenue analytics & statistics
- KYC document management
- API integration for merchants
- Webhook callbacks
- Settlement management
- Commission tracking for resellers

---

## 📁 Project Structure

```
payme-2d-gateway/
├── api/                          # Backend APIs
│   ├── config/
│   │   ├── database.php         # PostgreSQL connection
│   │   └── security.php         # Security helpers
│   ├── auth/
│   │   ├── login.php            # User authentication
│   │   └── register.php         # User registration
│   ├── payment/
│   │   ├── create_payment_link.php
│   │   └── verify_payment.php
│   ├── merchant/
│   │   ├── get_transactions.php
│   │   └── upload_kyc.php
│   └── admin/
│       └── verify_kyc.php
├── admin/                        # Admin panel
│   ├── dashboard.html
│   ├── merchants.html
│   └── transactions.html
├── merchant/                     # Merchant portal
│   ├── merchant-login.html
│   └── merchant-dashboard.html
├── client/                       # Client portal
│   ├── client-login.html
│   └── client-dashboard.html
├── reseller/                     # Reseller system
│   ├── reseller-register.html
│   └── reseller-dashboard.html
├── js/                          # JavaScript files
│   └── init-merchants.js        # Initialize default data
├── css/                         # Stylesheets
├── database.sql                 # MySQL schema
├── database_postgres.sql        # PostgreSQL schema
├── API_DOCUMENTATION.md         # Complete API docs
└── README.md                    # This file
```

---

## 🔧 Installation & Setup

### Prerequisites

- **PostgreSQL** 14+ or **MySQL** 5.7+
- **PHP** 7.4+ with PDO extension
- **Web Server** (Apache/Nginx)
- **Git**

### Step 1: Clone Repository

```bash
git clone https://github.com/2lokeshrao/payme-2d-gateway.git
cd payme-2d-gateway
```

### Step 2: Database Setup

#### For PostgreSQL:

```bash
# Create database
createdb -h localhost payme_gateway

# Import schema
psql -h localhost -d payme_gateway -f database_postgres.sql
```

#### For MySQL:

```bash
# Import schema
mysql -u root -p < database.sql
```

### Step 3: Configure Database Connection

Edit `api/config/database.php`:

```php
// For PostgreSQL (default)
private $host = 'localhost';
private $db_name = 'payme_gateway';
private $username = 'your_username';
private $password = 'your_password';

// Or use environment variables
$this->username = getenv('PGUSER') ?: 'postgres';
$this->password = getenv('PGPASSWORD') ?: '';
```

### Step 4: Set Permissions

```bash
# Create uploads directory
mkdir -p api/uploads/kyc
chmod -R 755 api/uploads

# Set proper permissions
chmod 644 api/config/*.php
chmod 644 api/**/*.php
```

### Step 5: Configure Web Server

#### Apache (.htaccess):

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/(.*)$ api/$1 [L]
```

#### Nginx:

```nginx
location /api/ {
    try_files $uri $uri/ /api/index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### Step 6: Test Installation

1. Open browser: `http://localhost/payme-2d-gateway`
2. Login as Admin:
   - Email: `admin@payme.com`
   - Password: `admin123`
3. Login as Merchant:
   - Email: `merchant@test.com`
   - Password: `Merchant@2025`

---

## 📚 Database Schema

### Tables (10 Total)

1. **users** - All user types (Admin, Merchant, Client, Reseller)
2. **merchants** - Business details, API keys, KYC status
3. **kyc_documents** - Document uploads and verification
4. **transactions** - Payment records with status tracking
5. **settlements** - Merchant payouts
6. **subscriptions** - License plans (Monthly/Yearly/Lifetime)
7. **resellers** - Affiliate program data
8. **referrals** - Commission tracking
9. **api_logs** - API request logging
10. **notifications** - User notifications

### Sample Data Included

- ✅ Admin user
- ✅ Test merchant with verified KYC
- ✅ Test reseller
- ✅ 4 sample transactions
- ✅ Active subscription
- ✅ Sample notifications

---

## 🔌 API Integration

### Quick Start

```javascript
// Create Payment Link
const response = await fetch('https://yoursite.com/api/payment/create_payment_link.php', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-API-Key': 'pk_test_51234567890abcdef'
  },
  body: JSON.stringify({
    amount: 1500.00,
    customer_email: 'customer@example.com',
    payment_method: 'upi',
    description: 'Order #12345',
    callback_url: 'https://yoursite.com/webhook'
  })
});

const data = await response.json();
console.log(data.data.payment_link);
```

### API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/auth/login.php` | POST | User authentication |
| `/api/auth/register.php` | POST | User registration |
| `/api/payment/create_payment_link.php` | POST | Create payment link |
| `/api/payment/verify_payment.php` | POST | Verify payment status |
| `/api/merchant/get_transactions.php` | GET | Get transaction list |
| `/api/merchant/upload_kyc.php` | POST | Upload KYC documents |
| `/api/admin/verify_kyc.php` | POST | Verify KYC (Admin) |

**Full API Documentation:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

---

## 🎯 Usage Examples

### For Merchants

1. **Get API Credentials**
   - Login to merchant dashboard
   - Copy API Key and Merchant ID
   - Read API documentation

2. **Create Payment Link**
   ```bash
   curl -X POST https://yoursite.com/api/payment/create_payment_link.php \
     -H "X-API-Key: your_api_key" \
     -H "Content-Type: application/json" \
     -d '{
       "amount": 1500.00,
       "customer_email": "customer@example.com",
       "payment_method": "upi"
     }'
   ```

3. **Verify Payment**
   ```bash
   curl -X POST https://yoursite.com/api/payment/verify_payment.php \
     -H "X-API-Key: your_api_key" \
     -H "Content-Type: application/json" \
     -d '{
       "transaction_id": "TXN1a2b3c4d5e6f7g8h9",
       "payment_status": "success",
       "utr_number": "UTR123456789"
     }'
   ```

### For Admins

1. **Verify Merchant KYC**
   - Login to admin panel
   - Navigate to Merchants section
   - Review KYC documents
   - Approve or reject

2. **Monitor Transactions**
   - View all transactions across merchants
   - Filter by status, date, merchant
   - Export reports

---

## 💰 Pricing Plans

| Plan | Price | Features |
|------|-------|----------|
| **Monthly** | ₹4,999/month | Full features, monthly billing |
| **Yearly** | ₹49,999/year | Save 17%, annual billing |
| **Lifetime** | ₹2,99,999 | One-time payment, lifetime access |

### Reseller Commission
- Up to **30% commission** on referrals
- Recurring revenue on subscriptions
- Dedicated reseller dashboard

---

## 🔒 Security Best Practices

1. **Change Default Passwords**
   ```sql
   UPDATE users SET password_hash = '$2y$10$your_new_hash' WHERE email = 'admin@payme.com';
   ```

2. **Use Environment Variables**
   ```bash
   export PGUSER="your_db_user"
   export PGPASSWORD="your_db_password"
   ```

3. **Enable HTTPS**
   - Use SSL certificate
   - Force HTTPS redirects
   - Secure cookie flags

4. **Regular Backups**
   ```bash
   pg_dump -h localhost payme_gateway > backup_$(date +%Y%m%d).sql
   ```

5. **Update Dependencies**
   - Keep PHP updated
   - Update PostgreSQL/MySQL
   - Monitor security advisories

---

## 🐛 Troubleshooting

### Database Connection Failed

```bash
# Check PostgreSQL is running
sudo systemctl status postgresql

# Test connection
psql -h localhost -d payme_gateway -U your_user
```

### API Returns 500 Error

```bash
# Check PHP error logs
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log
```

### File Upload Fails

```bash
# Check permissions
ls -la api/uploads/
chmod -R 755 api/uploads/

# Check PHP upload settings
php -i | grep upload_max_filesize
```

---

## 📈 Performance Optimization

1. **Enable PHP OPcache**
   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=10000
   ```

2. **Database Indexing**
   - All foreign keys indexed
   - Transaction queries optimized
   - Proper use of EXPLAIN ANALYZE

3. **Caching**
   - Use Redis for session storage
   - Cache API responses
   - CDN for static assets

---

## 🤝 Contributing

This is a proprietary project. For feature requests or bug reports, contact:
- Email: support@payme.com
- GitHub Issues: https://github.com/2lokeshrao/payme-2d-gateway/issues

---

## 📄 License

**Proprietary License**

This software is licensed for use only by authorized clients who have purchased a valid license.

### License Tiers:
- **Monthly License:** ₹4,999/month
- **Yearly License:** ₹49,999/year
- **Lifetime License:** ₹2,99,999 (one-time)

Unauthorized copying, distribution, or modification is strictly prohibited.

---

## 📞 Support

### Technical Support
- Email: support@payme.com
- Phone: +91 98765 43210
- Hours: 9 AM - 6 PM IST (Mon-Fri)

### Documentation
- API Docs: [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- Video Tutorials: Coming soon
- Knowledge Base: https://docs.payme.com

### Community
- Discord: https://discord.gg/payme
- Forum: https://forum.payme.com

---

## 🎉 Changelog

### Version 1.0.0 (October 7, 2025)
- ✅ Complete database schema with 10 tables
- ✅ Backend APIs with security features
- ✅ Authentication system (Login/Register)
- ✅ Payment link creation and verification
- ✅ KYC document upload and verification
- ✅ Transaction management
- ✅ Admin panel for merchant management
- ✅ Merchant dashboard with analytics
- ✅ Reseller commission tracking
- ✅ API documentation
- ✅ Sample data for testing

---

## 🙏 Acknowledgments

- Built with ❤️ by the PayMe Team
- PostgreSQL for robust database
- PHP for backend APIs
- Bootstrap for responsive UI

---

**© 2025 PayMe 2D Gateway. All rights reserved.**

**Repository:** https://github.com/2lokeshrao/payme-2d-gateway
**Live Demo:** https://hungry-nights-cough.lindy.site
**Version:** 1.0.0
