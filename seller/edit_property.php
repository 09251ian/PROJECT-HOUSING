<?php
$active = 'seller_dashboard';
$property = $property ?? [];
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Property | House Marketplace</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-dark">

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container my-4">
  <div class="d-flex align-items-end justify-content-between mb-3">
    <div>
      <div class="section-title text-white fs-4 mb-0">
        <i class="bi bi-pencil-square me-2"></i>Edit Property
      </div>
      <div class="text-muted small">Update your listing details and image.</div>
    </div>
    <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-outline-light btn-sm">
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
        <ul>
            <?php foreach($errors as $field => $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="<?= base_url('/seller/edit_property/'.$property['id']) ?>" enctype="multipart/form-data" class="row g-3">
    <?= csrf_field() ?>

    <div class="col-md-6">
        <label for="title" class="form-label">Property Title</label>
        <input type="text" id="title" name="title" class="form-control" value="<?= esc(old('title', $property['title'])) ?>" required>
    </div>
    <div class="col-md-6">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?= esc(old('price', $property['price'])) ?>" required>
    </div>
    <div class="col-md-12">
        <label for="location" class="form-label">Location</label>
        <input type="text" id="location" name="location" class="form-control" value="<?= esc(old('location', $property['location'])) ?>" required>
    </div>
    <div class="col-md-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" class="form-control" rows="4" required><?= esc(old('description', $property['description'])) ?></textarea>
    </div>
    <div class="col-md-12">
        <label for="image" class="form-label">Property Image</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
        <?php if(!empty($property['image_path'])): ?>
            <img src="<?= esc($property['image_path']) ?>" alt="Property Image" style="height:150px; margin-top:10px;">
        <?php endif; ?>
    </div>
    <div class="col-12 text-end">
        <button type="submit" class="btn btn-success">Update Property</button>
    </div>
</form>

</div>

</body>
</html>
