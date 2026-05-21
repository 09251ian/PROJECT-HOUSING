<?php
$active = 'seller_archived';
$user = $user ?? session()->get('user') ?? [];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Archived Properties | House Marketplace</title>
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

  <div class="d-flex align-items-end justify-content-between mb-3">
    <div>
      <div class="section-title text-white fs-4 mb-0">
        <i class="bi bi-archive me-2"></i>Archived Properties
      </div>
      <div class="text-muted small">Restore or delete archived listings.</div>
    </div>
    <div>
      <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-outline-light btn-sm">
        <i class="bi bi-house me-1"></i>Dashboard
      </a>
    </div>
  </div>

  <?php if (!empty($properties)): ?>
    <div class="row g-4">
      <?php foreach ($properties as $property): ?>
        <div class="col-lg-6 mb-4">
          <div class="property-card h-100">
            <div class="property-media">
              <img
                src="<?= !empty($property['image_path']) ? base_url($property['image_path']) : 'https://via.placeholder.com/800x500?text=No+Image' ?>"
                alt="Property Image"
                style="height: 210px; object-fit: cover; width: 100%;"
              >
            </div>

            <div class="property-body">
              <div class="d-flex align-items-start justify-content-between gap-2">
                <div>
                  <h5 class="property-title"><?= esc($property['title'] ?? '') ?></h5>
                  <div class="property-price">₱<?= number_format((float)($property['price'] ?? 0), 2) ?></div>
                  <div class="property-location"><i class="bi bi-geo-alt me-1"></i><?= esc($property['location'] ?? '') ?></div>
                </div>
              </div>

              <p class="property-desc mb-3"><?= esc($property['description'] ?? '') ?></p>

              <div class="d-flex gap-2 flex-wrap">
                <form method="post" action="<?= base_url('/seller/unarchive') ?>" class="m-0">
                  <?= csrf_field() ?>
                  <input type="hidden" name="property_id" value="<?= (int)($property['id'] ?? 0) ?>">
                  <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                  </button>
                </form>

                <form method="post" action="<?= base_url('/seller/delete') ?>" class="m-0">
                  <?= csrf_field() ?>
                  <input type="hidden" name="property_id" value="<?= (int)($property['id'] ?? 0) ?>">
                  <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i>Delete Permanently
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="mt-3">
      <?= isset($pager) ? $pager->links() : '' ?>
    </div>
  <?php else: ?>
    <div class="text-muted">No archived properties found.</div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

