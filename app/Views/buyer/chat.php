<?php
$active = null; // header handles highlighting based on $user role
include __DIR__ . '/../partials/header.php';
?>

<div class="container my-4">
  <style>
    .chat-shell{display:flex;flex-direction:column;gap:10px;}
    .chat-message{padding:10px;border-radius:12px;max-width:75%;border:1px solid rgba(255,255,255,.08)}
    .chat-message.sender{background:rgba(34,197,94,.18);border-color:rgba(34,197,94,.25);align-self:flex-end}
    .chat-message.receiver{background:rgba(239,68,68,.14);border-color:rgba(239,68,68,.22);align-self:flex-start}
    #chat-box{height:420px;overflow-y:auto;display:flex;flex-direction:column;padding:12px;border:1px solid rgba(255,255,255,.10);border-radius:14px;background:rgba(15,27,51,.55)}
    .chat-meta{color:rgba(233,240,255,.7);font-size:12.5px}
  </style>

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <h4 class="m-0">Chat Messages</h4>
    <div class="text-muted small">Property #<?= esc($propertyId ?? '') ?></div>
  </div>

  <div class="chat-shell">
    <div id="chat-box">
      <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $message): ?>
          <?php $isSender = ((int)($message['sender_id'] ?? 0) === (int)($user['id'] ?? 0)); ?>
          <div class="chat-message <?= $isSender ? 'sender' : 'receiver' ?>">
            <small class="chat-meta"><?= $isSender ? 'You' : 'Them' ?> - <?= date('M d, Y h:i A', strtotime($message['created_at'] ?? 'now')) ?></small>
            <div class="mt-1"><?= esc($message['message'] ?? '') ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="text-muted">No messages yet.</p>
      <?php endif; ?>
    </div>

    <form method="post" action="<?= base_url('/message/' . ($receiverId ?? 0) . '/' . ($propertyId ?? 0)) ?>">
      <?= csrf_field() ?>
      <div class="input-group">
        <input type="text" name="message" class="form-control" placeholder="Type your message..." required autofocus>
        <button class="btn btn-primary" type="submit">Send</button>
      </div>
    </form>

    <div class="mt-3">
      <?php if (($userRole ?? '') === 'seller'): ?>
        <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
      <?php else: ?>
        <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

