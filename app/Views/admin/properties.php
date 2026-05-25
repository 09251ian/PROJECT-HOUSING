<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Properties | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <style>
    .btn-delete {
      background: #dc3545;
      border: none;
      padding: .15rem .5rem;
      font-size: 0.75rem;
    }
    .btn-delete:hover {
      background: #c82333;
    }
    .modal-content {
      background: #16213e;
      color: #eee;
    }
    .modal-header {
      border-bottom-color: #e94560;
    }
    .property-row {
      transition: opacity 0.3s;
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
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/offers') ?>">Offers</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div id="realtime-alert"></div>
  
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="text-white mb-0">Properties</h3>
    <a href="<?= base_url('/admin/add_property') ?>" class="btn btn-success btn-sm">
      <i class="bi bi-plus-square me-1"></i>Add Property
    </a>
  </div>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Location</th>
          <th>Price</th>
          <th>Seller</th>
          <th>Archived</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($properties ?? []) as $p): ?>
          <tr id="property-row-<?= $p['id'] ?>" class="property-row" data-property-id="<?= $p['id'] ?>">
            <td><?= esc($p['id']) ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span><?= esc($p['title']) ?></span>
                <a href="<?= base_url('/admin/edit_property/'.$p['id']) ?>" class="btn btn-outline-light btn-sm" style="padding: .15rem .4rem;">
                  <i class="bi bi-pencil-square"></i> Edit
                </a>
              </div>
            </td>
            <td><?= esc($p['location']) ?></td>
            <td>₱<?= number_format((float)$p['price'], 2) ?></td>
            <td><?= esc($p['seller_name'] ?? '') ?></td>
            <td><?= ((int)($p['is_archived'] ?? 0)) ? 'Yes' : 'No' ?></td>
            <td>
              <button class="btn btn-danger btn-delete" 
                      onclick="deleteProperty(<?= $p['id'] ?>, '<?= esc(addslashes($p['title'])) ?>')">
                <i class="bi bi-trash me-1"></i> Delete
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header border-danger">
        <h5 class="modal-title text-danger">
          <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete property: <strong id="propertyTitle"></strong>?
        <p class="text-danger mt-2"><i class="bi bi-trash"></i> This action cannot be undone!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
          <i class="bi bi-trash me-1"></i>Delete Permanently
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
let propertyToDelete = null;
let deleteModal = null;

// Connect to WebSocket for real-time updates
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true
});

// Buyer
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    notification.style.cssText = 'z-index: 9999; animation: slideIn 0.3s ease-out;';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-house-door-fill me-2"></i>
            <div>${escapeHtml(message)}</div>
            <button type="button" class="btn-close btn-close-white ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}

// Seller
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    notification.style.cssText = 'z-index: 9999; animation: slideIn 0.3s ease-out;';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-house-door-fill me-2"></i>
            <div>${escapeHtml(message)}</div>
            <button type="button" class="btn-close btn-close-white ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 5000);
}

if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);
}

socket.on('connect', () => {
    console.log('Admin connected to WebSocket server');
});

// Listen for property deletions (confirmation from server)
socket.on('property-deleted', function(data) {
    console.log('Property deleted:', data.id);
    
    // Remove the row from table
    const row = document.getElementById(`property-row-${data.id}`);
    if (row) {
        row.style.transition = 'opacity 0.3s';
        row.style.opacity = '0';
        setTimeout(() => {
            row.remove();
            showNotification('Property has been deleted', 'danger');
            
            // Show message if no properties left
            const tbody = document.querySelector('tbody');
            if (tbody && tbody.children.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No properties found</td></tr>';
            }
        }, 300);
    }
});

function deleteProperty(id, title) {
    propertyToDelete = id;
    document.getElementById('propertyTitle').textContent = title;
    if (!deleteModal) {
        deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    }
    deleteModal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
    if (!propertyToDelete) return;
    
    const confirmBtn = this;
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
    confirmBtn.disabled = true;
    
    try {
        const response = await fetch('<?= base_url('/admin/delete-property') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `property_id=${propertyToDelete}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            if (deleteModal) deleteModal.hide();
            showNotification('Property deleted successfully!', 'success');
        } else {
            showNotification(result.message || 'Delete failed', 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Error deleting property', 'danger');
    } finally {
        confirmBtn.innerHTML = originalText;
        confirmBtn.disabled = false;
        propertyToDelete = null;
    }
});

function showNotification(message, type) {
    const alertArea = document.getElementById('realtime-alert');
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    const alertHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    alertArea.innerHTML = alertHTML;
    setTimeout(() => {
        const alert = alertArea.querySelector('.alert');
        if (alert) alert.remove();
    }, 3000);
}

// Also listen for property updates (if admin edits a property)
socket.on('property-updated', function(property) {
    console.log('Property updated:', property);
    showNotification(`Property "${property.title}" has been updated`, 'info');
});

socket.on('property-added', function(property) {
    console.log('New property added:', property);
    showNotification(`New property "${property.title}" has been added`, 'success');
    // Optionally reload the page to show new property
    setTimeout(() => location.reload(), 2000);
});
</script>
</body>
</html>