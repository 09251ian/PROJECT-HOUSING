<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Users | House System</title>
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
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/properties') ?>">Properties</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/offers') ?>">Offers</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-4">
  <h3 class="text-white mb-3">Users</h3>

  <h5 class="text-white-50 mt-4">Buyers</h5>
  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Contact</th><th>Bio</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($buyers ?? []) as $u): ?>
          <tr>
            <td><?= esc($u['id']) ?></td>
            <td><?= esc($u['name']) ?></td>
            <td><?= esc($u['email']) ?></td>
            <td><?= esc($u['contact'] ?? '') ?></td>
            <td style="max-width:320px;"><?= esc($u['bio'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <h5 class="text-white-50 mt-4">Sellers</h5>
  <div class="table-responsive app-table-wrap">
    <table class="table table-dark table-striped align-middle">
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Contact</th><th>Bio</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (($sellers ?? []) as $u): ?>
          <tr>
            <td><?= esc($u['id']) ?></td>
            <td><?= esc($u['name']) ?></td>
            <td><?= esc($u['email']) ?></td>
            <td><?= esc($u['contact'] ?? '') ?></td>
            <td style="max-width:320px;"><?= esc($u['bio'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>

