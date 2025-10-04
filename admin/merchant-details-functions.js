// Merchant Details Page Functions
// PayMe 2D Gateway Admin Panel

// Get merchant ID from URL
function getMerchantId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id') || 'MERCH001';
}

// Edit Details Button
function editMerchantDetails() {
    const merchantId = getMerchantId();
    
    // Show confirmation
    if (confirm('Do you want to edit merchant details?')) {
        // In a real application, this would open an edit form
        // For now, we'll show an alert
        alert('Edit functionality will open a form to modify merchant details.\n\nMerchant ID: ' + merchantId);
        
        // You can redirect to an edit page:
        // window.location.href = 'merchant-edit.html?id=' + merchantId;
    }
}

// Approve KYC Button
function approveKYC() {
    const merchantId = getMerchantId();
    
    if (confirm('Are you sure you want to approve KYC for this merchant?')) {
        // Simulate API call
        showNotification('KYC approved successfully!', 'success');
        
        // Update status badge
        updateKYCStatus('approved');
        
        // In real app, make API call:
        // fetch('/api/merchants/' + merchantId + '/approve-kyc', { method: 'POST' })
    }
}

// View API Keys Button
function viewAPIKeys() {
    const merchantId = getMerchantId();
    
    // Redirect to merchant API keys page
    window.location.href = 'merchant-api-keys.html?id=' + merchantId;
}

// Suspend Merchant Button
function suspendMerchant() {
    const merchantId = getMerchantId();
    
    if (confirm('Are you sure you want to suspend this merchant?\n\nThis will temporarily disable their account.')) {
        // Simulate API call
        showNotification('Merchant suspended successfully!', 'warning');
        
        // Update status
        updateMerchantStatus('suspended');
        
        // In real app:
        // fetch('/api/merchants/' + merchantId + '/suspend', { method: 'POST' })
    }
}

// Block Merchant Button
function blockMerchant() {
    const merchantId = getMerchantId();
    
    const reason = prompt('Please enter reason for blocking this merchant:');
    
    if (reason && reason.trim() !== '') {
        if (confirm('Are you sure you want to BLOCK this merchant?\n\nThis action will permanently disable their account.')) {
            // Simulate API call
            showNotification('Merchant blocked successfully!', 'error');
            
            // Update status
            updateMerchantStatus('blocked');
            
            // In real app:
            // fetch('/api/merchants/' + merchantId + '/block', { 
            //     method: 'POST',
            //     body: JSON.stringify({ reason: reason })
            // })
        }
    } else if (reason !== null) {
        alert('Please provide a reason for blocking.');
    }
}

// Helper: Update KYC Status
function updateKYCStatus(status) {
    const badges = document.querySelectorAll('.status-badges .badge');
    badges.forEach(badge => {
        if (badge.textContent.includes('KYC')) {
            if (status === 'approved') {
                badge.textContent = 'KYC Verified';
                badge.style.background = '#43e97b';
            }
        }
    });
}

// Helper: Update Merchant Status
function updateMerchantStatus(status) {
    const badges = document.querySelectorAll('.status-badges .badge');
    badges.forEach(badge => {
        if (badge.textContent.includes('Active') || badge.textContent.includes('Suspended') || badge.textContent.includes('Blocked')) {
            if (status === 'suspended') {
                badge.textContent = 'Suspended';
                badge.style.background = '#ffa502';
            } else if (status === 'blocked') {
                badge.textContent = 'Blocked';
                badge.style.background = '#ff6b6b';
            }
        }
    });
}

// Helper: Show Notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification notification-' + type;
    notification.textContent = message;
    
    // Style
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.padding = '15px 20px';
    notification.style.borderRadius = '8px';
    notification.style.color = 'white';
    notification.style.fontWeight = '600';
    notification.style.zIndex = '10000';
    notification.style.boxShadow = '0 4px 12px rgba(0,0,0,0.15)';
    notification.style.animation = 'slideIn 0.3s ease';
    
    // Colors based on type
    if (type === 'success') {
        notification.style.background = '#43e97b';
    } else if (type === 'error') {
        notification.style.background = '#ff6b6b';
    } else if (type === 'warning') {
        notification.style.background = '#ffa502';
    } else {
        notification.style.background = '#667eea';
    }
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Merchant Details Functions Loaded');
    console.log('Merchant ID:', getMerchantId());
});
