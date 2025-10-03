# PayMe 2D Gateway - Deployment Guide

This guide provides step-by-step instructions for deploying PayMe 2D Gateway to various hosting platforms.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Local Development Setup](#local-development-setup)
3. [Shared Hosting Deployment](#shared-hosting-deployment)
4. [VPS/Cloud Server Deployment](#vpscloud-server-deployment)
5. [Docker Deployment](#docker-deployment)
6. [Post-Deployment Checklist](#post-deployment-checklist)
7. [Troubleshooting](#troubleshooting)

## Prerequisites

Before deploying, ensure you have:

- ✅ Web server (Apache/Nginx)
- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7 or higher
- ✅ Git (for version control)
- ✅ FTP/SSH access to your server
- ✅ Domain name (optional but recommended)
- ✅ SSL certificate (recommended for production)

## Local Development Setup

### Using XAMPP (Windows/Mac/Linux)

1. **Download and Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install and start Apache and MySQL

2. **Clone the Repository**
   ```bash
   cd C:\xampp\htdocs  # Windows
   # or
   cd /Applications/XAMPP/htdocs  # Mac
   # or
   cd /opt/lampp/htdocs  # Linux
   
   git clone https://github.com/2lokeshrao/payme-2d-gateway.git
   ```

3. **Create Database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Create database: `payme_gateway`
   - Import `database.sql`

4. **Configure Database**
   - Edit `config.php`
   - Set credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_NAME', 'payme_gateway');
     ```

5. **Access Application**
   - Visit: http://localhost/payme-2d-gateway

### Using PHP Built-in Server

1. **Clone Repository**
   ```bash
   git clone https://github.com/2lokeshrao/payme-2d-gateway.git
   cd payme-2d-gateway
   ```

2. **Setup Database**
   ```bash
   mysql -u root -p
   CREATE DATABASE payme_gateway;
   EXIT;
   mysql -u root -p payme_gateway < database.sql
   ```

3. **Start Server**
   ```bash
   php -S localhost:8000
   ```

4. **Access Application**
   - Visit: http://localhost:8000

## Shared Hosting Deployment

### cPanel Hosting

#### Step 1: Prepare Files

1. **Download/Clone Repository**
   ```bash
   git clone https://github.com/2lokeshrao/payme-2d-gateway.git
   cd payme-2d-gateway
   ```

2. **Create ZIP Archive**
   ```bash
   zip -r payme-gateway.zip .
   ```

#### Step 2: Upload Files

1. **Login to cPanel**
   - Access your hosting cPanel

2. **Navigate to File Manager**
   - Go to Files → File Manager
   - Navigate to `public_html` directory

3. **Upload ZIP File**
   - Click Upload
   - Select `payme-gateway.zip`
   - Wait for upload to complete

4. **Extract Files**
   - Right-click on ZIP file
   - Select Extract
   - Extract to `public_html` or subdirectory

#### Step 3: Create Database

1. **Go to MySQL Databases**
   - In cPanel, find MySQL Databases

2. **Create New Database**
   - Database name: `username_payme`
   - Click "Create Database"

3. **Create Database User**
   - Username: `username_payme_user`
   - Password: Generate strong password
   - Click "Create User"

4. **Add User to Database**
   - Select user and database
   - Grant ALL PRIVILEGES
   - Click "Add"

#### Step 4: Import Database

1. **Open phpMyAdmin**
   - In cPanel, click phpMyAdmin

2. **Select Database**
   - Click on your database name

3. **Import SQL File**
   - Click Import tab
   - Choose `database.sql`
   - Click Go

#### Step 5: Configure Application

1. **Edit config.php**
   - In File Manager, right-click `config.php`
   - Click Edit
   - Update credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'username_payme_user');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'username_payme');
     ```
   - Save changes

2. **Set Permissions**
   - Select all folders: Right-click → Permissions → 755
   - Select all files: Right-click → Permissions → 644

#### Step 6: Test Application

1. **Visit Your Domain**
   - https://yourdomain.com
   - Or: https://yourdomain.com/payme-2d-gateway

2. **Test Registration**
   - Create a test account
   - Login and test payment

3. **Test Admin Panel**
   - Visit: https://yourdomain.com/admin/login.html
   - Login with: admin / admin123
   - **Change password immediately!**

## VPS/Cloud Server Deployment

### Ubuntu/Debian Server

#### Step 1: Connect to Server

```bash
ssh root@your-server-ip
```

#### Step 2: Update System

```bash
sudo apt update
sudo apt upgrade -y
```

#### Step 3: Install LAMP Stack

```bash
# Install Apache
sudo apt install apache2 -y

# Install MySQL
sudo apt install mysql-server -y

# Install PHP
sudo apt install php libapache2-mod-php php-mysql php-curl php-json php-mbstring -y

# Enable Apache modules
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Step 4: Secure MySQL

```bash
sudo mysql_secure_installation
```

Follow prompts:
- Set root password
- Remove anonymous users: Yes
- Disallow root login remotely: Yes
- Remove test database: Yes
- Reload privilege tables: Yes

#### Step 5: Clone Repository

```bash
cd /var/www/html
sudo git clone https://github.com/2lokeshrao/payme-2d-gateway.git
sudo chown -R www-data:www-data payme-2d-gateway
sudo chmod -R 755 payme-2d-gateway
```

#### Step 6: Setup Database

```bash
sudo mysql -u root -p
```

In MySQL:
```sql
CREATE DATABASE payme_gateway;
CREATE USER 'payme_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON payme_gateway.* TO 'payme_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Import database:
```bash
sudo mysql -u root -p payme_gateway < /var/www/html/payme-2d-gateway/database.sql
```

#### Step 7: Configure Application

```bash
sudo nano /var/www/html/payme-2d-gateway/config.php
```

Update:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'payme_user');
define('DB_PASS', 'strong_password_here');
define('DB_NAME', 'payme_gateway');
```

#### Step 8: Configure Apache Virtual Host

```bash
sudo nano /etc/apache2/sites-available/payme.conf
```

Add:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
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

Enable site:
```bash
sudo a2ensite payme.conf
sudo systemctl reload apache2
```

#### Step 9: Install SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache -y

# Get SSL certificate
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Follow prompts
# Choose to redirect HTTP to HTTPS
```

#### Step 10: Configure Firewall

```bash
sudo ufw allow 'Apache Full'
sudo ufw enable
```

#### Step 11: Test Application

Visit: https://yourdomain.com

## Docker Deployment

### Step 1: Create Dockerfile

```bash
cd payme-2d-gateway
nano Dockerfile
```

Add:
```dockerfile
FROM php:7.4-apache

# Install MySQL extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

EXPOSE 80
```

### Step 2: Create docker-compose.yml

```yaml
version: '3.8'

services:
  web:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    depends_on:
      - db
    environment:
      - DB_HOST=db
      - DB_USER=payme_user
      - DB_PASS=payme_password
      - DB_NAME=payme_gateway

  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_DATABASE: payme_gateway
      MYSQL_USER: payme_user
      MYSQL_PASSWORD: payme_password
    volumes:
      - db_data:/var/lib/mysql
      - ./database.sql:/docker-entrypoint-initdb.d/database.sql

volumes:
  db_data:
```

### Step 3: Build and Run

```bash
docker-compose up -d
```

### Step 4: Access Application

Visit: http://localhost:8080

## Post-Deployment Checklist

### Security

- [ ] Change default admin password
- [ ] Enable HTTPS/SSL
- [ ] Update database credentials
- [ ] Set secure file permissions (755 for folders, 644 for files)
- [ ] Disable directory listing
- [ ] Enable PHP error logging (disable display_errors in production)
- [ ] Implement rate limiting
- [ ] Add CAPTCHA to forms
- [ ] Configure firewall rules
- [ ] Regular security updates

### Configuration

- [ ] Update config.php with production settings
- [ ] Set correct timezone in PHP
- [ ] Configure email settings (if applicable)
- [ ] Set up backup system
- [ ] Configure monitoring tools
- [ ] Set up error logging
- [ ] Test all functionality

### Testing

- [ ] Test user registration
- [ ] Test user login
- [ ] Test payment processing
- [ ] Test admin login
- [ ] Test admin dashboard
- [ ] Test on mobile devices
- [ ] Test on different browsers
- [ ] Load testing

### Optimization

- [ ] Enable PHP OPcache
- [ ] Enable Gzip compression
- [ ] Optimize images
- [ ] Minify CSS/JS (if needed)
- [ ] Set up CDN (optional)
- [ ] Configure caching headers
- [ ] Database indexing

## Troubleshooting

### Database Connection Error

**Problem**: "Database connection failed"

**Solution**:
1. Check database credentials in `config.php`
2. Verify MySQL service is running:
   ```bash
   sudo systemctl status mysql
   ```
3. Test database connection:
   ```bash
   mysql -u username -p database_name
   ```

### 500 Internal Server Error

**Problem**: White screen or 500 error

**Solution**:
1. Check Apache error logs:
   ```bash
   sudo tail -f /var/log/apache2/error.log
   ```
2. Check PHP error logs
3. Verify file permissions
4. Check .htaccess file

### Permission Denied Errors

**Problem**: Cannot write to files/folders

**Solution**:
```bash
sudo chown -R www-data:www-data /var/www/html/payme-2d-gateway
sudo chmod -R 755 /var/www/html/payme-2d-gateway
```

### Admin Login Not Working

**Problem**: Cannot login to admin panel

**Solution**:
1. Verify database was imported correctly
2. Check if admin_users table exists
3. Reset admin password manually in database:
   ```sql
   UPDATE admin_users SET password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' WHERE username = 'admin';
   ```
   (Password: admin123)

### SSL Certificate Issues

**Problem**: SSL not working

**Solution**:
1. Verify certificate installation:
   ```bash
   sudo certbot certificates
   ```
2. Renew certificate:
   ```bash
   sudo certbot renew
   ```
3. Check Apache SSL configuration

## Maintenance

### Regular Backups

**Database Backup**:
```bash
mysqldump -u username -p payme_gateway > backup_$(date +%Y%m%d).sql
```

**File Backup**:
```bash
tar -czf payme_backup_$(date +%Y%m%d).tar.gz /var/www/html/payme-2d-gateway
```

### Updates

**Update Application**:
```bash
cd /var/www/html/payme-2d-gateway
git pull origin main
```

**Update System**:
```bash
sudo apt update
sudo apt upgrade
```

### Monitoring

**Check Logs**:
```bash
# Apache access log
sudo tail -f /var/log/apache2/access.log

# Apache error log
sudo tail -f /var/log/apache2/error.log

# MySQL log
sudo tail -f /var/log/mysql/error.log
```

## Support

For deployment issues:
- GitHub Issues: https://github.com/2lokeshrao/payme-2d-gateway/issues
- Email: support@payme2d.com

---

**Last Updated**: October 3, 2025
