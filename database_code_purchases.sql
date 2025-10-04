-- Table for storing code purchase transactions
CREATE TABLE IF NOT EXISTS code_purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activation_code VARCHAR(20) NOT NULL,
    plan_type VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    transaction_id VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (customer_email),
    INDEX idx_transaction (transaction_id),
    INDEX idx_code (activation_code),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add customer details to activation_codes table if not exists
ALTER TABLE activation_codes 
ADD COLUMN IF NOT EXISTS customer_name VARCHAR(255),
ADD COLUMN IF NOT EXISTS customer_phone VARCHAR(20);

-- Insert sample subscription plans if not exists
INSERT IGNORE INTO subscription_plans (name, plan_type, price, duration_days, features, status) VALUES
('Monthly Plan', 'monthly', 60.00, 30, 'Self Bank Account Integration\nSelf UPI Payment Collection\nUnlimited Transactions\nAPI Access\nEmail Support', 'active'),
('Quarterly Plan', 'quarterly', 150.00, 90, 'Everything in Monthly Plan\nPriority Support\nAdvanced Analytics\nCustom Branding', 'active'),
('Yearly Plan', 'yearly', 500.00, 365, 'Everything in Quarterly Plan\n24/7 Priority Support\nDedicated Account Manager\nWhite Label Solution', 'active'),
('Lifetime Plan', 'lifetime', 2999.00, 36500, 'Everything in Yearly Plan\nLifetime Access\nNo Recurring Fees\nVIP Support', 'active');
