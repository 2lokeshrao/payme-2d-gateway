// API Keys Management
let apiKeys = {
    test: [],
    live: []
};

document.addEventListener('DOMContentLoaded', () => {
    loadAPIKeys();
});

async function loadAPIKeys() {
    // Simulate loading API keys
    apiKeys.test = [
        {
            id: 1,
            key: 'pk_test_4eC39HqLyjWDarjtT1zdp7dc',
            secret: 'sk_test_YOUR_STRIPE_SECRET_KEY_HERE',
            created: '2025-01-15',
            lastUsed: '2025-10-03'
        }
    ];
    
    displayAPIKeys();
}

function displayAPIKeys() {
    // Display Test Keys
    const testKeysContainer = document.getElementById('testKeys');
    if (apiKeys.test.length === 0) {
        testKeysContainer.innerHTML = '<p class="text-muted">No test keys generated yet</p>';
    } else {
        testKeysContainer.innerHTML = apiKeys.test.map(key => `
            <div class="api-key-item">
                <div class="api-key-info">
                    <div class="api-key-label">API Key:</div>
                    <div class="api-key-value">
                        <code>${key.key}</code>
                        <button onclick="copyToClipboard('${key.key}')" class="btn-icon">📋</button>
                    </div>
                    <div class="api-key-meta">
                        Created: ${key.created} | Last used: ${key.lastUsed}
                    </div>
                </div>
                <div class="api-key-actions">
                    <button onclick="deleteAPIKey(${key.id}, 'test')" class="btn btn-danger btn-sm">Delete</button>
                </div>
            </div>
        `).join('');
    }
    
    // Display Live Keys
    const liveKeysContainer = document.getElementById('liveKeys');
    if (apiKeys.live.length === 0) {
        liveKeysContainer.innerHTML = '<p class="text-muted">No live keys generated yet</p>';
    } else {
        liveKeysContainer.innerHTML = apiKeys.live.map(key => `
            <div class="api-key-item">
                <div class="api-key-info">
                    <div class="api-key-label">API Key:</div>
                    <div class="api-key-value">
                        <code>${key.key}</code>
                        <button onclick="copyToClipboard('${key.key}')" class="btn-icon">📋</button>
                    </div>
                    <div class="api-key-meta">
                        Created: ${key.created} | Last used: ${key.lastUsed}
                    </div>
                </div>
                <div class="api-key-actions">
                    <button onclick="deleteAPIKey(${key.id}, 'live')" class="btn btn-danger btn-sm">Delete</button>
                </div>
            </div>
        `).join('');
    }
}

function generateAPIKey(type) {
    const apiKey = 'pk_' + type + '_' + generateRandomString(32);
    const apiSecret = 'sk_' + type + '_' + generateRandomString(32);
    
    // Show modal with new keys
    document.getElementById('newApiKey').value = apiKey;
    document.getElementById('newApiSecret').value = apiSecret;
    document.getElementById('generateKeyModal').style.display = 'block';
    
    // Add to list
    const newKey = {
        id: Date.now(),
        key: apiKey,
        secret: apiSecret,
        created: new Date().toISOString().split('T')[0],
        lastUsed: 'Never'
    };
    
    apiKeys[type].push(newKey);
    displayAPIKeys();
}

function deleteAPIKey(id, type) {
    if (confirm('Are you sure you want to delete this API key? This action cannot be undone.')) {
        apiKeys[type] = apiKeys[type].filter(key => key.id !== id);
        displayAPIKeys();
        showAlert('success', 'API key deleted successfully');
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

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('success', 'Copied to clipboard!');
    });
}

function closeModal() {
    document.getElementById('generateKeyModal').style.display = 'none';
}

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
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
