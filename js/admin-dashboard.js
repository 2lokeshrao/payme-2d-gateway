// Check if admin is logged in
if (!localStorage.getItem('adminToken')) {
    window.location.href = 'login.html';
}

// Logout functionality
document.getElementById('logoutBtn').addEventListener('click', (e) => {
    e.preventDefault();
    localStorage.removeItem('adminToken');
    localStorage.removeItem('adminId');
    localStorage.removeItem('adminName');
    window.location.href = 'login.html';
});

// Load admin dashboard
async function loadAdminDashboard() {
    document.getElementById('spinner').classList.add('show');
    
    try {
        const response = await fetch('../api/admin_stats.php');
        const data = await response.json();
        
        if (data.success) {
            // Update stats
            document.getElementById('totalUsers').textContent = data.stats.total_users;
            document.getElementById('totalTransactions').textContent = data.stats.total_transactions;
            document.getElementById('totalRevenue').textContent = '$' + parseFloat(data.stats.total_revenue).toFixed(2);
            
            const successRate = data.stats.total_transactions > 0 
                ? ((data.stats.successful_transactions / data.stats.total_transactions) * 100).toFixed(1)
                : 0;
            document.getElementById('successRate').textContent = successRate + '%';
            
            // Update payment status
            document.getElementById('successCount').textContent = data.stats.successful_transactions;
            document.getElementById('pendingCount').textContent = data.stats.pending_transactions;
            document.getElementById('failedCount').textContent = data.stats.failed_transactions;
            
            // Update user activity
            document.getElementById('activeUsers').textContent = data.stats.active_users;
            document.getElementById('newUsers').textContent = data.stats.new_users_today;
            document.getElementById('totalRegistered').textContent = data.stats.total_users;
            
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
    const container = document.getElementById('recentTransactions');
    
    if (!transactions || transactions.length === 0) {
        container.innerHTML = '<p style="color: var(--color-text-secondary); text-align: center; padding: 20px;">No transactions yet.</p>';
        return;
    }
    
    let html = '<div class="table-container"><table><thead><tr><th>Transaction ID</th><th>User</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead><tbody>';
    
    transactions.forEach(txn => {
        const statusClass = txn.status === 'success' ? 'badge-success' : 
                           txn.status === 'pending' ? 'badge-pending' : 'badge-failed';
        
        html += `
            <tr>
                <td>${txn.transaction_id}</td>
                <td>${txn.user_name}</td>
                <td>${txn.currency} ${parseFloat(txn.amount).toFixed(2)}</td>
                <td><span class="badge ${statusClass}">${txn.status}</span></td>
                <td>${new Date(txn.created_at).toLocaleDateString()}</td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

// Load dashboard on page load
loadAdminDashboard();
