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

// Load transactions
async function loadTransactions() {
    document.getElementById('spinner').classList.add('show');
    
    try {
        const userId = localStorage.getItem('userId');
        const response = await fetch(`api/get_transactions.php?user_id=${userId}`);
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
                <td>${formattedDate}</td>
                <td>${txn.currency} ${parseFloat(txn.amount).toFixed(2)}</td>
                <td>**** **** **** ${txn.card_number_last4}</td>
                <td><span class="badge ${statusClass}">${txn.status.toUpperCase()}</span></td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    table.style.display = 'block';
}

// Load transactions on page load
loadTransactions();
