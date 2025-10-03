let currentStep = 1;
const totalSteps = 6;
const formData = {};

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    updateProgress();
    setupEventListeners();
});

function setupEventListeners() {
    // Next button
    document.getElementById('nextBtn').addEventListener('click', () => {
        if (validateStep(currentStep)) {
            saveStepData(currentStep);
            currentStep++;
            showStep(currentStep);
        }
    });

    // Previous button
    document.getElementById('prevBtn').addEventListener('click', () => {
        currentStep--;
        showStep(currentStep);
    });

    // Form submission
    document.getElementById('registrationForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        if (validateStep(6)) {
            await submitForm();
        }
    });

    // Same address checkbox
    document.getElementById('sameAddress').addEventListener('change', (e) => {
        const businessAddressSection = document.getElementById('businessAddressSection');
        businessAddressSection.style.display = e.target.checked ? 'none' : 'block';
    });

    // File upload handlers
    setupFileUpload('idProofFile', 'idProofPreview');
    setupFileUpload('addressProofFile', 'addressProofPreview');
    setupFileUpload('panCardFile', 'panCardPreview');
    setupFileUpload('businessDocFile', 'businessDocPreview');
    setupFileUpload('bankProofFile', 'bankProofPreview');
}

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.form-step').forEach(s => s.classList.remove('active'));
    
    // Show current step
    document.querySelector(`.form-step[data-step="${step}"]`).classList.add('active');
    
    // Update progress
    updateProgress();
    
    // Update buttons
    document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'block';
    document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'block';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'block' : 'none';
    
    // Show review summary on last step
    if (step === 6) {
        showReviewSummary();
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateProgress() {
    // Update step circles
    document.querySelectorAll('.step').forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove('active', 'completed');
        
        if (stepNum < currentStep) {
            step.classList.add('completed');
            step.querySelector('.step-circle').innerHTML = '✓';
        } else if (stepNum === currentStep) {
            step.classList.add('active');
            step.querySelector('.step-circle').innerHTML = stepNum;
        } else {
            step.querySelector('.step-circle').innerHTML = stepNum;
        }
    });
    
    // Update progress line
    const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressLine').style.width = progressPercentage + '%';
}

function validateStep(step) {
    const stepElement = document.querySelector(`.form-step[data-step="${step}"]`);
    const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#ef4444';
            isValid = false;
        } else {
            input.style.borderColor = '';
        }
    });
    
    // Additional validations
    if (step === 1) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        
        if (password !== confirmPassword) {
            showAlert('error', 'Passwords do not match');
            return false;
        }
        
        if (password.length < 8) {
            showAlert('error', 'Password must be at least 8 characters long');
            return false;
        }
        
        const email = document.getElementById('email').value;
        if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            showAlert('error', 'Please enter a valid email address');
            return false;
        }
    }
    
    if (step === 4) {
        const accountNumber = document.getElementById('accountNumber').value;
        const confirmAccountNumber = document.getElementById('confirmAccountNumber').value;
        
        if (accountNumber !== confirmAccountNumber) {
            showAlert('error', 'Account numbers do not match');
            return false;
        }
    }
    
    if (step === 5) {
        const idProofFile = document.getElementById('idProofFile').files[0];
        const addressProofFile = document.getElementById('addressProofFile').files[0];
        const panCardFile = document.getElementById('panCardFile').files[0];
        const bankProofFile = document.getElementById('bankProofFile').files[0];
        
        if (!idProofFile || !addressProofFile || !panCardFile || !bankProofFile) {
            showAlert('error', 'Please upload all required documents');
            return false;
        }
    }
    
    if (step === 6) {
        const termsAccept = document.getElementById('termsAccept').checked;
        const dataConsent = document.getElementById('dataConsent').checked;
        const accuracyConfirm = document.getElementById('accuracyConfirm').checked;
        
        if (!termsAccept || !dataConsent || !accuracyConfirm) {
            showAlert('error', 'Please accept all required terms and conditions');
            return false;
        }
    }
    
    if (!isValid) {
        showAlert('error', 'Please fill in all required fields');
    }
    
    return isValid;
}

function saveStepData(step) {
    const stepElement = document.querySelector(`.form-step[data-step="${step}"]`);
    const inputs = stepElement.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (input.type === 'checkbox') {
            formData[input.name] = input.checked;
        } else if (input.type === 'file') {
            formData[input.name] = input.files[0];
        } else {
            formData[input.name] = input.value;
        }
    });
}

function setupFileUpload(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    input.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('error', 'File size should not exceed 5MB');
                input.value = '';
                return;
            }
            
            // Show preview
            preview.innerHTML = `
                <div class="uploaded-file">
                    <span class="uploaded-file-name">📄 ${file.name} (${(file.size / 1024).toFixed(2)} KB)</span>
                    <span class="remove-file" onclick="removeFile('${inputId}', '${previewId}')">×</span>
                </div>
            `;
        }
    });
}

