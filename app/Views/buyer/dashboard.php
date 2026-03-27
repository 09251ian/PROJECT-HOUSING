<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Dashboard | House System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <style>
    .property-card {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .property-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
   
  </style>
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
  <div class="container">
    <div class="d-flex align-items-center">
      <span class="navbar-brand fw-bold me-3">🏡 Buyer Dashboard</span>
      <a href="<?= base_url('/profile/' . $user['id']) ?>" class="btn btn-outline-light btn-sm">👤 View Profile</a>
    </div>

    <div class="d-flex align-items-center">
      <span class="navbar-text text-white me-3">
        Welcome, <b><?= esc($user['name']) ?></b>
      </span>
      <a href="<?= base_url('/logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">

  <!-- Flash messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  

  <!-- Toast Notification Container -->
  <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-header">
        <strong class="me-auto"><i class="fas fa-bell"></i> Notification</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
      </div>
      <div class="toast-body" id="toastMessage"></div>
    </div>
  </div>

  <h3 class="text-success fw-bold mb-3">
    <i class="fas fa-home"></i> Available Properties
  </h3>

  <!-- Search & Filter -->
  <form id="searchForm" class="row g-2 mb-4">

    <div class="col-md-4">
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="text"
               name="search"
               id="search"
               class="form-control"
               placeholder="Search by title or description"
               value="<?= esc($search ?? '') ?>">
      </div>
    </div>

    <div class="col-md-3">
      <div class="input-group">
        <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
        <input type="text"
               name="location"
               id="location"
               class="form-control"
               placeholder="Search by location"
               value="<?= esc($location ?? '') ?>">
      </div>
    </div>

    <div class="col-md-3">
      <select name="price_range" id="price_range" class="form-select">
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
          <option value="<?= $key ?>" <?= (isset($price_range) && $price_range == $key) ? 'selected' : '' ?>>
            <?= $label ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-2 d-flex gap-2">
      <button type="submit" class="btn btn-success w-100">
        <i class="fas fa-search"></i> Search
      </button>
      <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100">
        <i class="fas fa-undo"></i> Reset
      </button>
    </div>

  </form>

  <!-- Loading Spinner -->
  <div id="loadingSpinner" class="text-center" style="display: none;">
    <div class="spinner-border text-success" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>

  <!-- Property Listings -->
  <div class="row" id="propertyResults">
    <div class="col-12 text-center">
      <div class="spinner-border text-success" role="status">
        <span class="visually-hidden">Loading properties...</span>
      </div>
    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>

// Store CSRF token and user info
const csrfToken = '<?= csrf_hash() ?>';
const userId = <?= $user['id'] ?? 0 ?>;
const userName = '<?= addslashes($user['name'] ?? 'Guest') ?>';
const userRole = '<?= $user['role'] ?? 'buyer' ?>';
const baseUrl = '<?= base_url() ?>';
const websocketPort = <?= $websocket_port ?? 8080 ?>;

// Debug logging
let debugLogs = [];
function addDebugLog(message, data = null) {
    const timestamp = new Date().toLocaleTimeString();
    const logEntry = `[${timestamp}] ${message}`;
    debugLogs.unshift(logEntry);
    if (debugLogs.length > 50) debugLogs.pop();
    
    const debugDiv = $('#debugLog');
    debugDiv.html(debugLogs.map(log => `<div style="border-bottom: 1px solid #333; padding: 2px 0;">${escapeHtml(log)}</div>`).join(''));
    
    if (data) {
        console.log(message, data);
    } else {
        console.log(message);
    }
}

$('#toggleDebugBtn').on('click', function() {
    $('#debugConsole').toggleClass('show');
    $(this).html($('#debugConsole').hasClass('show') ? '<i class="fas fa-bug"></i> Hide Debug' : '<i class="fas fa-bug"></i> Debug Console');
});

addDebugLog('=== Dashboard Initialized ===');
addDebugLog(`User: ${userName} (ID: ${userId})`);
addDebugLog(`Base URL: ${baseUrl}`);

// WebSocket Connection (silent)
let ws = null;
let reconnectAttempts = 0;
const maxReconnectAttempts = 10;
let pollingInterval = null;
let isConnecting = false;
let heartbeatInterval = null;

async function isWebSocketServerRunning() {
    try {
        const response = await fetch(baseUrl + '/buyer/api/websocket-status?port=' + websocketPort);
        const data = await response.json();
        addDebugLog(`Server status check: ${data.status}`);
        return data.status === 'online';
    } catch (error) {
        addDebugLog(`Server status check failed: ${error.message}`);
        return false;
    }
}

function connectWebSocket() {
    if (isConnecting || (ws && ws.readyState === WebSocket.OPEN)) return;
    
    isConnecting = true;
    
    isWebSocketServerRunning().then(serverRunning => {
        if (!serverRunning) {
            addDebugLog('WebSocket server not running, using polling mode');
            isConnecting = false;
            if (!pollingInterval) {
                pollingInterval = setInterval(() => loadProperties(), 15000);
            }
            return;
        }
        
        const wsUrl = `ws://${window.location.hostname}:${websocketPort}`;
        addDebugLog(`Attempting WebSocket connection to: ${wsUrl}`);
        
        try {
            ws = new WebSocket(wsUrl);
            
            const connectionTimeout = setTimeout(() => {
                if (ws && ws.readyState !== WebSocket.OPEN) {
                    addDebugLog('Connection timeout');
                    ws.close();
                    handleConnectionFailure();
                }
            }, 5000);
            
            ws.onopen = function() {
                clearTimeout(connectionTimeout);
                addDebugLog('WebSocket connected');
                reconnectAttempts = 0;
                isConnecting = false;
                
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
                
                if (heartbeatInterval) clearInterval(heartbeatInterval);
                heartbeatInterval = setInterval(() => {
                    if (ws && ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({ type: 'PING', payload: { timestamp: Date.now() } }));
                    }
                }, 30000);
                
                const authMessage = {
                    type: 'AUTH',
                    payload: { user_id: userId, role: userRole, name: userName }
                };
                ws.send(JSON.stringify(authMessage));
                addDebugLog('Authentication sent');
            };
            
            ws.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    handleWebSocketMessage(data);
                } catch (e) {
                    addDebugLog(`Error parsing message: ${e.message}`);
                }
            };
            
            ws.onclose = function() {
                clearTimeout(connectionTimeout);
                addDebugLog('WebSocket disconnected');
                isConnecting = false;
                if (heartbeatInterval) clearInterval(heartbeatInterval);
                
                if (reconnectAttempts < maxReconnectAttempts) {
                    reconnectAttempts++;
                    const delay = Math.min(3000 * reconnectAttempts, 30000);
                    addDebugLog(`Reconnecting in ${delay/1000}s`);
                    setTimeout(connectWebSocket, delay);
                } else {
                    addDebugLog('Max reconnections, switching to polling');
                    if (!pollingInterval) {
                        pollingInterval = setInterval(() => loadProperties(), 15000);
                    }
                }
            };
            
            ws.onerror = function() {
                addDebugLog('WebSocket error');
            };
            
        } catch (error) {
            addDebugLog(`Failed to create WebSocket: ${error.message}`);
            isConnecting = false;
            handleConnectionFailure();
        }
    });
}

