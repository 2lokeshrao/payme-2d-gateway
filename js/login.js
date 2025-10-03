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
    
    // Submit form
    try {
        const formData = new FormData();
        formData.append('email', email);
        formData.append('password', password);
        
        const response = await fetch('api/login.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        document.getElementById('spinner').classList.remove('show');
        
        if (data.success) {
            // Store user data in localStorage
            localStorage.setItem('userToken', data.data.token);
            localStorage.setItem('userId', data.data.user_id);
            localStorage.setItem('userName', data.data.name);
            localStorage.setItem('userEmail', data.data.email);
            
            showAlert('success', 'Login successful! Redirecting...');
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1500);
        } else {
            showAlert('error', data.message || 'Login failed. Please check your credentials.');
        }
    } catch (error) {
        document.getElementById('spinner').classList.remove('show');
        showAlert('error', 'An error occurred. Please try again.');
    }
});

function showAlert(type, message) {
    const alert = document.getElementById('alertMessage');
    alert.className = `alert alert-${type} show`;
    alert.textContent = message;
}
