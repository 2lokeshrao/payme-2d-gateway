// Admin Authentication check for protected admin pages
(function() {
    // Check if admin is logged in
    function checkAdminAuth() {
        const adminToken = localStorage.getItem('adminToken');
        const adminUsername = localStorage.getItem('adminUsername');
        const isAdminLoggedIn = localStorage.getItem('isAdminLoggedIn');
        
        // If not logged in, redirect to admin login
        if ((!adminToken || !adminUsername) && !isAdminLoggedIn) {
            // Don't redirect if already on login page
            const currentPage = window.location.pathname;
            if (!currentPage.includes('login.html') && 
                !currentPage.includes('forgot-password.html')) {
                // Redirect to admin login
                window.location.href = 'login.html';
            }
        }
    }
    
    // Check auth on page load
    checkAdminAuth();
    
    // Setup logout functionality
    function setupAdminLogout() {
        const logoutButtons = document.querySelectorAll('[href*="logout"], .logout-btn, #logoutBtn, .admin-logout');
        logoutButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Clear all admin session data
                localStorage.removeItem('adminToken');
                localStorage.removeItem('adminUsername');
                localStorage.removeItem('isAdminLoggedIn');
                
                // Redirect to admin login
                window.location.href = 'login.html';
            });
        });
    }
    
    // Setup on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupAdminLogout);
    } else {
        setupAdminLogout();
    }
    
    // Display admin info if available
    function displayAdminInfo() {
        const adminUsername = localStorage.getItem('adminUsername');
        
        // Update admin name displays
        const adminNameElements = document.querySelectorAll('.admin-name, #adminName, .admin-username');
        adminNameElements.forEach(el => {
            if (adminUsername) el.textContent = adminUsername;
        });
    }
    
    displayAdminInfo();
})();
