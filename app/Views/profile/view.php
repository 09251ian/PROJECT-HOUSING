<?php
$active = 'home';
$user = isset($user) && is_array($user) ? $user : [];
$userRole = $userRole ?? (session()->get('role') ?? null);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>User Profile | House Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-dark">

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-7">
      <div class="app-card">
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="profile-avatar">
            <img
              src="<?= !empty($user['profile_pic']) ? base_url('uploads/' . $user['profile_pic']) : base_url('assets/default-profile.png') ?>"
              alt="Profile Picture"
              class="profile-avatar__img"
              style="width: 64px; height: 64px; object-fit: cover; object-position: center; border-radius: 50%;"
              onerror="this.src='https://via.placeholder.com/300x300?text=User';"
            >
          </div>

          <div class="flex-grow-1">
            <div class="section-title fs-3 text-white mb-1">
              <i class="bi bi-person-circle me-2 text-success"></i><?= esc($user['name'] ?? '') ?>
            </div>
            <div class="text-muted small">
              <i class="bi bi-tag me-2"></i><?= ucfirst(esc($user['role'] ?? '')) ?>
            </div>
          </div>

          <div class="d-flex gap-2">
            <a href="<?= base_url('/' . ($userRole === 'seller' ? 'seller/dashboard' : ($userRole === 'buyer' ? 'buyer/dashboard' : ''))) ?>" class="btn btn-outline-light btn-sm">
              <i class="bi bi-house me-1"></i>Back to Dashboard
            </a>
          </div>
        </div>

        <hr class="my-4" style="border-color: rgba(255,255,255,.08);">

        <div class="row g-3">
          <div class="col-md-6">
            <div class="app-info-row">
              <div class="app-info-label"><i class="bi bi-envelope me-2"></i>Email</div>
              <div class="app-info-value"><?= esc($user['email'] ?? 'N/A') ?></div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="app-info-row">
              <div class="app-info-label"><i class="bi bi-telephone me-2"></i>Contact</div>
              <div class="app-info-value"><?= esc($user['contact'] ?? 'N/A') ?></div>
            </div>
          </div>
          <div class="col-12">
            <div class="app-info-row">
              <div class="app-info-label"><i class="bi bi-file-text me-2"></i>Bio</div>
              <div class="app-info-value"><?= esc($user['bio'] ?? '') ?></div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-3 text-muted small">
        <i class="bi bi-shield-check me-2"></i>Your profile helps other users trust and connect with you.
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>

