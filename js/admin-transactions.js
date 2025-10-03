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

// Load transactions
async function loadTransactions() {
    document.getElementById('spinner').classList.add('show');
    
    try {
        const response = await fetch('../api/admin_get_transactions.php');
        const data = await response.json();
        
        document.getElementById('spinner').classList.remove('show');
        
        if (data.success && data.transactions.length > 0) {
            displayTransactions(data.transactions);
        } else {
            document.getElementById('noTransactions').style.display = 'block';
        }
    } catch (error) {
        document.getElementById('spinner').classList.remove('show');
        document.getElementById('noTransactions').style.display = 'block';
    }
}

function displayTransactions(transactions) {
    const tbody = document.getElementById('transactionsBody');
    const table = document.getElementById('transactionsTable');
    
    let html = '';
    
    transactions.forEach(txn => {
        const statusClass = txn.status === 'success' ? 'badge-success' : 
                           txn.status === 'pending' ? 'badge-pending' : 'badge-failed';
        
        const date = new Date(txn.created_at);
        const formattedDate = date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        
        html += `
            <tr>
                <td>${txn.transaction_id}</td>
                <td>${txn.user_name}</td>
                <td>${txn.currency} ${parseFloat(txn.amount).toFixed(2)}</td>
                <td>${txn.card_type} **** ${txn.card_number_last4}</td>
                <td><span class="badge ${statusClass}">${txn.status.toUpperCase()}</span></td>
                <td>${formattedDate}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    table.style.display = 'block';
}

// Load transactions on page load
loadTransactions();
