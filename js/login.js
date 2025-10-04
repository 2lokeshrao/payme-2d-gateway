// Demo user credentials
const demoUsers = {
    'test@merchant.com': {
        password: 'password123',
        token: 'demo_token_merchant_123',
        user_id: 'mer_123',
        name: 'Test Merchant',
        email: 'test@merchant.com',
        role: 'merchant'
    },
    'demo@merchant.com': {
        password: 'demo123',
        token: 'demo_token_merchant_456',
        user_id: 'mer_456',
        name: 'Demo Merchant',
        email: 'demo@merchant.com',
        role: 'merchant'
    },
    'merchant@payme.com': {
        password: 'merchant123',
        token: 'demo_token_merchant_789',
        user_id: 'mer_789',
        name: 'PayMe Merchant',
        email: 'merchant@payme.com',
        role: 'merchant'
    }
};

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));
    
    // Get form values
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    
    // Validation
    let hasError = false;
    
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        document.getElementById('emailError').classList.add('show');
        hasError = true;
    }
    
    if (password.length < 6) {
        document.getElementById('passwordError').classList.add('show');
        hasError = true;
    }
    
    if (hasError) return;
    
    // Show spinner
    document.getElementById('spinner').classList.add('show');
    
    // Simulate API call delay
    setTimeout(() => {
        document.getElementById('spinner').classList.remove('show');
        
        // Check demo credentials
        const user = demoUsers[email];
        
        if (user && user.password === password) {
            // Store user data in localStorage
            localStorage.setItem('userToken', user.token);
            localStorage.setItem('userId', user.user_id);
            localStorage.setItem('userName', user.name);
            localStorage.setItem('userEmail', user.email);
            localStorage.setItem('userRole', user.role);
            
            showAlert('success', 'Login successful! Redirecting...');
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1500);
        } else {
            showAlert('error', 'Invalid email or password. Please try again.');
        }
    }, 1000);
});

function showAlert(type, message) {
    const alert = document.getElementById('alertMessage');
    alert.className = `alert alert-${type} show`;
    alert.textContent = message;
}
