<?php
$active = 'home';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create Account | House Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-dark">

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
      <div class="app-card">
        <div class="text-center mb-4">
          <div class="text-success fs-3"><i class="bi bi-person-plus"></i></div>
          <h2 class="mt-2 fw-bold">Create an Account</h2>
          <div class="text-muted small">Join the marketplace to save favorites and message sellers.</div>
        </div>

        <form action="<?= base_url('/register') ?>" method="POST" enctype="multipart/form-data">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>

          <div class="mb-3">
            <label for="contact" class="form-label">Contact Number</label>
            <input type="text" name="contact" id="contact" class="form-control">
          </div>

          <div class="mb-3">
            <label for="bio" class="form-label">Short Bio</label>
            <textarea name="bio" id="bio" rows="3" class="form-control" placeholder="Write something about yourself..."></textarea>
          </div>

          <div class="mb-3">
            <label for="profile_pic" class="form-label">Profile Picture</label>
            <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="">-- Select Role --</option>
              <option value="buyer">Buyer</option>
              <option value="seller">Seller</option>
            </select>
          </div>

          <button type="submit" class="btn btn-success w-100">
            <i class="bi bi-check-circle me-1"></i>Register
          </button>

          <div class="mt-3 text-center">
            <a href="<?= base_url('/') ?>" class="btn btn-outline-light w-100">
              <i class="bi bi-arrow-left me-1"></i>Back to Home
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>

