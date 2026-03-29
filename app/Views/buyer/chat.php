<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chat - House System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
        margin-left: auto;
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
        <h4 class="mb-3">
            Chat Messages
            <small class="text-muted" id="typingStatus"></small>
        </h4>

        <div id="chat-box">
            <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
            <?php $isSender = ($message['sender_id'] == $user['id']); ?>
            <div class="chat-message <?= $isSender ? 'sender' : 'receiver' ?>" data-message-id="<?= $message['id'] ?>">
                <small class="text-muted"><?= $isSender ? 'You' : 'Them' ?> -
                    <?= date('M d, Y h:i A', strtotime($message['created_at'])) ?></small>
                <div><?= esc($message['message']) ?></div>
            </div>
            <?php endforeach; ?>
            <?php else: ?>
            <p class="text-muted">No messages yet.</p>
            <?php endif; ?>
        </div>

        <form id="messageForm">
            <?= csrf_field() ?>
            <div class="input-group">
                <input type="text" id="messageInput" name="message" class="form-control"
                    placeholder="Type your message..." required autofocus>
                <button class="btn btn-primary" type="submit" id="sendBtn">Send</button>
            </div>
            <div class="text-end mt-1">
                <small class="text-muted" id="charCounter">0/500</small>
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

    <script>
    const baseUrl = '<?= base_url() ?>';
    const receiverId = <?= json_encode($receiverId) ?>;
    const propertyId = <?= json_encode($propertyId) ?>;
    const userId = <?= json_encode($user['id']) ?>;
    const csrfToken = '<?= csrf_hash() ?>';
    let lastMessageId = 0;

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
            hour12: true
        });
    }

    function scrollToBottom() {
        const chatBox = document.getElementById('chat-box');
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function addMessageToChat(message, isSender) {
        const chatBox = document.getElementById('chat-box');
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${isSender ? 'sender' : 'receiver'}`;
        messageDiv.setAttribute('data-message-id', message.id);
        messageDiv.innerHTML = `
            <small class="text-muted">${isSender ? 'You' : 'Them'} - ${formatTime(message.created_at)}</small>
            <div>${escapeHtml(message.message)}</div>
        `;
        chatBox.appendChild(messageDiv);
        scrollToBottom();
    }

    function sendMessage() {
        const input = $('#messageInput');
        const message = input.val().trim();
        if (!message) return false;

        const sendBtn = $('#sendBtn');
        const originalText = sendBtn.html();
        sendBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: baseUrl + `/message/ajax/${receiverId}/${propertyId}`,
            type: 'POST',
            data: {
                message: message,
                csrf_test_name: csrfToken
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success) {
                    input.val('');
                    updateCharCounter();
                    fetchMessages(true);
                } else {
                    alert('Error: ' + (response.error || 'Could not send message'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Send error:', status, error);
                alert('Network error. Please try again.');
            },
            complete: function() {
                sendBtn.prop('disabled', false).html(originalText);
                input.focus();
            }
        });
        return false;
    }

    function fetchMessages() {
        $.ajax({
            url: baseUrl + `/message/ajax/${receiverId}/${propertyId}`,
            type: 'GET',
            data: {
                since: lastMessageId,
                _t: Date.now()
            },
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.success && response.messages && response.messages.length > 0) {
                    response.messages.forEach(msg => {
                        if (msg.id > lastMessageId) {
                            const isSender = (msg.sender_id == userId);
                            addMessageToChat(msg, isSender);
                            if (msg.id > lastMessageId) lastMessageId = msg.id;
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.warn('Fetch error:', status, error);
            }
        });
    }

    function updateCharCounter() {
        const len = $('#messageInput').val().length;
        $('#charCounter').text(len + '/500');
        if (len > 500) {
            $('#messageInput').val($('#messageInput').val().substring(0, 500));
            updateCharCounter();
        }
    }

    let pollingInterval = null;

    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => fetchMessages(), 3000);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    }

    $(document).ready(function() {
        // Set lastMessageId from existing messages
        const messages = document.querySelectorAll('.chat-message');
        if (messages.length > 0) {
            const lastMsg = messages[messages.length - 1];
            const lastId = lastMsg.getAttribute('data-message-id');
            if (lastId) lastMessageId = parseInt(lastId);
        }

        $('#messageForm').on('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });

        startPolling();
        $('#messageInput').focus();
        scrollToBottom();
    });

    window.addEventListener('beforeunload', function() {
        stopPolling();
    });
    </script>
</body>

</html>