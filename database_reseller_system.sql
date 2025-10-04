-- ============================================================================
-- PayMe 2D Gateway - Reseller & Activation Code System Database Schema
-- ============================================================================
-- Created: October 4, 2025
-- Purpose: Complete reseller management and activation code tracking system
-- ============================================================================

-- ============================================================================
-- 1. RESELLERS TABLE
-- ============================================================================
-- Stores reseller account information
CREATE TABLE IF NOT EXISTS resellers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password_hash VARCHAR(255) NOT NULL,
    commission_rate DECIMAL(5,2) DEFAULT 20.00 COMMENT 'Commission percentage (e.g., 20.00 for 20%)',
    allowed_plans JSON COMMENT 'Array of plan IDs reseller can sell',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    total_sales INT DEFAULT 0,
    total_revenue DECIMAL(10,2) DEFAULT 0.00,
    pending_payout DECIMAL(10,2) DEFAULT 0.00,
    last_payout_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Reseller accounts management';

-- ============================================================================
-- 2. ACTIVATION CODES TABLE
-- ============================================================================
-- Stores all activation codes (admin + reseller generated)
CREATE TABLE IF NOT EXISTS activation_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'Activation code (e.g., PM2D-XXXX-XXXX-XXXX)',
    plan_id INT NOT NULL,
    created_by_type ENUM('admin', 'reseller') DEFAULT 'admin',
    created_by_id INT COMMENT 'Admin ID or Reseller ID',
    reseller_id INT COMMENT 'Reseller who owns this code',
    status ENUM('unused', 'used', 'expired', 'revoked') DEFAULT 'unused',
    validity_days INT DEFAULT 365 COMMENT 'How many days code is valid before expiry',
    expires_at DATETIME COMMENT 'When the code expires if not used',
    used_by_user_id INT COMMENT 'User who activated this code',
    used_by_email VARCHAR(255) COMMENT 'Email of user who used code',
    used_at DATETIME COMMENT 'When code was activated',
    subscription_start_date DATETIME COMMENT 'When subscription started after activation',
    subscription_end_date DATETIME COMMENT 'When subscription ends',
    notes TEXT COMMENT 'Additional notes about this code',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (reseller_id) REFERENCES resellers(id) ON DELETE SET NULL,
    INDEX idx_code (code),
    INDEX idx_status (status),
    INDEX idx_reseller (reseller_id),
    INDEX idx_plan (plan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Activation codes for subscription activation';

-- ============================================================================
-- 3. RESELLER EARNINGS TABLE
-- ============================================================================
-- Tracks commission earnings for each reseller
CREATE TABLE IF NOT EXISTS reseller_earnings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reseller_id INT NOT NULL,
    activation_code_id INT NOT NULL,
    plan_id INT NOT NULL,
    plan_price DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    paid_at DATETIME,
    payment_method VARCHAR(50) COMMENT 'How commission was paid',
    payment_reference VARCHAR(255) COMMENT 'Payment transaction reference',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reseller_id) REFERENCES resellers(id) ON DELETE CASCADE,
    FOREIGN KEY (activation_code_id) REFERENCES activation_codes(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id) ON DELETE CASCADE,
    INDEX idx_reseller (reseller_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Reseller commission earnings tracking';

-- ============================================================================
-- 4. RESELLER PAYOUTS TABLE
-- ============================================================================
-- Tracks payout history to resellers
CREATE TABLE IF NOT EXISTS reseller_payouts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reseller_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL COMMENT 'Bank transfer, PayPal, etc.',
    payment_reference VARCHAR(255),
    bank_account_details JSON COMMENT 'Bank details for payout',
    status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    requested_at DATETIME,
    processed_at DATETIME,
    completed_at DATETIME,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reseller_id) REFERENCES resellers(id) ON DELETE CASCADE,
    INDEX idx_reseller (reseller_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Reseller payout history';

-- ============================================================================
-- 5. CODE ACTIVATION HISTORY TABLE
-- ============================================================================
-- Detailed log of all code activation attempts
CREATE TABLE IF NOT EXISTS code_activation_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activation_code_id INT NOT NULL,
    user_id INT,
    email VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    status ENUM('success', 'failed', 'invalid_code', 'expired_code', 'already_used') NOT NULL,
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activation_code_id) REFERENCES activation_codes(id) ON DELETE CASCADE,
    INDEX idx_code (activation_code_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log of all code activation attempts';

-- ============================================================================
-- 6. RESELLER CUSTOMERS TABLE
-- ============================================================================
-- Tracks customers assigned to each reseller
CREATE TABLE IF NOT EXISTS reseller_customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reseller_id INT NOT NULL,
    user_id INT NOT NULL,
    activation_code_id INT,
    customer_email VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255),
    subscription_plan_id INT,
    subscription_status ENUM('active', 'expired', 'cancelled') DEFAULT 'active',
    subscription_start_date DATETIME,
    subscription_end_date DATETIME,
    total_paid DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (reseller_id) REFERENCES resellers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (activation_code_id) REFERENCES activation_codes(id) ON DELETE SET NULL,
    FOREIGN KEY (subscription_plan_id) REFERENCES subscription_plans(id) ON DELETE SET NULL,
    INDEX idx_reseller (reseller_id),
    INDEX idx_user (user_id),
    INDEX idx_email (customer_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Reseller customer relationships';

-- ============================================================================
-- 7. SYSTEM SETTINGS TABLE (Enhanced)
-- ============================================================================
-- Add reseller-related system settings
CREATE TABLE IF NOT EXISTS system_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE COMMENT 'Can resellers see this setting?',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='System-wide settings';

-- ============================================================================
-- 8. INSERT DEFAULT SYSTEM SETTINGS
-- ============================================================================
INSERT INTO system_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('default_commission_rate', '20', 'number', 'Default commission rate for new resellers (%)', FALSE),
('min_payout_amount', '100', 'number', 'Minimum amount for reseller payout ($)', TRUE),
('code_validity_days', '365', 'number', 'Default validity period for activation codes (days)', TRUE),
('allow_reseller_registration', 'false', 'boolean', 'Allow public reseller registration', FALSE),
('reseller_approval_required', 'true', 'boolean', 'Require admin approval for new resellers', FALSE),
('code_format', 'PM2D-XXXX-XXXX-XXXX', 'string', 'Format for activation codes', TRUE)
ON DUPLICATE KEY UPDATE updated_at = CURRENT_TIMESTAMP;

-- ============================================================================
-- 9. VIEWS FOR REPORTING
-- ============================================================================

-- View: Reseller Performance Summary
CREATE OR REPLACE VIEW reseller_performance AS
SELECT 
    r.id,
    r.name,
    r.email,
    r.commission_rate,
    r.status,
    COUNT(DISTINCT ac.id) as total_codes_generated,
    COUNT(DISTINCT CASE WHEN ac.status = 'used' THEN ac.id END) as codes_used,
    COUNT(DISTINCT rc.id) as total_customers,
    SUM(re.commission_amount) as total_earnings,
    SUM(CASE WHEN re.status = 'pending' THEN re.commission_amount ELSE 0 END) as pending_earnings,
    SUM(CASE WHEN re.status = 'paid' THEN re.commission_amount ELSE 0 END) as paid_earnings,
    r.created_at as joined_date
FROM resellers r
LEFT JOIN activation_codes ac ON r.id = ac.reseller_id
LEFT JOIN reseller_customers rc ON r.id = rc.reseller_id
LEFT JOIN reseller_earnings re ON r.id = re.reseller_id
GROUP BY r.id;

-- View: Active Subscriptions from Codes
CREATE OR REPLACE VIEW active_code_subscriptions AS
SELECT 
    ac.code,
    ac.reseller_id,
    r.name as reseller_name,
    sp.plan_name,
    sp.price as plan_price,
    ac.used_by_email,
    ac.subscription_start_date,
    ac.subscription_end_date,
    DATEDIFF(ac.subscription_end_date, NOW()) as days_remaining,
    CASE 
        WHEN ac.subscription_end_date > NOW() THEN 'active'
        ELSE 'expired'
    END as status
FROM activation_codes ac
JOIN subscription_plans sp ON ac.plan_id = sp.id
LEFT JOIN resellers r ON ac.reseller_id = r.id
WHERE ac.status = 'used';

-- ============================================================================
-- 10. STORED PROCEDURES
-- ============================================================================

-- Procedure: Generate Activation Code
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS generate_activation_code(
    IN p_plan_id INT,
    IN p_created_by_type ENUM('admin', 'reseller'),
    IN p_created_by_id INT,
    IN p_reseller_id INT,
    IN p_validity_days INT,
    OUT p_code VARCHAR(50)
)
BEGIN
    DECLARE v_code VARCHAR(50);
    DECLARE v_exists INT;
    
    -- Generate unique code
    REPEAT
        SET v_code = CONCAT('PM2D-', 
            LPAD(FLOOR(RAND() * 10000), 4, '0'), '-',
            LPAD(FLOOR(RAND() * 10000), 4, '0'), '-',
            LPAD(FLOOR(RAND() * 10000), 4, '0'));
        
        SELECT COUNT(*) INTO v_exists FROM activation_codes WHERE code = v_code;
    UNTIL v_exists = 0 END REPEAT;
    
    -- Insert code
    INSERT INTO activation_codes (
        code, plan_id, created_by_type, created_by_id, reseller_id, 
        validity_days, expires_at
    ) VALUES (
        v_code, p_plan_id, p_created_by_type, p_created_by_id, p_reseller_id,
        p_validity_days, DATE_ADD(NOW(), INTERVAL p_validity_days DAY)
    );
    
    SET p_code = v_code;
END //
DELIMITER ;

-- Procedure: Activate Code
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS activate_code(
    IN p_code VARCHAR(50),
    IN p_user_id INT,
    IN p_email VARCHAR(255),
    OUT p_success BOOLEAN,
    OUT p_message VARCHAR(255)
)
BEGIN
    DECLARE v_code_id INT;
    DECLARE v_plan_id INT;
    DECLARE v_status VARCHAR(20);
    DECLARE v_expires_at DATETIME;
    DECLARE v_plan_duration INT;
    DECLARE v_reseller_id INT;
    DECLARE v_plan_price DECIMAL(10,2);
    DECLARE v_commission_rate DECIMAL(5,2);
    DECLARE v_commission_amount DECIMAL(10,2);
    
    -- Check if code exists
    SELECT id, plan_id, status, expires_at, reseller_id 
    INTO v_code_id, v_plan_id, v_status, v_expires_at, v_reseller_id
    FROM activation_codes 
    WHERE code = p_code;
    
    IF v_code_id IS NULL THEN
        SET p_success = FALSE;
        SET p_message = 'Invalid activation code';
        
        INSERT INTO code_activation_history (activation_code_id, user_id, email, status, error_message)
        VALUES (NULL, p_user_id, p_email, 'invalid_code', 'Code not found');
        
    ELSEIF v_status != 'unused' THEN
        SET p_success = FALSE;
        SET p_message = 'Code already used';
        
        INSERT INTO code_activation_history (activation_code_id, user_id, email, status, error_message)
        VALUES (v_code_id, p_user_id, p_email, 'already_used', 'Code already activated');
        
    ELSEIF v_expires_at < NOW() THEN
        SET p_success = FALSE;
        SET p_message = 'Code has expired';
        
        UPDATE activation_codes SET status = 'expired' WHERE id = v_code_id;
        
        INSERT INTO code_activation_history (activation_code_id, user_id, email, status, error_message)
        VALUES (v_code_id, p_user_id, p_email, 'expired_code', 'Code expired');
        
    ELSE
        -- Get plan details
        SELECT duration_days, price INTO v_plan_duration, v_plan_price
        FROM subscription_plans WHERE id = v_plan_id;
        
        -- Activate code
        UPDATE activation_codes 
        SET status = 'used',
            used_by_user_id = p_user_id,
            used_by_email = p_email,
            used_at = NOW(),
            subscription_start_date = NOW(),
            subscription_end_date = DATE_ADD(NOW(), INTERVAL v_plan_duration DAY)
        WHERE id = v_code_id;
        
        -- Create subscription
        INSERT INTO user_subscriptions (user_id, plan_id, subscription_status, start_date, end_date)
        VALUES (p_user_id, v_plan_id, 'active', NOW(), DATE_ADD(NOW(), INTERVAL v_plan_duration DAY));
        
        -- Calculate reseller commission if applicable
        IF v_reseller_id IS NOT NULL THEN
            SELECT commission_rate INTO v_commission_rate FROM resellers WHERE id = v_reseller_id;
            SET v_commission_amount = (v_plan_price * v_commission_rate / 100);
            
            INSERT INTO reseller_earnings (reseller_id, activation_code_id, plan_id, plan_price, commission_rate, commission_amount)
            VALUES (v_reseller_id, v_code_id, v_plan_id, v_plan_price, v_commission_rate, v_commission_amount);
            
            UPDATE resellers 
            SET total_sales = total_sales + 1,
                total_revenue = total_revenue + v_plan_price,
                pending_payout = pending_payout + v_commission_amount
            WHERE id = v_reseller_id;
        END IF;
        
        -- Log success
        INSERT INTO code_activation_history (activation_code_id, user_id, email, status)
        VALUES (v_code_id, p_user_id, p_email, 'success');
        
        SET p_success = TRUE;
        SET p_message = 'Code activated successfully';
    END IF;
END //
DELIMITER ;

-- ============================================================================
-- 11. TRIGGERS
-- ============================================================================

-- Trigger: Auto-expire codes
DELIMITER //
CREATE TRIGGER IF NOT EXISTS check_code_expiry_before_use
BEFORE UPDATE ON activation_codes
FOR EACH ROW
BEGIN
    IF NEW.status = 'used' AND NEW.expires_at < NOW() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot activate expired code';
    END IF;
END //
DELIMITER ;

-- ============================================================================
-- 12. INDEXES FOR PERFORMANCE
-- ============================================================================
CREATE INDEX idx_activation_codes_expires ON activation_codes(expires_at);
CREATE INDEX idx_reseller_earnings_status ON reseller_earnings(status, reseller_id);
CREATE INDEX idx_code_activation_created ON code_activation_history(created_at);

-- ============================================================================
-- END OF SCHEMA
-- ============================================================================

-- Sample data for testing (optional)
-- INSERT INTO resellers (name, email, password_hash, commission_rate, allowed_plans) VALUES
-- ('Test Reseller', 'reseller@test.com', '$2y$10$...', 20.00, '[1,2,3]');

SELECT 'Reseller & Activation Code System Database Schema Created Successfully!' as Status;
