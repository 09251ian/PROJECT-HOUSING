<?php
$active = 'buyer_favorites';
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div>
      <h3 class="text-success fw-bold mb-0">My Favorites</h3>
      <div class="text-muted small">Saved properties you marked for later</div>
    </div>

    <div class="d-flex gap-2">
      <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-outline-primary btn-sm">🏠 Browse</a>
    </div>
  </div>

  <?php if (!empty($favorites)): ?>
    <div class="row">
      <?php foreach ($favorites as $property): ?>
        <div class="col-md-6 mb-4">
          <div class="card shadow-sm border-0 rounded-4 h-100">
            <img src="<?= !empty($property['image_path']) ? base_url($property['image_path']) : 'https://via.placeholder.com/400x250?text=No+Image' ?>"
                 class="card-img-top" style="height:250px; object-fit:cover;" alt="Property Image">

            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start gap-2">
                <h5 class="card-title text-success fw-bold mb-1"><?= esc($property['title']) ?></h5>
              </div>

              <p class="card-text mb-2"><?= esc($property['description']) ?></p>

              <div class="fw-bold text-primary mb-1">₱<?= number_format((float)($property['price'] ?? 0), 2) ?></div>
              <div class="text-muted small mb-2">📍 <?= esc($property['location'] ?? '') ?></div>

              <div class="mb-3">
                <small class="text-muted">
                  Seller: <?= esc($property['seller_name'] ?? $property['seller_id'] ?? '') ?>
                </small>
              </div>

              <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('/message/' . ($property['seller_id'] ?? 0) . '/' . $property['id']) ?>"
                   class="btn btn-outline-success btn-sm" title="Message Seller">
                  💬 Message Seller
                </a>

                <form method="post" action="<?= base_url('/buyer/favorites/toggle') ?>" class="d-inline">
                  <?= csrf_field() ?>
                  <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                  <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="text-center py-5">
      <p class="text-muted mb-3">You have no saved properties yet.</p>
      <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-success">Browse Properties</a>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

