-- Database Schema for Subscription Management System
-- PayMe 2D Gateway

-- Add columns to existing clients table
ALTER TABLE clients 
ADD COLUMN license_type VARCHAR(20) DEFAULT 'monthly' COMMENT 'monthly, yearly, lifetime',
ADD COLUMN purchase_date DATETIME DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN expiry_date DATETIME,
ADD COLUMN auto_renewal BOOLEAN DEFAULT false,
ADD COLUMN status VARCHAR(20) DEFAULT 'active' COMMENT 'active, expired, grace_period, cancelled',
ADD COLUMN last_payment_date DATETIME,
ADD COLUMN renewal_reminder_sent BOOLEAN DEFAULT false,
ADD COLUMN grace_period_started DATETIME,
ADD COLUMN gateway_enabled BOOLEAN DEFAULT true;

-- Create license_history table
CREATE TABLE IF NOT EXISTS license_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    license_type VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    expiry_date DATETIME,
    transaction_id VARCHAR(100),
    payment_method VARCHAR(50),
    status VARCHAR(20) DEFAULT 'completed' COMMENT 'completed, pending, failed',
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_client_id (client_id),
    INDEX idx_payment_date (payment_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create license_logs table for tracking actions
CREATE TABLE IF NOT EXISTS license_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    action VARCHAR(50) NOT NULL COMMENT 'gateway_disabled, gateway_enabled, reminder_sent, renewal_completed',
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_client_id (client_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create email_queue table for tracking emails
CREATE TABLE IF NOT EXISTS email_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    email_type VARCHAR(50) NOT NULL COMMENT 'renewal_reminder, grace_period, expiry_notification',
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    sent_at DATETIME,
    status VARCHAR(20) DEFAULT 'pending' COMMENT 'pending, sent, failed',
    retry_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    INDEX idx_status (status),
    INDEX idx_email_type (email_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data for testing (optional)
-- INSERT INTO clients (name, email, phone, business_name, license_type, purchase_date, expiry_date, status, gateway_enabled)
-- VALUES 
-- ('Test Client 1', 'test1@example.com', '9876543210', 'Test Business 1', 'monthly', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 'active', true),
-- ('Test Client 2', 'test2@example.com', '9876543211', 'Test Business 2', 'yearly', NOW(), DATE_ADD(NOW(), INTERVAL 1 YEAR), 'active', true),
-- ('Test Client 3', 'test3@example.com', '9876543212', 'Test Business 3', 'lifetime', NOW(), '2099-12-31 23:59:59', 'active', true);

-- Indexes for better performance
CREATE INDEX idx_license_type ON clients(license_type);
CREATE INDEX idx_expiry_date ON clients(expiry_date);
CREATE INDEX idx_status ON clients(status);
CREATE INDEX idx_gateway_enabled ON clients(gateway_enabled);

-- View for active subscriptions
CREATE OR REPLACE VIEW active_subscriptions AS
SELECT 
    c.id,
    c.name,
    c.email,
    c.license_type,
    c.purchase_date,
    c.expiry_date,
    c.status,
    c.gateway_enabled,
    DATEDIFF(c.expiry_date, NOW()) as days_remaining,
    CASE 
        WHEN c.license_type = 'lifetime' THEN 'Lifetime'
        WHEN DATEDIFF(c.expiry_date, NOW()) < 0 THEN 'Expired'
        WHEN DATEDIFF(c.expiry_date, NOW()) <= 7 THEN 'Expiring Soon'
        ELSE 'Active'
    END as subscription_status
FROM clients c
WHERE c.status IN ('active', 'grace_period')
ORDER BY c.expiry_date ASC;

-- View for revenue tracking
CREATE OR REPLACE VIEW revenue_summary AS
SELECT 
    DATE_FORMAT(payment_date, '%Y-%m') as month,
    license_type,
    COUNT(*) as total_renewals,
    SUM(amount) as total_revenue
FROM license_history
WHERE status = 'completed'
GROUP BY DATE_FORMAT(payment_date, '%Y-%m'), license_type
ORDER BY month DESC;

-- Stored procedure to process renewal
DELIMITER //
CREATE PROCEDURE process_renewal(
    IN p_client_id INT,
    IN p_amount DECIMAL(10,2),
    IN p_transaction_id VARCHAR(100)
)
BEGIN
    DECLARE v_license_type VARCHAR(20);
    DECLARE v_current_expiry DATETIME;
    DECLARE v_new_expiry DATETIME;
    
    -- Get current license info
    SELECT license_type, expiry_date INTO v_license_type, v_current_expiry
    FROM clients WHERE id = p_client_id;
    
    -- Calculate new expiry date
    IF v_license_type = 'monthly' THEN
        SET v_new_expiry = DATE_ADD(v_current_expiry, INTERVAL 1 MONTH);
    ELSEIF v_license_type = 'yearly' THEN
        SET v_new_expiry = DATE_ADD(v_current_expiry, INTERVAL 1 YEAR);
    END IF;
    
    -- Update client record
    UPDATE clients 
    SET expiry_date = v_new_expiry,
        status = 'active',
        gateway_enabled = true,
        last_payment_date = NOW(),
        renewal_reminder_sent = false
    WHERE id = p_client_id;
    
    -- Insert into license history
    INSERT INTO license_history (client_id, license_type, amount, payment_date, expiry_date, transaction_id, status)
    VALUES (p_client_id, v_license_type, p_amount, NOW(), v_new_expiry, p_transaction_id, 'completed');
    
    -- Log the action
    INSERT INTO license_logs (client_id, action, description)
    VALUES (p_client_id, 'renewal_completed', CONCAT('License renewed until ', v_new_expiry));
    
END //
DELIMITER ;

-- Function to check if license is valid
DELIMITER //
CREATE FUNCTION is_license_valid(p_client_id INT) 
RETURNS BOOLEAN
DETERMINISTIC
BEGIN
    DECLARE v_license_type VARCHAR(20);
    DECLARE v_expiry_date DATETIME;
    DECLARE v_status VARCHAR(20);
    
    SELECT license_type, expiry_date, status 
    INTO v_license_type, v_expiry_date, v_status
    FROM clients WHERE id = p_client_id;
    
    IF v_license_type = 'lifetime' THEN
        RETURN TRUE;
    END IF;
    
    IF v_status = 'expired' THEN
        RETURN FALSE;
    END IF;
    
    IF NOW() > v_expiry_date THEN
        RETURN FALSE;
    END IF;
    
    RETURN TRUE;
END //
DELIMITER ;

-- Trigger to log status changes
DELIMITER //
CREATE TRIGGER after_client_status_update
AFTER UPDATE ON clients
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO license_logs (client_id, action, description)
        VALUES (NEW.id, 'status_changed', CONCAT('Status changed from ', OLD.status, ' to ', NEW.status));
    END IF;
    
    IF OLD.gateway_enabled != NEW.gateway_enabled THEN
        INSERT INTO license_logs (client_id, action, description)
        VALUES (NEW.id, 
                IF(NEW.gateway_enabled = true, 'gateway_enabled', 'gateway_disabled'),
                IF(NEW.gateway_enabled = true, 'Gateway enabled', 'Gateway disabled'));
    END IF;
END //
DELIMITER ;

-- Grant permissions (adjust as needed)
-- GRANT SELECT, INSERT, UPDATE ON payme_gateway.* TO 'gateway_user'@'localhost';
-- GRANT EXECUTE ON PROCEDURE payme_gateway.process_renewal TO 'gateway_user'@'localhost';

-- Cleanup old logs (run periodically)
-- DELETE FROM license_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);
-- DELETE FROM email_queue WHERE status = 'sent' AND created_at < DATE_SUB(NOW(), INTERVAL 3 MONTH);
