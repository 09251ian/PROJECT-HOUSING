<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Property | House System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <span class="navbar-brand fw-bold">🏡 Edit Property</span>
            <div class="d-flex align-items-center">
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
        <?php if(isset($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $field => $error): ?>
                <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="post" action="<?= base_url('/seller/edit_property/'.$property['id']) ?>"
            enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>

            <div class="col-md-6">
                <label for="title" class="form-label">Property Title</label>
                <input type="text" id="title" name="title" class="form-control"
                    value="<?= esc(old('title', $property['title'])) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control"
                    value="<?= esc(old('price', $property['price'])) ?>" required>
            </div>
            <div class="col-md-12">
                <label for="location" class="form-label">Location</label>
                <input type="text" id="location" name="location" class="form-control"
                    value="<?= esc(old('location', $property['location'])) ?>" required>
            </div>
            <div class="col-md-12">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"
                    required><?= esc(old('description', $property['description'])) ?></textarea>
            </div>
            <div class="col-md-12">
                <label for="image" class="form-label">Property Image</label>
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <?php if(!empty($property['image_path'])): ?>
                <img src="<?= esc($property['image_path']) ?>" alt="Property Image"
                    style="height:150px; margin-top:10px;">
                <?php endif; ?>
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-success">Update Property</button>
            </div>
        </form>

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

        const editForm = document.querySelector('#edit-property-form');
        if (editForm) {
            editForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Check if a new image is selected
                const imageInput = this.querySelector('input[type="file"]');
                let imagePath = null;
                if (imageInput.files.length > 0) {
                    // Upload new image
                    const formData = new FormData();
                    formData.append('image', imageInput.files[0]);
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
                            imagePath = result.image_path;
                        } else {
                            alert('Image upload failed: ' + result.error);
                            return;
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Error uploading image');
                        return;
                    }
                }

                // Prepare property data
                const propertyId = <?= json_encode($property['id']) ?>;
                const propertyData = {
                    property_id: propertyId,
                    title: this.title.value,
                    price: parseFloat(this.price.value),
                    location: this.location.value,
                    description: this.description.value
                };
                if (imagePath) {
                    propertyData.image_path = imagePath;
                }
                sendWSMessage('UPDATE_PROPERTY', propertyData);
            });
        }
    });
    </script>

</body>


</html>