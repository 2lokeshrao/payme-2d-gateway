// Settlements Management
document.addEventListener('DOMContentLoaded', () => {
    loadSettlements();
});

function loadSettlements() {
    // Settlements data already in HTML
    console.log('Settlements loaded');
}

function viewSettlement(settlementId) {
    alert('Viewing settlement details for: ' + settlementId);
    // In production, open modal with detailed breakdown
}

function exportSettlements() {
    alert('Exporting settlements to CSV...');
    // In production, generate and download CSV file
}

function updateBankAccount() {
    alert('Opening bank account update form...');
    // In production, open modal to update bank details
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'login.html';
    }
}
