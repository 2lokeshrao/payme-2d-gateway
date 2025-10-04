document.getElementById('adminLoginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));
    
    // Get form values
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    
    // Validation
    let hasError = false;
    
    if (username.length < 3) {
        document.getElementById('usernameError').classList.add('show');
        hasError = true;
    }
    
    if (password.length < 6) {
        document.getElementById('passwordError').classList.add('show');
        hasError = true;
    }
    
    if (hasError) return;
    
    // Show spinner
    document.getElementById('spinner').classList.add('show');
    
    // Simulate API call with timeout
    setTimeout(() => {
        document.getElementById('spinner').classList.remove('show');
        
        // Check credentials (client-side for demo)
        const validCredentials = {
            'admin': 'admin123',
            'admin@payme2d.com': 'admin123'
        };
        
        if (validCredentials[username] && validCredentials[username] === password) {
            // Generate token
            const token = 'admin_token_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            
            // Store in localStorage
            localStorage.setItem('adminToken', token);
            localStorage.setItem('adminId', '1');
            localStorage.setItem('adminName', 'Admin User');
            localStorage.setItem('adminEmail', 'admin@payme2d.com');
            localStorage.setItem('isAdminLoggedIn', 'true');
            
            showAlert('success', 'Login successful! Redirecting to dashboard...');
            
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1500);
        } else {
            showAlert('error', 'Invalid username or password. Use: admin / admin123');
        }
    }, 1000);
});

function showAlert(type, message) {
    const alert = document.getElementById('alertMessage');
    alert.className = `alert alert-${type} show`;
    alert.textContent = message;
}
