-- PayMe 2D Gateway - PostgreSQL Database Schema
-- Complete Database Schema with Foreign Keys and Sample Data

-- Drop existing database if exists (run separately)
-- DROP DATABASE IF EXISTS payme_gateway;
-- CREATE DATABASE payme_gateway;

-- Connect to database
-- \c payme_gateway;

-- ============================================
-- 1. USERS TABLE (Admin, Client, Merchant, Reseller)
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    user_type VARCHAR(20) NOT NULL CHECK (user_type IN ('admin', 'client', 'merchant', 'reseller')),
    email VARCHAR(255) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('active', 'inactive', 'suspended', 'pending')),
    email_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_type ON users(user_type);
CREATE INDEX idx_users_status ON users(status);

-- ============================================
-- 2. MERCHANTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS merchants (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    merchant_id VARCHAR(50) UNIQUE NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    business_type VARCHAR(100),
    business_category VARCHAR(100),
    website_url VARCHAR(255),
    business_address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100) DEFAULT 'India',
    pincode VARCHAR(20),
    gstin VARCHAR(50),
    pan_number VARCHAR(20),
    kyc_status VARCHAR(20) DEFAULT 'pending' CHECK (kyc_status IN ('pending', 'submitted', 'verified', 'rejected')),
    kyc_verified_at TIMESTAMP NULL,
    api_key VARCHAR(255) UNIQUE,
    api_secret VARCHAR(255),
    webhook_url VARCHAR(255),
    settlement_account_number VARCHAR(50),
    settlement_ifsc VARCHAR(20),
    settlement_bank_name VARCHAR(100),
    commission_rate DECIMAL(5,2) DEFAULT 2.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_merchants_merchant_id ON merchants(merchant_id);
CREATE INDEX idx_merchants_kyc_status ON merchants(kyc_status);

-- ============================================
-- 3. KYC DOCUMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS kyc_documents (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER NOT NULL REFERENCES merchants(id) ON DELETE CASCADE,
    document_type VARCHAR(50) NOT NULL CHECK (document_type IN ('pan_card', 'aadhar_card', 'gst_certificate', 'bank_statement', 'business_proof', 'other')),
    document_number VARCHAR(100),
    document_file_path VARCHAR(255) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    verification_status VARCHAR(20) DEFAULT 'pending' CHECK (verification_status IN ('pending', 'verified', 'rejected')),
    verified_by INTEGER REFERENCES users(id) ON DELETE SET NULL,
    verified_at TIMESTAMP NULL,
    rejection_reason TEXT
);

CREATE INDEX idx_kyc_merchant ON kyc_documents(merchant_id, verification_status);

