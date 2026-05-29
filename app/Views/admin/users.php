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
    /* Global Search Bar */
    .global-search-bar {
        background: transparent;
        border-radius: 10px;
        padding: 10px 15px;
        margin-bottom: 20px;
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
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/audit') ?>">Audit</a>
      <a class="btn btn-outline-light btn-sm active" href="<?= base_url('/admin/users') ?>">Users</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <!-- Real-time notification area -->
  <div id="realtime-alert-area" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="text-white mb-0">Users Management</h3>
    <div class="text-white">
      <i class="bi bi-people-fill"></i> 
      <span id="total-buyers" style="color: #ffffff;"><?= $buyersPager->total ?? 0 ?></span> Buyers | 
      <span id="total-sellers" style="color: #ffffff;"><?= $sellersPager->total ?? 0 ?></span> Sellers
    </div>
  </div>

  <!-- ========== GLOBAL SEARCH BAR ========== -->
  <div class="global-search-bar">
    <form method="get" action="<?= base_url('/admin/users') ?>" class="row g-2">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text bg-dark text-secondary border-secondary">
            <i class="bi bi-search-heart"></i>
          </span>
          <input type="text" 
                 name="global_search" 
                 class="form-control bg-dark text-white border-secondary" 
                 placeholder="Global search: Search by name, email, or contact across all users..." 
                 value="<?= esc($globalSearch ?? '') ?>">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </div>
      <div class="col-md-2">
        <?php if (!empty($globalSearch)): ?>
          <a href="<?= base_url('/admin/users') ?>" class="btn btn-outline-secondary w-100">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        <?php endif; ?>
      </div>
    </form>
    <?php if (!empty($globalSearch)): ?>
      <div class="mt-2 text-white small">
        <i class="bi bi-info-circle"></i> Global search results for: "<strong class="text-warning"><?= esc($globalSearch) ?></strong>"
      </div>
    <?php endif; ?>
  </div>
  <!-- ========== END GLOBAL SEARCH BAR ========== -->

  <!-- BUYERS SECTION -->
  <h5 class="text-white mt-4">
    <i class="bi bi-person-badge-fill"></i> Buyers 
    <span class="badge bg-info ms-2" id="buyers-count-badge"><?= $buyersPager->total ?? 0 ?></span>
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
        <?php if (!empty($buyers)): ?>
          <?php foreach ($buyers as $u): ?>
            <tr data-user-id="<?= esc($u['id']) ?>" data-user-role="buyer" class="user-row">
              <td><?= esc($u['id']) ?></td>
              <td><i class="bi bi-person-circle"></i> <?= esc($u['name']) ?></td>
              <td><?= esc($u['email']) ?></td>
              <td><?= esc($u['contact'] ?? 'N/A') ?></td>
              <td style="max-width:320px;"><?= esc($u['bio'] ?? 'N/A') ?></td>
              <td><small><?= esc($u['created_at'] ?? 'N/A') ?></small></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center text-muted">No buyers found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Buyers Pagination -->
  <?php if (isset($buyersPager) && $buyersPager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-3">
      <nav aria-label="Buyers pagination">
          <ul class="pagination">
              <?php if ($buyersPager->currentPage > 1): ?>
                  <li class="page-item"><a class="page-link" href="?buyers_page=<?= $buyersPager->currentPage - 1 ?>&sellers_page=<?= $sellersPager->currentPage ?? 1 ?>&global_search=<?= urlencode($globalSearch ?? '') ?>">&laquo; Prev</a></li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $buyersPager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $buyersPager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?buyers_page=<?= $i ?>&sellers_page=<?= $sellersPager->currentPage ?? 1 ?>&global_search=<?= urlencode($globalSearch ?? '') ?>"><?= $i ?></a>
                  </li>
              <?php endfor; ?>

              <?php if ($buyersPager->currentPage < $buyersPager->lastPage): ?>
                  <li class="page-item"><a class="page-link" href="?buyers_page=<?= $buyersPager->currentPage + 1 ?>&sellers_page=<?= $sellersPager->currentPage ?? 1 ?>&global_search=<?= urlencode($globalSearch ?? '') ?>">Next &raquo;</a></li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>
  <div class="text-center text-white small mb-3">
      Showing <strong><?= $buyersPager->firstItem ?></strong> to <strong><?= $buyersPager->lastItem ?></strong> of <strong><?= $buyersPager->total ?></strong> buyers
  </div>
  <?php endif; ?>

  <!-- SELLERS SECTION -->
  <h5 class="text-white mt-4">
    <i class="bi bi-briefcase-fill"></i> Sellers 
    <span class="badge bg-warning ms-2" id="sellers-count-badge"><?= $sellersPager->total ?? 0 ?></span>
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
        <?php if (!empty($sellers)): ?>
          <?php foreach ($sellers as $u): ?>
            <tr data-user-id="<?= esc($u['id']) ?>" data-user-role="seller" class="user-row">
              <td><?= esc($u['id']) ?></td>
              <td><i class="bi bi-person-circle"></i> <?= esc($u['name']) ?></td>
              <td><?= esc($u['email']) ?></td>
              <td><?= esc($u['contact'] ?? 'N/A') ?></td>
              <td style="max-width:320px;"><?= esc($u['bio'] ?? 'N/A') ?></td>
              <td><small><?= esc($u['created_at'] ?? 'N/A') ?></small></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center text-muted">No sellers found</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Sellers Pagination -->
  <?php if (isset($sellersPager) && $sellersPager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-3">
      <nav aria-label="Sellers pagination">
          <ul class="pagination">
              <?php if ($sellersPager->currentPage > 1): ?>
                  <li class="page-item"><a class="page-link" href="?buyers_page=<?= $buyersPager->currentPage ?? 1 ?>&sellers_page=<?= $sellersPager->currentPage - 1 ?>&global_search=<?= urlencode($globalSearch ?? '') ?>">&laquo; Prev</a></li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $sellersPager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $sellersPager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?buyers_page=<?= $buyersPager->currentPage ?? 1 ?>&sellers_page=<?= $i ?>&global_search=<?= urlencode($globalSearch ?? '') ?>"><?= $i ?></a>
                  </li>
              <?php endfor; ?>

              <?php if ($sellersPager->currentPage < $sellersPager->lastPage): ?>
                  <li class="page-item"><a class="page-link" href="?buyers_page=<?= $buyersPager->currentPage ?? 1 ?>&sellers_page=<?= $sellersPager->currentPage + 1 ?>&global_search=<?= urlencode($globalSearch ?? '') ?>">Next &raquo;</a></li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>
  <div class="text-center text-white small mb-3">
      Showing <strong><?= $sellersPager->firstItem ?></strong> to <strong><?= $sellersPager->lastItem ?></strong> of <strong><?= $sellersPager->total ?></strong> sellers
  </div>
  <?php endif; ?>

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
    socket.emit('register', 'admin');
});

socket.on('disconnect', () => {
    console.log('❌ Disconnected from websocket server');
    showRealtimeAlert('Disconnected from real-time updates', 'warning');
});

socket.on('connect_error', (error) => {
    console.error('Connection error:', error);
    showRealtimeAlert('Unable to connect to real-time updates', 'danger');
});

// Listen for new user registrations
socket.on('new-user', function(user) {
    console.log('👤 New user registered:', user);
    
    const existingUser = document.querySelector(`tr[data-user-id="${user.id}"]`);
    if (existingUser) {
        console.log('User already exists, skipping');
        return;
    }
    
    // For pagination, we need to reload to show the user in correct page
    showRealtimeAlert(`New ${user.role.toUpperCase()} registered: ${escapeHtml(user.name)}`, 'success');
    setTimeout(() => location.reload(), 2000);
});

console.log('Admin users page ready - listening for new user registrations');
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>