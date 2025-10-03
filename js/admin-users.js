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

// Load users
async function loadUsers() {
    document.getElementById('spinner').classList.add('show');
    
    try {
        const response = await fetch('../api/admin_get_users.php');
        const data = await response.json();
        
        document.getElementById('spinner').classList.remove('show');
        
        if (data.success && data.users.length > 0) {
            displayUsers(data.users);
        } else {
            document.getElementById('noUsers').style.display = 'block';
        }
    } catch (error) {
        document.getElementById('spinner').classList.remove('show');
        document.getElementById('noUsers').style.display = 'block';
    }
}

function displayUsers(users) {
    const tbody = document.getElementById('usersBody');
    const table = document.getElementById('usersTable');
    
    let html = '';
    
    users.forEach(user => {
        const statusClass = user.status === 'active' ? 'badge-success' : 
                           user.status === 'inactive' ? 'badge-pending' : 'badge-failed';
        
        const date = new Date(user.created_at);
        const formattedDate = date.toLocaleDateString();
        
        html += `
            <tr>
                <td>${user.id}</td>
                <td>${user.full_name}</td>
                <td>${user.email}</td>
                <td>${user.phone}</td>
                <td><span class="badge ${statusClass}">${user.status.toUpperCase()}</span></td>
                <td>${formattedDate}</td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    table.style.display = 'block';
}

// Load users on page load
loadUsers();
