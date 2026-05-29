<?php
// Buyer dashboard (marketplace style)
$user = $user ?? session()->get('user') ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>

  <style>
    .property-card .text-muted{
      color: var(--text) !important;
    }
  </style>

  <style>
    body.app-dark .card,
    body.app-dark .card .card-body,
    body.app-dark .card .card-title,
    body.app-dark .card .card-text{
      background: transparent;
      color: var(--text) !important;
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
        color: #fff;
        padding: 8px 16px;
        font-size: 14px;
    }

    .pagination .page-link:hover {
        background-color: #0f3460;
        color: #ff6b8a;
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

    .page-item:first-child .page-link {
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    .page-item:last-child .page-link {
        border-top-right-radius: 8px;
        border-bottom-right-radius: 8px;
    }
  </style>

  <meta charset="UTF-8">
  <title>Buyer Dashboard | House Marketplace</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

</head>

<body class="app-dark">

<?php
$active = 'buyer_dashboard';
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">

  <!-- Flash messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <div id="realtime-alert-area"></div>

  <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap mb-3">
    <h3 class="text-success fw-bold mb-0">Available Properties</h3>
  </div>

  <!-- Search & Filter -->
  <form method="get" class="row g-2 mb-4" id="search-form">
    <div class="col-md-4">
      <input type="text" name="search" class="form-control" placeholder="Search by title or description" value="<?= esc($search ?? '') ?>">
    </div>
    <div class="col-md-3">
      <input type="text" name="location" class="form-control" placeholder="Search by location" value="<?= esc($location ?? '') ?>">
    </div>
    <div class="col-md-3">
      <select name="price_range" class="form-select">
        <option value="">Filter by Price</option>
        <?php
        $priceOptions = [
            "1" => "Below ₱1,000,000",
            "2" => "₱1,000,000 - ₱10,000,000",
            "3" => "₱10,000,000 - ₱20,000,000",
            "4" => "₱20,000,000 - ₱30,000,000",
            "5" => "₱30,000,000 - ₱40,000,000",
            "6" => "₱40,000,000 - ₱50,000,000",
            "7" => "Above ₱50,000,000"
        ];
        foreach ($priceOptions as $key => $label):
        ?>
          <option value="<?= $key ?>" <?= (isset($price_range) && $price_range == $key) ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-success w-100">Search</button>
      <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-outline-secondary w-100">Reset</a>
    </div>
  </form>

  <!-- PROPERTY LISTINGS -->
  <div class="row" id="property-container">
    <?php if (!empty($properties)): ?>
      <?php foreach ($properties as $property): ?>
        <?php
          $propertyId = $property['id'];
          $existingOffer = $existingOffers[$propertyId] ?? null;
          $chatExist = $chatsExist[$propertyId] ?? false;
          $isFavorite = $favorites[$propertyId] ?? false;
          $imagePath = !empty($property['image_path']) && file_exists(FCPATH . $property['image_path']) 
            ? base_url($property['image_path']) . '?v=' . time()
            : 'https://via.placeholder.com/400x250?text=No+Image';

        ?>
        <div class="col-md-6 mb-4 realtime-property" data-property-id="<?= $propertyId ?>">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <img src="<?= $imagePath ?>" class="card-img-top" style="height:250px; object-fit:cover;" onerror="this.src='https://via.placeholder.com/400x250?text=No+Image'" alt="Property Image">
            <div class="card-body">
              <h5 class="card-title text-success fw-bold property-title"><?= esc($property['title']) ?></h5>
              <p class="property-description"><?= esc($property['description']) ?></p>
              <p class="fw-bold text-primary property-price">₱<?= number_format($property['price'], 2) ?></p>
              <p class="mb-1 property-location"><b>📍 Location:</b> <?= esc($property['location']) ?></p>
              <p class="property-seller"><small>Seller: <?= esc($property['seller_name']) ?></small></p>

              <a href="<?= base_url('/message/' . $property['seller_id'] . '/' . $propertyId) ?>" class="btn btn-outline-success btn-sm mt-2">💬 Message Seller</a>

              <form method="post" action="<?= base_url('/buyer/favorites/toggle') ?>" class="d-inline">
                <?= csrf_field() ?>
                <input type="hidden" name="property_id" value="<?= $propertyId ?>">
                <button type="submit" class="btn btn-sm <?= $isFavorite ? 'btn-outline-danger' : 'btn-outline-primary' ?> ms-2"><?= $isFavorite ? '★ Saved' : '☆ Save' ?></button>
              </form>

              <?php if ($existingOffer): ?>
                <div class="offer-status mt-2">
                  <?php if ($existingOffer['status'] === 'pending'): ?>
                    <span class="badge bg-warning text-dark">⏳ Offer Pending - Waiting for seller response</span>
                  <?php elseif ($existingOffer['status'] === 'accepted'): ?>
                    <span class="badge bg-success">✅ Offer Accepted! Congratulations!</span>
                  <?php elseif ($existingOffer['status'] === 'rejected'): ?>
                    <span class="badge bg-danger">❌ Offer Rejected</span>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                    <form method="post" action="/PROJECT-HOUSING-main/public/make_offer" class="d-flex align-items-center gap-2 mt-2 offer-form">
                        <?= csrf_field() ?> 
                        <input type="hidden" name="property_id" value="<?= $propertyId ?>">
                        <input type="number" step="0.01" name="amount" class="form-control w-50" placeholder="Enter offer amount" required>
                        <button type="submit" class="btn btn-primary btn-sm">Make Offer</button>
                    </form>     
                    <div class="offer-status"></div>
                <?php endif; ?>

              <div class="mt-3 text-center">
                <?php if ($chatExist): ?>
                  <p class="text-success small mb-0">You have an active chat with this seller.</p>
                <?php else: ?>
                  <p class="text-muted small mb-0">No chats yet for this property.</p>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle me-2"></i>
          No properties found matching your search or filter criteria.
        </div>
      </div>
    <?php endif; ?>
  </div>

    <!-- PAGINATION AT THE BOTTOM -->
    <?php if (isset($pager) && $pager->lastPage > 1): ?>
    <div class="d-flex justify-content-center mt-5 mb-4">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <!-- Previous Button -->
                <?php if ($pager->currentPage > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pager->currentPage - 1 ?>&<?= http_build_query(array_filter(['search' => $search ?? '', 'location' => $location ?? '', 'price_range' => $price_range ?? ''])) ?>" aria-label="Previous">
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
                        <a class="page-link" href="?page=<?= $i ?>&<?= http_build_query(array_filter(['search' => $search ?? '', 'location' => $location ?? '', 'price_range' => $price_range ?? ''])) ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if ($pager->currentPage < $pager->lastPage): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $pager->currentPage + 1 ?>&<?= http_build_query(array_filter(['search' => $search ?? '', 'location' => $location ?? '', 'price_range' => $price_range ?? ''])) ?>" aria-label="Next">
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
    <div class="text-center text-white small mb-4">
        Showing <strong><?= $pager->firstItem ?></strong> to <strong><?= $pager->lastItem ?></strong> 
        of <strong><?= $pager->total ?></strong> properties
    </div>
    <?php endif; ?>

    <!-- Showing results info -->
    <div class="text-center text-muted small mb-4">
    </div>

  <!-- Empty state for when all properties are removed -->
  <div id="empty-state" style="display: none;" class="text-center py-5">
    <i class="bi bi-house-slash" style="font-size: 4rem; color: #6c757d;"></i>
    <h4 class="text-white mt-3">No Properties Available</h4>
    <p class="text-white">Check back later for new listings!</p>
  </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<!-- SOCKET.IO -->
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
// ========== GET CURRENT USER ==========
const currentUserId = <?= json_encode($user['id'] ?? 0) ?>;
console.log('Buyer ID:', currentUserId);

// ========== ADD CONNECTION STATUS INDICATOR ==========
const statusDiv = document.createElement('div');
statusDiv.style.position = 'fixed';
statusDiv.style.bottom = '10px';
statusDiv.style.right = '10px';
statusDiv.style.padding = '5px 10px';
statusDiv.style.borderRadius = '5px';
statusDiv.style.fontSize = '12px';
statusDiv.style.zIndex = '9999';
statusDiv.style.backgroundColor = '#dc3545';
statusDiv.style.color = 'white';
statusDiv.innerHTML = '🔌 Connecting...';
document.body.appendChild(statusDiv);
// ========== END CONNECTION STATUS ==========

// ========== CONNECT TO WEBSOCKET ==========
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true,
    reconnectionAttempts: 5,
    reconnectionDelay: 1000
});

// ========== HELPER FUNCTIONS ==========
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'success') {
    const alertArea = document.getElementById('realtime-alert-area');
    if (alertArea) {
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert" style="animation: slideIn 0.3s ease-out;">
                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                <strong>${type === 'success' ? 'Success!' : type === 'danger' ? 'Alert!' : 'Info:'}</strong> ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        alertArea.insertAdjacentHTML('afterbegin', alertHTML);
        
        setTimeout(() => {
            const alert = alertArea.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }
}

function checkEmptyState() {
    const propertiesContainer = document.getElementById('property-container');
    const emptyState = document.getElementById('empty-state');
    
    if (propertiesContainer && emptyState) {
        const properties = propertiesContainer.querySelectorAll('.realtime-property');
        if (properties.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }
}

// Add animation styles
if (!document.querySelector('#notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
        @keyframes slideIn { 
            from { transform: translateY(-100%); opacity: 0; } 
            to { transform: translateY(0); opacity: 1; } 
        }
        @keyframes highlight {
            0% { background-color: rgba(40, 167, 69, 0.2); }
            100% { background-color: transparent; }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.95); }
        }
        .property-highlight {
            animation: highlight 2s ease-out;
        }
        .property-fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }
    `;
    document.head.appendChild(style);
}

// ========== FUNCTION TO ADD PROPERTY TO DASHBOARD ==========
function addPropertyToBuyerDashboard(property) {
    console.log('🔧 addPropertyToBuyerDashboard CALLED');
    const propertiesContainer = document.getElementById('property-container');
    if (!propertiesContainer) {
        console.error('Properties container not found!');
        return;
    }
    
    // Check if property already exists
    if (document.querySelector(`.realtime-property[data-property-id="${property.id}"]`)) {
        console.log('Property already exists, skipping:', property.id);
        return;
    }
    
    // Build image path
    let imagePath = 'https://via.placeholder.com/400x250?text=No+Image';
    if (property.image_path && property.image_path !== 'null' && property.image_path !== '') {
        let cleanPath = property.image_path.replace(/^\/+/, '');
        imagePath = 'http://localhost/PROJECT-HOUSING-main/public/' + cleanPath + '?t=' + new Date().getTime();
        console.log('Image path constructed:', imagePath);
    }
    
    const csrfToken = document.querySelector('input[name="csrf_test_name"]')?.value || '';
    
    const propertyHTML = `
        <div class="col-md-6 mb-4 realtime-property" data-property-id="${property.id}">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <img src="${imagePath}" class="card-img-top" style="height:250px; object-fit:cover;" 
                     onerror="this.src='https://via.placeholder.com/400x250?text=No+Image'" alt="Property Image">
                <div class="card-body">
                    <h5 class="card-title text-success fw-bold property-title">${escapeHtml(property.title)}</h5>
                    <p class="property-description">${escapeHtml(property.description ? property.description.substring(0, 150) : '')}</p>
                    <p class="fw-bold text-primary property-price">₱${parseFloat(property.price || 0).toLocaleString()}</p>
                    <p class="mb-1 property-location"><b>📍 Location:</b> ${escapeHtml(property.location)}</p>
                    <p class="property-seller"><small>Seller: ${escapeHtml(property.seller_name)}</small></p>
                    
                    <a href="/message/${property.seller_id}/${property.id}" class="btn btn-outline-success btn-sm mt-2">💬 Message Seller</a>
                    
                    <form method="post" action="/buyer/favorites/toggle" class="d-inline">
                        <input type="hidden" name="csrf_test_name" value="${csrfToken}">
                        <input type="hidden" name="property_id" value="${property.id}">
                        <button type="submit" class="btn btn-sm btn-outline-primary ms-2">☆ Save</button>
                    </form>
                    
                    <form method="post" action="/make_offer" class="d-flex align-items-center gap-2 mt-2 offer-form">
                        <input type="hidden" name="csrf_test_name" value="${csrfToken}">
                        <input type="hidden" name="property_id" value="${property.id}">
                        <input type="number" step="0.01" name="amount" class="form-control w-50" placeholder="Enter offer amount" required>
                        <button type="submit" class="btn btn-primary btn-sm">Make Offer</button>
                    </form>
                    <div class="offer-status"></div>
                    
                    <div class="mt-3 text-center">
                        <p class="text-muted small mb-0">No chats yet for this property.</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    propertiesContainer.insertAdjacentHTML('beforeend', propertyHTML);
    
    // Highlight the new property
    const newProperty = document.querySelector(`.realtime-property[data-property-id="${property.id}"]`);
    if (newProperty) {
        newProperty.classList.add('property-highlight');
        setTimeout(() => newProperty.classList.remove('property-highlight'), 2000);
        newProperty.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    checkEmptyState();
    console.log('✅ Property added successfully');
}

// ========== SOCKET CONNECTION EVENTS ==========
socket.on('connect', () => {
    console.log('✅ Connected to websocket server');
    statusDiv.style.backgroundColor = '#28a745';
    statusDiv.innerHTML = '🔌 WebSocket Connected';
    socket.emit('register', currentUserId.toString());
});

socket.on('registered', (data) => {
    console.log('✅ Registered with server:', data);
});

socket.on('disconnect', () => {
    console.log('❌ Disconnected from websocket server');
    statusDiv.style.backgroundColor = '#dc3545';
    statusDiv.innerHTML = '🔌 Disconnected';
});

socket.on('connect_error', (error) => {
    console.error('Connection error:', error);
    showNotification('Real-time updates unavailable. Page will still work normally.', 'warning');
});

// ========== LISTEN FOR NEW PROPERTIES (THIS IS THE CRITICAL EVENT) ==========
socket.on('property-added', function(property) {
    console.log('🔔🔔🔔 PROPERTY-ADDED EVENT RECEIVED! 🔔🔔🔔');
    console.log('Property title:', property.title);
    console.log('Property ID:', property.id);
    console.log('Image path:', property.image_path);
    console.log('Full property data:', property);
    
    // Check if property already exists
    const existingProperty = document.querySelector(`.realtime-property[data-property-id="${property.id}"]`);
    console.log('Existing property found?', existingProperty ? 'YES - skipping' : 'NO - will add');
    
    if (!existingProperty) {
        console.log('Calling addPropertyToBuyerDashboard...');
        addPropertyToBuyerDashboard(property);
        showNotification(`New property available: ${property.title}`, 'success');
    } else {
        console.log('Property already exists in DOM, skipping');
    }
});

// ========== LISTEN FOR PROPERTY UPDATES ==========
socket.on('property-updated', function(property) {
    console.log('✏️ Property update received:', property.title);
    console.log('Image path in update:', property.image_path);

    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${property.id}"]`);
    
    if (propertyCard) {
        const titleElement = propertyCard.querySelector('.card-title');
        const priceElement = propertyCard.querySelector('.property-price');
        const locationElement = propertyCard.querySelector('.property-location');
        const descElement = propertyCard.querySelector('.property-description');
        const imgElement = propertyCard.querySelector('.card-img-top');
        
        if (titleElement) titleElement.textContent = property.title;
        if (priceElement) priceElement.textContent = `₱${parseFloat(property.price || 0).toLocaleString()}`;
        if (locationElement) locationElement.innerHTML = `<b>📍 Location:</b> ${escapeHtml(property.location)}`;
        if (descElement) descElement.textContent = property.description;
        
        if (property.image_path && property.image_path !== 'null' && property.image_path !== '') {
            let cleanPath = property.image_path.replace(/^\/+/, '');
            let newImagePath = 'http://localhost/PROJECT-HOUSING-main/public/' + cleanPath + '?t=' + new Date().getTime();
            console.log('Updating image to:', newImagePath);
            if (imgElement) imgElement.src = newImagePath;
        }
        
        propertyCard.classList.add('property-highlight');
        setTimeout(() => propertyCard.classList.remove('property-highlight'), 2000);
        showNotification(`Property updated: ${property.title}`, 'info');
    } else {
        console.log('Property card not found for ID:', property.id);
    }
});

// ========== LISTEN FOR ARCHIVED PROPERTIES ==========
socket.on('archive-property', function(data) {
    console.log('📦 Property archived - removing from buyer dashboard:', data.id);
    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${data.id}"]`);
    if (propertyCard) {
        propertyCard.classList.add('property-fade-out');
        setTimeout(() => {
            propertyCard.remove();
            checkEmptyState();
            showNotification('A property has been removed from listings', 'info');
        }, 300);
    }
});

// ========== LISTEN FOR UNARCHIVED PROPERTIES ==========
socket.on('unarchive-property', function(data) {
    console.log('📦 Property unarchived - adding back to buyer dashboard:', data.id);
    fetch(`/api/property/${data.id}`)
        .then(response => response.json())
        .then(property => {
            addPropertyToBuyerDashboard(property);
            showNotification(`Property "${property.title}" is back on the market!`, 'success');
        })
        .catch(error => {
            console.error('Error fetching restored property:', error);
            location.reload();
        });
});

// ========== LISTEN FOR PROPERTY DELETION ==========
socket.on('property-deleted', function(data) {
    console.log('🗑️ Property deleted:', data.id);
    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${data.id}"]`);
    if (propertyCard) {
        propertyCard.classList.add('property-fade-out');
        setTimeout(() => propertyCard.remove(), 300);
        checkEmptyState();
        showNotification('A property has been permanently removed', 'danger');
    }
});

// ========== OFFER STATUS UPDATES ==========
socket.on('offer-status-updated', function(data) {
    console.log('📋 Offer status update:', data);
    showNotification(`${data.message}`, data.status === 'accepted' ? 'success' : 'danger');
    
    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${data.property_id}"]`);
    if (propertyCard) {
        const offerStatus = propertyCard.querySelector('.offer-status');
        if (offerStatus) {
            if (data.status === 'accepted') {
                offerStatus.innerHTML = `<div class="mt-2"><span class="badge bg-success">✅ ${data.message}</span></div>`;
                const offerForm = propertyCard.querySelector('.offer-form');
                if (offerForm) offerForm.style.display = 'none';
            } else if (data.status === 'rejected') {
                offerStatus.innerHTML = `<div class="mt-2"><span class="badge bg-danger">❌ ${data.message}</span></div>`;
                const offerForm = propertyCard.querySelector('.offer-form');
                if (offerForm) offerForm.style.display = 'flex';
            }
        }
    }
});

// ========== NEW OFFER NOTIFICATION ==========
socket.on('new-offer', function(data) {
    console.log('💰 New offer on property:', data);
    if (data.property_id) {
        showNotification(`New offer of ₱${parseFloat(data.amount || 0).toLocaleString()} made on "${data.property_title}"`, 'info');
    }
});

// Initial check for empty state
setTimeout(checkEmptyState, 500);

console.log('Buyer dashboard ready - waiting for real-time updates...');
</script>

<!-- Bootstrap JS for alerts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>