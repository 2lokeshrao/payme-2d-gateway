// Payment Methods Configuration
let paymentMethods = {
    card: true,
    upi: true,
    netbanking: true,
    wallet: true,
    crypto: false,
    emi: false
};

document.addEventListener('DOMContentLoaded', () => {
    loadPaymentMethods();
});

function loadPaymentMethods() {
    // Load saved configuration
    document.getElementById('cardEnabled').checked = paymentMethods.card;
    document.getElementById('upiEnabled').checked = paymentMethods.upi;
    document.getElementById('netbankingEnabled').checked = paymentMethods.netbanking;
    document.getElementById('walletEnabled').checked = paymentMethods.wallet;
    document.getElementById('cryptoEnabled').checked = paymentMethods.crypto;
    document.getElementById('emiEnabled').checked = paymentMethods.emi;
}

function togglePaymentMethod(method, enabled) {
    paymentMethods[method] = enabled;
    console.log(`${method} payment method ${enabled ? 'enabled' : 'disabled'}`);
}

async function savePaymentMethods() {
    // Show loading
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '⏳ Saving...';
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000));
    
    // Success
    btn.innerHTML = '✅ Saved!';
    showAlert('success', 'Payment methods configuration saved successfully');
    
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = '💾 Save Configuration';
    }, 2000);
}

function showAlert(type, message) {
    const alert = document.getElementById('alertMessage');
    alert.className = `alert alert-${type} show`;
    alert.textContent = message;
    setTimeout(() => alert.classList.remove('show'), 3000);
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'login.html';
    }
}
