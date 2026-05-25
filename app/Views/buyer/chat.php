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

<script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
<script>
// Get user info from PHP
const currentUserId = <?= json_encode($user['id'] ?? 0) ?>;
const currentUserName = <?= json_encode($user['name'] ?? '') ?>;
const receiverId = <?= json_encode($receiverId ?? 0) ?>;
const propertyId = <?= json_encode($propertyId ?? 0) ?>;

// Connect to WebSocket server
const socket = io('http://localhost:3000', {
    transports: ['websocket', 'polling'],
    reconnection: true
});

// Register user when connected
socket.on('connect', () => {
    console.log('Connected to WebSocket');
    socket.emit('register', currentUserId);
});

// Listen for incoming messages
socket.on('private message', (data) => {
    if (data.from == receiverId) {
        addMessageToChat(data.fromName, data.content, data.timestamp, false);
    }
});

// Send message via WebSocket
function sendMessageRealtime(message) {
    const messageData = {
        to: receiverId,
        from: currentUserId,
        fromName: currentUserName,
        content: message,
        propertyId: propertyId,
        timestamp: new Date().toISOString()
    };
    socket.emit('private message', messageData);
    addMessageToChat(currentUserName, message, new Date().toISOString(), true);
}

// Add message to chat box
function addMessageToChat(senderName, message, timestamp, isSender) {
    const chatBox = document.getElementById('chat-box');
    if (!chatBox) return;
    
    // Remove "No messages yet" if exists
    if (chatBox.children.length === 1 && chatBox.children[0].tagName === 'P') {
        chatBox.innerHTML = '';
    }
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${isSender ? 'sender' : 'receiver'}`;
    messageDiv.innerHTML = `
        <small class="chat-meta">${isSender ? 'You' : escapeHtml(senderName)} - ${new Date(timestamp).toLocaleString()}</small>
        <div class="mt-1">${escapeHtml(message)}</div>
    `;
    chatBox.appendChild(messageDiv);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Override form submission
const chatForm = document.querySelector('.input-group')?.closest('form');
if (chatForm) {
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const messageInput = document.querySelector('input[name="message"]');
        const message = messageInput?.value.trim();
        if (message) {
            sendMessageRealtime(message);
            messageInput.value = '';
            messageInput.focus();
        }
    });
}
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>

