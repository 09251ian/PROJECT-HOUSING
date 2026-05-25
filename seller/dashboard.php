<?php
$active = 'seller_dashboard';
$user = $user ?? session()->get('user') ?? [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Seller Dashboard | House Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-dark">

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <div id="seller-realtime-alert-area"></div>

  <div class="d-flex align-items-center justify-content-between mb-3">
    <div>
      <div class="section-title text-white fs-4 mb-0">
        <i class="bi bi-speedometer2 me-2"></i>Seller Dashboard
      </div>
      <div class="text-muted small">Manage listings, offers, and chats.</div>
    </div>
    <div class="d-flex gap-2">
      <a href="<?= base_url('/seller/archived') ?>" class="btn btn-outline-light btn-sm">
        <i class="bi bi-archive me-1"></i>Archived
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-4">
      <div class="app-card h-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div>
            <div class="text-primary fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Property</div>
            <div class="text-muted small">Create a listing with a clear description and price.</div>
          </div>
        </div>

        <form method="post" action="<?= base_url('/seller/add_property') ?>" enctype="multipart/form-data" class="row g-3">
          <?= csrf_field() ?>
          <div class="col-12">
            <label class="form-label">Property Title</label>
            <input type="text" name="title" class="form-control" placeholder="e.g. Modern 2BR Condo" required>
          </div>
          <div class="col-12">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" placeholder="e.g. 4500000" required>
          </div>
          <div class="col-12">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" placeholder="e.g. Ormoc City, Leyte" required>
          </div>
          <div class="col-12">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" placeholder="Describe the property..." required></textarea>
          </div>
          <div class="col-12">
            <label class="form-label">Property Image</label>
            <input type="file" name="image" class="form-control" accept="image/*" required>
          </div>
          <div class="col-12 text-end">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-house-add me-1"></i>Add Property
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="col-lg-8">
      <div class="d-flex align-items-end justify-content-between mb-3">
        <div>
          <div class="section-title text-primary fs-4">Your Properties</div>
          <div class="text-muted small">Keep track of offers and chats per listing.</div>
        </div>
      </div>

      <?php if (!empty($properties)): ?>
        <div class="row" id="properties-container">
          <?php foreach ($properties as $property): ?>
            <div class="col-lg-6 mb-4">
              <div class="property-card h-100" data-property-id="<?= $property['id'] ?>">
                <div class="property-media">
                  <img
                    src="<?= !empty($property['image_path']) ? base_url($property['image_path']) : 'https://via.placeholder.com/800x500?text=No+Image' ?>"
                    alt="Property Image"
                  >
                </div>

                <div class="property-body">
                  <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                      <h5 class="property-title"><?= esc($property['title']) ?></h5>
                      <div class="property-price">₱<?= number_format((float)($property['price'] ?? 0), 2) ?></div>
                      <div class="property-location"><i class="bi bi-geo-alt me-1"></i><?= esc($property['location'] ?? '') ?></div>
                    </div>
                  </div>

                  <p class="property-desc mb-3"><?= esc($property['description'] ?? '') ?></p>

                  <div class="d-flex gap-2 flex-wrap mb-3">
                    <a href="<?= base_url('/seller/edit_property/' . $property['id']) ?>" class="btn btn-outline-primary btn-sm">
                      <i class="bi bi-pencil-square me-1"></i>Edit
                    </a>

                    <form method="post" action="<?= base_url('/seller/archive') ?>" class="m-0">
                      <?= csrf_field() ?>
                      <input type="hidden" name="property_id" value="<?= (int)$property['id'] ?>">
                      <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-archive me-1"></i>Archive
                      </button>
                    </form>
                  </div>

                  <div class="mb-3">
                    <div class="text-muted small fw-bold mb-2">Offers</div>
                    <div class="offers-container">
                      <?php $offers = $offersData[$property['id']] ?? []; ?>
                      <?php if (!empty($offers)): ?>
                        <div class="d-grid gap-2">
                          <?php foreach ($offers as $offer): ?>
                            <div class="app-card" style="padding:12px;" data-offer-id="<?= $offer['id'] ?>">
                              <div class="d-flex align-items-center justify-content-between">
                                <div class="fw-bold">
                                  <i class="bi bi-person me-1"></i><?= esc($offer['buyer_name'] ?? '') ?>
                                </div>
                                <div class="text-primary fw-bold">₱<?= number_format((float)($offer['amount'] ?? 0), 2) ?></div>
                              </div>
                              <div class="mt-2 d-flex align-items-center justify-content-between">
                                <?php
                                  $status = $offer['status'] ?? '';
                                  $pill = match($status) {
                                    'accepted' => 'property-pill--success',
                                    'rejected' => 'property-pill--danger',
                                    default => 'property-pill--warning'
                                  };
                                ?>
                                <span class="property-pill <?= $pill ?>"><?= ucfirst($status) ?></span>
                              </div>

                              <?php if (($offer['status'] ?? '') === 'pending'): ?>
                                <div class="d-flex gap-2 mt-2">
                                  <form method="post" action="<?= base_url('/seller/offer_action') ?>" class="m-0 flex-fill">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="offer_id" value="<?= (int)$offer['id'] ?>">
                                    <input type="hidden" name="action" value="accept">
                                    <button type="submit" class="btn btn-success btn-sm w-100">
                                      <i class="bi bi-check2-circle me-1"></i>Accept
                                    </button>
                                  </form>
                                  <form method="post" action="<?= base_url('/seller/offer_action') ?>" class="m-0 flex-fill">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="offer_id" value="<?= (int)$offer['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger btn-sm w-100">
                                      <i class="bi bi-x-circle me-1"></i>Reject
                                    </button>
                                  </form>
                                </div>
                              <?php endif; ?>

                              <div class="d-flex gap-2 flex-wrap mt-2">
                                <a href="<?= base_url('/message/' . $offer['buyer_id'] . '/' . $property['id']) ?>" class="btn btn-outline-primary btn-sm">
                                  <i class="bi bi-chat-left-text me-1"></i>Message
                                </a>
                                <a href="<?= base_url('/profile/' . $offer['buyer_id']) ?>" class="btn btn-outline-light btn-sm">
                                  <i class="bi bi-person me-1"></i>Profile
                                </a>
                              </div>
                            </div>
                          <?php endforeach; ?>
                        </div>
                      <?php else: ?>
                        <div class="text-muted small">No offers yet.</div>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div>
                    <div class="text-muted small fw-bold mb-2">Active Chats</div>
                    <?php $chats = $chatsData[$property['id']] ?? []; ?>
                    <?php if (!empty($chats)): ?>
                      <div class="d-grid gap-2">
                        <?php foreach ($chats as $chat): ?>
                          <a href="<?= base_url('/message/' . $chat['buyer_id'] . '/' . $property['id']) ?>" class="btn btn-outline-success btn-sm text-start">
                            <i class="bi bi-chat-dots me-1"></i>Chat with <?= esc($chat['buyer_name'] ?? '') ?>
                          </a>
                        <?php endforeach; ?>
                      </div>
                    <?php else: ?>
                      <div class="text-muted small">No chats yet for this property.</div>
                    <?php endif; ?>
                  </div>

                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="text-muted">You haven't added any properties yet.</div>
      <?php endif; ?>

    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

<!-- SOCKET.IO -->
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
// Get current user info - IMPORTANT: These must be defined FIRST
const currentSellerId = <?= json_encode($user['id'] ?? 0) ?>;
const currentSellerName = <?= json_encode($user['name'] ?? '') ?>;

console.log('Seller ID:', currentSellerId);
console.log('Seller Name:', currentSellerName);

// Connect to WebSocket server
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true
});

