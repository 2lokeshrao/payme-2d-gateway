// Checkout Widget
let selectedPaymentMethod = 'card';
let selectedBank = null;
let selectedWallet = null;
let selectedCrypto = null;

function showPaymentMethod(method) {
    selectedPaymentMethod = method;
    
    // Update tabs
    document.querySelectorAll('.payment-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    event.target.closest('.payment-tab').classList.add('active');
    
    // Update forms
    document.querySelectorAll('.payment-form').forEach(form => {
        form.classList.remove('active');
    });
    document.getElementById(method + '-form').classList.add('active');
}

function selectBank(bankCode) {
    selectedBank = bankCode;
    document.querySelectorAll('.bank-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    event.target.classList.add('selected');
}

function selectWallet(walletCode) {
    selectedWallet = walletCode;
    document.querySelectorAll('.wallet-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    event.target.classList.add('selected');
}

function selectCrypto(cryptoCode) {
    selectedCrypto = cryptoCode;
    document.querySelectorAll('.crypto-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    event.target.classList.add('selected');
}

async function processPayment(method) {
    // Validate based on payment method
    if (method === 'card') {
        const cardNumber = document.getElementById('cardNumber').value;
        const cardExpiry = document.getElementById('cardExpiry').value;
        const cardCVV = document.getElementById('cardCVV').value;
        const cardName = document.getElementById('cardName').value;
        
        if (!cardNumber || !cardExpiry || !cardCVV || !cardName) {
            alert('Please fill in all card details');
            return;
        }
    } else if (method === 'upi') {
        const upiId = document.getElementById('upiId').value;
        if (!upiId) {
            alert('Please enter your UPI ID');
            return;
        }
    } else if (method === 'netbanking') {
        if (!selectedBank) {
            alert('Please select a bank');
            return;
        }
    } else if (method === 'wallet') {
        if (!selectedWallet) {
            alert('Please select a wallet');
            return;
        }
    } else if (method === 'crypto') {
        if (!selectedCrypto) {
            alert('Please select a cryptocurrency');
            return;
        }
    }
    
    // Show processing
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '⏳ Processing...';
    
    // Simulate payment processing
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // Redirect to success page
    window.location.href = 'payment-success.html?txn=' + generateTxnId();
}

function generateTxnId() {
    return 'TXN' + Date.now() + Math.random().toString(36).substr(2, 9).toUpperCase();
}

// Format card number
document.addEventListener('DOMContentLoaded', () => {
    const cardNumberInput = document.getElementById('cardNumber');
    if (cardNumberInput) {
        cardNumberInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\s/g, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });
    }
    
    const cardExpiryInput = document.getElementById('cardExpiry');
    if (cardExpiryInput) {
        cardExpiryInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.slice(0, 2) + '/' + value.slice(2, 4);
            }
            e.target.value = value;
        });
    }
});