function handleConnectionFailure() {
    if (reconnectAttempts < maxReconnectAttempts) {
        reconnectAttempts++;
        const delay = Math.min(3000 * reconnectAttempts, 30000);
        setTimeout(connectWebSocket, delay);
    } else {
        addDebugLog('Using polling mode');
        if (!pollingInterval) {
            pollingInterval = setInterval(() => loadProperties(), 15000);
        }
    }
}

function handleWebSocketMessage(data) {
    switch(data.type) {
        case 'AUTH_SUCCESS':
            addDebugLog('Authentication successful');
            loadProperties();
            break;
        case 'NEW_OFFER':
            if (data.payload?.property_title) {
                showToast(`💰 New offer on "${data.payload.property_title}"`, 'info');
            }
            loadProperties();
            break;
        case 'OFFER_UPDATED':
            const statusText = data.payload.status === 'accepted' ? 'accepted! 🎉' : 'rejected';
            showToast(`📝 Your offer has been ${statusText}`, data.payload.status === 'accepted' ? 'success' : 'warning');
            loadProperties();
            break;
        case 'OFFER_SUBMITTED':
            showToast('✅ Offer submitted!', 'success');
            loadProperties();
            break;
        case 'NEW_MESSAGE':
            if (data.payload?.sender_name) {
                showToast(`💬 New message from ${data.payload.sender_name}`, 'info');
            }
            break;
        default:
            break;
    }
}