// Helper function to escape HTML (defined once)
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to show notifications in the alert area
function showNotification(message, type = 'success') {
    const alertArea = document.getElementById('seller-realtime-alert-area');
    if (alertArea) {
        const alertHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <strong>Realtime Update:</strong> ${escapeHtml(message)}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        alertArea.insertAdjacentHTML('afterbegin', alertHTML);
        setTimeout(() => {
            const alerts = alertArea.querySelectorAll('.alert');
            if (alerts.length > 0) {
                alerts[0].remove();
            }
        }, 5000);
    }
}

// Add animation styles
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

// Register user when connected
socket.on('connect', () => {
    console.log('✅ Seller dashboard connected to websocket server');
    socket.emit('register', currentSellerId);
});

socket.on('registered', (data) => {
    console.log('✅ Seller registered with server:', data);
});

socket.on('disconnect', () => {
    console.log('❌ Seller dashboard disconnected from websocket server');
});

socket.on('connect_error', (error) => {
    console.error('Connection error:', error);
});

// ========== LISTEN FOR NEW PROPERTIES FROM ADMIN ==========
socket.on('property-added', function(property) {
    console.log('📦 New property received by seller:', property);
    console.log('Current seller ID:', currentSellerId);
    console.log('Property seller ID:', property.seller_id);
    
    // Check if this property belongs to this seller
    if (property.seller_id == currentSellerId) {
        console.log('✅ This property belongs to current seller, adding to dashboard...');
        addPropertyToDashboard(property);
        showNotification(`New property "${property.title}" has been added to your listings!`, 'success');
    } else {
        console.log('⏭️ Property belongs to another seller, skipping...');
    }
});

