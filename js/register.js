// Multi-step registration form handler
let currentStep = 1;
const totalSteps = 6;

// Initialize form
document.addEventListener('DOMContentLoaded', () => {
    showStep(1);
    setupEventListeners();
});

function setupEventListeners() {
    // Next button
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', handleNext);
    }
    
    // Previous button
    const prevBtn = document.getElementById('prevBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', handlePrevious);
    }
    
    // Submit button
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.addEventListener('click', handleSubmit);
    }
    
    // Same address checkbox
    const sameAddressCheckbox = document.getElementById('sameAddress');
    if (sameAddressCheckbox) {
        sameAddressCheckbox.addEventListener('change', handleSameAddress);
    }
}

function showStep(step) {
    // Hide all steps
    for (let i = 1; i <= totalSteps; i++) {
        const stepElement = document.getElementById(`step${i}`);
        if (stepElement) {
            stepElement.style.display = 'none';
        }
    }
    
    // Show current step
    const currentStepElement = document.getElementById(`step${step}`);
    if (currentStepElement) {
        currentStepElement.style.display = 'block';
    }
    
    // Update progress indicators
    updateProgressIndicators(step);
    
    // Update buttons
    updateButtons(step);
    
    currentStep = step;
}

function updateProgressIndicators(step) {
    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.querySelector(`.step-indicator[data-step="${i}"]`);
        if (indicator) {
            if (i < step) {
                indicator.classList.add('completed');
                indicator.classList.remove('active');
            } else if (i === step) {
                indicator.classList.add('active');
                indicator.classList.remove('completed');
            } else {
                indicator.classList.remove('active', 'completed');
            }
        }
    }
}

function updateButtons(step) {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (prevBtn) {
        prevBtn.style.display = step === 1 ? 'none' : 'inline-block';
    }
    
    if (nextBtn) {
        nextBtn.style.display = step === totalSteps ? 'none' : 'inline-block';
    }
    
    if (submitBtn) {
        submitBtn.style.display = step === totalSteps ? 'inline-block' : 'none';
    }
}

function handleNext() {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            showStep(currentStep + 1);
        }
    }
}

function handlePrevious() {
    if (currentStep > 1) {
        showStep(currentStep - 1);
    }
}

function validateStep(step) {
    const stepElement = document.getElementById(`step${step}`);
    if (!stepElement) return true;
    
    const requiredFields = stepElement.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    if (!isValid) {
        showAlert('error', 'Please fill in all required fields');
    }
    
    return isValid;
}

function handleSameAddress(e) {
    const businessAddress = document.getElementById('businessAddress');
    if (businessAddress) {
        businessAddress.style.display = e.target.checked ? 'none' : 'block';
    }
}

async function handleSubmit() {
    // Validate all steps
    for (let i = 1; i <= totalSteps; i++) {
        if (!validateStep(i)) {
            showStep(i);
            return;
        }
    }
    
    // Check terms acceptance
    const termsCheckbox = document.getElementById('termsAccept');
    if (termsCheckbox && !termsCheckbox.checked) {
        showAlert('error', 'Please accept the Terms & Conditions');
        return;
    }
    
    // Show loading
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';
    }
    
    // Simulate API call
    setTimeout(() => {
        // Get form data
        const formData = collectFormData();
        
        // Store in localStorage (demo)
        const merchantId = 'mer_' + Date.now();
        localStorage.setItem('pendingMerchant', JSON.stringify({
            id: merchantId,
            ...formData,
            status: 'pending',
            created_at: new Date().toISOString()
        }));
        
        showAlert('success', 'Registration submitted successfully! Redirecting...');
        
        setTimeout(() => {
            window.location.href = 'login.html?registered=true';
        }, 2000);
    }, 1500);
}

function collectFormData() {
    return {
        // Personal Info
        firstName: document.getElementById('firstName')?.value || '',
        lastName: document.getElementById('lastName')?.value || '',
        email: document.getElementById('email')?.value || '',
        phone: document.getElementById('phone')?.value || '',
        dob: document.getElementById('dob')?.value || '',
        gender: document.getElementById('gender')?.value || '',
        
        // Business Details
        businessType: document.getElementById('businessType')?.value || '',
        businessName: document.getElementById('businessName')?.value || '',
        tradeName: document.getElementById('tradeName')?.value || '',
        businessCategory: document.getElementById('businessCategory')?.value || '',
        website: document.getElementById('website')?.value || '',
        gstNumber: document.getElementById('gstNumber')?.value || '',
        panNumber: document.getElementById('panNumber')?.value || '',
        
        // Address
        address1: document.getElementById('address1')?.value || '',
        address2: document.getElementById('address2')?.value || '',
        city: document.getElementById('city')?.value || '',
        state: document.getElementById('state')?.value || '',
        pincode: document.getElementById('pincode')?.value || '',
        country: document.getElementById('country')?.value || '',
        
        // Bank Details
        accountHolder: document.getElementById('accountHolder')?.value || '',
        accountNumber: document.getElementById('accountNumber')?.value || '',
        ifscCode: document.getElementById('ifscCode')?.value || '',
        bankName: document.getElementById('bankName')?.value || '',
        branchName: document.getElementById('branchName')?.value || '',
        accountType: document.getElementById('accountType')?.value || '',
        upiId: document.getElementById('upiId')?.value || ''
    };
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        z-index: 10000;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}
