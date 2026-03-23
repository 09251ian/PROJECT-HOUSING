<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">

  <!-- 🔙 Back Button -->
  <div class="mb-3">
    <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary btn-sm">⬅ Back</a>
  </div>

  <div class="card shadow-lg mx-auto" style="max-width: 500px;">
    <div class="card-header bg-success text-white text-center">
      <h4>Create an Account</h4>
    </div>
    <div class="card-body">
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

        <div class="mb-3">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" required>
            <option value="">-- Select Role --</option>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
          </select>
        </div>

        <button type="submit" class="btn btn-success w-100">Register</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
