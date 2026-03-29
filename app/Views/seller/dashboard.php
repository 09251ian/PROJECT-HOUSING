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

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <span class="navbar-brand fw-bold">🏡 Seller Dashboard</span>
            <div class="d-flex align-items-center">
                <span class="navbar-text text-white me-3">
                    Welcome, <b><?= esc($user['name']) ?></b>
                </span>
                <a href="<?= base_url('/profile/'.$user['id']) ?>" class="btn btn-outline-light btn-sm me-2">👤 View
                    Profile</a>
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

        <!-- Add Property Form (with id for JavaScript) -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <h4 class="text-success fw-bold mb-3">Add New Property</h4>
                <form id="add-property-form" method="post" action="<?= base_url('/seller/add_property') ?>"
                    enctype="multipart/form-data" class="row g-3">
                    <?= csrf_field() ?>
                    <div class="col-md-6">
                        <input type="text" name="title" class="form-control" placeholder="Property Title" required>
                    </div>
                    <div class="col-md-6">
                        <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
                    </div>
                    <div class="col-md-12">
                        <input type="text" name="location" class="form-control"
                            placeholder="Location (e.g. Ormoc City, Leyte)" required>
                    </div>
                    <div class="col-12">
                        <textarea name="description" class="form-control" placeholder="Description..."
                            required></textarea>
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
        <div class="row" id="properties-container">
            <?php foreach($properties as $property): ?>
            <!-- Each property card has data-property-id -->
            <div class="col-md-6 mb-4" data-property-id="<?= $property['id'] ?>">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <img src="<?= !empty($property['image_path']) ? base_url($property['image_path']) : 'https://via.placeholder.com/400x250?text=No+Image' ?>"
                        class="card-img-top" style="height: 250px; object-fit: cover;" alt="Property Image">
                    <div class="card-body">
                        <h5 class="card-title text-success fw-bold"><?= esc($property['title']) ?></h5>
                        <p class="card-text"><?= esc($property['description']) ?></p>
                        <p class="mb-1"><b>📍 Location:</b> <?= esc($property['location']) ?></p>
                        <p class="fw-bold text-primary">₱<?= number_format($property['price'],2) ?></p>

                        <div class="d-flex justify-content-between mb-2">
                            <a href="<?= base_url('/seller/edit_property/'.$property['id']) ?>"
                                class="btn btn-sm btn-outline-primary">✏️ Edit</a>
                            <!-- Archive button as plain button with class and data attribute -->
                            <button type="button" class="btn btn-warning btn-sm archive-btn"
                                data-property-id="<?= $property['id'] ?>">📦 Archive</button>
                        </div>

                        <!-- Offers Section (container for offers) -->
                        <h6 class="text-muted">Offers:</h6>
                        <div class="offers-section" data-property-id="<?= $property['id'] ?>">
                            <?php $offers = $offersData[$property['id']] ?? []; ?>
                            <?php if(!empty($offers)): ?>
                            <ul class="list-group mb-2">
                                <?php foreach($offers as $offer): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap"
                                    data-offer-id="<?= $offer['id'] ?>">
                                    <div>
                                        💬 <b><?= esc($offer['buyer_name']) ?></b> offered
                                        <span
                                            class="text-primary fw-bold">₱<?= number_format($offer['amount'],2) ?></span><br>
                                        <small>Status:
                                            <span
                                                class="fw-bold text-<?= $offer['status']=='accepted'?'success':($offer['status']=='rejected'?'danger':'secondary') ?>">
                                                <?= ucfirst($offer['status']) ?>
                                            </span>
                                        </small><br>
                                        <a href="<?= base_url('/message/'.$offer['buyer_id'].'/'.$property['id']) ?>"
                                            class="btn btn-outline-primary btn-sm mt-2">💬 Message Buyer</a>
                                        <a href="<?= base_url('/profile/'.$offer['buyer_id']) ?>"
                                            class="btn btn-outline-secondary btn-sm mt-2">👤 View Profile</a>
                                    </div>

                                    <?php if($offer['status'] == 'pending'): ?>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-success accept-offer"
                                            data-offer-id="<?= $offer['id'] ?>"
                                            data-property-id="<?= $property['id'] ?>">Accept</button>
                                        <button type="button" class="btn btn-sm btn-danger reject-offer"
                                            data-offer-id="<?= $offer['id'] ?>"
                                            data-property-id="<?= $property['id'] ?>">Reject</button>
                                    </div>
                                    <?php endif; ?>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                            <?php else: ?>
                            <p class="text-muted small mb-2">No offers yet.</p>
                            <?php endif; ?>
                        </div>

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
    <script>
    window.baseUrl = "<?= base_url() ?>";
    </script>
    <script>
    // Embed user data for WebSocket
    window.userId = <?= json_encode($user['id']) ?>;
    window.userRole = 'seller';
    window.userName = <?= json_encode($user['name']) ?>;
    </script>
    <script src="<?= base_url('js/websocket.js') ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        connectWebSocket();

        // Handle "Add Property" form via WebSocket
        // Handle "Add Property" form
        const addForm = document.getElementById('add-property-form');
        if (addForm) {
            addForm.addEventListener('submit', async function(e) {
                // If WebSocket is not connected, let the form submit normally
                if (!wsConnected) {
                    return; // form will submit as usual
                }

                // Otherwise, handle via WebSocket (prevent default, upload image, send message)
                e.preventDefault();

                const formData = new FormData(this);
                try {
                    const response = await fetch('<?= base_url('/seller/upload_image') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
                        }
                    });
                    const result = await response.json();
                    if (result.success) {
                        const propertyData = {
                            title: this.title.value,
                            price: parseFloat(this.price.value),
                            location: this.location.value,
                            description: this.description.value,
                            image_path: result.image_path
                        };
                        sendWSMessage('CREATE_PROPERTY', propertyData);
                        this.reset(); // clear the form
                    } else {
                        alert('Image upload failed: ' + result.error);
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error uploading image');
                }
            });
        }

        // Handle archive buttons
        // Handle archive buttons
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('archive-btn')) {
                const propertyId = e.target.getAttribute('data-property-id');
                if (confirm('Archive this property?')) {
                    if (wsConnected) {
                        sendWSMessage('ARCHIVE_PROPERTY', {
                            property_id: propertyId
                        });
                    } else {
                        // Fallback: send AJAX to /seller/archive
                        const formData = new FormData();
                        formData.append('property_id', propertyId);
                        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                        fetch('<?= base_url('/seller/archive') ?>', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Remove property from DOM or reload page
                                    location.reload();
                                } else {
                                    alert(data.error || 'Archive failed');
                                }
                            })
                            .catch(err => {
                                console.error(err);
                                alert('Error archiving property');
                            });
                    }
                }
            }
        });

        // Handle accept/reject buttons
        document.body.addEventListener('click', function(e) {
            if (e.target.classList.contains('accept-offer') || e.target.classList.contains(
                    'reject-offer')) {
                const offerId = e.target.getAttribute('data-offer-id');
                const propertyId = e.target.getAttribute('data-property-id');
                const action = e.target.classList.contains('accept-offer') ? 'accept' : 'reject';

                if (wsConnected) {
                    sendWSMessage('UPDATE_OFFER', {
                        offer_id: offerId,
                        status: action
                    });
                } else {
                    // Fallback: send AJAX to /seller/offerAction
                    const formData = new FormData();
                    formData.append('offer_id', offerId);
                    formData.append('action', action);
                    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                    fetch('<?= base_url('/seller/offerAction') ?>', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload(); // Reload to reflect changes
                            } else {
                                alert(data.error || 'Action failed');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Error processing offer');
                        });
                }
            }
        });
    });
    </script>
</body>

</html>