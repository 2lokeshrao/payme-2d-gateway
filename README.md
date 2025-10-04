# 💳 PayMe 2D Gateway - Complete Payment & Reseller System

A comprehensive payment gateway solution with built-in reseller management, activation code system, and self-payment features.

![Version](https://img.shields.io/badge/version-1.0.0-blue)
![PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange)
![License](https://img.shields.io/badge/license-Proprietary-red)

---

## 🌟 Key Features

### 💼 Admin Panel
- **Plan Management**: Create custom subscription plans with flexible pricing
- **Reseller Management**: Onboard and manage resellers with commission tracking
- **Activation Codes**: Generate single or bulk codes (up to 100 at once)
- **Analytics Dashboard**: Real-time statistics and performance metrics
- **System Configuration**: Customize settings and payment methods

### 🎯 Reseller Portal
- **Code Generation**: Create activation codes for assigned plans
- **Sales Tracking**: Monitor sales performance and customer base
- **Commission Dashboard**: Track earnings and pending payouts
- **Customer Management**: View and manage customer subscriptions
- **Limited Access**: Secure, role-based access control

### 👤 User Features
- **Subscription Activation**: Easy code-based subscription activation
- **Self-Payment Setup**: Configure bank accounts, UPI, and crypto wallets
- **Payment Gateway**: Accept payments via cards, UPI, net banking
- **Transaction History**: Track all payment activities
- **API Integration**: RESTful APIs for seamless integration

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     ADMIN PANEL                              │
│  • Full System Control                                       │
│  • Plan & Reseller Management                                │
│  • Code Generation & Analytics                               │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                  RESELLER PORTAL                             │
│  • Limited Dashboard Access                                  │
│  • Code Generation (Assigned Plans)                          │
│  • Sales & Commission Tracking                               │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                   END USER SYSTEM                            │
│  • Subscription Activation                                   │
│  • Payment Gateway Features                                  │
│  • Self-Payment Configuration                                │
└─────────────────────────────────────────────────────────────┘
```

---

## 📁 Project Structure

```
payme-2d-gateway/
├── admin/
│   ├── dashboard.html              # Admin main dashboard
│   ├── plan-management.html        # Subscription plan management
│   ├── reseller-management.html    # Reseller account management
│   └── activation-codes.html       # Code generation & tracking
│
├── reseller/
│   ├── login.html                  # Reseller authentication
│   └── dashboard.html              # Reseller control panel
│
├── api/
│   ├── admin/
│   │   ├── plans.php              # Plan CRUD operations
│   │   ├── resellers.php          # Reseller management API
│   │   └── activation-codes.php   # Code generation API
│   │
│   ├── reseller/
│   │   ├── login.php              # Reseller authentication
│   │   ├── stats.php              # Reseller statistics
│   │   ├── plans.php              # Allowed plans list
│   │   ├── generate-code.php      # Code generation
│   │   └── codes.php              # Code history
│   │
│   └── config.php                 # Database configuration
│
├── dashboard.html                  # User main dashboard
├── subscription.html               # Subscription management
├── activate-subscription.html      # Code activation page
├── self-payment-settings.html      # Payment configuration
├── payment-methods.html            # Payment method setup
├── transactions.html               # Transaction history
│
├── database_reseller_system.sql    # Complete database schema
├── RESELLER_SYSTEM_DOCUMENTATION.md # Detailed documentation
└── README.md                       # This file
```

---

## 🚀 Quick Start

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional)

### Installation

1. **Clone or Download the Project**
```bash
cd /var/www/html
git clone <repository-url> payme-gateway
cd payme-gateway
```

2. **Create Database**
```bash
mysql -u root -p
CREATE DATABASE payme_gateway;
USE payme_gateway;
SOURCE database_reseller_system.sql;
```

3. **Configure Database Connection**
Edit `api/config.php`:
```php
$host = 'localhost';
$dbname = 'payme_gateway';
$username = 'your_username';
$password = 'your_password';
```

4. **Set Permissions**
```bash
chmod 755 payme-gateway
chmod 644 *.html
chmod 644 api/*.php
```

5. **Create Admin Account**
```sql
INSERT INTO admins (username, email, password_hash, role)
VALUES ('admin', 'admin@example.com', 
        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
        'super_admin');
-- Default password: password (change immediately!)
```

6. **Access the System**
- Admin Panel: `http://your-domain.com/admin/dashboard.html`
- Reseller Portal: `http://your-domain.com/reseller/login.html`
- User Dashboard: `http://your-domain.com/dashboard.html`

---

## 📖 Usage Guide

### For Administrators

#### Creating a Subscription Plan
1. Navigate to **Plan Management**
2. Click **Create New Plan**
3. Enter plan details:
   - Name (e.g., "Monthly Pro")
   - Type (Monthly/Quarterly/Yearly/Lifetime)
   - Price ($60)
   - Duration (30 days)
   - Features (list of features)
4. Click **Create Plan**

#### Setting Up a Reseller
1. Go to **Reseller Management**
2. Click **Add New Reseller**
3. Fill in reseller information:
   - Name, Email, Phone
   - Password (will be hashed)
   - Commission Rate (e.g., 20%)
   - Assign Plans (select which plans they can sell)
4. Click **Create Reseller**
5. Share login credentials with reseller

#### Generating Activation Codes
1. Navigate to **Activation Codes**
2. Choose generation method:
   - **Single Code**: For individual customers
   - **Bulk Generation**: Up to 100 codes at once
3. Select plan and optional reseller assignment
4. Set validity period (default: 365 days)
5. Click **Generate**
6. Copy and distribute codes

### For Resellers

#### Logging In
1. Visit `/reseller/login.html`
2. Enter your email and password
3. Access your dashboard

#### Generating Codes for Customers
1. From dashboard, select a plan (from your allowed plans)
2. Optionally enter customer email
3. Click **Generate Code**
4. Code format: `PM2D-XXXX-XXXX-XXXX`
5. Send code to customer

#### Tracking Your Performance
- **Total Sales**: Number of codes activated
- **Active Codes**: Unused codes still valid
- **Total Earnings**: All-time commission earned
- **Pending Payout**: Commission awaiting payment

### For End Users

#### Activating a Subscription
1. Visit `/activate-subscription.html`
2. Enter your 16-character activation code
3. Click **Activate Subscription**
4. Your subscription starts immediately

#### Setting Up Self-Payment
1. Go to **Self Payment Settings**
2. Configure your payment methods:
   - **Bank Account**: Add bank details for direct deposits
   - **UPI**: Add UPI ID for instant payments
   - **Crypto Wallets**: Add BTC, ETH, USDT, BNB addresses
3. Enable auto-settlement
4. Save settings

---

## 🔌 API Documentation

### Admin APIs

#### Plans API (`/api/admin/plans.php`)

**List All Plans**
```http
GET /api/admin/plans.php?action=list
```

**Create Plan**
```http
POST /api/admin/plans.php?action=create
Content-Type: application/json

{
  "name": "Monthly Pro",
  "type": "monthly",
  "price": 60,
  "duration": 30,
  "features": ["Feature 1", "Feature 2"],
  "status": "active"
}
```

#### Resellers API (`/api/admin/resellers.php`)

**Create Reseller**
```http
POST /api/admin/resellers.php?action=create
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "phone": "+1234567890",
  "password": "secure_password",
  "commission_rate": 20,
  "allowed_plans": [1, 2, 3],
  "status": "active"
}
```

#### Activation Codes API (`/api/admin/activation-codes.php`)

**Generate Single Code**
```http
POST /api/admin/activation-codes.php?action=generate
Content-Type: application/json

{
  "plan_id": 1,
  "reseller_id": 5,
  "validity_days": 365,
  "customer_email": "customer@example.com"
}
```

**Generate Bulk Codes**
```http
POST /api/admin/activation-codes.php?action=generate-bulk
Content-Type: application/json

{
  "plan_id": 1,
  "reseller_id": 5,
  "quantity": 50,
  "validity_days": 365
}
```

### Reseller APIs

#### Login
```http
POST /api/reseller/login.php
Content-Type: application/json

{
  "email": "reseller@example.com",
  "password": "password"
}
```

#### Generate Code
```http
POST /api/reseller/generate-code.php
Content-Type: application/json

{
  "plan_id": 1,
  "customer_email": "customer@example.com"
}
```

---

## 💾 Database Schema

### Key Tables

- **`resellers`**: Reseller account information
- **`activation_codes`**: All generated activation codes
- **`reseller_earnings`**: Commission tracking
- **`reseller_customers`**: Customer-reseller relationships
- **`subscription_plans`**: Available subscription plans
- **`user_subscriptions`**: Active user subscriptions

### Stored Procedures

- **`generate_activation_code()`**: Generates unique codes
- **`activate_code()`**: Activates subscription and calculates commission

### Views

- **`reseller_performance`**: Reseller statistics summary
- **`active_code_subscriptions`**: Active subscriptions from codes

See `database_reseller_system.sql` for complete schema.

---

## 🔒 Security Features

- ✅ **Password Hashing**: bcrypt for secure password storage
- ✅ **Session Management**: Secure session handling
- ✅ **Role-Based Access**: Admin, Reseller, User permissions
- ✅ **SQL Injection Prevention**: Prepared statements
- ✅ **XSS Protection**: Input sanitization
- ✅ **Unique Code Generation**: Collision-free activation codes
- ✅ **Expiry Validation**: Automatic code expiration
- ✅ **One-Time Use**: Codes can only be used once

---

## 📊 Business Model

### Revenue Streams

1. **Direct Sales**: Admin sells codes directly to users
2. **Reseller Sales**: Resellers sell codes with commission
3. **Subscription Plans**: Recurring revenue from active subscriptions

### Commission Structure

```
Plan Price: $100
Commission Rate: 20%
Reseller Earns: $20
Admin Keeps: $80
```

### Code-Based Activation

- Users purchase activation codes
- Codes activate specific plan subscriptions
- Automatic time period tracking
- Service stops when subscription expires
- Renewal requires new activation code

---

## 🎯 Use Cases

### 1. SaaS Platform
Sell subscription-based software access through resellers worldwide.

### 2. Payment Gateway Service
Provide payment processing services with tiered pricing plans.

### 3. Digital Products
Distribute digital products (courses, ebooks, software) via activation codes.

### 4. Membership Sites
Manage membership access with flexible subscription plans.

### 5. API Access
Sell API access tiers with usage-based pricing.

---

## 🛠️ Customization

### Changing Code Format

Edit `generateUniqueCode()` function in APIs:
```php
$code = sprintf('CUSTOM-%04d-%04d-%04d', 
    rand(1000, 9999), 
    rand(1000, 9999), 
    rand(1000, 9999)
);
```

### Adding New Plan Types

Update `subscription_plans` table and admin forms:
```sql
ALTER TABLE subscription_plans 
MODIFY plan_type ENUM('monthly', 'quarterly', 'yearly', 'lifetime', 'custom', 'trial');
```

### Custom Commission Rates

Set per-reseller commission rates in reseller management:
```sql
UPDATE resellers 
SET commission_rate = 25.00 
WHERE id = 1;
```

---

## 📈 Analytics & Reporting

### Admin Dashboard Metrics
- Total Plans & Active Plans
- Total Resellers & Active Resellers
- Codes Generated & Used
- Total Revenue & Commission Paid

### Reseller Dashboard Metrics
- Total Sales Count
- Active Codes Available
- Total Earnings (All-Time)
- Pending Payout Amount

### SQL Reports

**Top Resellers by Revenue**
```sql
SELECT * FROM reseller_performance 
ORDER BY total_earnings DESC 
LIMIT 10;
```

**Monthly Revenue Report**
```sql
SELECT 
    DATE_FORMAT(used_at, '%Y-%m') as month,
    COUNT(*) as activations,
    SUM(sp.price) as revenue
FROM activation_codes ac
JOIN subscription_plans sp ON ac.plan_id = sp.id
WHERE ac.status = 'used'
GROUP BY month;
```

---

## 🐛 Troubleshooting

### Common Issues

**Issue**: Code activation fails
**Solution**: Check code status, expiry date, and user authentication

**Issue**: Reseller can't generate codes
**Solution**: Verify plan is in reseller's `allowed_plans` list

**Issue**: Commission not calculated
**Solution**: Ensure `reseller_id` is set when code is generated

**Issue**: Login fails
**Solution**: Verify credentials and account status (active/inactive)

---

## 📚 Documentation

- **Full Documentation**: See `RESELLER_SYSTEM_DOCUMENTATION.md`
- **Database Schema**: See `database_reseller_system.sql`
- **API Reference**: See API Documentation section above

---

## 🔄 Version History

### Version 1.0.0 (October 4, 2025)
- ✅ Initial release
- ✅ Admin panel with plan management
- ✅ Reseller portal with code generation
- ✅ Activation code system
- ✅ Commission tracking
- ✅ Self-payment features
- ✅ Complete API suite
- ✅ Comprehensive documentation

---

## 🤝 Contributing

This is a proprietary system. For feature requests or bug reports, contact the development team.

---

## 📞 Support

- **Email**: support@payme-gateway.com
- **Documentation**: Full docs in `RESELLER_SYSTEM_DOCUMENTATION.md`
- **Technical Support**: Available for licensed users

---

## 📄 License

Copyright © 2025 PayMe 2D Gateway. All rights reserved.

This is proprietary software. Unauthorized copying, distribution, or modification is prohibited.

---

## 🎉 Acknowledgments

Built with modern web technologies:
- PHP for backend processing
- MySQL for data storage
- Vanilla JavaScript for frontend interactivity
- Responsive CSS for beautiful UI

---

**Made with ❤️ by the PayMe 2D Gateway Team**

**Last Updated**: October 4, 2025
