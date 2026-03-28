<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Chat - House System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  .chat-message {
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 8px;
    max-width: 75%;
  }
  .chat-message.sender {
    background-color: #d1e7dd;
    align-self: flex-end;
  }
  .chat-message.receiver {
    background-color: #f8d7da;
    align-self: flex-start;
  }
  #chat-box {
    height: 400px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    margin-bottom: 10px;
    background: #fff;
  }
</style>
</head>
<body class="bg-light">

<div class="container mt-4">
  <h4 class="mb-3">Chat Messages</h4>

  <div id="chat-box">
    <?php if (!empty($messages)): ?>
      <?php foreach ($messages as $message): ?>
        <?php $isSender = ($message['sender_id'] == $user['id']); ?>
        <div class="chat-message <?= $isSender ? 'sender' : 'receiver' ?>">
          <small class="text-muted"><?= $isSender ? 'You' : 'Them' ?> - <?= date('M d, Y h:i A', strtotime($message['created_at'])) ?></small>
          <div><?= esc($message['message']) ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-muted">No messages yet.</p>
    <?php endif; ?>
  </div>

  <form method="post" action="<?= base_url('/message/' . $receiverId . '/' . $propertyId) ?>">
    <?= csrf_field() ?>
    <div class="input-group">
      <input type="text" name="message" class="form-control" placeholder="Type your message..." required autofocus>
      <button class="btn btn-primary" type="submit">Send</button>
    </div>
  </form>

  <div class="mt-3">
    <?php if ($userRole === 'seller'): ?>
      <a href="<?= base_url('/seller/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    <?php else: ?>
      <a href="<?= base_url('/buyer/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
<!--update-->