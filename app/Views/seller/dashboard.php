    <!-- app/Views/seller/dashboard.php -->
    <!doctype html>
    <html lang="en">
    <head>
    <meta charset="utf-8">
    <title>Seller Dashboard | House System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <span class="navbar-brand fw-bold">🏡 Seller Dashboard</span>
        <div class="d-flex align-items-center">
        <span class="navbar-text text-white me-3">
            Welcome, <b><?= esc($user['name']) ?></b>
        </span>
        <a href="<?= base_url('/profile/'.$user['id']) ?>" class="btn btn-outline-light btn-sm me-2">👤 View Profile</a>
        <a href="<?= base_url('/seller/archived') ?>" class="btn btn-outline-light btn-sm me-2">📦 Archived</a>
        <a href="<?= base_url('/logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
    </nav>

    <div class="container my-4">

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <!-- Add Property Form -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
        <h4 class="text-success fw-bold mb-3">Add New Property</h4>
        <form method="post" action="<?= base_url('/seller/add_property') ?>" enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>
            <div class="col-md-6">
            <input type="text" name="title" class="form-control" placeholder="Property Title" required>
            </div>
            <div class="col-md-6">
            <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
            </div>
            <div class="col-md-12">
            <input type="text" name="location" class="form-control" placeholder="Location (e.g. Ormoc City, Leyte)" required>
            </div>
            <div class="col-12">
            <textarea name="description" class="form-control" placeholder="Description..." required></textarea>
            </div>
            <div class="col-12">
            <input type="file" name="image" class="form-control" accept="image/*" required>
            </div>
            <div class="col-12 text-end">
            <button type="submit" class="btn btn-success">Add Property</button>
            </div>
        </form>
        </div>
    </div>

    <!-- Display Properties -->
    <h4 class="mb-3 text-primary">Your Properties</h4>

    <?php if(!empty($properties)): ?>
        <div class="row">
        <?php foreach($properties as $property): ?>
            <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 rounded-4 h-100">
<img src="<?= !empty($property['image_path']) ? base_url($property['image_path']) : 'https://via.placeholder.com/400x250?text=No+Image' ?>" 
    class="card-img-top" style="height: 250px; object-fit: cover;" alt="Property Image">

                <div class="card-body">
                <h5 class="card-title text-success fw-bold"><?= esc($property['title']) ?></h5>
                <p class="card-text"><?= esc($property['description']) ?></p>
                <p class="mb-1"><b>📍 Location:</b> <?= esc($property['location']) ?></p>
                <p class="fw-bold text-primary">₱<?= number_format($property['price'],2) ?></p>

                <div class="d-flex justify-content-between mb-2">
                    <a href="<?= base_url('/seller/edit_property/'.$property['id']) ?>" class="btn btn-sm btn-outline-primary">✏️ Edit</a>


                    <form method="post" action="<?= base_url('/seller/archive') ?>" class="m-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                    <button type="submit" class="btn btn-warning btn-sm">📦 Archive</button>
                    </form>
                </div>

                <!-- Offers -->
                <h6 class="text-muted">Offers:</h6>
                <?php $offers = $offersData[$property['id']] ?? []; ?>
                <?php if(!empty($offers)): ?>
                    <ul class="list-group mb-2">
                    <?php foreach($offers as $offer): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            💬 <b><?= esc($offer['buyer_name']) ?></b> offered 
                            <span class="text-primary fw-bold">₱<?= number_format($offer['amount'],2) ?></span><br>
                            <small>Status: 
                            <span class="fw-bold text-<?= $offer['status']=='accepted'?'success':($offer['status']=='rejected'?'danger':'secondary') ?>">
                                <?= ucfirst($offer['status']) ?>
                            </span>
                            </small><br>
                            <a href="<?= base_url('/message/'.$offer['buyer_id'].'/'.$property['id']) ?>" 
                            class="btn btn-outline-primary btn-sm mt-2">💬 Message Buyer</a>
                            <a href="<?= base_url('/profile/'.$offer['buyer_id']) ?>" 
                            class="btn btn-outline-secondary btn-sm mt-2">👤 View Profile</a>
                        </div>

                        <?php if($offer['status']=='pending'): ?>
                            <div>
                            <form method="post" action="<?= base_url('/seller/offer_action') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                                <input type="hidden" name="action" value="accept">
                                <button type="submit" class="btn btn-sm btn-success">Accept</button>
                            </form>
                            <form method="post" action="<?= base_url('/seller/offer_action') ?>" class="d-inline">
                                <?= csrf_field() ?>
                                <input type="hidden" name="offer_id" value="<?= $offer['id'] ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                            </form>
                            </div>
                        <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted small mb-2">No offers yet.</p>
                <?php endif; ?>

                <!-- Active Chats -->
                <?php $chats = $chatsData[$property['id']] ?? []; ?>
                <?php if(!empty($chats)): ?>
                    <h6 class="text-muted mb-2">Active Chats:</h6>
                    <?php foreach($chats as $chat): ?>
                    <a href="<?= base_url('/message/'.$chat['buyer_id'].'/'.$property['id']) ?>" 
                        class="btn btn-outline-success btn-sm mb-2 w-100 text-start">
                        💬 Chat with <?= esc($chat['buyer_name']) ?>
                    </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted small mb-0">No chats yet for this property.</p>
                <?php endif; ?>

                </div>
            </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">You haven’t added any properties yet.</p>
    <?php endif; ?>

    </div>
    </body>
    </html>
<!--minor update-->