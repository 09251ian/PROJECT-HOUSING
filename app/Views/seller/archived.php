<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Archived Properties | House System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <span class="navbar-brand fw-bold">🏡 Archived Properties</span>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">
                    Welcome, <b><?= esc($user['name']) ?></b>
                </span>
                <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-outline-light btn-sm me-2">🏠
                    Dashboard</a>
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

        <h4 class="mb-3 text-primary">Archived Properties</h4>

        <?php if(!empty($properties)): ?>
        <div class="row">
            <?php foreach($properties as $property): ?>
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <img src="<?= esc($property['image_path'] ?? 'https://via.placeholder.com/400x250?text=No+Image') ?>"
                        class="card-img-top" style="height: 250px; object-fit: cover;" alt="Property Image">

                    <div class="card-body">
                        <h5 class="card-title text-success fw-bold"><?= esc($property['title']) ?></h5>
                        <p class="card-text"><?= esc($property['description']) ?></p>
                        <p class="mb-1"><b>📍 Location:</b> <?= esc($property['location']) ?></p>
                        <p class="fw-bold text-primary">₱<?= number_format($property['price'],2) ?></p>

                        <div class="d-flex justify-content-between mb-2">
                            <form method="post" action="<?= base_url('/seller/unarchive') ?>" class="m-0">
                                <?= csrf_field() ?>
                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                <button type="submit" class="btn btn-success btn-sm">Restore</button>
                            </form>
                            <form method="post" action="<?= base_url('/seller/delete') ?>" class="m-0">
                                <?= csrf_field() ?>
                                <input type="hidden" name="property_id" value="<?= $property['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete Permanently</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?= $pager->links() ?>

        <?php else: ?>
        <p class="text-muted">No archived properties found.</p>
        <?php endif; ?>

    </div>

    <script>
    window.userId = <?= json_encode($user['id']) ?>;
    window.userRole = 'seller';
    window.userName = <?= json_encode($user['name']) ?>;
    </script>
    <script src="<?= base_url('js/websocket.js') ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        connectWebSocket();
    });
    </script>
</body>

</html>