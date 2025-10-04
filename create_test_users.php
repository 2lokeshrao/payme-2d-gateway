<?php
require_once 'config.php';

echo "Creating test users...\n\n";

// Admin User
$adminEmail = 'admin@payme2d.com';
$adminPassword = 'admin123';
$adminPasswordHash = password_hash($adminPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    INSERT INTO users (name, email, password, role, status, created_at) 
    VALUES (?, ?, ?, 'admin', 'active', NOW())
    ON CONFLICT (email) DO UPDATE SET
        password = EXCLUDED.password,
        role = 'admin',
        status = 'active'
");
$adminName = 'Admin User';
$stmt->bind_param('sss', $adminName, $adminEmail, $adminPasswordHash);

if ($stmt->execute()) {
    echo "✅ Admin user created/updated successfully!\n";
    echo "   Email: $adminEmail\n";
    echo "   Password: $adminPassword\n\n";
} else {
    echo "❌ Error creating admin user: " . $stmt->error . "\n\n";
}

// Merchant User
$merchantEmail = 'merchant@test.com';
$merchantPassword = 'merchant123';
$merchantPasswordHash = password_hash($merchantPassword, PASSWORD_BCRYPT);

$stmt = $conn->prepare("
    INSERT INTO users (name, email, password, role, status, created_at) 
    VALUES (?, ?, ?, 'merchant', 'active', NOW())
    ON CONFLICT (email) DO UPDATE SET
        password = EXCLUDED.password,
        role = 'merchant',
        status = 'active'
    RETURNING id
");
$merchantName = 'Test Merchant';
$stmt->bind_param('sss', $merchantName, $merchantEmail, $merchantPasswordHash);

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $merchantUserId = $result->fetch_assoc()['id'] ?? null;
    
    echo "✅ Merchant user created/updated successfully!\n";
    echo "   Email: $merchantEmail\n";
    echo "   Password: $merchantPassword\n\n";
    
    // Create merchant profile
    if ($merchantUserId) {
        $stmt = $conn->prepare("
            INSERT INTO merchants (user_id, business_name, business_type, website, status, created_at)
            VALUES (?, 'Test Business', 'ecommerce', 'https://testbusiness.com', 'active', NOW())
            ON CONFLICT (user_id) DO UPDATE SET
                business_name = 'Test Business',
                status = 'active'
            RETURNING id
        ");
        $stmt->bind_param('i', $merchantUserId);
        
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $merchantId = $result->fetch_assoc()['id'] ?? null;
            
            echo "✅ Merchant profile created!\n\n";
            
            // Create API key
            if ($merchantId) {
                $apiKey = 'pk_test_' . bin2hex(random_bytes(16));
                $stmt = $conn->prepare("
                    INSERT INTO api_keys (merchant_id, key_type, api_key, status, created_at)
                    VALUES (?, 'test', ?, 'active', NOW())
                    ON CONFLICT DO NOTHING
                ");
                $stmt->bind_param('is', $merchantId, $apiKey);
                
                if ($stmt->execute()) {
                    echo "✅ API key created: $apiKey\n\n";
                }
            }
        }
    }
} else {
    echo "❌ Error creating merchant user: " . $stmt->error . "\n\n";
}

echo "========================================\n";
echo "🎉 Test Users Created Successfully!\n";
echo "========================================\n\n";

echo "🔐 ADMIN LOGIN:\n";
echo "   URL: https://payme-gateway.lindy.site/admin/login.php\n";
echo "   Email: admin@payme2d.com\n";
echo "   Password: admin123\n\n";

echo "👤 MERCHANT LOGIN:\n";
echo "   URL: https://payme-gateway.lindy.site/login.html\n";
echo "   Email: merchant@test.com\n";
echo "   Password: merchant123\n\n";

echo "⚠️  Remember to change these passwords in production!\n";

$stmt->close();
$conn->close();
?>
