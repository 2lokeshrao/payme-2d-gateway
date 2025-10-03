document.getElementById('registerForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));
    
    // Get form values
    const fullName = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    // Validation
    let hasError = false;
    
    if (fullName.length < 2) {
        document.getElementById('nameError').classList.add('show');
        hasError = true;
    }
    
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        document.getElementById('emailError').classList.add('show');
        hasError = true;
    }
    
    if (phone.length < 10) {
        document.getElementById('phoneError').classList.add('show');
        hasError = true;
    }
    
    if (password.length < 6) {
        document.getElementById('passwordError').classList.add('show');
        hasError = true;
    }
    
    if (password !== confirmPassword) {
        document.getElementById('confirmPasswordError').classList.add('show');
        hasError = true;
    }
    
    if (hasError) return;
    
    // Show spinner
    document.getElementById('spinner').classList.add('show');
    
    // Submit form
    try {
        const formData = new FormData();
        formData.append('fullName', fullName);
        formData.append('email', email);
        formData.append('phone', phone);
        formData.append('password', password);
        
        const response = await fetch('api/register.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        document.getElementById('spinner').classList.remove('show');
        
        if (data.success) {
            showAlert('success', 'Account created successfully! Redirecting to login...');
            setTimeout(() => {
                window.location.href = 'login.html';
            }, 2000);
        } else {
            showAlert('error', data.message || 'Registration failed. Please try again.');
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
