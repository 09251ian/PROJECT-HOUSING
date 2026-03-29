// WebSocket connection
let ws;
let wsConnected = false;
let pendingAuth = false;

function connectWebSocket() {
    ws = new WebSocket('ws://localhost:8080');

    ws.onopen = function() {
        console.log('WebSocket connection established');
        // Authenticate as seller
        if (window.userId && window.userRole) {
            ws.send(JSON.stringify({
                type: 'AUTH',
                payload: {
                    user_id: window.userId,
                    role: window.userRole,
                    name: window.userName
                }
            }));
            pendingAuth = true;
        }
    };

    ws.onmessage = function(event) {
        let data = JSON.parse(event.data);
        console.log('Received:', data.type, data.payload);

        // Handle authentication result
        if (data.type === 'AUTH_SUCCESS') {
            console.log('Authenticated successfully');
            wsConnected = true;
            pendingAuth = false;
            // Optionally fetch initial data (properties, offers) via WebSocket if needed
        } else if (data.type === 'AUTH_FAILED') {
            console.error('Authentication failed:', data.payload.message);
            wsConnected = false;
        }

        // Handle property creation/update/archive responses
        switch (data.type) {
            case 'PROPERTY_CREATED':
                addPropertyToDOM(data.payload);
                break;
            case 'PROPERTY_UPDATED':
                updatePropertyInDOM(data.payload);
                break;
            case 'PROPERTY_ARCHIVED':
                removePropertyFromDOM(data.payload.property_id);
                break;
            case 'PROPERTY_DELETED':
                removePropertyFromDOM(data.payload.property_id);
                break;
            case 'NEW_OFFER':
                addOfferToDOM(data.payload);
                break;
            case 'OFFER_UPDATED':
                updateOfferInDOM(data.payload);
                break;
            case 'NEW_MESSAGE':
                // Show notification or update chat list
                showNotification('New message from ' + data.payload.sender_name);
                break;
            default:
                // Handle other types if needed
                break;
        }
    };

    ws.onerror = function(error) {
        console.error('WebSocket error:', error);
        wsConnected = false;
    };

    ws.onclose = function() {
        console.log('WebSocket disconnected. Reconnecting in 5 seconds...');
        wsConnected = false;
        setTimeout(connectWebSocket, 5000);
    };
}

// Helper: add property card to DOM
function addPropertyToDOM(property) {
    const container = document.getElementById('properties-container');
    if (!container) return;

    // Avoid duplicates
    if (document.querySelector(`.col-md-6[data-property-id="${property.id}"]`)) return;

    const imageUrl = property.image_path ? window.baseUrl + '/' + property.image_path : 'https://via.placeholder.com/400x250?text=No+Image';

    const propertyHtml = `
        <div class="col-md-6 mb-4" data-property-id="${property.id}">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <img src="${imageUrl}" class="card-img-top" style="height: 250px; object-fit: cover;" alt="Property Image">
                <div class="card-body">
                    <h5 class="card-title text-success fw-bold">${escapeHtml(property.title)}</h5>
                    <p class="card-text">${escapeHtml(property.description)}</p>
                    <p class="mb-1"><b>📍 Location:</b> ${escapeHtml(property.location)}</p>
                    <p class="fw-bold text-primary">₱${Number(property.price).toFixed(2)}</p>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <a href="/seller/edit_property/${property.id}" class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                        <button type="button" class="btn btn-warning btn-sm archive-btn" data-property-id="${property.id}">📦 Archive</button>
                    </div>
                    
                    <h6 class="text-muted">Offers:</h6>
                    <div class="offers-section" data-property-id="${property.id}">
                        <p class="text-muted small mb-2">No offers yet.</p>
                    </div>
                    
                    <p class="text-muted small mb-0">Chat feature coming soon.</p>
                </div>
            </div>
        </div>
    `;

    // Insert at the top (newest first)
    container.insertAdjacentHTML('afterbegin', propertyHtml);
}

