# PayMe 2D Gateway - Complete Reseller & Activation System Documentation

## 📋 Table of Contents
1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Database Schema](#database-schema)
4. [API Endpoints](#api-endpoints)
5. [User Roles & Permissions](#user-roles--permissions)
6. [Business Flow](#business-flow)
7. [Installation Guide](#installation-guide)
8. [Usage Guide](#usage-guide)
9. [Security Features](#security-features)
10. [Troubleshooting](#troubleshooting)

---

## 🎯 System Overview

The PayMe 2D Gateway Reseller System is a comprehensive subscription management platform that enables:

- **Admin Panel**: Full control over plans, resellers, and activation codes
- **Reseller Portal**: Limited access for resellers to generate codes and track sales
- **User Activation**: Code-based subscription activation for end users
- **Commission Tracking**: Automatic commission calculation and payout management

### Key Features
✅ Multi-tier user system (Admin → Reseller → End User)
✅ Activation code generation (single & bulk)
✅ Commission-based reseller model
✅ Plan management with custom pricing
✅ Automatic subscription activation
✅ Real-time statistics and reporting
✅ Secure authentication for all roles

---

## 🏗️ Architecture

### Three-Tier System

```
┌─────────────────────────────────────────────────────────────┐
│                        MAIN ADMIN                            │
│  • Create/Edit Plans                                         │
│  • Manage Resellers                                          │
│  • Generate Activation Codes                                 │
│  • View All Statistics                                       │
│  • System Configuration                                      │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                       RESELLERS                              │
│  • Generate Codes (Assigned Plans Only)                      │
│  • View Own Sales Statistics                                 │
│  • Track Commission Earnings                                 │
│  • Manage Own Customers                                      │
│  • Limited Dashboard Access                                  │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                       END USERS                              │
│  • Purchase Activation Codes                                 │
│  • Activate Subscriptions                                    │
│  • Use Payment Gateway Features                              │
│  • View Subscription Status                                  │
│  • Configure Self-Payment Settings                           │
└─────────────────────────────────────────────────────────────┘
```

---

## 💾 Database Schema

### Core Tables

#### 1. `resellers`
Stores reseller account information.

```sql
CREATE TABLE resellers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    commission_rate DECIMAL(5,2) DEFAULT 20.00,
    allowed_plans JSON,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    total_sales INT DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0.00,
    pending_payout DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 2. `activation_codes`
Stores all activation codes (admin + reseller generated).

```sql
CREATE TABLE activation_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    plan_id INT NOT NULL,
    created_by_type ENUM('admin', 'reseller') DEFAULT 'admin',
    created_by_id INT,
    reseller_id INT,
    status ENUM('unused', 'used', 'expired', 'revoked') DEFAULT 'unused',
    validity_days INT DEFAULT 365,
    expires_at DATETIME,
    used_by_user_id INT,
    used_by_email VARCHAR(255),
    used_at DATETIME,
    subscription_start_date DATETIME,
    subscription_end_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 3. `reseller_earnings`
Tracks commission earnings for each reseller.

```sql
CREATE TABLE reseller_earnings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reseller_id INT NOT NULL,
    activation_code_id INT NOT NULL,
    plan_id INT NOT NULL,
    plan_price DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    paid_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 4. `reseller_customers`
Tracks customers assigned to each reseller.

```sql
CREATE TABLE reseller_customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reseller_id INT NOT NULL,
    user_id INT NOT NULL,
    activation_code_id INT,
    customer_email VARCHAR(255) NOT NULL,
    subscription_status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    subscription_start_date DATETIME,
    subscription_end_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Complete Schema
See `database_reseller_system.sql` for the complete database schema including:
- Reseller payouts table
- Code activation history
- System settings
- Views for reporting
- Stored procedures
- Triggers

---

## 🔌 API Endpoints

### Admin APIs

#### Plan Management (`/api/admin/plans.php`)

| Action | Method | Endpoint | Description |
|--------|--------|----------|-------------|
| List Plans | GET | `?action=list` | Get all subscription plans |
| Get Plan | GET | `?action=get&id={id}` | Get specific plan details |
| Create Plan | POST | `?action=create` | Create new plan |
| Update Plan | PUT | `?action=update` | Update existing plan |
| Delete Plan | DELETE | `?action=delete&id={id}` | Delete plan |
| Get Stats | GET | `?action=stats` | Get plan statistics |

**Create Plan Request:**
```json
{
  "name": "Monthly Pro",
  "type": "monthly",
  "price": 60,
  "duration": 30,
  "features": ["Feature 1", "Feature 2"],
  "status": "active"
}
```

#### Reseller Management (`/api/admin/resellers.php`)

| Action | Method | Endpoint | Description |
|--------|--------|----------|-------------|
| List Resellers | GET | `?action=list` | Get all resellers |
| Get Reseller | GET | `?action=get&id={id}` | Get specific reseller |
| Create Reseller | POST | `?action=create` | Create new reseller |
| Update Reseller | PUT | `?action=update` | Update reseller |
| Delete Reseller | DELETE | `?action=delete&id={id}` | Delete reseller |
| Get Stats | GET | `?action=stats` | Get reseller statistics |

**Create Reseller Request:**
```json
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

#### Activation Code Management (`/api/admin/activation-codes.php`)

| Action | Method | Endpoint | Description |
|--------|--------|----------|-------------|
| List Codes | GET | `?action=list` | Get all activation codes |
| Generate Code | POST | `?action=generate` | Generate single code |
| Generate Bulk | POST | `?action=generate-bulk` | Generate multiple codes |
| Delete Code | DELETE | `?action=delete&id={id}` | Delete unused code |
| Get Stats | GET | `?action=stats` | Get code statistics |
| Activate Code | POST | `?action=activate` | Activate a code |

**Generate Code Request:**
```json
{
  "plan_id": 1,
  "reseller_id": 5,
  "validity_days": 365,
  "customer_email": "customer@example.com"
}
```

**Generate Bulk Request:**
```json
{
  "plan_id": 1,
  "reseller_id": 5,
  "quantity": 50,
  "validity_days": 365
}
```

### Reseller APIs

#### Authentication (`/api/reseller/login.php`)

**Login Request:**
```json
{
  "email": "reseller@example.com",
  "password": "password"
}
```

#### Statistics (`/api/reseller/stats.php`)
- GET request
- Returns: total_sales, active_codes, total_earnings, pending_payout

#### Plans (`/api/reseller/plans.php`)
- GET request
- Returns: List of plans reseller is allowed to sell

#### Generate Code (`/api/reseller/generate-code.php`)

**Request:**
```json
{
  "plan_id": 1,
  "customer_email": "customer@example.com"
}
```

#### Recent Codes (`/api/reseller/codes.php?action=recent`)
- GET request
- Returns: Last 10 codes generated by reseller

---

## 👥 User Roles & Permissions

### Admin
**Full Access:**
- ✅ Create/Edit/Delete Plans
- ✅ Create/Edit/Delete Resellers
- ✅ Generate Activation Codes (All Plans)
- ✅ View All Statistics
- ✅ Manage System Settings
- ✅ View All Transactions
- ✅ Process Payouts

**Access URLs:**
- Admin Panel: `/admin/dashboard.html`
- Plan Management: `/admin/plan-management.html`
- Reseller Management: `/admin/reseller-management.html`
- Activation Codes: `/admin/activation-codes.html`

### Reseller
**Limited Access:**
- ✅ Generate Codes (Assigned Plans Only)
- ✅ View Own Sales Statistics
- ✅ Track Commission Earnings
- ✅ View Own Customers
- ❌ Cannot Access Admin Panel
- ❌ Cannot Modify Plans
- ❌ Cannot View Other Resellers

**Access URLs:**
- Reseller Login: `/reseller/login.html`
- Reseller Dashboard: `/reseller/dashboard.html`

### End User
**Basic Access:**
- ✅ Activate Subscription with Code
- ✅ View Subscription Status
- ✅ Use Payment Gateway Features
- ✅ Configure Self-Payment Settings
- ❌ Cannot Access Admin/Reseller Panels
- ❌ Cannot Generate Codes

**Access URLs:**
- User Dashboard: `/dashboard.html`
- Activate Subscription: `/activate-subscription.html`
- Subscription Status: `/subscription.html`

---

## 💼 Business Flow

### 1. Admin Creates Reseller Account

```
Admin → Create Reseller
  ├─ Set Name, Email, Phone
  ├─ Set Commission Rate (e.g., 20%)
  ├─ Assign Plans Reseller Can Sell
  └─ Generate Login Credentials
```

### 2. Reseller Generates Activation Code

```
Reseller Login → Dashboard
  ├─ Select Plan (from allowed plans)
  ├─ Enter Customer Email (optional)
  ├─ Generate Code (PM2D-XXXX-XXXX-XXXX)
  └─ Send Code to Customer
```

### 3. Customer Activates Subscription

```
Customer → Activation Page
  ├─ Enter 16-Character Code
  ├─ System Validates Code
  ├─ Subscription Activated
  ├─ Commission Calculated
  └─ Reseller Earnings Updated
```

### 4. Commission Tracking

```
Code Activation
  ├─ Calculate Commission (Plan Price × Commission Rate)
  ├─ Create Earning Record (Status: Pending)
  ├─ Update Reseller Stats
  └─ Admin Processes Payout Later
```

---

## 🚀 Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- SSL certificate (recommended)

### Step 1: Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE payme_gateway;
USE payme_gateway;

# Import schema
SOURCE database_reseller_system.sql;
```

### Step 2: Configure Database Connection

Edit `api/config.php`:

```php
<?php
$host = 'localhost';
$dbname = 'payme_gateway';
$username = 'your_db_user';
$password = 'your_db_password';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### Step 3: Set Permissions

```bash
# Set proper permissions
chmod 755 /path/to/payme-2d-gateway
chmod 644 /path/to/payme-2d-gateway/*.html
chmod 644 /path/to/payme-2d-gateway/api/*.php
```

### Step 4: Create Admin Account

```sql
INSERT INTO admins (username, email, password_hash, role)
VALUES ('admin', 'admin@example.com', '$2y$10$...', 'super_admin');
```

### Step 5: Configure System Settings

Access admin panel and configure:
- Default commission rate
- Code validity period
- Minimum payout amount
- Payment methods

---

## 📖 Usage Guide

### For Admins

#### Creating a Plan
1. Go to **Plan Management**
2. Click **Create New Plan**
3. Fill in details:
   - Plan Name
   - Type (Monthly/Quarterly/Yearly)
   - Price
   - Duration (days)
   - Features (one per line)
4. Click **Create Plan**

#### Creating a Reseller
1. Go to **Reseller Management**
2. Click **Add New Reseller**
3. Fill in details:
   - Name, Email, Phone
   - Password
   - Commission Rate (%)
   - Select Plans to Assign
4. Click **Create Reseller**
5. Share login credentials with reseller

#### Generating Activation Codes
1. Go to **Activation Codes**
2. Choose generation method:
   - **Single Code**: For one customer
   - **Bulk Codes**: Up to 100 codes
3. Select plan and reseller (optional)
4. Set validity period
5. Click **Generate**
6. Copy and distribute codes

### For Resellers

#### Logging In
1. Go to `/reseller/login.html`
2. Enter email and password
3. Access reseller dashboard

#### Generating Codes
1. From dashboard, select plan
2. Enter customer email (optional)
3. Click **Generate Code**
4. Copy code and send to customer

#### Tracking Earnings
1. View dashboard statistics
2. Check **Total Earnings**
3. Monitor **Pending Payout**
4. View sales history

### For End Users

#### Activating Subscription
1. Go to `/activate-subscription.html`
2. Enter 16-character code
3. Click **Activate Subscription**
4. Subscription starts immediately

#### Checking Subscription Status
1. Go to `/subscription.html`
2. View current plan details
3. Check expiry date
4. Renew when needed

---

## 🔒 Security Features

### Authentication
- ✅ Password hashing (bcrypt)
- ✅ Session management
- ✅ Role-based access control
- ✅ CSRF protection (recommended to add)

### Code Security
- ✅ Unique code generation
- ✅ Expiry date validation
- ✅ One-time use enforcement
- ✅ Activation attempt logging

### Data Protection
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (input sanitization)
- ✅ Secure password storage
- ✅ HTTPS enforcement (recommended)

### Best Practices
1. Use HTTPS for all pages
2. Implement rate limiting on APIs
3. Add CSRF tokens to forms
4. Regular security audits
5. Keep PHP and MySQL updated

---

## 🐛 Troubleshooting

### Common Issues

#### 1. Code Already Used
**Problem:** User tries to activate already-used code
**Solution:** Generate new code for customer

#### 2. Code Expired
**Problem:** Code past expiry date
**Solution:** Generate new code with extended validity

#### 3. Reseller Can't Generate Code
**Problem:** Plan not in allowed_plans list
**Solution:** Admin must assign plan to reseller

#### 4. Commission Not Calculated
**Problem:** Earnings record not created
**Solution:** Check reseller_id in activation_codes table

#### 5. Login Failed
**Problem:** Invalid credentials
**Solution:** Reset password or check account status

### Database Issues

#### Reset Reseller Password
```sql
UPDATE resellers 
SET password_hash = '$2y$10$...' 
WHERE email = 'reseller@example.com';
```

#### Check Code Status
```sql
SELECT code, status, expires_at, used_at 
FROM activation_codes 
WHERE code = 'PM2D-XXXX-XXXX-XXXX';
```

#### View Reseller Earnings
```sql
SELECT * FROM reseller_earnings 
WHERE reseller_id = 1 
ORDER BY created_at DESC;
```

---

## 📊 Reporting & Analytics

### Admin Reports
- Total revenue by plan
- Reseller performance comparison
- Code usage statistics
- Commission payout summary

### Reseller Reports
- Personal sales history
- Commission earnings timeline
- Customer subscription status
- Code activation rate

### SQL Queries for Reports

#### Top Performing Resellers
```sql
SELECT * FROM reseller_performance 
ORDER BY total_earnings DESC 
LIMIT 10;
```

#### Active Subscriptions
```sql
SELECT * FROM active_code_subscriptions 
WHERE status = 'active';
```

#### Monthly Revenue
```sql
SELECT 
    DATE_FORMAT(used_at, '%Y-%m') as month,
    COUNT(*) as codes_used,
    SUM(sp.price) as revenue
FROM activation_codes ac
JOIN subscription_plans sp ON ac.plan_id = sp.id
WHERE ac.status = 'used'
GROUP BY month
ORDER BY month DESC;
```

---

## 🎯 Future Enhancements

### Planned Features
- [ ] Automated payout processing
- [ ] Email notifications for code activation
- [ ] SMS integration for code delivery
- [ ] Multi-currency support
- [ ] Reseller referral program
- [ ] Advanced analytics dashboard
- [ ] Mobile app for resellers
- [ ] API documentation (Swagger)
- [ ] Webhook support
- [ ] White-label reseller portals

---

## 📞 Support

For technical support or questions:
- Email: support@payme-gateway.com
- Documentation: https://docs.payme-gateway.com
- GitHub: https://github.com/payme-gateway

---

## 📄 License

Copyright © 2025 PayMe 2D Gateway. All rights reserved.

---

**Last Updated:** October 4, 2025
**Version:** 1.0.0
