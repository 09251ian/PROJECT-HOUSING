<!doctype html>
<html lang="en">

<head>

  <meta charset="utf-8">

  <title>Admin Dashboard | House System</title>

  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">

</head>

<body class="app-dark">

<nav class="navbar navbar-expand-lg navbar-dark app-navbar">

  <div class="container">

    <a class="navbar-brand fw-bold"
       href="<?= base_url('/admin/dashboard') ?>">
       🏠 House Admin
    </a>

    <div class="d-flex gap-2">

      <a class="btn btn-outline-light btn-sm"
         href="<?= base_url('/admin/users') ?>">
         Users
      </a>

      <a class="btn btn-outline-light btn-sm"
         href="<?= base_url('/admin/properties') ?>">
         Properties
      </a>

      <a class="btn btn-outline-light btn-sm"
         href="<?= base_url('/admin/offers') ?>">
         Offers
      </a>

      <a class="btn btn-outline-light btn-sm"
         href="<?= base_url('/admin/payments') ?>">
         Payments
      </a>

      <a class="btn btn-outline-light btn-sm" 
        href="<?= base_url('/admin/audit') ?>">
        Audit
      </a>

      <a class="btn btn-danger btn-sm"
         href="<?= base_url('/logout') ?>">
         Logout
      </a>

    </div>

  </div>

</nav>

