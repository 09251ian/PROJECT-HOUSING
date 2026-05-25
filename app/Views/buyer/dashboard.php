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
    <div class="alert alert-success">
      <?= session()->getFlashdata('success') ?>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap mb-3">

    <h3 class="text-success fw-bold mb-0">
      Available Properties
    </h3>

    <div class="mb-0">

      <!-- Search & Filter -->
      <form method="get" class="row g-2 mb-4">

        <div class="col-md-4">
          <input type="text"
                 name="search"
                 class="form-control"
                 placeholder="Search by title or description"
                 value="<?= esc($search ?? '') ?>">
        </div>

        <div class="col-md-3">
          <input type="text"
                 name="location"
                 class="form-control"
                 placeholder="Search by location"
                 value="<?= esc($location ?? '') ?>">
        </div>

        <div class="col-md-3">

          <select name="price_range" class="form-select">

            <option value="">
              Filter by Price
            </option>

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

              <option value="<?= $key ?>" <?= (isset($price_range) && $price_range == $key) ? 'selected' : '' ?>>
                <?= $label ?>
              </option>

            <?php endforeach; ?>

          </select>

        </div>

        <div class="col-md-2 d-flex gap-2">

          <button type="submit" class="btn btn-success w-100">
            Search
          </button>

          <a href="<?= base_url('/buyer/dashboard') ?>"
             class="btn btn-outline-secondary w-100">
            Reset
          </a>

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
              // Fix image path for existing properties
              $imagePath = !empty($property['image_path']) && file_exists(FCPATH . $property['image_path']) 
                  ? base_url($property['image_path']) 
                  : 'https://via.placeholder.com/400x250?text=No+Image';
            ?>

            <div class="col-md-6 mb-4 realtime-property" data-property-id="<?= $propertyId ?>">

              <div class="card shadow-sm border-0 rounded-4 h-100">

                <img src="<?= $imagePath ?>"
                     class="card-img-top"
                     style="height:250px; object-fit:cover;"
                     onerror="this.src='https://via.placeholder.com/400x250?text=No+Image'"
                     alt="Property Image">

                <div class="card-body">

                  <h5 class="card-title text-success fw-bold">
                    <?= esc($property['title']) ?>
                  </h5>

                  <p>
                    <?= esc($property['description']) ?>
                  </p>

                  <p class="fw-bold text-primary">
                    ₱<?= number_format($property['price'], 2) ?>
                  </p>

                  <p class="mb-1">
                    <b>📍 Location:</b>
                    <?= esc($property['location']) ?>
                  </p>

                  <p>
                    <small>
                      Seller: <?= esc($property['seller_name']) ?>
                    </small>
                  </p>

                  <a href="<?= base_url('/message/' . $property['seller_id'] . '/' . $propertyId) ?>"
                     class="btn btn-outline-success btn-sm mt-2">
                    💬 Message Seller
                  </a>

                  <form method="post"
                        action="<?= base_url('/buyer/favorites/toggle') ?>"
                        class="d-inline">

                    <?= csrf_field() ?>

                    <input type="hidden"
                           name="property_id"
                           value="<?= $propertyId ?>">

                    <button type="submit"
                            class="btn btn-sm <?= $isFavorite ? 'btn-outline-danger' : 'btn-outline-primary' ?> ms-2">

                      <?= $isFavorite ? '★ Saved' : '☆ Save' ?>

                    </button>

                  </form>

                  <?php if ($existingOffer): ?>
                    <div class="offer-status mt-2">
                      <?php if ($existingOffer['status'] === 'pending'): ?>
                        <span class="badge bg-warning text-dark">
                          ⏳ Offer Pending - Waiting for seller response
                        </span>
                      <?php elseif ($existingOffer['status'] === 'accepted'): ?>
                        <span class="badge bg-success">
                          ✅ Offer Accepted! Congratulations!
                        </span>
                      <?php elseif ($existingOffer['status'] === 'rejected'): ?>
                        <span class="badge bg-danger">
                          ❌ Offer Rejected
                        </span>
                      <?php endif; ?>
                    </div>
                  <?php else: ?>
                    <form method="post"
                          action="<?= base_url('/make_offer') ?>"
                          class="d-flex align-items-center gap-2 mt-2 offer-form">
                      <?= csrf_field() ?>
                      <input type="hidden"
                             name="property_id"
                             value="<?= $propertyId ?>">
                      <input type="number"
                             step="0.01"
                             name="amount"
                             class="form-control w-50"
                             placeholder="Enter offer amount"
                             required>
                      <button type="submit"
                              class="btn btn-primary btn-sm">
                        Make Offer
                      </button>
                    </form>
                    <div class="offer-status"></div>
                  <?php endif; ?>

                  <div class="mt-3 text-center">

                    <?php if ($chatExist): ?>

                      <p class="text-success small mb-0">
                        You have an active chat with this seller.
                      </p>

                    <?php else: ?>

                      <p class="text-muted small mb-0">
                        No chats yet for this property.
                      </p>

                    <?php endif; ?>

                  </div>

                </div>

              </div>
            </div>

          <?php endforeach; ?>

        <?php else: ?>

          <p class="text-muted text-center">
            No properties found matching your search or filter criteria.
          </p>

        <?php endif; ?>

      </div>

    </div>

  </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<!-- SOCKET.IO -->
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true
});

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

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to show notifications
function showNotification(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.cssText = 'z-index: 9999; animation: slideIn 0.3s ease-out;';
    
    let bgColor = '#28a745';
    if (type === 'info') bgColor = '#17a2b8';
    if (type === 'danger') bgColor = '#dc3545';
    if (type === 'warning') bgColor = '#ffc107';
    
    toast.style.background = bgColor;
    toast.style.color = 'white';
    
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi ${type === 'success' ? 'bi-house-door-fill' : type === 'info' ? 'bi-pencil-square' : 'bi-trash-fill'} me-2"></i>
            <div>${escapeHtml(message)}</div>
            <button type="button" class="btn-close btn-close-white ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

socket.on('connect', () => {
    console.log('✅ Connected to websocket server');
});

socket.on('disconnect', () => {
    console.log('❌ Disconnected from websocket server');
});

socket.on('connect_error', (error) => {
    console.error('Connection error:', error);
});

// Listen for offer status updates
socket.on('offer-status-updated', function(data) {
    console.log('📋 Offer status update received:', data);
    
    // Find the property card that contains this offer
    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${data.property_id}"]`);
    
    if (propertyCard) {
        // Find the offer section in this property card
        const offerStatus = propertyCard.querySelector('.offer-status');
        const offerForm = propertyCard.querySelector('.offer-form');
        
        if (data.status === 'accepted') {
            // Update UI to show accepted status
            if (offerStatus) {
                offerStatus.innerHTML = `
                    <div class="mt-2">
                        <span class="badge bg-success">
                            ✅ Offer Accepted - ${escapeHtml(data.message)}
                        </span>
                        <div class="small text-success mt-1">
                            Seller: ${escapeHtml(data.seller_name)}
                        </div>
                    </div>
                `;
            }
            
            // Disable/hide the offer form
            if (offerForm) {
                offerForm.style.display = 'none';
            }
            
            showNotification(`🎉 Offer accepted for ${escapeHtml(data.property_title)}! ${data.message}`, 'success');
            
        } else if (data.status === 'rejected') {
            // Update UI to show rejected status
            if (offerStatus) {
                offerStatus.innerHTML = `
                    <div class="mt-2">
                        <span class="badge bg-danger">
                            ❌ Offer Rejected - ${escapeHtml(data.message)}
                        </span>
                        <div class="small text-danger mt-1">
                            Seller: ${escapeHtml(data.seller_name)}
                        </div>
                    </div>
                `;
            }
            
            // Optionally disable the offer form
            if (offerForm) {
                const makeOfferBtn = offerForm.querySelector('button[type="submit"]');
                if (makeOfferBtn) {
                    makeOfferBtn.disabled = true;
                    makeOfferBtn.textContent = 'Offer Rejected';
                }
            }
            
            showNotification(`❌ Offer rejected for ${escapeHtml(data.property_title)}`, 'danger');
        }
    }
    
    // Create a floating notification
    showNotification(
        `${data.message} - ${escapeHtml(data.property_title)}`,
        data.status === 'accepted' ? 'success' : 'danger'
    );
});

// 1. LISTEN FOR NEW PROPERTIES
// Listen for new properties added by admin
socket.on('property-added', function(property) {
    console.log('📦 New property received by buyer:', property);
    addPropertyToMarketplace(property);
    showNotification(`New property listed: ${property.title}`, 'success');
});

function addPropertyToMarketplace(property) {
    const propertiesContainer = document.getElementById('property-container');
    if (!propertiesContainer) return;
    
    let imagePath = 'https://via.placeholder.com/400x250?text=No+Image';
    if (property.image_path && property.image_path !== 'null') {
        imagePath = '/' + property.image_path.replace(/^\//, '');
    }
    
    const propertyHTML = `
        <div class="col-md-6 mb-4 realtime-property" data-property-id="${property.id}">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <img src="${imagePath}" class="card-img-top" style="height:250px; object-fit:cover;" 
                     onerror="this.src='https://via.placeholder.com/400x250?text=No+Image'" 
                     alt="Property Image">
                <div class="card-body">
                    <h5 class="card-title text-success fw-bold">${escapeHtml(property.title)}</h5>
                    <p>${escapeHtml(property.description ? property.description.substring(0, 150) : '')}</p>
                    <p class="fw-bold text-primary">₱${parseFloat(property.price || 0).toLocaleString()}</p>
                    <p class="mb-1"><b>📍 Location:</b> ${escapeHtml(property.location)}</p>
                    <p><small>Seller: ${escapeHtml(property.seller_name)}</small></p>
                    <a href="/message/${property.seller_id}/${property.id}" class="btn btn-outline-success btn-sm mt-2">💬 Message Seller</a>
                    <form method="post" action="/buyer/favorites/toggle" class="d-inline">
                        <input type="hidden" name="property_id" value="${property.id}">
                        <button type="submit" class="btn btn-sm btn-outline-primary ms-2">☆ Save</button>
                    </form>
                    <form method="post" action="/make_offer" class="d-flex align-items-center gap-2 mt-2 offer-form">
                        <input type="hidden" name="property_id" value="${property.id}">
                        <input type="number" step="0.01" name="amount" class="form-control w-50" placeholder="Enter offer amount" required>
                        <button type="submit" class="btn btn-primary btn-sm">Make Offer</button>
                    </form>
                    <div class="offer-status"></div>
                    <div class="alert alert-success mt-3 mb-0 small">
                        🎉 New property just listed!
                    </div>
                </div>
            </div>
        </div>
    `;
    
    propertiesContainer.insertAdjacentHTML('afterbegin', propertyHTML);
    
    // Highlight the new property
    const newProperty = document.querySelector(`.realtime-property[data-property-id="${property.id}"]`);
    if (newProperty) {
        newProperty.style.transition = 'background-color 0.5s';
        newProperty.style.backgroundColor = '#2a5a2a';
        setTimeout(() => {
            newProperty.style.backgroundColor = '';
        }, 3000);
        newProperty.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// 2. LISTEN FOR PROPERTY UPDATES
socket.on('property-updated', function(property) {
    console.log('✏️ Property updated:', property);
    
    // Find the property card
    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${property.id}"]`);
    
    if (propertyCard) {
        // Update title
        const titleElement = propertyCard.querySelector('.card-title');
        if (titleElement) titleElement.textContent = property.title;
        
        // Update description
        const descElement = propertyCard.querySelector('.property-description');
        if (descElement) descElement.textContent = property.description ? property.description.substring(0, 150) : 'No description available';
        
        // Update price
        const priceElement = propertyCard.querySelector('.property-price');
        if (priceElement) priceElement.textContent = `₱${parseFloat(property.price || 0).toLocaleString()}`;
        
        // Update location
        const locationElement = propertyCard.querySelector('.property-location');
        if (locationElement) locationElement.innerHTML = `<b>📍 Location:</b> ${escapeHtml(property.location || 'Location not specified')}`;
        
        // Update seller name
        const sellerElement = propertyCard.querySelector('.property-seller');
        if (sellerElement && property.seller_name) {
            sellerElement.innerHTML = `<small>Seller: ${escapeHtml(property.seller_name)}</small>`;
        }
        
        // Update image if changed
        if (property.image_path && property.image_path !== 'null') {
            const imgElement = propertyCard.querySelector('img');
            let newImagePath = '/' + property.image_path.replace(/^\//, '');
            imgElement.src = newImagePath;
        }
        
        // Highlight the updated card
        propertyCard.style.transition = 'background-color 0.5s';
        propertyCard.style.backgroundColor = '#fff3cd';
        setTimeout(() => {
            propertyCard.style.backgroundColor = '';
        }, 2000);
        
        showNotification(`Property updated: ${property.title}`, 'info');
    } else {
        console.log('Property card not found for ID:', property.id);
    }
});

// 3. LISTEN FOR PROPERTY DELETION
socket.on('property-deleted', function(data) {
    console.log('🗑️ Property deleted:', data.id);
    
    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${data.id}"]`);
    if (propertyCard) {
        propertyCard.style.transition = 'opacity 0.3s';
        propertyCard.style.opacity = '0';
        setTimeout(() => {
            propertyCard.remove();
        }, 300);
        showNotification(`Property has been removed`, 'danger');
    }
});

// 4. LISTEN FOR PROPERTY ARCHIVED
socket.on('property-archived', function(data) {
    console.log('📦 Property archived:', data.id);
    
    const propertyCard = document.querySelector(`.realtime-property[data-property-id="${data.id}"]`);
    if (propertyCard) {
        propertyCard.style.transition = 'opacity 0.3s';
        propertyCard.style.opacity = '0';
        setTimeout(() => {
            propertyCard.remove();
        }, 300);
        showNotification(`Property has been archived`, 'warning');
    }
});

// 5. LISTEN FOR PROPERTY UNARCHIVED (restored)
socket.on('property-unarchived', function(data) {
    console.log('🔄 Property unarchived:', data.id);
    showNotification(`Property has been restored`, 'info');
    // Optionally reload or fetch the property again
    setTimeout(() => {
        location.reload();
    }, 2000);
});

// Debug: Log all events for troubleshooting
socket.onAny((event, ...args) => {
    if (!['property-added', 'property-updated', 'property-deleted', 'property-archived', 'property-unarchived', 'offer-status-updated'].includes(event)) {
        console.log('Other event:', event, args);
    }
});

// Add animation style
if (!document.querySelector('#socket-styles')) {
    const style = document.createElement('style');
    style.id = 'socket-styles';
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        .realtime-property {
            animation: slideIn 0.5s ease-out;
        }
    `;
    document.head.appendChild(style);
}
</script>

</body>
</html>