-- ============================================
-- 4. TRANSACTIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS transactions (
    id SERIAL PRIMARY KEY,
    transaction_id VARCHAR(100) UNIQUE NOT NULL,
    merchant_id INTEGER NOT NULL REFERENCES merchants(id) ON DELETE CASCADE,
    payment_method VARCHAR(50) NOT NULL CHECK (payment_method IN ('upi', 'crypto', 'bank_transfer', 'card', 'wallet')),
    amount DECIMAL(15,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'INR',
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    customer_phone VARCHAR(20),
    customer_upi_id VARCHAR(255),
    payment_status VARCHAR(20) DEFAULT 'pending' CHECK (payment_status IN ('pending', 'processing', 'success', 'failed', 'refunded')),
    payment_gateway VARCHAR(50),
    gateway_transaction_id VARCHAR(255),
    payment_screenshot VARCHAR(255),
    utr_number VARCHAR(100),
    description TEXT,
    callback_url VARCHAR(255),
    redirect_url VARCHAR(255),
    ip_address VARCHAR(50),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL
);

CREATE INDEX idx_transactions_id ON transactions(transaction_id);
CREATE INDEX idx_transactions_merchant ON transactions(merchant_id, payment_status);
CREATE INDEX idx_transactions_status ON transactions(payment_status);
CREATE INDEX idx_transactions_created ON transactions(created_at);

-- ============================================
-- 5. SETTLEMENTS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS settlements (
    id SERIAL PRIMARY KEY,
    settlement_id VARCHAR(100) UNIQUE NOT NULL,
    merchant_id INTEGER NOT NULL REFERENCES merchants(id) ON DELETE CASCADE,
    settlement_amount DECIMAL(15,2) NOT NULL,
    commission_amount DECIMAL(15,2) NOT NULL,
    net_amount DECIMAL(15,2) NOT NULL,
    transaction_count INTEGER DEFAULT 0,
    settlement_status VARCHAR(20) DEFAULT 'pending' CHECK (settlement_status IN ('pending', 'processing', 'completed', 'failed')),
    settlement_date DATE,
    bank_reference_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL
);

CREATE INDEX idx_settlements_merchant ON settlements(merchant_id, settlement_status);

-- ============================================
-- 6. SUBSCRIPTIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS subscriptions (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    plan_type VARCHAR(20) NOT NULL CHECK (plan_type IN ('monthly', 'yearly', 'lifetime')),
    plan_name VARCHAR(100),
    amount DECIMAL(15,2) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    status VARCHAR(20) DEFAULT 'pending' CHECK (status IN ('active', 'expired', 'cancelled', 'pending')),
    payment_transaction_id VARCHAR(100),
    auto_renewal BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_subscriptions_user ON subscriptions(user_id, status);

-- ============================================
-- 7. RESELLERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS resellers (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    reseller_code VARCHAR(50) UNIQUE NOT NULL,
    commission_rate DECIMAL(5,2) DEFAULT 30.00,
    total_referrals INTEGER DEFAULT 0,
    total_earnings DECIMAL(15,2) DEFAULT 0.00,
    bank_account_number VARCHAR(50),
    bank_ifsc VARCHAR(20),
    bank_name VARCHAR(100),
    pan_number VARCHAR(20),
    status VARCHAR(20) DEFAULT 'active' CHECK (status IN ('active', 'inactive', 'suspended')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_resellers_code ON resellers(reseller_code);

-- ============================================
-- 8. REFERRALS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS referrals (
    id SERIAL PRIMARY KEY,
    reseller_id INTEGER NOT NULL REFERENCES resellers(id) ON DELETE CASCADE,
    referred_user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    subscription_id INTEGER REFERENCES subscriptions(id) ON DELETE SET NULL,
    commission_amount DECIMAL(15,2) DEFAULT 0.00,
    commission_status VARCHAR(20) DEFAULT 'pending' CHECK (commission_status IN ('pending', 'paid', 'cancelled')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at TIMESTAMP NULL
);

-- ============================================
-- 9. API LOGS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS api_logs (
    id SERIAL PRIMARY KEY,
    merchant_id INTEGER REFERENCES merchants(id) ON DELETE CASCADE,
    endpoint VARCHAR(255) NOT NULL,
    method VARCHAR(10) NOT NULL,
    request_data TEXT,
    response_data TEXT,
    status_code INTEGER,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_api_logs_merchant ON api_logs(merchant_id, created_at);

-- ============================================
-- 10. NOTIFICATIONS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS notifications (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type VARCHAR(20) DEFAULT 'info' CHECK (type IN ('info', 'success', 'warning', 'error')),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_notifications_user ON notifications(user_id, is_read);

-- ============================================
-- SAMPLE DATA FOR TESTING
-- ============================================

-- Insert Admin User (password: admin123)
INSERT INTO users (user_type, email, username, password_hash, full_name, phone, status, email_verified) VALUES
('admin', 'admin@payme.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', '+919876543210', 'active', TRUE);

-- Insert Test Merchant User (password: Merchant@2025)
INSERT INTO users (user_type, email, username, password_hash, full_name, phone, status, email_verified) VALUES
('merchant', 'merchant@test.com', 'merchant@test.com', '$2y$10$vI8aWBnW3fID.ZQ4/zo1G.q1lRps.9cGLcZEiGDMVr5yP1p5LoXxu', 'Test Business Owner', '+919876543211', 'active', TRUE);

-- Insert Merchant Details
INSERT INTO merchants (user_id, merchant_id, business_name, business_type, business_category, website_url, city, state, country, kyc_status, api_key, api_secret, commission_rate) VALUES
(2, 'MERCH001', 'Test Business', 'E-commerce', 'Electronics', 'https://testbusiness.com', 'Mumbai', 'Maharashtra', 'India', 'verified', 'pk_test_51234567890abcdef', 'sk_test_51234567890abcdef', 2.00);

-- Insert Sample Transactions
INSERT INTO transactions (transaction_id, merchant_id, payment_method, amount, customer_name, customer_email, customer_phone, payment_status, gateway_transaction_id, created_at, completed_at) VALUES
('TXN001', 1, 'upi', 1500.00, 'John Doe', 'john@example.com', '+919876543212', 'success', 'GTW123456', NOW() - INTERVAL '5 days', NOW() - INTERVAL '5 days'),
('TXN002', 1, 'upi', 2500.00, 'Jane Smith', 'jane@example.com', '+919876543213', 'success', 'GTW123457', NOW() - INTERVAL '4 days', NOW() - INTERVAL '4 days'),
('TXN003', 1, 'crypto', 5000.00, 'Bob Johnson', 'bob@example.com', '+919876543214', 'success', 'GTW123458', NOW() - INTERVAL '3 days', NOW() - INTERVAL '3 days'),
('TXN004', 1, 'upi', 1200.00, 'Alice Brown', 'alice@example.com', '+919876543215', 'pending', NULL, NOW() - INTERVAL '1 day', NULL);

-- Insert Sample Subscription
INSERT INTO subscriptions (user_id, plan_type, plan_name, amount, start_date, end_date, status, payment_transaction_id) VALUES
(2, 'yearly', 'Yearly License', 49999.00, CURRENT_DATE, CURRENT_DATE + INTERVAL '1 year', 'active', 'SUB001');

-- Insert Reseller
INSERT INTO users (user_type, email, username, password_hash, full_name, phone, status, email_verified) VALUES
('reseller', 'reseller@test.com', 'reseller@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test Reseller', '+919876543216', 'active', TRUE);

INSERT INTO resellers (user_id, reseller_code, commission_rate, total_referrals, total_earnings) VALUES
(3, 'RES001', 30.00, 5, 75000.00);

-- Insert Sample Notifications
INSERT INTO notifications (user_id, title, message, type, is_read) VALUES
(2, 'Welcome to PayMe Gateway', 'Your merchant account has been successfully created!', 'success', FALSE),
(2, 'KYC Verification Complete', 'Your KYC documents have been verified successfully.', 'success', TRUE);

-- ============================================
-- END OF SCHEMA
-- ============================================
