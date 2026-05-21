<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Add Property | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-dark">

<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/admin/dashboard') ?>">🏠 House Admin</a>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/dashboard') ?>">Dashboard</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/users') ?>">Users</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/properties') ?>">Properties</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/offers') ?>">Offers</a>
      <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/payments') ?>">Payments</a>
      <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
    </div>
  </div>
</nav>

<div class="container my-4">
  <div class="d-flex align-items-end justify-content-between mb-3">
    <div>
      <div class="section-title text-white fs-4 mb-0">
        <i class="bi bi-plus-square me-2"></i>Add Property
      </div>
      <div class="text-muted small">Create a new listing (title, price, location, and image).</div>
    </div>
    <a href="<?= base_url('/admin/properties') ?>" class="btn btn-outline-light btn-sm">
      <i class="bi bi-arrow-left me-1"></i>Back
    </a>
  </div>

  <?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
  <?php endif; ?>
  <?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>

  <?php if(isset($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach($errors as $field => $error): ?>
          <li><?= esc($error) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" action="<?= base_url('/admin/add_property') ?>" enctype="multipart/form-data" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-md-6">
      <label for="seller_id" class="form-label">Seller</label>
      <select id="seller_id" name="seller_id" class="form-select" required>
        <option value="">-- Select Seller --</option>
        <?php foreach(($sellers ?? []) as $s): ?>
          <?php $selected = (string)old('seller_id', $seller_id ?? '') === (string)$s['id'] ? 'selected' : ''; ?>
          <option value="<?= esc($s['id']) ?>" <?= $selected ?>><?= esc($s['name'] ?? '') ?> (ID: <?= esc($s['id']) ?>)</option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-md-6">
      <label for="price" class="form-label">Price</label>
      <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?= esc(old('price', $price ?? '')) ?>" required>
    </div>

    <div class="col-md-12">
      <label for="title" class="form-label">Property Title</label>
      <input type="text" id="title" name="title" class="form-control" value="<?= esc(old('title', $title ?? '')) ?>" required>
    </div>

    <div class="col-md-12">
      <label for="location" class="form-label">Location</label>
      <input type="text" id="location" name="location" class="form-control" value="<?= esc(old('location', $location ?? '')) ?>" required>
    </div>

    <div class="col-md-12">
      <label for="description" class="form-label">Description</label>
      <textarea id="description" name="description" class="form-control" rows="4" required><?= esc(old('description', $description ?? '')) ?></textarea>
    </div>

    <div class="col-md-12">
      <label for="image" class="form-label">Property Image</label>
      <input type="file" id="image" name="image" class="form-control" accept="image/*">
      <div class="form-text text-muted">Optional. Leave empty to create without an image.</div>
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-success">Add Property</button>
    </div>
  </form>
</div>

</body>
</html>

