<?php
// Landing page (marketplace style)
?>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>House Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= base_url('assets/app.css') ?>" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="app-dark">

<nav class="navbar navbar-expand-lg navbar-dark app-navbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= base_url('/') ?>">
      <span class="app-logo">🏠</span>
      <span>House Marketplace</span>
    </a>

    <div class="d-flex align-items-center gap-2">
      <?php $u = $user ?? session()->get('user') ?? []; ?>
      <?php if (!empty($u)): ?>
        <?php if (($u['role'] ?? '') === 'buyer'): ?>
          <a class="btn btn-outline-light btn-sm" href="<?= base_url('/buyer/dashboard') ?>">Buyer Dashboard</a>
        <?php elseif (($u['role'] ?? '') === 'seller'): ?>
          <a class="btn btn-outline-light btn-sm" href="<?= base_url('/seller/dashboard') ?>">Seller Dashboard</a>
        <?php else: ?>
          <a class="btn btn-outline-light btn-sm" href="<?= base_url('/admin/dashboard') ?>">Admin Dashboard</a>
        <?php endif; ?>
        <a class="btn btn-danger btn-sm" href="<?= base_url('/logout') ?>">Logout</a>
      <?php else: ?>
        <a class="btn btn-outline-light btn-sm" href="<?= base_url('/login') ?>">Login</a>
        <a class="btn btn-primary btn-sm" href="<?= base_url('/register') ?>">Register</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<header class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-lg-6">
        <h1 class="hero-title">
          Find your next home,
          <span class="text-primary">faster</span>.
        </h1>
        <p class="hero-sub mt-3">
          Discover verified properties, save favorites, message sellers, and make offers—all in one place.
        </p>

        <div class="d-flex gap-2 flex-wrap mt-4">
          <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-primary btn-lg px-4">
            <i class="bi bi-house-door me-2"></i>Browse Properties
          </a>
          <a href="<?= base_url('/register') ?>" class="btn btn-outline-light btn-lg px-4">
            <i class="bi bi-person-plus me-2"></i>Create Account
          </a>
        </div>
      </div>

      <div class="col-lg-6">
        <div class="search-shell">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <div class="section-title text-white">Quick Search</div>
              <div class="text-muted small">Search by title and location</div>
            </div>
            <span class="badge text-bg-primary">Marketplace</span>
          </div>

          <form method="get" action="<?= base_url('/buyer/dashboard') ?>" class="row g-2">
            <div class="col-md-7">
              <input type="text" name="search" class="form-control" placeholder="e.g. 2BR condo, house, lot" />
            </div>
            <div class="col-md-5">
              <input type="text" name="location" class="form-control" placeholder="e.g. Ormoc City" />
            </div>
            <div class="col-12 mt-1">
              <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-search me-1"></i>Search Listings
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="row mt-5 g-4">
      <div class="col-md-4">
        <div class="trust-card h-100">
          <div class="d-flex align-items-center gap-3">
            <div class="text-primary fs-3"><i class="bi bi-shield-check"></i></div>
            <div>
              <div class="fw-bold">Verified Listings</div>
              <div class="text-muted small">Curated properties with clear details.</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="trust-card h-100">
          <div class="d-flex align-items-center gap-3">
            <div class="text-primary fs-3"><i class="bi bi-chat-dots"></i></div>
            <div>
              <div class="fw-bold">Fast Messaging</div>
              <div class="text-muted small">Contact sellers without leaving the site.</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="trust-card h-100">
          <div class="d-flex align-items-center gap-3">
            <div class="text-primary fs-3"><i class="bi bi-credit-card"></i></div>
            <div>
              <div class="fw-bold">Secure Offers & Payments</div>
              <div class="text-muted small">Track your transactions in one dashboard.</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<section class="py-4">
  <div class="container">
    <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
      <div>
        <div class="section-title text-white fs-4">Featured Properties</div>
        <div class="text-muted">A quick snapshot of what’s available today.</div>
      </div>
      <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-outline-primary">
        View all
      </a>
    </div>

    <div class="row">
      <?php $featured = $featured ?? []; ?>
      <?php if (!empty($featured)): ?>

        <?php
          $favorites = $favorites ?? [];
          $existingOffers = $existingOffers ?? [];
          $chatsExist = $chatsExist ?? [];
        ?>
        <?php foreach ($featured as $property): ?>
          <?php
            $propertyId = $property['id'];
            $isFavorite = $favorites[$propertyId] ?? false;
            $existingOffer = $existingOffers[$propertyId] ?? null;
            $chatExist = $chatsExist[$propertyId] ?? false;
          ?>
          <?php include __DIR__ . '/partials/property_card.php'; ?>
        <?php endforeach; ?>
      <?php else: ?>
        <?php for ($i=1;$i<=6;$i++): ?>
          <div class="col-lg-4 col-md-6 mb-4">
            <div class="property-card">
              <div class="property-media">
                <img src="<?= base_url('public/uploads/house'.$i.'.jpg') ?>" alt="Featured" onerror="this.src='https://via.placeholder.com/800x500?text=Featured+Property'" />
              </div>
              <div class="property-body">
                <h5 class="property-title">Featured Listing <?= $i ?></h5>
                <div class="property-price">₱5,000,000</div>
                <p class="property-desc">Modern living spaces with great location—save favorites and make offers.</p>
                <div class="d-flex gap-2 mt-2">
                  <span class="property-pill property-pill--warning">Pending Offer</span>
                </div>
              </div>
            </div>
          </div>
        <?php endfor; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="section-title text-white fs-4 mb-2">How it works</div>
    <div class="text-muted mb-4">Three simple steps to buy or sell with confidence.</div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="trust-card">
          <div class="text-primary fw-bold mb-2"><i class="bi bi-1-circle"></i> Step 1</div>
          <div class="fw-bold">Browse & Save</div>
          <div class="text-muted small">Search properties and save your favorites.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="trust-card">
          <div class="text-primary fw-bold mb-2"><i class="bi bi-2-circle"></i> Step 2</div>
          <div class="fw-bold">Message Sellers</div>
          <div class="text-muted small">Ask questions and negotiate offers.</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="trust-card">
          <div class="text-primary fw-bold mb-2"><i class="bi bi-3-circle"></i> Step 3</div>
          <div class="fw-bold">Make an Offer</div>
          <div class="text-muted small">Track status and manage transactions.</div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