// Function to add property to seller dashboard without refresh
function addPropertyToDashboard(property) {
    const propertiesContainer = document.getElementById('properties-container');
    if (!propertiesContainer) {
        console.error('Properties container not found');
        return;
    }
    
    let imagePath = 'https://via.placeholder.com/800x500?text=No+Image';
    if (property.image_path && property.image_path !== 'null') {
        imagePath = '/' + property.image_path.replace(/^\//, '');
    }
    
    const propertyHTML = `
        <div class="col-lg-6 mb-4">
            <div class="property-card h-100" data-property-id="${property.id}">
                <div class="property-media">
                    <img src="${imagePath}" 
                         alt="Property Image"
                         onerror="this.src='https://via.placeholder.com/800x500?text=No+Image'">
                </div>
                <div class="property-body">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div>
                            <h5 class="property-title">${escapeHtml(property.title)}</h5>
                            <div class="property-price">₱${parseFloat(property.price || 0).toLocaleString()}</div>
                            <div class="property-location"><i class="bi bi-geo-alt me-1"></i>${escapeHtml(property.location)}</div>
                        </div>
                    </div>
                    <p class="property-desc mb-3">${escapeHtml(property.description ? property.description.substring(0, 100) : '')}</p>
                    <div class="d-flex gap-2 flex-wrap mb-3">
                        <a href="/seller/edit_property/${property.id}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        <form method="post" action="/seller/archive" class="m-0">
                            <input type="hidden" name="property_id" value="${property.id}">
                            <button type="submit" class="btn btn-warning btn-sm">
                                <i class="bi bi-archive me-1"></i>Archive
                            </button>
                        </form>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted small fw-bold mb-2">Offers</div>
                        <div class="offers-container">
                            <div class="text-muted small">No offers yet.</div>
                        </div>
                    </div>
                    <div class="alert alert-success mt-3 mb-0 small">
                        🎉 New property added by admin!
                    </div>
                </div>
            </div>
        </div>
    `;
    
    propertiesContainer.insertAdjacentHTML('afterbegin', propertyHTML);
    
    // Highlight the new property
    const newProperty = document.querySelector(`.property-card[data-property-id="${property.id}"]`);
    if (newProperty) {
        newProperty.style.transition = 'background-color 0.5s';
        newProperty.style.backgroundColor = '#2a5a2a';
        setTimeout(() => {
            newProperty.style.backgroundColor = '';
        }, 3000);
        
        // Scroll to show the new property
        newProperty.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}

// Listen for new offers
socket.on('new-offer', function(data) {
    console.log('💰 New offer received:', data);
    
    // Find the property card for this offer
    const propertyCard = document.querySelector(`.property-card[data-property-id="${data.property_id}"]`);
    
    if (propertyCard) {
        // Update the offers section
        const offersContainer = propertyCard.querySelector('.offers-container');
        
        if (offersContainer) {
            // Check if offer already exists
            const existingOffer = offersContainer.querySelector(`[data-offer-id="${data.id}"]`);
            if (!existingOffer) {
                const newOfferHTML = `
                    <div class="app-card" style="padding:12px; margin-bottom: 10px;" data-offer-id="${data.id}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-bold">
                                <i class="bi bi-person me-1"></i>${escapeHtml(data.buyer_name)}
                            </div>
                            <div class="text-primary fw-bold">₱${parseFloat(data.amount || 0).toLocaleString()}</div>
                        </div>
                        <div class="mt-2 d-flex align-items-center justify-content-between">
                            <span class="property-pill property-pill--warning">Pending</span>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <form method="post" action="/seller/offer_action" class="m-0 flex-fill">
                                <input type="hidden" name="offer_id" value="${data.id}">
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="bi bi-check2-circle me-1"></i>Accept
                                </button>
                            </form>
                            <form method="post" action="/seller/offer_action" class="m-0 flex-fill">
                                <input type="hidden" name="offer_id" value="${data.id}">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger btn-sm w-100">
                                    <i class="bi bi-x-circle me-1"></i>Reject
                                </button>
                            </form>
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <a href="/message/${data.buyer_id}/${data.property_id}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-chat-left-text me-1"></i>Message
                            </a>
                            <a href="/profile/${data.buyer_id}" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-person me-1"></i>Profile
                            </a>
                        </div>
                    </div>
                `;
                
                if (offersContainer.querySelector('.d-grid')) {
                    const gridContainer = offersContainer.querySelector('.d-grid');
                    gridContainer.insertAdjacentHTML('afterbegin', newOfferHTML);
                } else {
                    offersContainer.innerHTML = `<div class="d-grid gap-2">${newOfferHTML}</div>`;
                }
                
                const noOffersMsg = offersContainer.querySelector('.text-muted');
                if (noOffersMsg && noOffersMsg.textContent.includes('No offers yet')) {
                    noOffersMsg.remove();
                }
                
                const newOffer = offersContainer.querySelector(`[data-offer-id="${data.id}"]`);
                if (newOffer) {
                    newOffer.style.transition = 'background-color 0.5s';
                    newOffer.style.backgroundColor = '#fff3cd';
                    setTimeout(() => {
                        newOffer.style.backgroundColor = '';
                    }, 3000);
                }
            }
        }
        
        showNotification(`💰 New offer of ₱${parseFloat(data.amount || 0).toLocaleString()} from ${escapeHtml(data.buyer_name)} on ${escapeHtml(data.property_title)}`, 'info');
    }
});

// Log all events for debugging
socket.onAny((event, ...args) => {
    console.log('Socket event:', event, args);
});
</script>
</body>
</html>