<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>User Profile | House System</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body {
        background-color: #f8f9fa;
    }
    .profile-header {
        background-color: #198754;
        color: white;
        padding: 30px 20px;
        border-radius: 0.5rem 0.5rem 0 0;
        text-align: center;
    }
    .profile-header h4 {
        margin-bottom: 5px;
        font-weight: 700;
    }
    .profile-header p {
        font-weight: 300;
        font-size: 1rem;
    }
    .profile-picture {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid white;
        margin-top: -75px;
        background-color: white;
    }
    .profile-card {
        max-width: 600px;
        margin: 40px auto;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .profile-info .row + .row {
        margin-top: 15px;
    }
    .profile-label {
        font-weight: 600;
        color: #555;
    }
    .profile-value {
        color: #333;
    }
    .back-btn {
        max-width: 600px;
        margin: 20px auto 40px;
        display: flex;
        justify-content: flex-start;
    }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
<div class="container">
    <span class="navbar-brand fw-bold">🏡 User Profile</span>
    <div class="d-flex align-items-center">
        <?php if (session()->get('role') === 'seller'): ?>
            <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <?php elseif (session()->get('role') === 'buyer'): ?>
            <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <?php else: ?>
            <a href="<?= base_url('/') ?>" class="btn btn-outline-light btn-sm me-2"><i class="bi bi-house"></i> Home</a>
        <?php endif; ?>
        <a href="<?= base_url('/logout') ?>" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>
</nav>

<div class="profile-card">
    <div class="profile-header">
        <img src="<?= !empty($user['profile_pic']) ? base_url('uploads/' . $user['profile_pic']) : base_url('uploads/default-profile.png') ?>" alt="Profile Picture" class="profile-picture">
        <h4><?= esc($user['name']) ?></h4>
        <p><i class="bi bi-person-circle"></i> <?= ucfirst(esc($user['role'])) ?></p>
    </div>
    <div class="p-4 profile-info">
        <div class="row">
            <div class="col-5 profile-label"><i class="bi bi-envelope"></i> Email:</div>
            <div class="col-7 profile-value"><?= esc($user['email']) ?></div>
        </div>
        <div class="row">
            <div class="col-5 profile-label"><i class="bi bi-telephone"></i> Contact:</div>
            <div class="col-7 profile-value"><?= esc($user['contact'] ?? 'N/A') ?></div>
        </div>
        <div class="row">
            <div class="col-5 profile-label"><i class="bi bi-file-text"></i> Bio:</div>
            <div class="col-7 profile-value"><?= esc($user['bio'] ?? '') ?></div>
        </div>
    </div>
</div>

<div class="back-btn">
    <?php if (isset($userRole) && $userRole === 'seller'): ?>
        <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-success"><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a>
    <?php else: ?>
        <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-success"><i class="bi bi-arrow-left-circle"></i> Back to Dashboard</a>
    <?php endif; ?>
</div>

</body>
</html>
