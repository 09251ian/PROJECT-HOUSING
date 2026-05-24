<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Offers | House System</title>
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
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/properties') ?>">Properties</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>
<div class="container py-4">
  <h3 class="text-white mb-3">Offers</h3>

  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th><th>Property</th><th>Buyer</th><th>Amount</th><th>Status</th><th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($offers ?? []) as $o): ?>
          <tr>
            <td><?= esc($o['id']) ?></td>
            <td><?= esc($o['property_title'] ?? '') ?></td>
            <td><?= esc($o['buyer_name'] ?? '') ?></td>
            <td>₱<?= number_format((float)$o['amount'], 2) ?></td>
            <td><?= esc($o['status']) ?></td>
            <td><?= esc($o['created_at'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>

