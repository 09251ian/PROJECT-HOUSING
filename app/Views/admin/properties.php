<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Properties | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
</head>
<body class="app-dark">
<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/admin/dashboard') ?>">🏠 House Admin</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/users') ?>">Users</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/offers') ?>">Offers</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-4">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="text-white mb-0">Properties</h3>
    <a href="<?= base_url('/admin/add_property') ?>" class="btn btn-success btn-sm">
      <i class="bi bi-plus-square me-1"></i>Add Property
    </a>
  </div>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th><th>Title</th><th>Location</th><th>Price</th><th>Seller</th><th>Archived</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($properties ?? []) as $p): ?>
          <tr>
            <td><?= esc($p['id']) ?></td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span><?= esc($p['title']) ?></span>
                <a href="<?= base_url('/admin/edit_property/'.$p['id']) ?>" class="btn btn-outline-light btn-sm" style="padding: .15rem .4rem;">
                  <i class="bi bi-pencil-square"></i> Edit
                </a>
              </div>
            </td>
            <td><?= esc($p['location']) ?></td>
            <td>₱<?= number_format((float)$p['price'], 2) ?></td>
            <td><?= esc($p['seller_name'] ?? '') ?></td>
            <td><?= ((int)($p['is_archived'] ?? 0)) ? 'Yes' : 'No' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>

