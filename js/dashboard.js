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

// Load user data
async function loadDashboard() {
    const userName = localStorage.getItem('userName') || 'User';
    document.getElementById('userName').textContent = userName;
    
    document.getElementById('spinner').classList.add('show');
    
    try {
        const userId = localStorage.getItem('userId');
        const response = await fetch(`api/get_user_stats.php?user_id=${userId}`);
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('totalTransactions').textContent = data.stats.total_transactions;
            document.getElementById('successfulPayments').textContent = data.stats.successful_payments;
            document.getElementById('totalAmount').textContent = '$' + parseFloat(data.stats.total_amount).toFixed(2);
            document.getElementById('pendingPayments').textContent = data.stats.pending_payments;
            
            // Load recent transactions
            loadRecentTransactions(data.recent_transactions);
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
    } finally {
        document.getElementById('spinner').classList.remove('show');
    }
}

function loadRecentTransactions(transactions) {
    const container = document.getElementById('transactionsContainer');
    
    if (!transactions || transactions.length === 0) {
        container.innerHTML = '<p style="color: var(--color-text-secondary); text-align: center; padding: 20px;">No transactions yet. <a href="payment.html" style="color: var(--color-accent);">Make your first payment</a></p>';
        return;
    }
    
    let html = '<div class="table-container"><table><thead><tr><th>Transaction ID</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>';
    
    transactions.forEach(txn => {
        const statusClass = txn.status === 'success' ? 'badge-success' : 
                           txn.status === 'pending' ? 'badge-pending' : 'badge-failed';
        
        html += `
            <tr>
                <td>${txn.transaction_id}</td>
                <td>${new Date(txn.created_at).toLocaleDateString()}</td>
                <td>${txn.currency} ${parseFloat(txn.amount).toFixed(2)}</td>
                <td><span class="badge ${statusClass}">${txn.status}</span></td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Load dashboard on page load
loadDashboard();
