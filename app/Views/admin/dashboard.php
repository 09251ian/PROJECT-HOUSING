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

  <!-- REALTIME NOTIFICATION -->
  <div id="realtime-alert-area"></div>

  <div class="row g-3">

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Buyers
        </div>

        <div class="app-card-value text-primary">
          <?= htmlspecialchars((string)($buyersCount ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Sellers
        </div>

        <div class="app-card-value text-primary">
          <?= htmlspecialchars((string)($sellersCount ?? 0), ENT_QUOTES, 'UTF-8') ?>
        </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Properties
        </div>

        <div class="app-card-value text-primary"
             id="property-count">

          <?= htmlspecialchars((string)($propertiesCount ?? 0), ENT_QUOTES, 'UTF-8') ?>

        </div>

      </div>

    </div>

    <div class="col-md-3">

      <div class="app-card">

        <div class="app-card-title">
          Offers
        </div>

        <div class="app-card-value text-primary">
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

            <div class="app-card-value text-primary">
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

const socket = io('http://localhost:3000');

socket.on('connect', () => {

    console.log('Admin connected to websocket server');

});

socket.on('property-added', function(property) {

    console.log('New property detected:', property);

    // UPDATE PROPERTY COUNT
    const propertyCountElement = document.getElementById('property-count');

    let currentCount = parseInt(propertyCountElement.innerText);

    propertyCountElement.innerText = currentCount + 1;

    // SHOW REALTIME ALERT
    const alertArea = document.getElementById('realtime-alert-area');

    const alertHTML = `
    
    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <strong>Realtime Update:</strong>

        New property added:
        <b>${property.title}</b>

        <br>

        Seller:
        ${property.seller_name}

        <br>

        Location:
        ${property.location}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>
    
    `;

    alertArea.insertAdjacentHTML('afterbegin', alertHTML);

});

</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>