function removeFile(inputId, previewId) {
    document.getElementById(inputId).value = '';
    document.getElementById(previewId).innerHTML = '';
}

function showReviewSummary() {
    const summary = document.getElementById('reviewSummary');
    
    summary.innerHTML = `
        <h3 style="margin-bottom: 20px; font-size: 18px;">Application Summary</h3>
        
        <div style="margin-bottom: 24px;">
            <h4 style="font-size: 14px; color: var(--slate-11); margin-bottom: 8px;">Personal Information</h4>
            <p style="margin: 4px 0;"><strong>Name:</strong> ${formData.firstName || ''} ${formData.lastName || ''}</p>
            <p style="margin: 4px 0;"><strong>Email:</strong> ${formData.email || ''}</p>
            <p style="margin: 4px 0;"><strong>Phone:</strong> ${formData.phone || ''}</p>
            <p style="margin: 4px 0;"><strong>Date of Birth:</strong> ${formData.dateOfBirth || ''}</p>
        </div>
        
        <div style="margin-bottom: 24px;">
            <h4 style="font-size: 14px; color: var(--slate-11); margin-bottom: 8px;">Business Details</h4>
            <p style="margin: 4px 0;"><strong>Business Name:</strong> ${formData.businessName || ''}</p>
            <p style="margin: 4px 0;"><strong>Business Type:</strong> ${formData.businessType || ''}</p>
            <p style="margin: 4px 0;"><strong>Category:</strong> ${formData.businessCategory || ''}</p>
            <p style="margin: 4px 0;"><strong>PAN:</strong> ${formData.pan || ''}</p>
            ${formData.gst ? `<p style="margin: 4px 0;"><strong>GST:</strong> ${formData.gst}</p>` : ''}
        </div>
        
        <div style="margin-bottom: 24px;">
            <h4 style="font-size: 14px; color: var(--slate-11); margin-bottom: 8px;">Address</h4>
            <p style="margin: 4px 0;">${formData.address1 || ''}, ${formData.address2 || ''}</p>
            <p style="margin: 4px 0;">${formData.city || ''}, ${formData.state || ''} - ${formData.pincode || ''}</p>
            <p style="margin: 4px 0;">${formData.country || ''}</p>
        </div>
        
        <div style="margin-bottom: 24px;">
            <h4 style="font-size: 14px; color: var(--slate-11); margin-bottom: 8px;">Bank Details</h4>
            <p style="margin: 4px 0;"><strong>Account Holder:</strong> ${formData.accountHolderName || ''}</p>
            <p style="margin: 4px 0;"><strong>Account Number:</strong> ****${formData.accountNumber ? formData.accountNumber.slice(-4) : ''}</p>
            <p style="margin: 4px 0;"><strong>IFSC:</strong> ${formData.ifscCode || ''}</p>
            <p style="margin: 4px 0;"><strong>Bank:</strong> ${formData.bankName || ''}</p>
            ${formData.upiId ? `<p style="margin: 4px 0;"><strong>UPI ID:</strong> ${formData.upiId}</p>` : ''}
        </div>
        
        <div>
            <h4 style="font-size: 14px; color: var(--slate-11); margin-bottom: 8px;">Documents Uploaded</h4>
            <p style="margin: 4px 0;">✓ Identity Proof</p>
            <p style="margin: 4px 0;">✓ Address Proof</p>
            <p style="margin: 4px 0;">✓ PAN Card</p>
            <p style="margin: 4px 0;">✓ Bank Account Proof</p>
            ${formData.businessDocFile ? '<p style="margin: 4px 0;">✓ Business Registration Document</p>' : ''}
        </div>
    `;
}

async function submitForm() {
    // Show spinner
    document.getElementById('spinner').classList.add('show');
    
    // Create FormData object
    const submitData = new FormData();
    
    // Add all form data
    for (const key in formData) {
        submitData.append(key, formData[key]);
    }
    
    try {
        // Simulate API call (replace with actual API endpoint)
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // In production, uncomment this:
        // const response = await fetch('api/register.php', {
        //     method: 'POST',
        //     body: submitData
        // });
        // const data = await response.json();
        
        document.getElementById('spinner').classList.remove('show');
        
        // Show success message
        showAlert('success', 'Application submitted successfully! You will receive a confirmation email shortly.');
        
        // Redirect after 3 seconds
        setTimeout(() => {
            window.location.href = 'login.html';
        }, 3000);
        
    } catch (error) {
        document.getElementById('spinner').classList.remove('show');
        showAlert('error', 'An error occurred. Please try again.');
    }
}

function showAlert(type, message) {
    const alert = document.getElementById('alertMessage');
    alert.className = `alert alert-${type} show`;
    alert.textContent = message;
    
    setTimeout(() => {
        alert.classList.remove('show');
    }, 5000);
}
