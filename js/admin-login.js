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
    
    // Submit form
    try {
        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);
        
        const response = await fetch('../api/admin_login.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        document.getElementById('spinner').classList.remove('show');
        
        if (data.success) {
            localStorage.setItem('adminToken', data.data.token);
            localStorage.setItem('adminId', data.data.admin_id);
            localStorage.setItem('adminName', data.data.username);
            
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
