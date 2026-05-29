<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Audit | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    .badge-create { background-color: #28a745; }
    .badge-update { background-color: #ffc107; color: #000; }
    .badge-delete { background-color: #dc3545; }
    .badge-login { background-color: #17a2b8; }
    .badge-logout { background-color: #6c757d; }
    .badge-register { background-color: #20c997; }
    .badge-archive { background-color: #fd7e14; }
    .badge-unarchive { background-color: #20c997; }
    .old-value { color: #dc3545; text-decoration: line-through; }
    .new-value { color: #28a745; }
    .section-title { border-left: 4px solid #e94560; padding-left: 15px; margin: 25px 0 15px 0; }
    .pagination { margin: 0; }
    .pagination .page-link {
        background-color: #16213e;
        border-color: #0f3460;
        color: white;
        padding: 6px 12px;
        font-size: 13px;
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
    .search-bar {
        background: transparent;
        border-radius: 10px;
        padding: 10px 15px;
        margin-bottom: 15px;
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
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/offers') ?>">Offers</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-outline-light btn-sm active" href="<?= base_url('/admin/audit') ?>">Audit</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="text-white mb-0">
      <i class="bi bi-journal-text"></i> Audit Logs
    </h3>
  </div>

  <!-- ========== GLOBAL SEARCH BAR ========== -->
  <div class="search-bar mb-4">
    <form method="get" action="<?= base_url('/admin/audit') ?>" class="row g-2">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-dark text-secondary border-secondary">
            <i class="bi bi-search-heart"></i>
          </span>
          <input type="text" 
                 name="global_search" 
                 class="form-control bg-dark text-white border-secondary" 
                 placeholder="Global search: Search by user ID, role, activity, property title..." 
                 value="<?= esc($globalSearch ?? '') ?>">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
      </div>
      <div class="col-md-2">
        <?php if (!empty($globalSearch)): ?>
          <a href="<?= base_url('/admin/audit') ?>" class="btn btn-outline-secondary w-100">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        <?php endif; ?>
      </div>
    </form>
    <?php if (!empty($globalSearch)): ?>
      <div class="mt-2 text-white-50 small">
        <i class="bi bi-info-circle"></i> Global search results for: "<strong class="text-warning"><?= esc($globalSearch) ?></strong>"
      </div>
    <?php endif; ?>
  </div>
  <!-- ========== END GLOBAL SEARCH BAR ========== -->

  <!-- ========== TABLE 1: LOGIN/LOGOUT HISTORY ========== -->
  <div class="section-title">
    <h4 class="text-white mb-0">
      <i class="bi bi-box-arrow-in-right"></i> Login / Logout History
    </h4>
    <p class="text-white small">Track user sessions and authentication activities</p>
  </div>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>Time</th>
          <th>User ID</th>
          <th>Role</th>
          <th>Activity</th>
          <th>Details</th>
        </tr>
      </thead>
      <tbody id="login-logout-table">
        <?php if (empty($loginLogs)): ?>
          <tr><td colspan="5" class="text-center text-white">No login/logout records found</td></tr>
        <?php else: ?>
          <?php foreach ($loginLogs as $l): ?>
            <tr>
              <td><small><?= date('M d, Y h:i A', strtotime($l['created_at'] ?? 'now')) ?></small></td>
              <td><strong><?= esc($l['actor_user_id'] ?? '?') ?></strong></td>
              <td>
                <span class="badge bg-<?= $l['actor_role'] === 'admin' ? 'danger' : ($l['actor_role'] === 'seller' ? 'warning' : 'info') ?>">
                  <?= strtoupper(esc($l['actor_role'] ?? 'GUEST')) ?>
                </span>
              </td>
              <td>
                <span class="badge badge-<?= $l['activity_type'] ?? 'unknown' ?>">
                  <?= strtoupper(esc($l['activity_type'] ?? '')) ?>
                </span>
              </td>
              <td>
                <?php
                $meta = json_decode($l['metadata'] ?? '{}', true);
                if ($l['activity_type'] === 'login' && isset($meta['email'])): ?>
                  <i class="bi bi-envelope"></i> <?= esc($meta['email']) ?>
                <?php elseif ($l['activity_type'] === 'register'): ?>
                  <i class="bi bi-person-plus"></i> New user: <strong><?= esc($meta['name'] ?? '') ?></strong><br>
                  <small>Email: <?= esc($meta['email'] ?? '') ?> | Role: <?= esc($meta['role'] ?? '') ?></small>
                <?php else: ?>
                  <span class="text-white">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination for Login/Logout Table -->
  <?php if (isset($loginPager) && $loginPager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-3 mb-4">
      <nav aria-label="Login/Logout pagination">
          <ul class="pagination">
              <?php if ($loginPager->currentPage > 1): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage - 1 ?>&property_page=<?= $propertyPager->currentPage ?? 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          &laquo; Prev
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $loginPager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $loginPager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?login_page=<?= $i ?>&property_page=<?= $propertyPager->currentPage ?? 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          <?= $i ?>
                      </a>
                  </li>
              <?php endfor; ?>

              <?php if ($loginPager->currentPage < $loginPager->lastPage): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage + 1 ?>&property_page=<?= $propertyPager->currentPage ?? 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          Next &raquo;
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>
  <div class="text-center text-white small mb-4">
      Showing <strong><?= $loginPager->firstItem ?></strong> to <strong><?= $loginPager->lastItem ?></strong> 
      of <strong><?= $loginPager->total ?></strong> records
  </div>
  <?php endif; ?>

  <!-- ========== TABLE 2: PROPERTY CRUD HISTORY ========== -->
  <div class="section-title">
    <h4 class="text-white mb-0">
      <i class="bi bi-building"></i> Property CRUD History
    </h4>
    <p class="text-white small">Track property creations, updates, deletions, and archive status</p>
  </div>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>Time</th>
          <th>Property</th>
          <th>Action</th>
          <th>Changed By</th>
          <th>Changes / Details</th>
        </tr>
      </thead>
      <tbody id="property-crud-table">
        <?php if (empty($propertyLogs)): ?>
          <tr><td colspan="5" class="text-center text-white">No property activity records found</td></tr>
        <?php else: ?>
          <?php foreach ($propertyLogs as $l): ?>
            <tr>
              <td><small><?= date('M d, Y h:i A', strtotime($l['created_at'] ?? 'now')) ?></small></td>
              <td>
                <?php
                $meta = json_decode($l['metadata'] ?? '{}', true);
                $title = '';
                if ($l['activity_type'] === 'create' || $l['activity_type'] === 'delete' || $l['activity_type'] === 'archive' || $l['activity_type'] === 'unarchive') {
                  $title = $meta['title'] ?? 'N/A';
                } elseif ($l['activity_type'] === 'update') {
                  $title = $meta['new']['title'] ?? $meta['old']['title'] ?? 'N/A';
                }
                ?>
                <strong><?= esc($title) ?></strong><br>
                <small class="text-muted">ID: <?= esc($l['entity_id'] ?? '?') ?></small>
              </td>
              <td>
                <?php if ($l['activity_type'] === 'create'): ?>
                  <span class="badge badge-create"><i class="bi bi-plus-circle"></i> CREATE</span>
                <?php elseif ($l['activity_type'] === 'update'): ?>
                  <span class="badge badge-update"><i class="bi bi-pencil-square"></i> UPDATE</span>
                <?php elseif ($l['activity_type'] === 'delete'): ?>
                  <span class="badge badge-delete"><i class="bi bi-trash"></i> DELETE</span>
                <?php elseif ($l['activity_type'] === 'archive'): ?>
                  <span class="badge badge-archive"><i class="bi bi-archive"></i> ARCHIVE</span>
                <?php elseif ($l['activity_type'] === 'unarchive'): ?>
                  <span class="badge badge-unarchive"><i class="bi bi-arrow-repeat"></i> UNARCHIVE</span>
                <?php endif; ?>
              </td>
              <td>
                <strong><?= esc($l['actor_role'] ?? '?') ?></strong><br>
                <small class="text-muted">ID: <?= esc($l['actor_user_id'] ?? '?') ?></small>
                <?php if (isset($meta['source'])): ?>
                  <br><small class="text-muted">Source: <?= esc($meta['source']) ?></small>
                <?php endif; ?>
              </td>
              <td style="max-width:400px;">
                <?php
                $meta = json_decode($l['metadata'] ?? '{}', true);
                
                if ($l['activity_type'] === 'create'):
                ?>
                  <div class="text-success">
                    <i class="bi bi-plus-circle"></i> Property created<br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'update'): ?>
                  <div class="text-warning">
                    <i class="bi bi-pencil-square"></i> Property Information Changed<br>
                    <?php if (isset($meta['old']['title']) && $meta['old']['title'] != $meta['new']['title']): ?>
                      Title: <span class="old-value"><?= esc($meta['old']['title']) ?></span> → <span class="new-value"><?= esc($meta['new']['title']) ?></span><br>
                    <?php endif; ?>
                    <?php if (isset($meta['old']['price']) && $meta['old']['price'] != $meta['new']['price']): ?>
                      Price: <span class="old-value">₱<?= number_format($meta['old']['price'], 2) ?></span> → <span class="new-value">₱<?= number_format($meta['new']['price'], 2) ?></span><br>
                    <?php endif; ?>
                    <?php if (isset($meta['old']['location']) && $meta['old']['location'] != $meta['new']['location']): ?>
                      Location: <span class="old-value"><?= esc($meta['old']['location']) ?></span> → <span class="new-value"><?= esc($meta['new']['location']) ?></span>
                    <?php endif; ?>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'delete'): ?>
                  <div class="text-danger">
                    <i class="bi bi-trash"></i> <strong>PROPERTY DELETED!</strong><br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                    <br><span class="text-danger">⚠️ This property has been permanently removed</span>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'archive'): ?>
                  <div class="text-warning">
                    <i class="bi bi-archive"></i> Property <strong>ARCHIVED</strong><br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                    <br><span class="text-warning">📦 Property is now hidden from buyers</span>
                  </div>
                
                <?php elseif ($l['activity_type'] === 'unarchive'): ?>
                  <div class="text-success">
                    <i class="bi bi-arrow-repeat"></i> Property <strong>UNARCHIVED / RESTORED</strong><br>
                    📍 <?= esc($meta['location'] ?? 'N/A') ?><br>
                    💰 ₱<?= number_format($meta['price'] ?? 0, 2) ?>
                    <br><span class="text-success">✅ Property is now visible to buyers again</span>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination for Property CRUD Table -->
  <?php if (isset($propertyPager) && $propertyPager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-3 mb-4">
      <nav aria-label="Property CRUD pagination">
          <ul class="pagination">
              <?php if ($propertyPager->currentPage > 1): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage ?? 1 ?>&property_page=<?= $propertyPager->currentPage - 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          &laquo; Prev
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">&laquo; Prev</span></li>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $propertyPager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $propertyPager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage ?? 1 ?>&property_page=<?= $i ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          <?= $i ?>
                      </a>
                  </li>
              <?php endfor; ?>

              <?php if ($propertyPager->currentPage < $propertyPager->lastPage): ?>
                  <li class="page-item">
                      <a class="page-link" href="?login_page=<?= $loginPager->currentPage ?? 1 ?>&property_page=<?= $propertyPager->currentPage + 1 ?>&login_search=<?= urlencode($loginSearch ?? '') ?>&property_search=<?= urlencode($propertySearch ?? '') ?>">
                          Next &raquo;
                      </a>
                  </li>
              <?php else: ?>
                  <li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>
              <?php endif; ?>
          </ul>
      </nav>
  </div>
  <div class="text-center text-white small mb-4">
      Showing <strong><?= $propertyPager->firstItem ?></strong> to <strong><?= $propertyPager->lastItem ?></strong> 
      of <strong><?= $propertyPager->total ?></strong> records
  </div>
  <?php endif; ?>

</div>

<!-- SOCKET.IO FOR REAL-TIME AUDIT UPDATES -->
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
// Connect to WebSocket server
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true
});

// Connection status indicator
const statusDiv = document.createElement('div');
statusDiv.style.position = 'fixed';
statusDiv.style.bottom = '10px';
statusDiv.style.left = '10px';
statusDiv.style.padding = '5px 10px';
statusDiv.style.borderRadius = '5px';
statusDiv.style.fontSize = '12px';
statusDiv.style.zIndex = '9999';
statusDiv.style.backgroundColor = '#dc3545';
statusDiv.style.color = 'white';
statusDiv.innerHTML = '🔌 Connecting...';
document.body.appendChild(statusDiv);

socket.on('connect', () => {
    console.log('✅ Connected to WebSocket for real-time audit');
    statusDiv.style.backgroundColor = '#28a745';
    statusDiv.innerHTML = '🔌 Audit Live';
    
    // Register as admin
    socket.emit('register', 'admin_audit');
});

socket.on('disconnect', () => {
    statusDiv.style.backgroundColor = '#dc3545';
    statusDiv.innerHTML = '🔌 Disconnected';
});

socket.on('connect_error', (error) => {
    console.error('Connection error:', error);
    statusDiv.style.backgroundColor = '#dc3545';
    statusDiv.innerHTML = '🔌 Connection Failed';
});

// Listen for new audit logs in real-time
socket.on('audit-log-added', (auditLog) => {
    console.log('📋 New audit log received:', auditLog);
    
    // Check if it's a login/logout event
    if (auditLog.activity_type === 'login' || auditLog.activity_type === 'logout' || auditLog.activity_type === 'register') {
        addLoginLogoutToTable(auditLog);
    } else if (auditLog.entity_type === 'property') {
        addPropertyLogToTable(auditLog);
    }
    
    // Show notification
    showNotification(auditLog);
});

// Function to add login/logout to table
// Function to add login/logout to table
function addLoginLogoutToTable(log) {
    const tableBody = document.querySelector('#login-logout-table');
    if (!tableBody) return;
    
    // Remove "No records" message if exists
    if (tableBody.children.length === 1 && tableBody.children[0].innerText.includes('No login/logout records')) {
        tableBody.innerHTML = '';
    }
    
    const meta = log.metadata ? JSON.parse(log.metadata) : {};
    const date = new Date(log.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + 
                          ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    
    let roleClass = '';
    let roleText = log.actor_role;
    if (log.actor_role === 'admin') roleClass = 'danger';
    else if (log.actor_role === 'seller') roleClass = 'warning';
    else roleClass = 'info';
    
    let details = '';
    if (log.activity_type === 'login' && meta.email) {
        details = `<i class="bi bi-envelope"></i> ${escapeHtml(meta.email)}`;
    } else if (log.activity_type === 'register') {
        details = `<i class="bi bi-person-plus"></i> New user: <strong>${escapeHtml(meta.name || '')}</strong><br>
                   <small>Email: ${escapeHtml(meta.email || '')} | Role: ${escapeHtml(meta.role || '')}</small>`;
    }
    
    const row = document.createElement('tr');
    row.style.animation = 'highlight 2s ease-out';
    row.innerHTML = `
        <td><small>${formattedDate}</small></td>
        <td><strong>${escapeHtml(log.actor_user_id)}</strong></td>
        <td><span class="badge bg-${roleClass}">${escapeHtml(roleText).toUpperCase()}</span></td>
        <td><span class="badge badge-${log.activity_type}">${escapeHtml(log.activity_type).toUpperCase()}</span></td>
        <td>${details || '—'}</td>
    `;
    
    tableBody.insertBefore(row, tableBody.firstChild);
}

// Function to add property logs to table
function addPropertyLogToTable(log) {
    const tableBody = document.querySelector('#property-crud-table');
    if (!tableBody) return;
    
    if (tableBody.children.length === 1 && tableBody.children[0].innerText.includes('No property activity records')) {
        tableBody.innerHTML = '';
    }
    
    const meta = log.metadata ? JSON.parse(log.metadata) : {};
    const date = new Date(log.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + 
                          ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    
    let title = '';
    if (log.activity_type === 'create' || log.activity_type === 'delete' || log.activity_type === 'archive' || log.activity_type === 'unarchive') {
        title = meta.title || 'N/A';
    } else if (log.activity_type === 'update') {
        title = meta.new?.title || meta.old?.title || 'N/A';
    }
    
    let actionBadge = '';
    if (log.activity_type === 'create') actionBadge = '<span class="badge badge-create"><i class="bi bi-plus-circle"></i> CREATE</span>';
    else if (log.activity_type === 'update') actionBadge = '<span class="badge badge-update"><i class="bi bi-pencil-square"></i> UPDATE</span>';
    else if (log.activity_type === 'delete') actionBadge = '<span class="badge badge-delete"><i class="bi bi-trash"></i> DELETE</span>';
    else if (log.activity_type === 'archive') actionBadge = '<span class="badge badge-archive"><i class="bi bi-archive"></i> ARCHIVE</span>';
    else if (log.activity_type === 'unarchive') actionBadge = '<span class="badge badge-unarchive"><i class="bi bi-arrow-repeat"></i> UNARCHIVE</span>';
    
    const row = document.createElement('tr');
    row.style.animation = 'highlight 2s ease-out';
    row.innerHTML = `
        <td><small>${formattedDate}</small></td>
        <td><strong>${escapeHtml(title)}</strong><br><small class="text-muted">ID: ${escapeHtml(log.entity_id)}</small></td>
        <td>${actionBadge}</td>
        <td><strong>${escapeHtml(log.actor_role)}</strong><br><small class="text-muted">ID: ${escapeHtml(log.actor_user_id)}</small></td>
        <td style="max-width:400px;">${formatPropertyDetails(log, meta) || '—'}</td>
    `;
    
    tableBody.insertBefore(row, tableBody.firstChild);
}

// Function to add property logs to table
function addPropertyLogToTable(log) {
    // Similar function for property CRUD logs
    const tableBody = document.querySelector('#property-crud-table');
    if (!tableBody) return;
    
    if (tableBody.children.length === 1 && tableBody.children[0].innerText.includes('No property activity records')) {
        tableBody.innerHTML = '';
    }
    
    const meta = log.metadata ? JSON.parse(log.metadata) : {};
    const date = new Date(log.created_at);
    const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + 
                          ' ' + date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    
    let title = '';
    if (log.activity_type === 'create' || log.activity_type === 'delete' || log.activity_type === 'archive' || log.activity_type === 'unarchive') {
        title = meta.title || 'N/A';
    } else if (log.activity_type === 'update') {
        title = meta.new?.title || meta.old?.title || 'N/A';
    }
    
    let actionBadge = '';
    if (log.activity_type === 'create') actionBadge = '<span class="badge badge-create"><i class="bi bi-plus-circle"></i> CREATE</span>';
    else if (log.activity_type === 'update') actionBadge = '<span class="badge badge-update"><i class="bi bi-pencil-square"></i> UPDATE</span>';
    else if (log.activity_type === 'delete') actionBadge = '<span class="badge badge-delete"><i class="bi bi-trash"></i> DELETE</span>';
    else if (log.activity_type === 'archive') actionBadge = '<span class="badge badge-archive"><i class="bi bi-archive"></i> ARCHIVE</span>';
    else if (log.activity_type === 'unarchive') actionBadge = '<span class="badge badge-unarchive"><i class="bi bi-arrow-repeat"></i> UNARCHIVE</span>';
    
    const row = document.createElement('tr');
    row.style.animation = 'highlight 2s ease-out';
    row.innerHTML = `
        <td><small>${formattedDate}</small></td>
        <td><strong>${escapeHtml(title)}</strong><br><small class="text-muted">ID: ${escapeHtml(log.entity_id)}</small></td>
        <td>${actionBadge}</td>
        <td><strong>${escapeHtml(log.actor_role)}</strong><br><small class="text-muted">ID: ${escapeHtml(log.actor_user_id)}</small></td>
        <td style="max-width:400px;">${formatPropertyDetails(log, meta)}</td>
    `;
    
    tableBody.insertBefore(row, tableBody.firstChild);
    updateRecordCount();
}

function formatPropertyDetails(log, meta) {
    if (log.activity_type === 'create') {
        return `<div class="text-success"><i class="bi bi-plus-circle"></i> Property created<br>📍 ${escapeHtml(meta.location || 'N/A')}<br>💰 ₱${(meta.price || 0).toLocaleString()}</div>`;
    } else if (log.activity_type === 'update') {
        let changes = '<div class="text-warning"><i class="bi bi-pencil-square"></i> Changes:<br>';
        if (meta.old?.title && meta.old.title !== meta.new?.title) {
            changes += `Title: <span class="old-value">${escapeHtml(meta.old.title)}</span> → <span class="new-value">${escapeHtml(meta.new.title)}</span><br>`;
        }
        if (meta.old?.price && meta.old.price !== meta.new?.price) {
            changes += `Price: <span class="old-value">₱${(meta.old.price || 0).toLocaleString()}</span> → <span class="new-value">₱${(meta.new.price || 0).toLocaleString()}</span><br>`;
        }
        changes += '</div>';
        return changes;
    } else if (log.activity_type === 'delete') {
        return `<div class="text-danger"><i class="bi bi-trash"></i> <strong>PROPERTY DELETED!</strong><br>📍 ${escapeHtml(meta.location || 'N/A')}<br>💰 ₱${(meta.price || 0).toLocaleString()}</div>`;
    } else if (log.activity_type === 'archive') {
        return `<div class="text-warning"><i class="bi bi-archive"></i> Property <strong>ARCHIVED</strong><br>📦 Hidden from buyers</div>`;
    } else if (log.activity_type === 'unarchive') {
        return `<div class="text-success"><i class="bi bi-arrow-repeat"></i> Property <strong>UNARCHIVED</strong><br>✅ Visible to buyers again</div>`;
    }
    return '';
}

function showNotification(log) {
    // Create a toast notification
    const notification = document.createElement('div');
    notification.className = `alert alert-info position-fixed top-0 end-0 m-3`;
    notification.style.cssText = 'z-index: 9999; animation: slideIn 0.3s ease-out; background: #17a2b8; color: white;';
    
    let message = '';
    if (log.activity_type === 'login') {
        const meta = JSON.parse(log.metadata || '{}');
        message = `🔐 User ${log.actor_role} logged in (ID: ${log.actor_user_id})`;
        if (meta.email) message += ` - ${meta.email}`;
    } else if (log.activity_type === 'logout') {
        message = `🚪 User ${log.actor_role} logged out (ID: ${log.actor_user_id})`;
    } else if (log.activity_type === 'register') {
        const meta = JSON.parse(log.metadata || '{}');
        message = `📝 New user registered: ${meta.name || ''} (${meta.email || ''})`;
    }
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-bell-fill me-2"></i>
            <div>${escapeHtml(message)}</div>
            <button type="button" class="btn-close btn-close-white ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 4000);
}

function updateRecordCount() {
    // Update the total count display
    const loginRows = document.querySelectorAll('#login-logout-table tbody tr');
    const totalSpan = document.querySelector('.total-records');
    if (totalSpan && loginRows.length) {
        totalSpan.innerText = loginRows.length;
    }
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Add animation styles
if (!document.querySelector('#audit-styles')) {
    const style = document.createElement('style');
    style.id = 'audit-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes highlight {
            0% { background-color: rgba(40, 167, 69, 0.3); }
            100% { background-color: transparent; }
        }
    `;
    document.head.appendChild(style);
}

console.log('Real-time audit monitoring active');
</script>
</body>
</html>