function showToast(message, type = 'info') {
    const toastElement = $('#liveToast');
    const toastBody = $('#toastMessage');
    
    toastElement.removeClass('bg-success bg-danger bg-warning bg-info text-white');
    if (type === 'success') toastElement.addClass('bg-success text-white');
    else if (type === 'error') toastElement.addClass('bg-danger text-white');
    else if (type === 'warning') toastElement.addClass('bg-warning');
    else toastElement.addClass('bg-info text-white');
    
    toastBody.text(message);
    const toast = new bootstrap.Toast(toastElement[0]);
    toast.show();
    
    setTimeout(() => {
        toastElement.removeClass('bg-success bg-danger bg-warning bg-info');
    }, 4000);
}

// Load properties via AJAX
function loadProperties() {
    const search = $('#search').val();
    const location = $('#location').val();
    const price_range = $('#price_range').val();
    
    $('#loadingSpinner').show();
    
    addDebugLog(`Loading properties - Search: "${search}", Location: "${location}", Price: "${price_range}"`);
    
    $.ajax({
        url: baseUrl + '/buyer/api/properties',
        type: "GET",
        data: {
            search: search,
            location: location,
            price_range: price_range,
            _t: Date.now()
        },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            addDebugLog(`Properties loaded: ${response.properties?.length || 0} found`);
            if (response.success) {
                updatePropertyListings(response.properties);
            } else {
                addDebugLog(`Error from API: ${response.error}`);
                showToast('Error loading properties', 'error');
            }
        },
        error: function(xhr, status, error) {
            addDebugLog(`AJAX error: ${status} - ${error}`);
            showToast('Network error loading properties', 'error');
        },
        complete: function() {
            $('#loadingSpinner').hide();
        }
    });
}

