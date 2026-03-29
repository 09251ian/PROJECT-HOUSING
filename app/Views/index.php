<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Home | House Selling & Buying System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body text-center p-5">
            <h1 class="mb-4 text-primary fw-bold">🏠 House Selling & Buying System</h1>

            <?php if (isset($user)): ?>
              <h5 class="mb-3">Welcome, 
                <span class="text-success fw-bold"><?= esc($user['name']) ?></span>!
              </h5>
              
              <?php if ($user['role'] === 'buyer'): ?>
                <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-primary w-100 mb-2">Go to Buyer Dashboard</a>
              <?php else: ?>
                <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-success w-100 mb-2">Go to Seller Dashboard</a>
              <?php endif; ?>

              <a href="<?= base_url('/logout') ?>" class="btn btn-outline-danger w-100">Logout</a>

            <?php else: ?>
              <p class="mb-4">Please login or register to continue.</p>
              <a href="<?= base_url('/login') ?>" class="btn btn-primary w-100 mb-2">Login</a>
              <a href="<?= base_url('/register') ?>" class="btn btn-outline-secondary w-100">Register</a>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
