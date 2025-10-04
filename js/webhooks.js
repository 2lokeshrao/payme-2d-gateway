// Webhooks Management
document.addEventListener('DOMContentLoaded', () => {
    loadWebhookConfig();
});

function loadWebhookConfig() {
    console.log('Webhook configuration loaded');
}

function saveWebhookConfig() {
    const webhookUrl = document.getElementById('webhookUrl').value;
    
    if (!webhookUrl) {
        alert('Please enter a webhook URL');
        return;
    }
    
    // Validate URL
    try {
        new URL(webhookUrl);
    } catch (e) {
        alert('Please enter a valid URL');
        return;
    }
    
    // Save configuration
    alert('Webhook configuration saved successfully!');
}

function testWebhook() {
    const webhookUrl = document.getElementById('webhookUrl').value;
    
    if (!webhookUrl) {
        alert('Please enter a webhook URL first');
        return;
    }
    
    alert('Sending test webhook to: ' + webhookUrl);
    // In production, send actual test webhook
}

function regenerateSecret() {
    if (confirm('Are you sure you want to regenerate the webhook secret? This will invalidate the current secret.')) {
        const newSecret = 'whsec_' + generateRandomString(32);
        document.getElementById('webhookSecret').value = newSecret;
        alert('New webhook secret generated!');
    }
}

function refreshLogs() {
    alert('Refreshing webhook logs...');
    // In production, reload logs from API
}

function viewWebhookDetails(webhookId) {
    alert('Viewing webhook details for: ' + webhookId);
    // In production, show detailed payload and response
}

function retryWebhook(webhookId) {
    if (confirm('Retry sending this webhook?')) {
        alert('Retrying webhook: ' + webhookId);
        // In production, trigger webhook retry
    }
}

function generateRandomString(length) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let result = '';
    for (let i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'login.html';
    }
}
