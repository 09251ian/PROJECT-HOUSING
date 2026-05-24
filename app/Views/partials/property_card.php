<?php
// Usage:
// - expects $property array with: id,title,description,price,location,image_path,seller_id,seller_name
// - expects optional: $isFavorite, $existingOffer, $chatExist
// - expects $favorites/$existingOffers/$chatsExist already resolved by caller.

/** @var array $property */
/** @var bool $isFavorite */
/** @var array|null $existingOffer */
/** @var bool $chatExist */
?>
<div class="col-lg-4 col-md-6 mb-4">
  <div class="property-card h-100">
    <div class="property-media">
      <img
        src="<?= !empty($property['image_path']) ? base_url($property['image_path']) : 'https://via.placeholder.com/800x500?text=No+Image' ?>"
        alt="<?= esc($property['title']) ?>"
      >
      <div class="property-badge-row">
        <?php if (!empty($isFavorite)): ?>
          <span class="property-badge property-badge--saved"><i class="bi bi-star-fill me-1"></i>Saved</span>
        <?php endif; ?>
      </div>
    </div>

    <div class="property-body">
      <div class="d-flex align-items-start justify-content-between gap-2">
        <div>
          <h5 class="property-title"><?= esc($property['title']) ?></h5>
          <div class="property-price">₱<?= number_format((float)($property['price'] ?? 0), 2) ?></div>
        </div>
        <div class="text-end">
          <div class="property-location"><i class="bi bi-geo-alt me-1"></i><?= esc($property['location'] ?? '') ?></div>
        </div>
      </div>

      <p class="property-desc"><?= esc($property['description'] ?? '') ?></p>

      <div class="property-meta">
        <span class="me-3"><i class="bi bi-person me-1"></i><?= esc($property['seller_name'] ?? '') ?></span>
      </div>

      <div class="property-actions">
        <?php if (!empty($chatExist)): ?>
          <div class="property-hint text-success"><i class="bi bi-chat-dots me-1"></i>Active chat</div>
        <?php else: ?>
          <div class="property-hint text-muted"><i class="bi bi-chat-dots me-1"></i>No chat yet</div>
        <?php endif; ?>

        <div class="d-flex flex-wrap gap-2 mt-2">
          <a href="<?= base_url('/message/' . ($property['seller_id'] ?? 0) . '/' . $property['id']) ?>" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-chat-left-text me-1"></i>Message
          </a>

          <?php if (empty($existingOffer)): ?>
            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#offerModal_<?= (int)$property['id'] ?>">
              <i class="bi bi-send me-1"></i>Make Offer
            </a>
          <?php endif; ?>

          <?php if (!empty($existingOffer)): ?>
            <?php
              $status = $existingOffer['status'] ?? '';
              $badgeClass = match($status) {
                'accepted' => 'property-pill--success',
                'rejected' => 'property-pill--danger',
                default => 'property-pill--warning',
              };
            ?>
            <span class="property-pill <?= $badgeClass ?>">
              <?= match($status) { 'accepted' => 'Accepted', 'rejected' => 'Rejected', default => 'Pending' }; ?> Offer
            </span>
          <?php endif; ?>

          <form method="post" action="<?= base_url('/buyer/favorites/toggle') ?>" class="m-0">
            <?= csrf_field() ?>
            <input type="hidden" name="property_id" value="<?= (int)$property['id'] ?>">
            <button type="submit" class="btn btn-sm <?= !empty($isFavorite) ? 'btn-outline-danger' : 'btn-outline-light' ?>">
              <?= !empty($isFavorite) ? '★ Saved' : '☆ Save' ?>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php if (empty($existingOffer)): ?>
  <div class="modal fade" id="offerModal_<?= (int)$property['id'] ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content app-modal">
        <div class="modal-header">
          <h5 class="modal-title">Make an offer</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" action="<?= base_url('/make_offer') ?>" class="d-grid gap-2">
            <?= csrf_field() ?>
            <input type="hidden" name="property_id" value="<?= (int)$property['id'] ?>">
            <label class="form-label">Offer amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" required placeholder="Enter amount">
            <button type="submit" class="btn btn-primary mt-2"><i class="bi bi-send me-1"></i>Submit Offer</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

