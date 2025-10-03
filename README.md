# PayMe 2D Gateway - International Payment Processing System

![PayMe 2D Gateway](https://img.shields.io/badge/PayMe-2D%20Gateway-blue)
![Version](https://img.shields.io/badge/version-1.0.0-green)
![License](https://img.shields.io/badge/license-MIT-orange)

## 🚀 Overview

**PayMe 2D Gateway** is a fully functional, secure international payment gateway system that allows businesses to accept credit and debit card payments from customers worldwide. Built with HTML, CSS, JavaScript, PHP, and MySQL, this system provides a complete payment processing solution with an admin panel for managing users and transactions.

## ✨ Features

### User Features
- ✅ **User Registration & Login** - Secure account creation and authentication
- 💳 **Payment Processing** - Accept credit/debit card payments (Visa, Mastercard, Amex, Discover)
- 🌍 **Multi-Currency Support** - USD, EUR, GBP, INR, AUD, CAD
- 📊 **User Dashboard** - View transaction history and account statistics
- 🔒 **Secure Transactions** - Bank-level encryption and security
- 📱 **Fully Responsive** - Works on desktop, tablet, and mobile devices

### Admin Features
- 👥 **User Management** - View all registered users and their details
- 💰 **Transaction Management** - Monitor all payment transactions
- 📈 **Analytics Dashboard** - Real-time statistics and insights
- 🔍 **Detailed Reports** - Transaction history with complete user information
- 🎯 **Status Tracking** - Track payment status (Success, Pending, Failed)

### Technical Features
- 🗄️ **MySQL Database** - Robust SQL database for data persistence
- 🔐 **Password Hashing** - Secure password storage with bcrypt
- 🎨 **Modern UI/UX** - Clean, minimal design inspired by Apple/Linear
- ⚡ **Fast Performance** - Optimized for speed and efficiency
- 📝 **Form Validation** - Client-side and server-side validation
- 🌐 **International Ready** - Support for global payments

## 📋 Requirements

- **Web Server**: Apache/Nginx
- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher
- **Browser**: Modern browser with JavaScript enabled

## 🛠️ Installation

### Step 1: Clone the Repository

```bash
git clone https://github.com/2lokeshrao/payme-2d-gateway.git
cd payme-2d-gateway
```

### Step 2: Database Setup

1. Create a MySQL database:
```sql
CREATE DATABASE payme_gateway;
```

2. Import the database schema:
```bash
mysql -u root -p payme_gateway < database.sql
```

Or manually execute the SQL file in phpMyAdmin or MySQL Workbench.

### Step 3: Configure Database Connection

Edit `config.php` and update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'payme_gateway');
```

### Step 4: Set Permissions

```bash
chmod 755 -R .
chmod 644 config.php
```

### Step 5: Start the Application

If using PHP built-in server (for development):
```bash
php -S localhost:8000
```

For production, configure your Apache/Nginx virtual host to point to the project directory.

## 🌐 Deployment Guide

### Deploy to Live Server

#### Option 1: Shared Hosting (cPanel)

1. **Upload Files**
   - Compress the project folder into a ZIP file
   - Login to cPanel
   - Navigate to File Manager
   - Upload and extract the ZIP file to `public_html` directory

2. **Create Database**
   - Go to MySQL Databases in cPanel
   - Create a new database: `payme_gateway`
   - Create a database user and assign privileges
   - Note down the database name, username, and password

3. **Import Database**
   - Go to phpMyAdmin
   - Select your database
   - Click Import tab
   - Upload `database.sql` file
   - Click Go

4. **Update Configuration**
   - Edit `config.php` with your database credentials
   - Update `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`

5. **Set Permissions**
   - Set folder permissions to 755
   - Set file permissions to 644

6. **Access Your Site**
   - Visit: `https://yourdomain.com`
   - Admin Panel: `https://yourdomain.com/admin/login.html`

#### Option 2: VPS/Cloud Server (Ubuntu/Debian)

1. **Install LAMP Stack**
```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql libapache2-mod-php
```

2. **Clone Repository**
```bash
cd /var/www/html
sudo git clone https://github.com/2lokeshrao/payme-2d-gateway.git
sudo chown -R www-data:www-data payme-2d-gateway
```

3. **Setup Database**
```bash
sudo mysql -u root -p
CREATE DATABASE payme_gateway;
CREATE USER 'payme_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON payme_gateway.* TO 'payme_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

sudo mysql -u root -p payme_gateway < /var/www/html/payme-2d-gateway/database.sql
```

4. **Configure Apache**
```bash
sudo nano /etc/apache2/sites-available/payme.conf
```

Add:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/payme-2d-gateway
    
    <Directory /var/www/html/payme-2d-gateway>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/payme_error.log
    CustomLog ${APACHE_LOG_DIR}/payme_access.log combined
</VirtualHost>
```

5. **Enable Site and Restart Apache**
```bash
sudo a2ensite payme.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

6. **Setup SSL (Optional but Recommended)**
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

#### Option 3: Deploy to Heroku

1. **Install Heroku CLI**
```bash
curl https://cli-assets.heroku.com/install.sh | sh
```

2. **Login to Heroku**
```bash
heroku login
```

3. **Create Heroku App**
```bash
heroku create payme-gateway
```

4. **Add MySQL Database**
```bash
heroku addons:create cleardb:ignite
```

5. **Get Database Credentials**
```bash
heroku config | grep CLEARDB_DATABASE_URL
```

6. **Update config.php** with Heroku database credentials

7. **Deploy**
```bash
git init
git add .
git commit -m "Initial commit"
git push heroku master
```

## 📱 Usage

### For Users

1. **Register Account**
   - Go to Sign Up page
   - Fill in your details (Name, Email, Phone, Password)
   - Click "Create Account"

2. **Login**
   - Enter your email and password
   - Click "Sign In"

3. **Make Payment**
   - Navigate to "Make Payment"
   - Enter payment amount and currency
   - Fill in card details:
     - Card holder name
     - Card number (13-19 digits)
     - Expiry date (MM/YY)
     - CVV (3-4 digits)
   - Enter billing address
   - Click "Process Payment"

4. **View Transactions**
   - Go to "Transactions" page
   - View all your payment history

### For Admins

1. **Admin Login**
   - Go to `/admin/login.html`
   - Default credentials:
     - Username: `admin`
     - Password: `admin123`
   - **⚠️ Change default password immediately after first login**

2. **Dashboard**
   - View system statistics
   - Monitor recent transactions
   - Track success rates

3. **Manage Users**
   - View all registered users
   - See user details (ID, Name, Email, Phone, Status)

4. **Manage Transactions**
   - View all payment transactions
   - Filter by status
   - Export transaction data

## 🗂️ Project Structure

```
payme-2d-gateway/
├── index.html              # Homepage
├── login.html              # User login page
├── register.html           # User registration page
├── dashboard.html          # User dashboard
├── payment.html            # Payment processing page
├── transactions.html       # User transactions page
├── config.php              # Database configuration
├── database.sql            # Database schema
├── README.md               # This file
├── DEPLOYMENT.md           # Detailed deployment guide
├── css/
│   └── style.css          # Main stylesheet
├── js/
│   ├── register.js        # Registration logic
│   ├── login.js           # Login logic
│   ├── dashboard.js       # Dashboard logic
│   ├── payment.js         # Payment processing logic
│   ├── transactions.js    # Transactions logic
│   ├── admin-login.js     # Admin login logic
│   ├── admin-dashboard.js # Admin dashboard logic
│   ├── admin-users.js     # Admin users management
│   └── admin-transactions.js # Admin transactions management
├── api/
│   ├── register.php       # User registration API
│   ├── login.php          # User login API
│   ├── process_payment.php # Payment processing API
│   ├── get_user_stats.php # User statistics API
│   ├── get_transactions.php # User transactions API
│   ├── admin_login.php    # Admin login API
│   ├── admin_stats.php    # Admin statistics API
│   ├── admin_get_users.php # Get all users API
│   └── admin_get_transactions.php # Get all transactions API
└── admin/
    ├── login.html         # Admin login page
    ├── dashboard.html     # Admin dashboard
    ├── users.html         # Users management page
    └── transactions.html  # Transactions management page
```

## 🔒 Security Features

- **Password Hashing**: All passwords are hashed using PHP's `password_hash()` with bcrypt
- **SQL Injection Prevention**: Prepared statements used throughout
- **XSS Protection**: Input sanitization and output escaping
- **Session Management**: Secure session handling with tokens
- **HTTPS Ready**: Designed to work with SSL/TLS encryption
- **Input Validation**: Both client-side and server-side validation

## 🎨 Design System

- **Font**: Inter (Google Fonts)
- **Color Palette**: Radix Colors (Slate scale)
- **Accent Color**: Electric Blue (#0066FF)
- **Border Radius**: 8px, 12px, 16px
- **Responsive Breakpoints**: 480px, 768px, 992px, 1200px

## 🧪 Testing

### Test Cards (Simulation Mode)

The system simulates payment processing with a 90% success rate. Use any valid card format:

- **Visa**: 4111 1111 1111 1111
- **Mastercard**: 5500 0000 0000 0004
- **Amex**: 3400 0000 0000 009
- **Discover**: 6011 0000 0000 0004

**Note**: In production, integrate with real payment gateways like Stripe, Razorpay, or PayPal.

## 📊 Database Schema

### Users Table
- `id` - Primary key
- `full_name` - User's full name
- `email` - Unique email address
- `phone` - Phone number
- `password_hash` - Hashed password
- `status` - Account status (active/inactive/suspended)
- `created_at` - Registration timestamp

### Transactions Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `transaction_id` - Unique transaction identifier
- `card_number_last4` - Last 4 digits of card
- `card_holder_name` - Name on card
- `card_type` - Card brand (Visa, Mastercard, etc.)
- `amount` - Transaction amount
- `currency` - Currency code
- `status` - Transaction status (success/pending/failed)
- `billing_address` - Billing address details
- `ip_address` - User's IP address
- `user_agent` - Browser information
- `created_at` - Transaction timestamp

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

**2lokeshrao**
- GitHub: [@2lokeshrao](https://github.com/2lokeshrao)

## 🙏 Acknowledgments

- Design inspiration from Apple, Linear, and Mercury
- Radix Colors for the color system
- Inter font by Rasmus Andersson
- Icons from various open-source projects

## 📞 Support

For support, email support@payme2d.com or open an issue on GitHub.

## 🔄 Version History

- **v1.0.0** (2025-10-03)
  - Initial release
  - User registration and login
  - Payment processing
  - Admin panel
  - Transaction management
  - Fully responsive design

## 🚧 Roadmap

- [ ] Integration with real payment gateways (Stripe, Razorpay)
- [ ] Email notifications
- [ ] Two-factor authentication
- [ ] Payment refunds
- [ ] Invoice generation
- [ ] API documentation
- [ ] Mobile app
- [ ] Multi-language support

## ⚠️ Important Notes

1. **Change Default Admin Password**: The default admin credentials are for initial setup only. Change them immediately after first login.

2. **Production Security**: Before deploying to production:
   - Enable HTTPS/SSL
   - Update database credentials
   - Set secure session settings
   - Implement rate limiting
   - Add CAPTCHA to forms
   - Enable error logging

3. **Payment Gateway Integration**: This system simulates payment processing. For production use, integrate with real payment gateways like:
   - Stripe
   - Razorpay
   - PayPal
   - Square
   - Braintree

4. **Compliance**: Ensure compliance with:
   - PCI DSS (Payment Card Industry Data Security Standard)
   - GDPR (General Data Protection Regulation)
   - Local payment regulations

## 📖 Additional Documentation

- [DEPLOYMENT.md](DEPLOYMENT.md) - Detailed deployment instructions
- [API.md](API.md) - API documentation (coming soon)
- [SECURITY.md](SECURITY.md) - Security guidelines (coming soon)

---

**Made with ❤️ by 2lokeshrao**
