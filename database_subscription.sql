-- Subscription & Licensing System for PayMe 2D Gateway
-- Merchants must purchase software before using self-payment features

-- Subscription Plans Table
CREATE TABLE IF NOT EXISTS subscription_plans (
    id INT PRIMARY KEY AUTO_INCREMENT,
    plan_name VARCHAR(100) NOT NULL,
    plan_type ENUM('monthly', 'quarterly', 'yearly') NOT NULL,
    price_usd DECIMAL(10,2) NOT NULL,
    features JSON,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default plan
INSERT INTO subscription_plans (plan_name, plan_type, price_usd, features) VALUES
('Professional Monthly', 'monthly', 60.00, '{"self_bank_account": true, "self_upi": true, "self_crypto": true, "unlimited_transactions": true, "api_access": true, "priority_support": true}'),
('Professional Quarterly', 'quarterly', 150.00, '{"self_bank_account": true, "self_upi": true, "self_crypto": true, "unlimited_transactions": true, "api_access": true, "priority_support": true, "discount": "17%"}'),
('Professional Yearly', 'yearly', 500.00, '{"self_bank_account": true, "self_upi": true, "self_crypto": true, "unlimited_transactions": true, "api_access": true, "priority_support": true, "discount": "30%"}');

-- User Subscriptions Table
CREATE TABLE IF NOT EXISTS user_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan_id INT NOT NULL,
    subscription_status ENUM('active', 'expired', 'cancelled', 'pending') DEFAULT 'pending',
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP NULL,
    auto_renew BOOLEAN DEFAULT TRUE,
    payment_method VARCHAR(50),
    last_payment_date TIMESTAMP NULL,
    next_billing_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

-- Subscription Payments Table
CREATE TABLE IF NOT EXISTS subscription_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    subscription_id INT NOT NULL,
    user_id INT NOT NULL,
    amount_usd DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Merchant Self Payment Settings Table
CREATE TABLE IF NOT EXISTS merchant_self_payment_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    
    -- Bank Account Details
    bank_enabled BOOLEAN DEFAULT FALSE,
    bank_name VARCHAR(100),
    account_holder_name VARCHAR(100),
    account_number VARCHAR(50),
    ifsc_code VARCHAR(20),
    account_type ENUM('savings', 'current'),
    
    -- UPI Details
    upi_enabled BOOLEAN DEFAULT FALSE,
    upi_id VARCHAR(100),
    upi_qr_code TEXT,
    
    -- Crypto Wallet Addresses
    crypto_enabled BOOLEAN DEFAULT FALSE,
    btc_wallet_address VARCHAR(100),
    eth_wallet_address VARCHAR(100),
    usdt_wallet_address VARCHAR(100),
    usdt_network ENUM('ERC-20', 'TRC-20', 'BEP-20'),
    bnb_wallet_address VARCHAR(100),
    
    -- Settings
    auto_settlement BOOLEAN DEFAULT TRUE,
    settlement_frequency ENUM('instant', 'daily', 'weekly') DEFAULT 'instant',
    
    -- Verification Status
    bank_verified BOOLEAN DEFAULT FALSE,
    upi_verified BOOLEAN DEFAULT FALSE,
    crypto_verified BOOLEAN DEFAULT FALSE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- License Keys Table
CREATE TABLE IF NOT EXISTS license_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    license_key VARCHAR(100) UNIQUE NOT NULL,
    plan_id INT NOT NULL,
    status ENUM('active', 'expired', 'revoked') DEFAULT 'active',
    issued_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date TIMESTAMP NOT NULL,
    last_verified TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
);

-- Feature Access Log
CREATE TABLE IF NOT EXISTS feature_access_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    feature_name VARCHAR(100) NOT NULL,
    access_granted BOOLEAN DEFAULT FALSE,
    access_reason VARCHAR(255),
    accessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