<div class="container py-4">

  <div class="d-flex align-items-center justify-content-between mb-3">

    <h3 class="text-white mb-0">
      Admin Dashboard
    </h3>

  </div>

  <!-- REALTIME NOTIFICATION AREA -->
  <div id="realtime-alert-area"></div>

  <div class="row g-3">

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Buyers
        </div>

        <div class="app-card-value text-primary" id="buyers-count">
          <?= htmlspecialchars((string)($buyersCount ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Sellers
        </div>

        <div class="app-card-value text-primary" id="sellers-count">
          <?= htmlspecialchars((string)($sellersCount ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Properties
        </div>

        <div class="app-card-value text-primary" id="property-count">
          <?= htmlspecialchars((string)($propertiesCount ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Offers
        </div>

        <div class="app-card-value text-primary" id="offers-count">
          <?= htmlspecialchars((string)($offersCount ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>

      </div>

    </div>

    <div class="col-md-12">

      <div class="app-card p-3">

        <div class="d-flex justify-content-between align-items-center">

          <div>

            <div class="app-card-title">
              Payments (Transactions)
            </div>

            <div class="app-card-value text-primary" id="payments-count">
              <?= htmlspecialchars((string)($paymentsCount ?? 0), ENT_QUOTES, 'UTF-8') ?>
            </div>

          </div>

          <a href="<?= base_url('/admin/payments') ?>"
             class="btn btn-outline-primary">

             Manage Payments

          </a>

        </div>

      </div>

    </div>

  </div>

  

</div>

<!-- SOCKET.IO -->
<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>

<script>
// FIXED: Initialize counters safely with fallback values
function getSafeNumber(elementId, defaultValue = 0) {
    const element = document.getElementById(elementId);
    if (!element) {
        console.warn(`Element with ID '${elementId}' not found`);
        return defaultValue;
    }
    const text = element.innerText || element.textContent || '';
    const number = parseInt(text.trim());
    return isNaN(number) ? defaultValue : number;
}

// Initialize counters with safe values
let currentBuyersCount = getSafeNumber('buyers-count', 0);
let currentSellersCount = getSafeNumber('sellers-count', 0);
let currentPropertiesCount = getSafeNumber('property-count', 0);
let currentOffersCount = getSafeNumber('offers-count', 0);
let currentPaymentsCount = getSafeNumber('payments-count', 0);

console.log('Initial counters:', {
    buyers: currentBuyersCount,
    sellers: currentSellersCount,
    properties: currentPropertiesCount,
    offers: currentOffersCount,
    payments: currentPaymentsCount
});

const socket = io('http://localhost:3000');

socket.on('connect', () => {
    console.log('✅ Admin connected to websocket server');
});

// Listen for new user registrations
socket.on('new-user', function(user) {
    console.log('👤 New user registered:', user);
    
    // Update counts based on user role
    if (user.role === 'buyer') {
        currentBuyersCount++;
        const buyersElement = document.getElementById('buyers-count');
        if (buyersElement) {
            buyersElement.innerText = currentBuyersCount;
        }
        console.log(`Buyers count updated to: ${currentBuyersCount}`);
    } else if (user.role === 'seller') {
        currentSellersCount++;
        const sellersElement = document.getElementById('sellers-count');
        if (sellersElement) {
            sellersElement.innerText = currentSellersCount;
        }
        console.log(`Sellers count updated to: ${currentSellersCount}`);
    }
    
    // Add to recent users table
    const usersTable = document.getElementById('recent-users-table');
    if (usersTable) {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>${user.id}</td>
            <td>${escapeHtml(user.name)}</td>
            <td>${escapeHtml(user.email)}</td>
            <td><span class="badge bg-${user.role === 'seller' ? 'warning' : 'info'}">${user.role}</span></td>
            <td>${user.created_at || 'Just now'}</td>
        `;
        
        usersTable.insertBefore(newRow, usersTable.firstChild);
        
        // Keep only last 10 rows
        while (usersTable.children.length > 10) {
            usersTable.removeChild(usersTable.lastChild);
        }
    }
    
    // Show real-time alert for new user
    showAlert(
        'info',
        `<strong>New User Registered!</strong><br>
         Name: ${escapeHtml(user.name)}<br>
         Email: ${escapeHtml(user.email)}<br>
         Role: ${user.role.toUpperCase()}`
    );
});

// Listen for property additions
socket.on('property-added', function(property) {
    console.log('🏠 New property detected:', property);
    
    // UPDATE PROPERTY COUNT
    currentPropertiesCount++;
    const propertyCountElement = document.getElementById('property-count');
    if (propertyCountElement) {
        propertyCountElement.innerText = currentPropertiesCount;
    }
    
    // SHOW REALTIME ALERT
    showAlert(
        'success',
        `<strong>New Property Added!</strong><br>
         Title: <b>${escapeHtml(property.title)}</b><br>
         Seller: ${escapeHtml(property.seller_name)}<br>
         Location: ${escapeHtml(property.location)}<br>
         Price: $${property.price}`
    );
});

// Listen for property deletions
socket.on('property-deleted', function(data) {
    console.log('🗑️ Property deleted:', data);
    
    // UPDATE PROPERTY COUNT
    currentPropertiesCount = Math.max(0, currentPropertiesCount - 1);
    const propertyCountElement = document.getElementById('property-count');
    if (propertyCountElement) {
        propertyCountElement.innerText = currentPropertiesCount;
    }
    
    // SHOW REALTIME ALERT
    showAlert(
        'danger',
        `<strong>Property Deleted!</strong><br>
         Property ID: ${data.id} has been removed.`
    );
});

// Listen for new offers
socket.on('new-offer', function(offer) {
    console.log('💰 New offer detected:', offer);
    
    // UPDATE OFFERS COUNT
    currentOffersCount++;
    const offersCountElement = document.getElementById('offers-count');
    if (offersCountElement) {
        offersCountElement.innerText = currentOffersCount;
    }
    
    // SHOW REALTIME ALERT
    showAlert(
        'warning',
        `<strong>New Offer Received!</strong><br>
         Amount: ₱${offer.amount}<br>
         Property: ${escapeHtml(offer.property_title)}<br>
         Buyer: ${escapeHtml(offer.buyer_name)}`
    );
});

// Helper function to show alerts
function showAlert(type, message) {
    const alertArea = document.getElementById('realtime-alert-area');
    if (!alertArea) return;
    
    const alertClass = type === 'success' ? 'alert-success' : 
                      (type === 'danger' ? 'alert-danger' : 
                      (type === 'warning' ? 'alert-warning' : 'alert-info'));
    
    const alertHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    alertArea.insertAdjacentHTML('afterbegin', alertHTML);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alerts = alertArea.querySelectorAll('.alert');
        if (alerts.length) {
            const alert = alerts[0];
            alert.classList.remove('show');
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    alert.remove();
                }
            }, 150);
        }
    }, 5000);
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Optional: Periodically refresh counts from server
function refreshCounts() {
    fetch('/admin/get_counts')
        .then(response => response.json())
        .then(data => {
            if (data.buyersCount !== undefined) {
                currentBuyersCount = data.buyersCount;
                const buyersElement = document.getElementById('buyers-count');
                if (buyersElement) buyersElement.innerText = data.buyersCount;
            }
            if (data.sellersCount !== undefined) {
                currentSellersCount = data.sellersCount;
                const sellersElement = document.getElementById('sellers-count');
                if (sellersElement) sellersElement.innerText = data.sellersCount;
            }
            if (data.propertiesCount !== undefined) {
                currentPropertiesCount = data.propertiesCount;
                const propertiesElement = document.getElementById('property-count');
                if (propertiesElement) propertiesElement.innerText = data.propertiesCount;
            }
            if (data.offersCount !== undefined) {
                currentOffersCount = data.offersCount;
                const offersElement = document.getElementById('offers-count');
                if (offersElement) offersElement.innerText = data.offersCount;
            }
            if (data.paymentsCount !== undefined) {
                currentPaymentsCount = data.paymentsCount;
                const paymentsElement = document.getElementById('payments-count');
                if (paymentsElement) paymentsElement.innerText = data.paymentsCount;
            }
            console.log('Counts refreshed from server:', data);
        })
        .catch(error => console.error('Error refreshing counts:', error));
}

// Refresh counts every 30 seconds as fallback
setInterval(refreshCounts, 30000);

// Also refresh on page visibility change (when user comes back to tab)
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        console.log('Page visible, refreshing counts...');
        refreshCounts();
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>