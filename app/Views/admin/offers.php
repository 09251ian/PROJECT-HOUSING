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
    /* Pagination Styles */
    .pagination {
        margin: 0;
    }
    .pagination .page-link {
        background-color: #16213e;
        border-color: #0f3460;
        color: white;
        padding: 8px 16px;
        font-size: 14px;
    }
    .pagination .page-link:hover {
        background-color: #0f3460;
        color: white;
        border-color: #e94560;
    }
    .pagination .page-item.active .page-link {
        background-color: #e94560;
        border-color: #e94560;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        background-color: #16213e;
        color: #6c757d;
        border-color: #0f3460;
    }

    /* Stats text colors */
    .stats-text {
        color: #ffffff;
    }

    .stats-number {
        color: #e94560;
        font-weight: bold;
    }

    .stats-label {
        color: #adb5bd;
    }

    /* Or make individual stat colors */
    .stat-total {
        color: #ffffff;
    }

    .stat-pending {
        color: #ffc107;
    }

    .stat-accepted {
        color: #28a745;
    }

    .stat-rejected {
        color: #dc3545;
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
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/audit') ?>">Audit</a>
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
      <p class="text-white small mt-2">Real-time offer updates from buyers</p>
    </div>

    <div class="text-muted">
      <i class="bi bi-envelope-paper-fill text-white"></i> 
      <strong><span id="total-offers" style="color: #ffffff;"><?= $pager->total ?? 0 ?></span><span style="color: #ffffff"> Total Offers</span></strong>
      <span class="ms-3" style="color: #ffc107;">
        <i class="bi bi-hourglass-split text-warning"></i> 
        <span id="pending-offers">0</span> Pending
      </span>
      <span class="ms-3" style="color: #28a745;">
        <i class="bi bi-check-circle-fill text-success"></i> 
        <span id="accepted-offers">0</span> Accepted
      </span>
      <span class="ms-3" style="color: #dc3545;">
        <i class="bi bi-x-circle-fill text-danger"></i> 
        <span id="rejected-offers">0</span> Rejected
      </span>
    </div>
  </div>

    <!-- SEARCH BAR - LEFT ALIGNED -->
    <div class="row mb-4">
        <div class="col-md-6">
        <form method="get" action="<?= base_url('/admin/offers') ?>" class="d-flex gap-2">
            <div class="input-group">
            <span class="input-group-text bg-dark text-secondary border-secondary">
                <i class="bi bi-search"></i>
            </span>
            <input type="text" 
                    name="search" 
                    class="form-control bg-dark text-white border-secondary" 
                    placeholder="Search by property title, buyer name, or amount..." 
                    value="<?= esc($search ?? '') ?>">
            <button class="btn btn-primary" type="submit">
                Search
            </button>
            </div>
            <?php if (!empty($search)): ?>
            <a href="<?= base_url('/admin/offers') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle"></i> Clear
            </a>
            <?php endif; ?>
        </form>
        <?php if (!empty($search)): ?>
            <div class="mt-2 text-muted small">
            <i class="bi bi-info-circle"></i> Showing results for: "<strong class="text-warning"><?= esc($search) ?></strong>"
            </div>
        <?php endif; ?>
        </div>
    </div>
    <!-- END SEARCH BAR -->

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

  <!-- PAGINATION -->
  <?php if (isset($pager) && $pager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-4">
      <nav aria-label="Page navigation">
          <ul class="pagination">
              <?php if ($pager->currentPage > 1): ?>
                  <li class="page-item">
                      <a class="page-link" href="?page=<?= $pager->currentPage - 1 ?>" aria-label="Previous">
                          <span aria-hidden="true">&laquo; Prev</span>
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled">
                      <span class="page-link">&laquo; Prev</span>
                  </li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $pager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $pager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>">
                          <?= $i ?>
                      </a>
                  </li>
              <?php endfor; ?>

              <?php if ($pager->currentPage < $pager->lastPage): ?>
                  <li class="page-item">
                      <a class="page-link" href="?page=<?= $pager->currentPage + 1 ?>" aria-label="Next">
                          <span aria-hidden="true">Next &raquo;</span>
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled">
                      <span class="page-link">Next &raquo;</span>
                  </li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>

  <!-- Showing results info -->
  <div class="text-center text-white small mt-2">
      Showing <strong><?= $pager->firstItem ?></strong> to <strong><?= $pager->lastItem ?></strong> 
      of <strong><?= $pager->total ?></strong> offers
  </div>
  <?php endif; ?>

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
// Helper functions (keep your existing helper functions here)
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatCurrency(amount) {
    return '₱' + parseFloat(amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function formatDate(dateString) {
    if (!dateString) return 'Just now';
    try {
        const date = new Date(dateString);
        return date.toLocaleString();
    } catch(e) {
        return dateString;
    }
}

function getStatusBadgeClass(status) {
    return `badge badge-status badge-${status}`;
}

function showRealtimeAlert(message, type = 'success') {
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
    
    const emptyState = document.getElementById('empty-state');
    if (emptyState) {
        emptyState.style.display = total === 0 ? 'block' : 'none';
    }
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
            // Reload page to reflect changes
            showRealtimeAlert(`Offer ${action} for ${escapeHtml(propertyTitle)} from ${escapeHtml(buyerName)} for ${formatCurrency(amount)}`, action === 'accepted' ? 'success' : 'warning');
            setTimeout(() => location.reload(), 1500);
        } else {
            showRealtimeAlert(data.message || 'Failed to update offer status', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showRealtimeAlert('Network error. Please try again.', 'danger');
    });
}

// Attach event listeners
function attachOfferButtonListeners() {
    document.querySelectorAll('.accept-offer').forEach(btn => {
        btn.onclick = function(e) {
            e.preventDefault();
            const offerId = this.getAttribute('data-offer-id');
            const propertyTitle = this.getAttribute('data-property-title');
            const buyerName = this.getAttribute('data-buyer-name');
            const amount = this.getAttribute('data-amount');
            
            if (confirm(`Accept offer of ${formatCurrency(amount)} from ${buyerName} for "${propertyTitle}"?`)) {
                handleOfferAction(offerId, 'accepted', propertyTitle, buyerName, amount);
            }
        };
    });
    
    document.querySelectorAll('.reject-offer').forEach(btn => {
        btn.onclick = function(e) {
            e.preventDefault();
            const offerId = this.getAttribute('data-offer-id');
            const propertyTitle = this.getAttribute('data-property-title');
            const buyerName = this.getAttribute('data-buyer-name');
            const amount = this.getAttribute('data-amount');
            
            if (confirm(`Reject offer of ${formatCurrency(amount)} from ${buyerName} for "${propertyTitle}"?`)) {
                handleOfferAction(offerId, 'rejected', propertyTitle, buyerName, amount);
            }
        };
    });
}

// Connect to WebSocket
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true
});

socket.on('connect', () => {
    console.log('✅ Connected to WebSocket');
    socket.emit('register', 'admin');
    showRealtimeAlert('Connected to real-time updates', 'info');
});

socket.on('new-offer', function(offer) {
    console.log('💰 New offer received:', offer);
    showRealtimeAlert(`New offer of ${formatCurrency(offer.amount)} from ${escapeHtml(offer.buyer_name)} for ${escapeHtml(offer.property_title)}`, 'success');
    setTimeout(() => location.reload(), 1500);
});

socket.on('offer-status-updated', function(update) {
    console.log('📋 Offer status update:', update);
    showRealtimeAlert(`Offer ${update.status.toUpperCase()} for ${escapeHtml(update.property_title)}`, update.status === 'accepted' ? 'success' : 'warning');
    setTimeout(() => location.reload(), 1500);
});

// Initial setup
document.addEventListener('DOMContentLoaded', () => {
    attachOfferButtonListeners();
    updateCounters();
});

console.log('Admin offers page ready');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>