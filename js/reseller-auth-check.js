// Reseller Authentication Check
(function() {
    // Check if user is logged in
    const resellerToken = localStorage.getItem('resellerToken');
    const resellerEmail = localStorage.getItem('resellerEmail');
    
    // Get current page
    const currentPage = window.location.pathname;
    
    // Check if current page is reseller dashboard
    if (currentPage.includes('reseller/dashboard.html')) {
        // If not logged in, redirect to login page
        if (!resellerToken || !resellerEmail) {
            console.log('No reseller authentication found. Redirecting to login...');
            window.location.href = 'login.html';  // Changed from ../reseller-login.html to login.html
        }
    }
})();
