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
    .btn-edit {
      background: #ffc107;
      border: none;
      padding: .15rem .5rem;
      font-size: 0.75rem;
      color: #000;
    }
    .btn-edit:hover {
      background: #e0a800;
      color: #000;
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
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    @keyframes highlight {
      0% { background-color: rgba(40, 167, 69, 0.3); }
      100% { background-color: transparent; }
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
  </style>

  <style>
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
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/audit') ?>">Audit</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <div id="realtime-alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
  
  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <h3 class="text-white mb-0">
        <i class="bi bi-building"></i> Properties Management
      </h3>
      <p class="text-white small mt-2">Real-time property updates</p>
    </div>
    <a href="<?= base_url('/admin/add_property') ?>" class="btn btn-success btn-sm">
      <i class="bi bi-plus-square me-1"></i>Add Property
    </a>
  </div>

  <!-- SEARCH BAR -->
  <div class="row mb-4">
    <div class="col-md-5">
      <form method="get" action="<?= base_url('/admin/properties') ?>" class="d-flex gap-2">
        <div class="input-group">
          <span class="input-group-text bg-dark text-secondary border-secondary">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" 
                 name="search" 
                 class="form-control bg-dark text-white border-secondary" 
                 placeholder="Search by title, location, or seller..." 
                 value="<?= esc($search ?? '') ?>">
          <button class="btn btn-primary" type="submit">
            <i class="bi bi-search"></i> Search
          </button>
        </div>
        <?php if (!empty($search)): ?>
          <a href="<?= base_url('/admin/properties') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x-circle"></i> Clear
          </a>
        <?php endif; ?>
      </form>
      <?php if (!empty($search)): ?>
        <div class="mt-2 text-white-50 small">
          <i class="bi bi-info-circle"></i> Showing results for: "<strong class="text-warning"><?= esc($search) ?></strong>"
        </div>
      <?php endif; ?>
    </div>
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
      <tbody id="properties-table-body">
        <?php foreach (($properties ?? []) as $p): ?>
          <tr id="property-row-<?= $p['id'] ?>" class="property-row" data-property-id="<?= $p['id'] ?>" data-property-title="<?= esc($p['title']) ?>">
            <td><?= esc($p['id']) ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span class="property-title-<?= $p['id'] ?>"><?= esc($p['title']) ?></span>
                <a href="<?= base_url('/admin/edit_property/'.$p['id']) ?>" class="btn btn-edit btn-sm">
                  <i class="bi bi-pencil-square"></i> Edit
                </a>
              </div>
            </td>
            <td class="property-location-<?= $p['id'] ?>"><?= esc($p['location']) ?></td>
            <td class="property-price-<?= $p['id'] ?>">₱<?= number_format((float)$p['price'], 2) ?></td>
            <td class="property-seller-<?= $p['id'] ?>"><?= esc($p['seller_name'] ?? '') ?></td>
            <td class="property-archived-<?= $p['id'] ?>"><?= ((int)($p['is_archived'] ?? 0)) ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-success">No</span>' ?></td>
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

    </table>
  </div>
  
  <!-- PAGINATION AT THE BOTTOM -->
  <?php if (isset($pager) && $pager->lastPage > 1): ?>
  <div class="d-flex justify-content-center mt-4">
      <nav aria-label="Page navigation">
          <ul class="pagination">
              <!-- Previous Button -->
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

              <!-- Page Numbers -->
              <?php for ($i = 1; $i <= $pager->lastPage; $i++): ?>
                  <li class="page-item <?= $i == $pager->currentPage ? 'active' : '' ?>">
                      <a class="page-link" href="?page=<?= $i ?>">
                          <?= $i ?>
                      </a>
                  </li>
              <?php endfor; ?>

              <!-- Next Button -->
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
      of <strong><?= $pager->total ?></strong> properties
  </div>
  <?php endif; ?>
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

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to show notification
function showNotification(message, type = 'success') {
    const alertArea = document.getElementById('realtime-alert');
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
            <strong>${type === 'success' ? 'Success!' : type === 'danger' ? 'Deleted!' : 'Update:'}</strong><br>
            ${escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertArea.innerHTML = alertHTML;
    
    // Auto-dismiss after 4 seconds
    setTimeout(() => {
        const alert = alertArea.querySelector('.alert');
        if (alert) {
            alert.classList.remove('show');
            setTimeout(() => {
                if (alert && alert.parentNode) alert.remove();
            }, 300);
        }
    }, 4000);
}

// Connect to WebSocket for real-time updates
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true,
    reconnectionAttempts: 10,
    reconnectionDelay: 1000
});

socket.on('connect', () => {
    console.log('✅ Admin properties page connected to WebSocket server');
    showNotification('Connected to real-time updates', 'info');
    
    // Register as admin
    socket.emit('register', 'admin');
});

socket.on('disconnect', () => {
    console.log('❌ Disconnected from WebSocket server');
    showNotification('Disconnected from real-time updates', 'warning');
});

socket.on('connect_error', (error) => {
    console.error('Connection error:', error);
    showNotification('Unable to connect to real-time updates', 'danger');
});

// Listen for property updates (when admin edits a property)
socket.on('property-updated', function(property) {
    console.log('✏️ Property update received:', property);
    
    // Update the property row in the table
    const row = document.getElementById(`property-row-${property.id}`);
    if (row) {
        // Update title
        const titleSpan = row.querySelector(`.property-title-${property.id}`);
        if (titleSpan) titleSpan.textContent = property.title;
        
        // Update location
        const locationCell = row.querySelector(`.property-location-${property.id}`);
        if (locationCell) locationCell.textContent = property.location;
        
        // Update price
        const priceCell = row.querySelector(`.property-price-${property.id}`);
        if (priceCell) priceCell.textContent = `₱${parseFloat(property.price || 0).toLocaleString()}`;
        
        // Update seller
        const sellerCell = row.querySelector(`.property-seller-${property.id}`);
        if (sellerCell) sellerCell.textContent = property.seller_name;
        
        // Update archived status
        const archivedCell = row.querySelector(`.property-archived-${property.id}`);
        if (archivedCell) {
            archivedCell.innerHTML = property.is_archived ? '<span class="badge bg-warning">Yes</span>' : '<span class="badge bg-success">No</span>';
        }
        
        // Highlight the updated row
        row.style.transition = 'background-color 0.3s';
        row.style.backgroundColor = 'rgba(40, 167, 69, 0.3)';
        setTimeout(() => {
            row.style.backgroundColor = '';
        }, 2000);
        
        showNotification(`Property "${property.title}" has been updated`, 'success');
    }
});

// Listen for property additions
socket.on('property-added', function(property) {
    console.log('📦 New property added:', property);
    showNotification(`New property "${property.title}" has been added`, 'success');
    
    // Reload page to show new property
    setTimeout(() => location.reload(), 1500);
});

// Listen for property deletions
socket.on('property-deleted', function(data) {
    console.log('🗑️ Property deleted:', data.id);
    
    // Remove the row from table
    const row = document.getElementById(`property-row-${data.id}`);
    if (row) {
        row.style.transition = 'opacity 0.3s';
        row.style.opacity = '0';
        setTimeout(() => {
            row.remove();
            showNotification('Property has been deleted', 'danger');
            
            // Show message if no properties left
            const tbody = document.getElementById('properties-table-body');
            if (tbody && tbody.children.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No properties found</td></tr>';
            }
        }, 300);
    }
});

// Listen for property archive events
socket.on('archive-property', function(data) {
    console.log('📦 Property archived:', data.id);
    showNotification('Property has been archived', 'info');
    setTimeout(() => location.reload(), 1000);
});

// Listen for property unarchive events
socket.on('unarchive-property', function(data) {
    console.log('🔄 Property unarchived:', data.id);
    showNotification('Property has been restored', 'success');
    setTimeout(() => location.reload(), 1000);
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
            // The row will be removed by the socket event
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

console.log('Admin properties page ready - listening for real-time updates');
</script>
</body>
</html>