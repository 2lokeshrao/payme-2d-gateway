-- Create test admin user
-- Email: admin@payme2d.com
-- Password: admin123

INSERT INTO users (name, email, password, role, status, created_at) 
VALUES (
    'Admin User',
    'admin@payme2d.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin',
    'active',
    NOW()
) ON CONFLICT (email) DO UPDATE SET
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'admin',
    status = 'active';

-- Create test merchant user
-- Email: merchant@test.com
-- Password: merchant123

INSERT INTO users (name, email, password, role, status, created_at) 
VALUES (
    'Test Merchant',
    'merchant@test.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: merchant123
    'merchant',
    'active',
    NOW()
) ON CONFLICT (email) DO UPDATE SET
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'merchant',
    status = 'active';

-- Create merchant profile for test merchant
INSERT INTO merchants (user_id, business_name, business_type, website, status, created_at)
SELECT id, 'Test Business', 'ecommerce', 'https://testbusiness.com', 'active', NOW()
FROM users WHERE email = 'merchant@test.com'
ON CONFLICT (user_id) DO UPDATE SET
    business_name = 'Test Business',
    status = 'active';

-- Create API key for test merchant
INSERT INTO api_keys (merchant_id, key_type, api_key, status, created_at)
SELECT m.id, 'test', 'pk_test_' || md5(random()::text), 'active', NOW()
FROM merchants m
JOIN users u ON m.user_id = u.id
WHERE u.email = 'merchant@test.com'
ON CONFLICT DO NOTHING;

SELECT 'Test users created successfully!' as message;
SELECT 'Admin: admin@payme2d.com / admin123' as admin_credentials;
SELECT 'Merchant: merchant@test.com / merchant123' as merchant_credentials;
