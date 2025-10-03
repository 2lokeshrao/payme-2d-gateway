# PayMe 2D Gateway - Quick Start Guide

Get up and running with PayMe 2D Gateway in 5 minutes!

## 🚀 Quick Installation

### Option 1: Using XAMPP (Easiest - Recommended for Beginners)

1. **Download XAMPP**
   - Visit: https://www.apachefriends.org/
   - Download for your OS (Windows/Mac/Linux)
   - Install XAMPP

2. **Start Services**
   - Open XAMPP Control Panel
   - Start Apache
   - Start MySQL

3. **Clone/Download Project**
   ```bash
   # Navigate to htdocs folder
   cd C:\xampp\htdocs  # Windows
   # or
   cd /Applications/XAMPP/htdocs  # Mac
   
   # Clone repository
   git clone https://github.com/2lokeshrao/payme-2d-gateway.git
   ```

4. **Setup Database**
   - Open browser: http://localhost/phpmyadmin
   - Click "New" to create database
   - Database name: `payme_gateway`
   - Click "Import" tab
   - Choose file: `database.sql` from project folder
   - Click "Go"

5. **Configure Application**
   - Copy `config.example.php` to `config.php`
   - Edit `config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');  // Leave empty for XAMPP
     define('DB_NAME', 'payme_gateway');
     ```

6. **Access Application**
   - Open browser: http://localhost/payme-2d-gateway
   - Done! 🎉

### Option 2: Using PHP Built-in Server (For Developers)

1. **Prerequisites**
   - PHP 7.4+ installed
   - MySQL installed

2. **Clone Repository**
   ```bash
   git clone https://github.com/2lokeshrao/payme-2d-gateway.git
   cd payme-2d-gateway
   ```

3. **Setup Database**
   ```bash
   mysql -u root -p
   CREATE DATABASE payme_gateway;
   EXIT;
   mysql -u root -p payme_gateway < database.sql
   ```

4. **Configure**
   ```bash
   cp config.example.php config.php
   # Edit config.php with your database credentials
   ```

5. **Start Server**
   ```bash
   php -S localhost:8000
   ```

6. **Access Application**
   - Open browser: http://localhost:8000

## 🎯 First Steps

### 1. Create User Account
- Go to: http://localhost:8000/register.html
- Fill in your details
- Click "Create Account"

### 2. Login
- Go to: http://localhost:8000/login.html
- Enter your email and password
- Click "Sign In"

### 3. Make a Test Payment
- Click "Make Payment"
- Enter amount: 100.00
- Select currency: USD
- Fill in card details (use test card):
  - Card Number: 4111 1111 1111 1111
  - Expiry: 12/25
  - CVV: 123
  - Name: Test User
- Fill in billing address
- Click "Process Payment"

### 4. View Transactions
- Click "Transactions" to see your payment history

### 5. Access Admin Panel
- Go to: http://localhost:8000/admin/login.html
- Default credentials:
  - Username: `admin`
  - Password: `admin123`
- **⚠️ IMPORTANT: Change password immediately!**

## 📊 Admin Panel Features

Once logged in as admin, you can:

1. **Dashboard** - View system statistics
2. **Users** - Manage all registered users
3. **Transactions** - View all payment transactions

## 🧪 Test Cards

Use these test card numbers (simulation mode):

| Card Type | Number | CVV | Expiry |
|-----------|--------|-----|--------|
| Visa | 4111 1111 1111 1111 | 123 | Any future date |
| Mastercard | 5500 0000 0000 0004 | 123 | Any future date |
| Amex | 3400 0000 0000 009 | 1234 | Any future date |
| Discover | 6011 0000 0000 0004 | 123 | Any future date |

**Note**: The system simulates payment processing with a 90% success rate.

## 🔧 Troubleshooting

### Database Connection Error

**Problem**: "Database connection failed"

**Solution**:
1. Check if MySQL is running
2. Verify database credentials in `config.php`
3. Make sure database `payme_gateway` exists
4. Check if database was imported correctly

### Blank Page / White Screen

**Problem**: Nothing shows up

**Solution**:
1. Check PHP error logs
2. Make sure PHP version is 7.4+
3. Verify all files are uploaded correctly
4. Check file permissions

### Admin Login Not Working

**Problem**: Cannot login to admin panel

**Solution**:
1. Make sure database was imported
2. Check if `admin_users` table exists
3. Try default credentials: admin / admin123

### Payment Not Processing

**Problem**: Payment fails or doesn't work

**Solution**:
1. Check browser console for errors (F12)
2. Verify you're logged in
3. Check if all form fields are filled
4. Try with test card numbers above

## 📱 Mobile Testing

The application is fully responsive. Test on:
- Desktop browsers (Chrome, Firefox, Safari, Edge)
- Tablet devices
- Mobile phones

## 🔒 Security Checklist (Before Going Live)

- [ ] Change admin password
- [ ] Update database credentials
- [ ] Enable HTTPS/SSL
- [ ] Set secure file permissions
- [ ] Disable PHP error display
- [ ] Enable error logging
- [ ] Add CAPTCHA to forms
- [ ] Implement rate limiting
- [ ] Regular backups

## 📚 Next Steps

1. Read full [README.md](README.md) for detailed documentation
2. Check [DEPLOYMENT.md](DEPLOYMENT.md) for production deployment
3. Customize design and branding
4. Integrate with real payment gateway (Stripe, Razorpay, etc.)
5. Add email notifications
6. Implement additional security features

## 💡 Tips

- **Development**: Use XAMPP or PHP built-in server
- **Production**: Use proper web hosting with SSL
- **Database**: Regular backups are essential
- **Security**: Always use HTTPS in production
- **Testing**: Test thoroughly before going live

## 🆘 Need Help?

- **Documentation**: Check README.md and DEPLOYMENT.md
- **Issues**: https://github.com/2lokeshrao/payme-2d-gateway/issues
- **Email**: support@payme2d.com

## 🎉 You're All Set!

Your PayMe 2D Gateway is now ready to use. Start accepting payments!

---

**Made with ❤️ by 2lokeshrao**
