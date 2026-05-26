<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Offers | House System</title>
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
      0% { background-color: rgba(255, 193, 7, 0.3); }
      100% { background-color: transparent; }
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }
    .new-offer-row {
      animation: slideIn 0.5s ease-out, highlight 2s ease-out;
    }
    .realtime-alert {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      min-width: 350px;
      animation: slideIn 0.3s ease-out;
      cursor: pointer;
    }
    .realtime-alert:hover {
      transform: translateX(-5px);
      transition: transform 0.3s ease;
    }
    .badge-status {
      font-size: 0.85rem;
      padding: 5px 12px;
    }
    .badge-pending { background-color: #ffc107; color: #000; }
    .badge-accepted { background-color: #28a745; }
    .badge-rejected { background-color: #dc3545; }
    .offer-count-badge {
      animation: pulse 0.5s ease-out;
    }
  </style>
</head>
<body class="app-dark">
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/admin/dashboard') ?>">🏠 House Admin</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/users') ?>">Users</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/properties') ?>">Properties</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <!-- Real-time notification area -->
  <div id="realtime-alert-area" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="text-white mb-0">
        <i class="bi bi-tag-fill text-warning"></i> Offers Management
      </h3>
      <p class="text-muted small mt-2">Real-time offer updates from buyers</p>
    </div>
    <div class="text-muted">
      <i class="bi bi-envelope-paper-fill"></i> 
      <span id="total-offers"><?= count($offers ?? []) ?></span> Total Offers
      <span class="ms-3">
        <i class="bi bi-hourglass-split text-warning"></i> 
        <span id="pending-offers"><?= count(array_filter($offers ?? [], function($o) { return $o['status'] === 'pending'; })) ?></span> Pending
      </span>
      <span class="ms-3">
        <i class="bi bi-check-circle-fill text-success"></i> 
        <span id="accepted-offers"><?= count(array_filter($offers ?? [], function($o) { return $o['status'] === 'accepted'; })) ?></span> Accepted
      </span>
      <span class="ms-3">
        <i class="bi bi-x-circle-fill text-danger"></i> 
        <span id="rejected-offers"><?= count(array_filter($offers ?? [], function($o) { return $o['status'] === 'rejected'; })) ?></span> Rejected
      </span>
    </div>
  </div>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Property</th>
          <th>Buyer</th>
          <th>Amount</th>
          <th>Status</th>
          <th>Created</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="offers-table-body">
        <?php foreach (($offers ?? []) as $o): ?>
          <tr data-offer-id="<?= esc($o['id']) ?>" data-offer-status="<?= esc($o['status']) ?>" class="offer-row">
            <td><?= esc($o['id']) ?></td>
            <td><i class="bi bi-house-door-fill text-info"></i> <?= esc($o['property_title'] ?? 'N/A') ?></td>
            <td><i class="bi bi-person-circle"></i> <?= esc($o['buyer_name'] ?? 'N/A') ?></td>
            <td class="fw-bold text-success">₱<?= number_format((float)$o['amount'], 2) ?></td>
            <td>
              <span class="badge badge-status badge-<?= esc($o['status']) ?> status-badge">
                <?= strtoupper(esc($o['status'])) ?>
              </span>
            </td>
            <td><small><?= esc($o['created_at'] ?? 'N/A') ?></small></td>
            <td>
              <?php if ($o['status'] === 'pending'): ?>
                <button class="btn btn-sm btn-success accept-offer" data-offer-id="<?= esc($o['id']) ?>" data-property-title="<?= esc($o['property_title'] ?? '') ?>" data-buyer-name="<?= esc($o['buyer_name'] ?? '') ?>" data-amount="<?= esc($o['amount']) ?>">
                  <i class="bi bi-check-lg"></i> Accept
                </button>
                <button class="btn btn-sm btn-danger reject-offer" data-offer-id="<?= esc($o['id']) ?>" data-property-title="<?= esc($o['property_title'] ?? '') ?>" data-buyer-name="<?= esc($o['buyer_name'] ?? '') ?>" data-amount="<?= esc($o['amount']) ?>">
                  <i class="bi bi-x-lg"></i> Reject
                </button>
              <?php else: ?>
                <span class="text-muted small">—</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Empty state -->
  <div id="empty-state" style="display: none;" class="text-center py-5">
    <i class="bi bi-inbox" style="font-size: 4rem; color: #6c757d;"></i>
    <h4 class="text-muted mt-3">No Offers Yet</h4>
    <p class="text-muted">When buyers make offers, they will appear here instantly</p>
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

// Helper function to format currency
function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

// Helper function to get status badge class
function getStatusBadgeClass(status) {
    return `badge badge-status badge-${status}`;
}

// Helper function to show real-time alert
function showRealtimeAlert(message, type = 'success', onClick = null) {
    const alertArea = document.getElementById('realtime-alert-area');
    if (!alertArea) return;
    
    const icons = {
        success: 'check-circle-fill',
        danger: 'exclamation-triangle-fill',
        warning: 'exclamation-triangle-fill',
        info: 'info-circle-fill'
    };
    
    const alertClass = type === 'success' ? 'alert-success' : 
                      (type === 'danger' ? 'alert-danger' : 
                      (type === 'warning' ? 'alert-warning' : 'alert-info'));
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show shadow-lg" role="alert" style="animation: slideIn 0.3s ease-out;">
            <i class="bi bi-${icons[type] || 'info-circle-fill'} me-2"></i>
            <strong>${type === 'success' ? 'New Offer!' : type === 'warning' ? 'Offer Update!' : 'Info:'}</strong><br>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertArea.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-dismiss after 6 seconds
    setTimeout(() => {
        const alerts = alertArea.querySelectorAll('.alert');
        if (alerts.length) {
            const alert = alerts[0];
            alert.classList.remove('show');
            setTimeout(() => {
                if (alert && alert.parentNode) alert.remove();
            }, 300);
        }
    }, 6000);
}

// Update counters
function updateCounters() {
    const rows = document.querySelectorAll('.offer-row');
    let total = rows.length;
    let pending = 0;
    let accepted = 0;
    let rejected = 0;
    
    rows.forEach(row => {
        const status = row.getAttribute('data-offer-status');
        if (status === 'pending') pending++;
        else if (status === 'accepted') accepted++;
        else if (status === 'rejected') rejected++;
    });
    
    const totalSpan = document.getElementById('total-offers');
    const pendingSpan = document.getElementById('pending-offers');
    const acceptedSpan = document.getElementById('accepted-offers');
    const rejectedSpan = document.getElementById('rejected-offers');
    
    if (totalSpan) totalSpan.innerText = total;
    if (pendingSpan) {
        pendingSpan.innerText = pending;
        pendingSpan.classList.add('offer-count-badge');
        setTimeout(() => pendingSpan.classList.remove('offer-count-badge'), 500);
    }
    if (acceptedSpan) acceptedSpan.innerText = accepted;
    if (rejectedSpan) rejectedSpan.innerText = rejected;
    
    // Check empty state
    const emptyState = document.getElementById('empty-state');
    if (emptyState) {
        emptyState.style.display = total === 0 ? 'block' : 'none';
    }
}

// Add offer to table
function addOfferToTable(offer) {
    // Check if offer already exists
    if (document.querySelector(`tr[data-offer-id="${offer.id}"]`)) {
        console.log('Offer already exists, skipping:', offer.id);
        return;
    }
    
    const statusBadgeClass = getStatusBadgeClass(offer.status);
    const actionsHtml = offer.status === 'pending' ? `
        <button class="btn btn-sm btn-success accept-offer" data-offer-id="${offer.id}" data-property-title="${escapeHtml(offer.property_title)}" data-buyer-name="${escapeHtml(offer.buyer_name)}" data-amount="${offer.amount}">
            <i class="bi bi-check-lg"></i> Accept
        </button>
        <button class="btn btn-sm btn-danger reject-offer" data-offer-id="${offer.id}" data-property-title="${escapeHtml(offer.property_title)}" data-buyer-name="${escapeHtml(offer.buyer_name)}" data-amount="${offer.amount}">
            <i class="bi bi-x-lg"></i> Reject
        </button>
    ` : '<span class="text-muted small">—</span>';
    
    const rowHTML = `
        <tr data-offer-id="${offer.id}" data-offer-status="${offer.status}" class="offer-row new-offer-row">
            <td>${escapeHtml(offer.id)}</td>
            <td><i class="bi bi-house-door-fill text-info"></i> ${escapeHtml(offer.property_title)}</td>
            <td><i class="bi bi-person-circle"></i> ${escapeHtml(offer.buyer_name)}</td>
            <td class="fw-bold text-success">${formatCurrency(offer.amount)}</td>
            <td>
                <span class="${statusBadgeClass}">
                    ${offer.status.toUpperCase()}
                </span>
            </td>
            <td><small>${formatDate(offer.created_at)}</small></td>
            <td>${actionsHtml}</td>
        </tr>
    `;
    
    const tableBody = document.getElementById('offers-table-body');
    if (tableBody) {
        tableBody.insertAdjacentHTML('afterbegin', rowHTML);
        
        // Attach event listeners to new buttons
        attachOfferButtonListeners();
        
        // Scroll to the new offer
        const newRow = document.querySelector(`tr[data-offer-id="${offer.id}"]`);
        if (newRow) {
            newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        // Update counters
        updateCounters();
    }
}

// Update offer status in table
function updateOfferStatus(offerId, newStatus) {
    const offerRow = document.querySelector(`tr[data-offer-id="${offerId}"]`);
    if (!offerRow) return;
    
    // Update status attribute
    offerRow.setAttribute('data-offer-status', newStatus);
    
    // Update status badge
    const statusBadge = offerRow.querySelector('.status-badge');
    if (statusBadge) {
        statusBadge.className = `badge badge-status badge-${newStatus}`;
        statusBadge.innerText = newStatus.toUpperCase();
    }
    
    // Update actions column
    const actionsCell = offerRow.querySelector('td:last-child');
    if (actionsCell) {
        if (newStatus === 'pending') {
            const offerIdAttr = offerRow.getAttribute('data-offer-id');
            const propertyTitle = offerRow.querySelector('td:nth-child(2)').innerText.replace(/[^\w\s]/g, '').trim();
            const buyerName = offerRow.querySelector('td:nth-child(3)').innerText.replace(/[^\w\s]/g, '').trim();
            const amountCell = offerRow.querySelector('td:nth-child(4)');
            const amount = amountCell ? amountCell.innerText.replace('₱', '').replace(/,/g, '') : '0';
            
            actionsCell.innerHTML = `
                <button class="btn btn-sm btn-success accept-offer" data-offer-id="${offerIdAttr}" data-property-title="${escapeHtml(propertyTitle)}" data-buyer-name="${escapeHtml(buyerName)}" data-amount="${amount}">
                    <i class="bi bi-check-lg"></i> Accept
                </button>
                <button class="btn btn-sm btn-danger reject-offer" data-offer-id="${offerIdAttr}" data-property-title="${escapeHtml(propertyTitle)}" data-buyer-name="${escapeHtml(buyerName)}" data-amount="${amount}">
                    <i class="bi bi-x-lg"></i> Reject
                </button>
            `;
        } else {
            actionsCell.innerHTML = '<span class="text-muted small">—</span>';
        }
    }
    
    // Highlight the updated row
    offerRow.classList.add('new-offer-row');
    setTimeout(() => {
        offerRow.classList.remove('new-offer-row');
    }, 2000);
    
    // Update counters
    updateCounters();
}

// Handle offer acceptance/rejection
function handleOfferAction(offerId, action, propertyTitle, buyerName, amount) {
    fetch('/admin/update-offer-status', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `offer_id=${offerId}&status=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateOfferStatus(offerId, action);
            showRealtimeAlert(
                `Offer ${action} for ${escapeHtml(propertyTitle)} from ${escapeHtml(buyerName)} for ${formatCurrency(amount)}`,
                action === 'accepted' ? 'success' : 'warning'
            );
        } else {
            showRealtimeAlert(data.message || 'Failed to update offer status', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showRealtimeAlert('Network error. Please try again.', 'danger');
    });
}

// Attach event listeners to offer action buttons
function attachOfferButtonListeners() {
    // Accept buttons
    document.querySelectorAll('.accept-offer').forEach(btn => {
        btn.removeEventListener('click', acceptHandler);
        btn.addEventListener('click', acceptHandler);
    });
    
    // Reject buttons
    document.querySelectorAll('.reject-offer').forEach(btn => {
        btn.removeEventListener('click', rejectHandler);
        btn.addEventListener('click', rejectHandler);
    });
}

function acceptHandler(e) {
    e.preventDefault();
    const btn = e.currentTarget;
    const offerId = btn.getAttribute('data-offer-id');
    const propertyTitle = btn.getAttribute('data-property-title');
    const buyerName = btn.getAttribute('data-buyer-name');
    const amount = btn.getAttribute('data-amount');
    
    if (confirm(`Accept offer of ${formatCurrency(amount)} from ${buyerName} for "${propertyTitle}"?`)) {
        handleOfferAction(offerId, 'accepted', propertyTitle, buyerName, amount);
    }
}

function rejectHandler(e) {
    e.preventDefault();
    const btn = e.currentTarget;
    const offerId = btn.getAttribute('data-offer-id');
    const propertyTitle = btn.getAttribute('data-property-title');
    const buyerName = btn.getAttribute('data-buyer-name');
    const amount = btn.getAttribute('data-amount');
    
    if (confirm(`Reject offer of ${formatCurrency(amount)} from ${buyerName} for "${propertyTitle}"?`)) {
        handleOfferAction(offerId, 'rejected', propertyTitle, buyerName, amount);
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
    console.log('✅ Admin offers page connected to websocket server');
    showRealtimeAlert('Connected to real-time updates', 'info');
    
    // Register as admin
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

// Listen for new offers
socket.on('new-offer', function(offer) {
    console.log('💰 New offer received:', offer);
    
    // Add to table
    addOfferToTable(offer);
    
    // Show notification
    const message = `
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-cash-stack fs-4 text-warning"></i>
            <div>
                <strong>${escapeHtml(offer.buyer_name)}</strong> made an offer<br>
                <strong>${formatCurrency(offer.amount)}</strong> for<br>
                <strong>"${escapeHtml(offer.property_title)}"</strong>
            </div>
        </div>
    `;
    showRealtimeAlert(message, 'success');
    
    // Play notification sound (optional - browsers may block)
    try {
        const audio = new Audio('/assets/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Audio play prevented:', e));
    } catch(e) {}
});

// Listen for offer status updates
socket.on('offer-status-updated', function(update) {
    console.log('📋 Offer status update:', update);
    
    updateOfferStatus(update.id, update.status);
    
    const message = `
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-${update.status === 'accepted' ? 'check-circle-fill text-success' : 'x-circle-fill text-danger'} fs-4"></i>
            <div>
                Offer for <strong>${escapeHtml(update.property_title)}</strong><br>
                from <strong>${escapeHtml(update.buyer_name)}</strong><br>
                was <strong>${update.status.toUpperCase()}</strong>
            </div>
        </div>
    `;
    showRealtimeAlert(message, update.status === 'accepted' ? 'success' : 'warning');
});

// Initial setup
document.addEventListener('DOMContentLoaded', () => {
    attachOfferButtonListeners();
    updateCounters();
    
    // Add refresh button
    const refreshBtn = document.createElement('button');
    refreshBtn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Refresh';
    refreshBtn.className = 'btn btn-outline-primary btn-sm ms-3';
    refreshBtn.onclick = () => location.reload();
    document.querySelector('.d-flex.justify-content-between.align-items-center.mb-3 div:first-child').appendChild(refreshBtn);
});

console.log('Admin offers page ready - listening for new offers');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>