// Authentication check for protected pages
(function() {
    // Check if user is logged in
    function checkAuth() {
        const userToken = localStorage.getItem('userToken');
        const userId = localStorage.getItem('userId');
        
        // If not logged in, redirect to login
        if (!userToken || !userId) {
            // Don't redirect if already on login/register page
            const currentPage = window.location.pathname;
            if (!currentPage.includes('login.html') && 
                !currentPage.includes('register.html') && 
                !currentPage.includes('index.html') &&
                !currentPage.includes('payment.html') &&
                !currentPage.includes('payment-success.html')) {
                window.location.href = '/login.html';
            }
        }
    }
    
    // Check auth on page load
    checkAuth();
    
    // Setup logout functionality
    function setupLogout() {
        const logoutButtons = document.querySelectorAll('[href*="logout"], .logout-btn, #logoutBtn');
        logoutButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Clear all session data
                localStorage.removeItem('userToken');
                localStorage.removeItem('userId');
                localStorage.removeItem('userName');
                localStorage.removeItem('userEmail');
                localStorage.removeItem('userRole');
                
                // Redirect to login
                window.location.href = '/login.html';
            });
        });
    }
    
    // Setup on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupLogout);
    } else {
        setupLogout();
    }
    
    // Display user info if available
    function displayUserInfo() {
        const userName = localStorage.getItem('userName');
        const userEmail = localStorage.getItem('userEmail');
        
        // Update user name displays
        const userNameElements = document.querySelectorAll('.user-name, #userName');
        userNameElements.forEach(el => {
            if (userName) el.textContent = userName;
        });
        
        // Update user email displays
        const userEmailElements = document.querySelectorAll('.user-email, #userEmail');
        userEmailElements.forEach(el => {
            if (userEmail) el.textContent = userEmail;
        });
    }
    
    displayUserInfo();
})();
