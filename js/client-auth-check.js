// Client Authentication Check
(function() {
    // Check if user is logged in
    const clientToken = localStorage.getItem('clientToken');
    const clientEmail = localStorage.getItem('clientEmail');
    
    // Get current page
    const currentPage = window.location.pathname.split('/').pop();
    
    // Pages that require authentication
    const protectedPages = [
        'client-dashboard.html',
        'client-merchants.html',
        'client-transactions.html',
        'client-settings.html',
        'client-payment-settings.html',
        'client-settlements.html',
        'merchant-register.html',
        'api-docs.html'
    ];
    
    // Check if current page is protected
    if (protectedPages.includes(currentPage)) {
        // If not logged in, redirect to login page
        if (!clientToken || !clientEmail) {
            console.log('No authentication found. Redirecting to login...');
            window.location.href = 'client-login.html';
        }
    }
})();
