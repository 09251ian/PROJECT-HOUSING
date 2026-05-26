<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Users | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    @keyframes slideIn {
      from { transform: translateX(-100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes highlight {
      0% { background-color: rgba(40, 167, 69, 0.3); }
      100% { background-color: transparent; }
    }
    .new-user-row {
      animation: slideIn 0.5s ease-out, highlight 2s ease-out;
    }
    .realtime-alert {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 300px;
      animation: slideIn 0.3s ease-out;
    }
  </style>
</head>
<body class="app-dark">
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/admin/dashboard') ?>">🏠 House Admin</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/properties') ?>">Properties</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/offers') ?>">Offers</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <!-- Real-time notification area -->
  <div id="realtime-alert-area" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-white mb-0">Users Management</h3>
    <div class="text-muted">
      <i class="bi bi-people-fill"></i> 
      <span id="total-buyers"><?= count($buyers ?? []) ?></span> Buyers | 
      <span id="total-sellers"><?= count($sellers ?? []) ?></span> Sellers
    </div>
  </div>

  <h5 class="text-white-50 mt-4">
    <i class="bi bi-person-badge-fill"></i> Buyers 
    <span class="badge bg-info ms-2" id="buyers-count-badge"><?= count($buyers ?? []) ?></span>
  </h5>
  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Bio</th>
          <th>Registered</th>
        </tr>
      </thead>
      <tbody id="buyers-table-body">
        <?php foreach (($buyers ?? []) as $u): ?>
          <tr data-user-id="<?= esc($u['id']) ?>" data-user-role="buyer" class="user-row">
            <td><?= esc($u['id']) ?></td>
            <td><i class="bi bi-person-circle"></i> <?= esc($u['name']) ?></td>
            <td><?= esc($u['email']) ?></td>
            <td><?= esc($u['contact'] ?? 'N/A') ?></td>
            <td style="max-width:320px;"><?= esc($u['bio'] ?? 'N/A') ?></td>
            <td><small><?= esc($u['created_at'] ?? 'N/A') ?></small></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h5 class="text-white-50 mt-4">
    <i class="bi bi-briefcase-fill"></i> Sellers 
    <span class="badge bg-warning ms-2" id="sellers-count-badge"><?= count($sellers ?? []) ?></span>
  </h5>
  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Contact</th>
          <th>Bio</th>
          <th>Registered</th>
        </tr>
      </thead>
      <tbody id="sellers-table-body">
        <?php foreach (($sellers ?? []) as $u): ?>
          <tr data-user-id="<?= esc($u['id']) ?>" data-user-role="seller" class="user-row">
            <td><?= esc($u['id']) ?></td>
            <td><i class="bi bi-person-circle"></i> <?= esc($u['name']) ?></td>
            <td><?= esc($u['email']) ?></td>
            <td><?= esc($u['contact'] ?? 'N/A') ?></td>
            <td style="max-width:320px;"><?= esc($u['bio'] ?? 'N/A') ?></td>
            <td><small><?= esc($u['created_at'] ?? 'N/A') ?></small></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- SOCKET.IO -->
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return 'Just now';
    try {
        const date = new Date(dateString);
        return date.toLocaleString();
    } catch(e) {
        return dateString;
    }
}

// Helper function to show real-time alert
function showRealtimeAlert(message, type = 'success') {
    const alertArea = document.getElementById('realtime-alert-area');
    if (!alertArea) return;
    
    const alertClass = type === 'success' ? 'alert-success' : 
                      (type === 'danger' ? 'alert-danger' : 
                      (type === 'warning' ? 'alert-warning' : 'alert-info'));
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show shadow-lg" role="alert" style="animation: slideIn 0.3s ease-out;">
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : type === 'danger' ? 'exclamation-triangle-fill' : 'info-circle-fill'} me-2"></i>
            <strong>${type === 'success' ? 'New User!' : 'Update!'}</strong><br>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertArea.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alerts = alertArea.querySelectorAll('.alert');
        if (alerts.length) {
            const alert = alerts[0];
            alert.classList.remove('show');
            setTimeout(() => {
                if (alert && alert.parentNode) alert.remove();
            }, 300);
        }
    }, 5000);
}

