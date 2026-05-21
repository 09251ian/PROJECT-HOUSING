<?php
// Shared header/navbar for all roles.
// Expects optional variables:
// - $user (array with id, name, role)
// - $active (string key)
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($title) ? esc($title) : 'House System' ?></title>
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

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#appNavbar" aria-controls="appNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="appNavbar">
      <?php $u = $user ?? session()->get('user') ?? []; ?>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <?php if (!empty($u) && (($u['role'] ?? '') === 'buyer')): ?>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'buyer_dashboard' ? 'active' : '' ?>" href="<?= base_url('/buyer/dashboard') ?>">Buyer</a></li>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'buyer_favorites' ? 'active' : '' ?>" href="<?= base_url('/buyer/favorites') ?>">Favorites</a></li>
        <?php elseif (!empty($u) && (($u['role'] ?? '') === 'seller')): ?>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'seller_dashboard' ? 'active' : '' ?>" href="<?= base_url('/seller/dashboard') ?>">Seller</a></li>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'seller_archived' ? 'active' : '' ?>" href="<?= base_url('/seller/archived') ?>">Archived</a></li>
        <?php elseif (!empty($u) && (($u['role'] ?? '') === 'admin')): ?>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'admin_dashboard' ? 'active' : '' ?>" href="<?= base_url('/admin/dashboard') ?>">Admin</a></li>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'admin_properties' ? 'active' : '' ?>" href="<?= base_url('/admin/properties') ?>">Properties</a></li>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'admin_offers' ? 'active' : '' ?>" href="<?= base_url('/admin/offers') ?>">Offers</a></li>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'admin_payments' ? 'active' : '' ?>" href="<?= base_url('/admin/payments') ?>">Payments</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link <?= ($active ?? '') === 'home' ? 'active' : '' ?>" href="<?= base_url('/') ?>">Home</a></li>
        <?php endif; ?>

        <li class="nav-item ms-lg-2">
          <?php if (!empty($u)): ?>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('/profile/' . ($u['id'] ?? '')) ?>"><i class="bi bi-person-circle me-1"></i>Profile</a>
            <a class="btn btn-danger btn-sm ms-2" href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right me-1"></i>Logout</a>
          <?php else: ?>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('/login') ?>"><i class="bi bi-box-arrow-in-right me-1"></i>Login</a>
            <a class="btn btn-primary btn-sm ms-2" href="<?= base_url('/register') ?>"><i class="bi bi-person-plus me-1"></i>Register</a>
          <?php endif; ?>
        </li>
      </ul>
    </div>
  </div>
</nav>

<main class="app-main">