// Helper: update property in DOM (e.g., update title, price, etc.)
function updatePropertyInDOM(property) {
    const card = document.querySelector(`.col-md-6[data-property-id="${property.id}"] .card`);
    if (!card) return;
    // Update fields
    card.querySelector('.card-title').textContent = property.title;
    card.querySelector('.card-text').textContent = property.description;
    // Update location
    const locationSpan = card.querySelector('.mb-1 b');
    if (locationSpan) locationSpan.parentElement.innerHTML = `<b>📍 Location:</b> ${escapeHtml(property.location)}`;
    // Update price
    const priceElem = card.querySelector('.fw-bold.text-primary');
    if (priceElem) priceElem.textContent = `₱${Number(property.price).toFixed(2)}`;
    // Update image if changed
    const img = card.querySelector('.card-img-top');
    if (img && property.image_path) img.src = property.image_path;
}

// Helper: remove property card from DOM
function removePropertyFromDOM(propertyId) {
    const card = document.querySelector(`.col-md-6[data-property-id="${propertyId}"]`);
    if (card) card.remove();
}

// Helper: add new offer to the property's offers list
function addOfferToDOM(offer) {
    const offersContainer = document.querySelector(`.offers-section[data-property-id="${offer.property_id}"]`);
    if (!offersContainer) return;

    // Get or create the offers list
    let offersList = offersContainer.querySelector('.list-group');
    if (!offersList) {
        offersContainer.innerHTML = '<ul class="list-group mb-2"></ul>';
        offersList = offersContainer.querySelector('.list-group');
        // Remove the "No offers yet." message if it exists
        const noOffersMsg = offersContainer.querySelector('.text-muted.small');
        if (noOffersMsg && noOffersMsg.innerText === 'No offers yet.') {
            noOffersMsg.remove();
        }
    }

    const offerHtml = `
        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap" data-offer-id="${offer.id}">
            <div>
                💬 <b>${escapeHtml(offer.buyer_name)}</b> offered 
                <span class="text-primary fw-bold">₱${Number(offer.amount).toFixed(2)}</span><br>
                <small>Status: 
                    <span class="fw-bold text-${offer.status === 'accepted' ? 'success' : (offer.status === 'rejected' ? 'danger' : 'secondary')}">
                        ${offer.status.charAt(0).toUpperCase() + offer.status.slice(1)}
                    </span>
                </small><br>
                <a href="/message/${offer.buyer_id}/${offer.property_id}" class="btn btn-outline-primary btn-sm mt-2">💬 Message Buyer</a>
                <a href="/profile/${offer.buyer_id}" class="btn btn-outline-secondary btn-sm mt-2">👤 View Profile</a>
            </div>
            ${offer.status === 'pending' ? `
                <div>
                    <button class="btn btn-sm btn-success accept-offer" data-offer-id="${offer.id}" data-property-id="${offer.property_id}">Accept</button>
                    <button class="btn btn-sm btn-danger reject-offer" data-offer-id="${offer.id}" data-property-id="${offer.property_id}">Reject</button>
                </div>
            ` : ''}
        </li>
    `;
    offersList.insertAdjacentHTML('beforeend', offerHtml);
}

// Helper: update existing offer (accept/reject)
function updateOfferInDOM(payload) {
    // Find the offer list item by data-offer-id on the <li> itself
    const offerItem = document.querySelector(`li.list-group-item[data-offer-id="${payload.offer_id}"]`);
    if (!offerItem) return;

    const statusSpan = offerItem.querySelector('.fw-bold.text-secondary, .fw-bold.text-success, .fw-bold.text-danger');
    if (statusSpan) {
        statusSpan.textContent = payload.status.charAt(0).toUpperCase() + payload.status.slice(1);
        statusSpan.className = `fw-bold text-${payload.status === 'accepted' ? 'success' : (payload.status === 'rejected' ? 'danger' : 'secondary')}`;
    }

    // Remove accept/reject buttons if offer is no longer pending
    if (payload.status !== 'pending') {
        const actionsDiv = offerItem.querySelector('div:last-child');
        if (actionsDiv && actionsDiv.innerHTML.includes('Accept')) {
            actionsDiv.innerHTML = '';
        }
    }
}

// Helper to send a WebSocket message
function sendWSMessage(type, payload) {
    if (!wsConnected) {
        console.error('WebSocket not connected');
        return false;
    }
    ws.send(JSON.stringify({ type, payload }));
    return true;
}

// Helper to escape HTML to prevent XSS
function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Function to show simple toast notification (optional)
function showNotification(message) {
    // Use Bootstrap toast or alert
    const toast = document.createElement('div');
    toast.className = 'alert alert-info alert-dismissible fade show position-fixed top-0 end-0 m-3';
    toast.style.zIndex = 9999;
    toast.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}