// Helper function to add user to table
function addUserToTable(user) {
    const rowHTML = `
        <tr data-user-id="${user.id}" data-user-role="${user.role}" class="user-row new-user-row">
            <td>${escapeHtml(user.id)}</td>
            <td><i class="bi bi-person-circle"></i> ${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td>${escapeHtml(user.contact || 'N/A')}</td>
            <td style="max-width:320px;">${escapeHtml(user.bio || 'N/A')}</td>
            <td><small>${formatDate(user.created_at)}</small></td>
        </tr>
    `;
    
    if (user.role === 'buyer') {
        const buyersTable = document.getElementById('buyers-table-body');
        if (buyersTable) {
            buyersTable.insertAdjacentHTML('afterbegin', rowHTML);
            
            // Update counters
            const buyersCount = document.getElementById('buyers-count-badge');
            const totalBuyers = document.getElementById('total-buyers');
            if (buyersCount) {
                let current = parseInt(buyersCount.innerText) || 0;
                buyersCount.innerText = current + 1;
            }
            if (totalBuyers) {
                let current = parseInt(totalBuyers.innerText) || 0;
                totalBuyers.innerText = current + 1;
            }
        }
    } else if (user.role === 'seller') {
        const sellersTable = document.getElementById('sellers-table-body');
        if (sellersTable) {
            sellersTable.insertAdjacentHTML('afterbegin', rowHTML);
            
            // Update counters
            const sellersCount = document.getElementById('sellers-count-badge');
            const totalSellers = document.getElementById('total-sellers');
            if (sellersCount) {
                let current = parseInt(sellersCount.innerText) || 0;
                sellersCount.innerText = current + 1;
            }
            if (totalSellers) {
                let current = parseInt(totalSellers.innerText) || 0;
                totalSellers.innerText = current + 1;
            }
        }
    }
    
    // Scroll to the new user
    const newRow = document.querySelector(`tr[data-user-id="${user.id}"]`);
    if (newRow) {
        newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Connect to WebSocket server
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true,
    reconnectionAttempts: 10,
    reconnectionDelay: 1000
});

socket.on('connect', () => {
    console.log('✅ Admin users page connected to websocket server');
    showRealtimeAlert('Connected to real-time updates', 'info');
    
    // Register as admin to receive notifications
    socket.emit('register', 'admin');
});

socket.on('disconnect', () => {
    console.log('❌ Disconnected from websocket server');
    showRealtimeAlert('Disconnected from real-time updates. Page will still work normally.', 'warning');
});

socket.on('connect_error', (error) => {
    console.error('Connection error:', error);
    showRealtimeAlert('Unable to connect to real-time updates. Check if server is running.', 'danger');
});

// Listen for new user registrations
socket.on('new-user', function(user) {
    console.log('👤 New user registered:', user);
    
    // Check if user already exists in the table
    const existingUser = document.querySelector(`tr[data-user-id="${user.id}"]`);
    if (existingUser) {
        console.log('User already exists in table, skipping:', user.id);
        return;
    }
    
    // Add user to the appropriate table
    addUserToTable(user);
    
    // Show notification
    const message = `
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-${user.role === 'buyer' ? 'person-badge' : 'briefcase'} fs-4"></i>
            <div>
                <strong>${escapeHtml(user.name)}</strong><br>
                <small>${user.role.toUpperCase()} just registered!</small><br>
                <small class="text-muted">${escapeHtml(user.email)}</small>
            </div>
        </div>
    `;
    showRealtimeAlert(message, 'success');
});

// Optional: Listen for user deletions or updates (if needed)
socket.on('user-updated', function(user) {
    console.log('✏️ User updated:', user);
    
    const userRow = document.querySelector(`tr[data-user-id="${user.id}"]`);
    if (userRow) {
        // Update user information
        const cells = userRow.cells;
        if (cells[1]) cells[1].innerHTML = `<i class="bi bi-person-circle"></i> ${escapeHtml(user.name)}`;
        if (cells[2]) cells[2].innerText = escapeHtml(user.email);
        if (cells[3]) cells[3].innerText = escapeHtml(user.contact || 'N/A');
        if (cells[4]) cells[4].innerHTML = escapeHtml(user.bio || 'N/A');
        
        // Highlight the updated row
        userRow.classList.add('new-user-row');
        setTimeout(() => {
            userRow.classList.remove('new-user-row');
        }, 2000);
        
        showRealtimeAlert(`User ${escapeHtml(user.name)} information updated`, 'info');
    }
});

// Optional: Manual refresh button (add if needed)
function refreshUsers() {
    showRealtimeAlert('Refreshing user list...', 'info');
    setTimeout(() => {
        location.reload();
    }, 500);
}

// Add refresh button to the page (optional)
const refreshButton = document.createElement('button');
refreshButton.innerHTML = '<i class="bi bi-arrow-repeat"></i> Refresh';
refreshButton.className = 'btn btn-outline-primary btn-sm ms-3';
refreshButton.onclick = refreshUsers;
document.querySelector('.d-flex.justify-content-between.align-items-center.mb-3').appendChild(refreshButton);

console.log('Admin users page ready - listening for new user registrations');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>