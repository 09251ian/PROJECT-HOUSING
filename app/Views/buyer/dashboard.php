<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Buyer Dashboard | House System</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
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
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <h3 class="text-success fw-bold mb-3">Available Properties</h3>

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
      <button type="submit" class="btn btn-success w-100">Search</button>
      <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-outline-secondary w-100">Reset</a>
    </div>

  </form>

  <!-- Property Listings -->
  <div class="row" id="propertyResults">

    <?php if (!empty($properties)): ?>

      <?php foreach ($properties as $property): ?>

        <?php
          $propertyId = $property['id'];
          $existingOffer = $existingOffers[$propertyId] ?? null;
          $chatExist = $chatsExist[$propertyId] ?? false;
        ?>

        <div class="col-md-6 mb-4">
          <div class="card shadow-sm border-0 rounded-4 h-100">

            <img src="<?= !empty($property['image_path']) ? base_url($property['image_path']) : 'https://via.placeholder.com/400x250?text=No+Image' ?>"
                 class="card-img-top"
                 style="height:250px; object-fit:cover;"
                 alt="Property Image">

            <div class="card-body">

              <h5 class="card-title text-success fw-bold"><?= esc($property['title']) ?></h5>

              <p><?= esc($property['description']) ?></p>

              <p class="fw-bold text-primary">
                ₱<?= number_format($property['price'], 2) ?>
              </p>

              <p class="mb-1">
                <b>📍 Location:</b> <?= esc($property['location']) ?>
              </p>

              <p>
                <small>Seller: <?= esc($property['seller_name']) ?></small>
              </p>

              <a href="<?= base_url('/message/' . $property['seller_id'] . '/' . $propertyId) ?>"
                 class="btn btn-outline-success btn-sm mt-2">
                 💬 Message Seller
              </a>

              <?php if ($existingOffer): ?>

                <div class="mt-2">

                  <?php if ($existingOffer['status'] === 'pending'): ?>
                    <span class="badge bg-warning text-dark">⏳ Offer Pending</span>

                  <?php elseif ($existingOffer['status'] === 'accepted'): ?>
                    <span class="badge bg-success">✅ Offer Accepted</span>

                  <?php elseif ($existingOffer['status'] === 'rejected'): ?>
                    <span class="badge bg-danger">❌ Offer Rejected</span>

                  <?php endif; ?>

                </div>

              <?php else: ?>

                <form method="post"
                      action="<?= base_url('/make_offer') ?>"
                      class="d-flex align-items-center gap-2 mt-2">

                  <?= csrf_field() ?>

                  <input type="hidden"
                         name="property_id"
                         value="<?= $propertyId ?>">

                  <input type="number"
                         step="0.01"
                         name="amount"
                         class="form-control w-50"
                         placeholder="Enter offer"
                         required>

                  <button type="submit" class="btn btn-primary btn-sm">
                    Make Offer
                  </button>

                </form>

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


<script>

/* AUTO REFRESH PROPERTY LIST */

function loadProperties(){

    $.ajax({
        url: "<?= base_url('/buyer/dashboard') ?>",
        type: "GET",

        success: function(response){

            let html = $(response).find("#propertyResults").html();
            $("#propertyResults").html(html);

        }
    });

}

/* Refresh every 5 seconds */

setInterval(function(){
    loadProperties();
}, 5000);

</script>

</body>
</html>