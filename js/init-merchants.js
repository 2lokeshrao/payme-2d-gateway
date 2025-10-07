// Initialize merchants in localStorage
(function() {
    // Check if merchants already exist
    let merchants = JSON.parse(localStorage.getItem('merchants') || '[]');
    
    // If no merchants exist, add the default Test Business merchant
    if (merchants.length === 0) {
        const defaultMerchant = {
            id: 1,
            businessName: 'Test Business',
            email: 'merchant@test.com',
            username: 'merchant@test.com',
            password: 'Merchant@2025',
            businessType: 'E-commerce',
            kycStatus: 'Pending',
            status: 'Active',
            createdAt: new Date().toISOString()
        };
        
        merchants.push(defaultMerchant);
        localStorage.setItem('merchants', JSON.stringify(merchants));
        console.log('✅ Default merchant initialized in localStorage');
    } else {
        console.log('✅ Merchants already exist in localStorage');
    }
})();
