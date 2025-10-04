-- PayMe 2D Gateway - Enhanced Database Schema
-- Complete Payment Gateway with Multiple Payment Methods

-- Users Table (Enhanced)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    status ENUM('pending', 'active', 'suspended', 'rejected') DEFAULT 'pending',
    kyc_status ENUM('pending', 'submitted', 'verified', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Business Details Table
CREATE TABLE IF NOT EXISTS business_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50) NOT NULL,
    business_type ENUM('individual', 'partnership', 'pvt_ltd', 'public_ltd', 'llp', 'proprietorship') NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    trade_name VARCHAR(255),
    business_category VARCHAR(100),
    website_url VARCHAR(255),
    gst_number VARCHAR(20),
    pan_number VARCHAR(20) NOT NULL,
    business_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Address Details Table
CREATE TABLE IF NOT EXISTS addresses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50) NOT NULL,
    address_type ENUM('registered', 'business') NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    pincode VARCHAR(10) NOT NULL,
    country VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bank Details Table
CREATE TABLE IF NOT EXISTS bank_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50) NOT NULL,
    account_holder_name VARCHAR(255) NOT NULL,
    account_number VARCHAR(50) NOT NULL,
    ifsc_code VARCHAR(20) NOT NULL,
    bank_name VARCHAR(255) NOT NULL,
    branch_name VARCHAR(255),
    account_type ENUM('savings', 'current') NOT NULL,
    swift_code VARCHAR(20),
    upi_id VARCHAR(100),
    upi_provider VARCHAR(50),
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- KYC Documents Table
CREATE TABLE IF NOT EXISTS kyc_documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50) NOT NULL,
    document_type ENUM('identity_proof', 'address_proof', 'pan_card', 'business_doc', 'bank_proof') NOT NULL,
    document_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT,
    verification_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    rejection_reason TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (verification_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Merchant API Keys Table
CREATE TABLE IF NOT EXISTS api_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50) NOT NULL,
    api_key VARCHAR(100) UNIQUE NOT NULL,
    api_secret VARCHAR(100) NOT NULL,
    key_type ENUM('test', 'live') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    permissions JSON,
    last_used TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_api_key (api_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Transactions Table (Enhanced with all payment methods)
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    transaction_id VARCHAR(100) UNIQUE NOT NULL,
    merchant_id VARCHAR(50) NOT NULL,
    customer_email VARCHAR(255),
    customer_phone VARCHAR(20),
    amount DECIMAL(15, 2) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'INR',
    payment_method ENUM('card', 'upi', 'netbanking', 'wallet', 'crypto', 'emi') NOT NULL,
    payment_provider VARCHAR(50),
    card_type VARCHAR(20),
    card_last4 VARCHAR(4),
    card_brand VARCHAR(20),
    upi_id VARCHAR(100),
    wallet_type VARCHAR(50),
    crypto_currency VARCHAR(20),
    crypto_address VARCHAR(255),
    status ENUM('pending', 'processing', 'success', 'failed', 'refunded', 'cancelled') DEFAULT 'pending',
    description TEXT,
    customer_name VARCHAR(255),
    customer_address TEXT,
    ip_address VARCHAR(50),
    user_agent TEXT,
    callback_url VARCHAR(500),
    redirect_url VARCHAR(500),
    webhook_url VARCHAR(500),
    metadata JSON,
    gateway_response JSON,
    error_message TEXT,
    refund_amount DECIMAL(15, 2) DEFAULT 0,
    refund_reason TEXT,
    settlement_status ENUM('pending', 'processing', 'settled', 'failed') DEFAULT 'pending',
    settlement_date DATE NULL,
    fee_amount DECIMAL(10, 2) DEFAULT 0,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    net_amount DECIMAL(15, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (merchant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_merchant_id (merchant_id),
    INDEX idx_status (status),
    INDEX idx_payment_method (payment_method),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Refunds Table
CREATE TABLE IF NOT EXISTS refunds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    refund_id VARCHAR(100) UNIQUE NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    merchant_id VARCHAR(50) NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    reason TEXT,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(transaction_id) ON DELETE CASCADE,
    FOREIGN KEY (merchant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_refund_id (refund_id),
    INDEX idx_transaction_id (transaction_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Settlements Table
CREATE TABLE IF NOT EXISTS settlements (
    id INT PRIMARY KEY AUTO_INCREMENT,
    settlement_id VARCHAR(100) UNIQUE NOT NULL,
    merchant_id VARCHAR(50) NOT NULL,
    total_amount DECIMAL(15, 2) NOT NULL,
    fee_amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) NOT NULL,
    net_amount DECIMAL(15, 2) NOT NULL,
    transaction_count INT NOT NULL,
    settlement_date DATE NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    utr_number VARCHAR(50),
    bank_reference VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (merchant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_settlement_id (settlement_id),
    INDEX idx_merchant_id (merchant_id),
    INDEX idx_settlement_date (settlement_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Webhooks Table
CREATE TABLE IF NOT EXISTS webhooks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    webhook_id VARCHAR(100) UNIQUE NOT NULL,
    merchant_id VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    payload JSON NOT NULL,
    url VARCHAR(500) NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    attempts INT DEFAULT 0,
    last_attempt TIMESTAMP NULL,
    response_code INT,
    response_body TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (merchant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_webhook_id (webhook_id),
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Payment Methods Configuration
CREATE TABLE IF NOT EXISTS payment_methods (
    id INT PRIMARY KEY AUTO_INCREMENT,
    merchant_id VARCHAR(50) NOT NULL,
    method_type ENUM('card', 'upi', 'netbanking', 'wallet', 'crypto', 'emi') NOT NULL,
    is_enabled BOOLEAN DEFAULT TRUE,
    configuration JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (merchant_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_merchant_id (merchant_id),
    UNIQUE KEY unique_merchant_method (merchant_id, method_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Supported Banks for Net Banking
CREATE TABLE IF NOT EXISTS supported_banks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bank_code VARCHAR(20) UNIQUE NOT NULL,
    bank_name VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    logo_url VARCHAR(500),
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Supported Wallets
CREATE TABLE IF NOT EXISTS supported_wallets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    wallet_code VARCHAR(20) UNIQUE NOT NULL,
    wallet_name VARCHAR(100) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    logo_url VARCHAR(500),
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Supported Cryptocurrencies
CREATE TABLE IF NOT EXISTS supported_crypto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    crypto_code VARCHAR(20) UNIQUE NOT NULL,
    crypto_name VARCHAR(100) NOT NULL,
    network VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    logo_url VARCHAR(500),
    display_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    role ENUM('super_admin', 'admin', 'support') DEFAULT 'admin',
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Activity Logs
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(50),
    user_type ENUM('merchant', 'admin') NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(50),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Admin
INSERT INTO admin_users (username, password, email, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@payme2d.com', 'System Administrator', 'super_admin');
-- Default password: admin123

-- Insert Supported Banks
INSERT INTO supported_banks (bank_code, bank_name, display_order) VALUES
('SBI', 'State Bank of India', 1),
('HDFC', 'HDFC Bank', 2),
('ICICI', 'ICICI Bank', 3),
('AXIS', 'Axis Bank', 4),
('PNB', 'Punjab National Bank', 5),
('BOB', 'Bank of Baroda', 6),
('KOTAK', 'Kotak Mahindra Bank', 7),
('YES', 'Yes Bank', 8),
('IDBI', 'IDBI Bank', 9),
('CANARA', 'Canara Bank', 10);

-- Insert Supported Wallets
INSERT INTO supported_wallets (wallet_code, wallet_name, display_order) VALUES
('PAYTM', 'Paytm', 1),
('PHONEPE', 'PhonePe', 2),
('GPAY', 'Google Pay', 3),
('AMAZONPAY', 'Amazon Pay', 4),
('MOBIKWIK', 'Mobikwik', 5),
('FREECHARGE', 'Freecharge', 6),
('AIRTEL', 'Airtel Money', 7),
('JIO', 'Jio Money', 8);

-- Insert Supported Cryptocurrencies
INSERT INTO supported_crypto (crypto_code, crypto_name, network, display_order) VALUES
('BTC', 'Bitcoin', 'Bitcoin', 1),
('ETH', 'Ethereum', 'Ethereum', 2),
('USDT', 'Tether', 'ERC20', 3),
('BNB', 'Binance Coin', 'BSC', 4),
('USDC', 'USD Coin', 'ERC20', 5),
('XRP', 'Ripple', 'Ripple', 6),
('ADA', 'Cardano', 'Cardano', 7),
('SOL', 'Solana', 'Solana', 8),
('DOGE', 'Dogecoin', 'Dogecoin', 9),
('TRX', 'Tron', 'Tron', 10);