// Update property listings
function updatePropertyListings(properties) {
    let html = '';
    
    if (!properties || properties.length === 0) {
        html = '<div class="col-12 text-center text-muted py-5"><i class="fas fa-home fa-3x mb-3"></i><p>No properties found.</p></div>';
    } else {
        properties.forEach(property => {
            const hasOffer = property.offer && property.offer.buyer_id === userId;
            const offerStatus = hasOffer ? property.offer.status : null;
            const offerId = hasOffer ? property.offer.id : null;
            const offerAmount = hasOffer ? property.offer.amount : null;
            
            let imagePath = 'https://via.placeholder.com/400x250/198754/ffffff?text=No+Image';
            if (property.image_path && property.image_path !== '') {
                if (property.image_path.startsWith('http')) {
                    imagePath = property.image_path;
                } else if (property.image_path.startsWith('/')) {
                    imagePath = baseUrl + property.image_path;
                } else {
                    imagePath = baseUrl + '/' + property.image_path;
                }
            }
            
            html += `
                <div class="col-md-6 mb-4 property-card" data-property-id="${property.id}">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <img src="${imagePath}" class="card-img-top" style="height:250px; object-fit:cover;"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/400x250/cccccc/666666?text=Image+Not+Found'"
                             alt="Property">
                        <div class="card-body">
                            <h5 class="card-title text-success fw-bold">${escapeHtml(property.title)}</h5>
                            <p class="card-text small">${escapeHtml(property.description.substring(0, 100))}${property.description.length > 100 ? '...' : ''}</p>
                            <p class="fw-bold text-primary fs-4">₱${formatNumber(property.price)}</p>
                            <p class="mb-1"><i class="fas fa-map-marker-alt"></i> ${escapeHtml(property.location)}</p>
                            <p class="mb-2 small text-muted"><i class="fas fa-user"></i> ${escapeHtml(property.seller_name)}</p>
                            <div class="d-flex gap-2 mb-3">
                                <a href="${baseUrl}/message/${property.seller_id}/${property.id}" class="btn btn-outline-success btn-sm flex-grow-1">
                                    <i class="fas fa-comment"></i> Message Seller
                                </a>
                            </div>
                            <div class="offer-section">
                                ${renderOfferSection(property, hasOffer, offerStatus, offerId, offerAmount)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    $('#propertyResults').html(html);
    attachOfferFormHandlers();
}

function renderOfferSection(property, hasOffer, offerStatus, offerId, offerAmount) {
    if (hasOffer) {
        if (offerStatus === 'pending') {
            return `
                <div class="alert alert-warning mb-0 p-2">
                    <i class="fas fa-clock"></i> <strong>Pending Offer</strong><br>
                    ₱${formatNumber(offerAmount)} - Waiting for response
                    <button class="btn btn-sm btn-outline-danger mt-2 w-100" onclick="cancelOffer(${offerId})">
                        <i class="fas fa-times"></i> Cancel Offer
                    </button>
                </div>`;
        } else if (offerStatus === 'accepted') {
            return `
                <div class="alert alert-success mb-0 p-2">
                    <i class="fas fa-check-circle"></i> <strong>Offer Accepted!</strong><br>
                    ₱${formatNumber(offerAmount)} - Contact seller.
                </div>`;
        } else if (offerStatus === 'rejected') {
            return `
                <div class="alert alert-danger mb-0 p-2">
                    <i class="fas fa-times-circle"></i> <strong>Offer Rejected</strong>
                    <button class="btn btn-sm btn-primary mt-2 w-100" onclick="makeNewOffer(${property.id})">
                        <i class="fas fa-plus"></i> Make New Offer
                    </button>
                </div>`;
        } else {
            return `
                <div class="alert alert-secondary mb-0 p-2">
                    <i class="fas fa-ban"></i> Offer ${offerStatus}
                    <button class="btn btn-sm btn-primary mt-2 w-100" onclick="makeNewOffer(${property.id})">
                        <i class="fas fa-plus"></i> Make New Offer
                    </button>
                </div>`;
        }
    } else {
        return `
            <form class="offer-form" data-property-id="${property.id}">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-money-bill"></i></span>
                    <input type="number" step="0.01" name="amount" class="form-control" 
                           placeholder="Enter offer amount" required min="1000">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Make Offer
                    </button>
                </div>
                <small class="text-muted">Minimum: ₱1,000</small>
            </form>`;
    }
}

// FIXED: Cancel offer function with proper error handling
window.cancelOffer = function(offerId) {
    if (!confirm('Cancel this offer?')) return;
    
    addDebugLog(`Cancelling offer ID: ${offerId}`);
    
    $.ajax({
        url: baseUrl + '/buyer/api/offer/cancel',
        type: "POST",
        data: {
            offer_id: offerId,
            csrf_test_name: csrfToken
        },
        dataType: 'json',
        success: function(response) {
            addDebugLog(`Cancel response:`, response);
            if (response.success) {
                showToast('✅ Offer cancelled', 'success');
                loadProperties();
                if (ws && ws.readyState === WebSocket.OPEN) {
                    ws.send(JSON.stringify({ type: 'CANCEL_OFFER', payload: { offer_id: offerId } }));
                }
            } else {
                addDebugLog(`Cancel failed: ${response.error}`);
                showToast('❌ ' + (response.error || 'Failed to cancel'), 'error');
            }
        },
        error: function(xhr, status, error) {
            addDebugLog(`Cancel AJAX error: ${status} - ${error}`);
            showToast('❌ Error cancelling offer', 'error');
        }
    });
};

window.makeNewOffer = function(propertyId) {
    const form = $(`.offer-form[data-property-id="${propertyId}"]`);
    if (form.length) {
        form.find('input[name="amount"]').focus();
        form[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
};

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatNumber(number) {
    return new Intl.NumberFormat('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(number);
}

// FIXED: Attach handlers with detailed error logging
function attachOfferFormHandlers() {
    $('.offer-form').off('submit').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const propertyId = form.data('property-id');
        const amountInput = form.find('input[name="amount"]');
        const amount = parseFloat(amountInput.val());
        
        addDebugLog(`=== Submitting Offer ===`);
        addDebugLog(`Property ID: ${propertyId}`);
        addDebugLog(`Amount: ${amount}`);
        addDebugLog(`User ID: ${userId}`);
        addDebugLog(`CSRF Token: ${csrfToken.substring(0, 20)}...`);
        addDebugLog(`API URL: ${baseUrl}/buyer/api/offer`);
        
        if (!amount || amount <= 0) {
            addDebugLog(`ERROR: Invalid amount: ${amount}`);
            showToast('Please enter a valid offer amount', 'warning');
            return;
        }
        
        if (amount < 1000) {
            addDebugLog(`ERROR: Amount below minimum: ${amount}`);
            showToast('Minimum offer amount is ₱1,000', 'warning');
            return;
        }
        
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        // Prepare data - try different formats that might be expected by backend
        const postData = {
            property_id: propertyId,
            amount: amount,
            csrf_test_name: csrfToken
        };
        
        addDebugLog(`Sending data:`, postData);
        
        $.ajax({
            url: baseUrl + '/buyer/api/offer',
            type: "POST",
            data: postData,
            dataType: 'json',
            timeout: 30000,
            success: function(response) {
                addDebugLog(`=== Offer Response ===`);
                addDebugLog(`Success: ${response.success}`);
                addDebugLog(`Full response:`, response);
                
                if (response.success) {
                    showToast('✅ Offer submitted! Waiting for seller response...', 'success');
                    loadProperties();
                    
                    if (ws && ws.readyState === WebSocket.OPEN) {
                        ws.send(JSON.stringify({
                            type: 'MAKE_OFFER',
                            payload: {
                                property_id: propertyId,
                                amount: amount,
                                buyer_id: userId,
                                buyer_name: userName
                            }
                        }));
                    }
                } else {
                    const errorMsg = response.error || response.message || 'Failed to submit offer';
                    addDebugLog(`ERROR: ${errorMsg}`);
                    showToast('❌ ' + errorMsg, 'error');
                    
                    if (errorMsg.toLowerCase().includes('pending')) {
                        loadProperties();
                    }
                }
            },
            error: function(xhr, status, error) {
                addDebugLog(`=== AJAX ERROR ===`);
                addDebugLog(`Status: ${status}`);
                addDebugLog(`Error: ${error}`);
                addDebugLog(`Status Code: ${xhr.status}`);
                addDebugLog(`Response Text: ${xhr.responseText}`);
                
                let errorMsg = 'Network error';
                try {
                    const responseJson = JSON.parse(xhr.responseText);
                    errorMsg = responseJson.error || responseJson.message || errorMsg;
                    addDebugLog(`Parsed error: ${errorMsg}`);
                } catch(e) {
                    addDebugLog(`Could not parse response: ${xhr.responseText}`);
                }
                
                showToast('❌ ' + errorMsg, 'error');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
}

// Search form handler
$('#searchForm').on('submit', function(e) {
    e.preventDefault();
    loadProperties();
});

$('#resetBtn').on('click', function() {
    $('#search').val('');
    $('#location').val('');
    $('#price_range').val('');
    loadProperties();
});

// Initialize
$(document).ready(function() {
    addDebugLog('Document ready, initializing...');
    loadProperties();
    setTimeout(() => connectWebSocket(), 1000);
});

// Clean up
window.addEventListener('beforeunload', function() {
    if (ws) ws.close();
    if (pollingInterval) clearInterval(pollingInterval);
    if (heartbeatInterval) clearInterval(heartbeatInterval);
});

// Manual reconnect for debugging
window.reconnectWebSocket = function() {
    if (ws) ws.close();
    if (pollingInterval) clearInterval(pollingInterval);
    if (heartbeatInterval) clearInterval(heartbeatInterval);
    reconnectAttempts = 0;
    isConnecting = false;
    connectWebSocket();
};

</script>

</body>
</html>