// Check if user is logged in
if (!localStorage.getItem('userToken')) {
    window.location.href = 'login.html';
}

// Logout functionality
document.getElementById('logoutBtn').addEventListener('click', (e) => {
    e.preventDefault();
    localStorage.removeItem('userToken');
    localStorage.removeItem('userId');
    localStorage.removeItem('userName');
    window.location.href = 'login.html';
});

// Card number formatting
document.getElementById('cardNumber').addEventListener('input', (e) => {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Expiry date formatting
document.getElementById('expiryDate').addEventListener('input', (e) => {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    e.target.value = value;
});

// CVV validation
document.getElementById('cvv').addEventListener('input', (e) => {
    e.target.value = e.target.value.replace(/\D/g, '');
});

// Form submission
document.getElementById('paymentForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    // Clear previous errors
    document.querySelectorAll('.form-error').forEach(el => el.classList.remove('show'));
    
    // Get form values
    const amount = document.getElementById('amount').value;
    const currency = document.getElementById('currency').value;
    const cardHolderName = document.getElementById('cardHolderName').value.trim();
    const cardNumber = document.getElementById('cardNumber').value.replace(/\s/g, '');
    const expiryDate = document.getElementById('expiryDate').value;
    const cvv = document.getElementById('cvv').value;
    const billingAddress = document.getElementById('billingAddress').value.trim();
    const billingCity = document.getElementById('billingCity').value.trim();
    const billingZip = document.getElementById('billingZip').value.trim();
    const billingCountry = document.getElementById('billingCountry').value;
    
    // Validation
    let hasError = false;
    
    if (amount < 1) {
        document.getElementById('amountError').classList.add('show');
        hasError = true;
    }
    
    if (cardHolderName.length < 3) {
        document.getElementById('cardHolderError').classList.add('show');
        hasError = true;
    }
    
    if (cardNumber.length < 13 || cardNumber.length > 19) {
        document.getElementById('cardNumberError').classList.add('show');
        hasError = true;
    }
    
    if (!expiryDate.match(/^\d{2}\/\d{2}$/)) {
        document.getElementById('expiryError').classList.add('show');
        hasError = true;
    }
    
    if (cvv.length < 3 || cvv.length > 4) {
        document.getElementById('cvvError').classList.add('show');
        hasError = true;
    }
    
    if (hasError) return;
    
    // Show spinner
    document.getElementById('spinner').classList.add('show');
    
    // Submit payment
    try {
        const formData = new FormData();
        formData.append('user_id', localStorage.getItem('userId'));
        formData.append('amount', amount);
        formData.append('currency', currency);
        formData.append('card_holder_name', cardHolderName);
        formData.append('card_number', cardNumber);
        formData.append('expiry_date', expiryDate);
        formData.append('cvv', cvv);
        formData.append('billing_address', billingAddress);
        formData.append('billing_city', billingCity);
        formData.append('billing_zip', billingZip);
        formData.append('billing_country', billingCountry);
        
        const response = await fetch('api/process_payment.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        document.getElementById('spinner').classList.remove('show');
        
        if (data.success) {
            showAlert('success', 'Payment processed successfully! Redirecting...');
            setTimeout(() => {
                window.location.href = 'transactions.html';
            }, 2000);
        } else {
            showAlert('error', data.message || 'Payment failed. Please try again.');
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
