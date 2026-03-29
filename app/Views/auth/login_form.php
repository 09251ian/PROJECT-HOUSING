<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login | House System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="card shadow-lg border-0 rounded-4">
          <div class="card-body p-5 text-center">

            <h2 class="mb-4 text-primary fw-bold">Login</h2>

            <?php if (session()->getFlashdata('success')): ?>
              <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
              </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
              </div>
            <?php endif; ?>

            <form method="post" action="<?= base_url('/login') ?>" class="text-start">
              <?= csrf_field() ?>

              <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
              </div>

              <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
              </div>

              <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
            </form>

            <p class="mb-2">Don’t have an account?</p>
            <a href="<?= base_url('/register') ?>" class="btn btn-outline-secondary w-100 mb-2">Register</a>
            <a href="<?= base_url('/') ?>" class="btn btn-outline-dark w-100">← Back to Home</a>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
