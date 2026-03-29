<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'WebSocket Test' ?> | Housing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 900px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .status-badge {
            font-size: 14px;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .status-connected {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-disconnected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-connecting {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .message-log {
            height: 400px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .message-item {
            padding: 5px;
            margin: 2px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .message-sent {
            color: #0066cc;
            border-left: 3px solid #0066cc;
            padding-left: 10px;
        }
        .message-received {
            color: #28a745;
            border-left: 3px solid #28a745;
            padding-left: 10px;
        }
        .message-error {
            color: #dc3545;
            border-left: 3px solid #dc3545;
            padding-left: 10px;
            background: #fff5f5;
        }
        .message-info {
            color: #6c757d;
            border-left: 3px solid #6c757d;
            padding-left: 10px;
        }
        .timestamp {
            color: #999;
            font-size: 10px;
            margin-right: 10px;
        }
        .badge-online {
            background: #28a745;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.6; }
            100% { opacity: 1; }
        }
        .input-group-custom {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h3 class="mb-0">🔌 WebSocket Connection Tester</h3>
                <small>Test your WebSocket server connection</small>
            </div>
            <div class="card-body">
                
                <!-- Server Status -->
                <div class="alert alert-info mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Server Information:</strong><br>
                            <small>WebSocket Endpoint: <code id="wsEndpoint">ws://<?= $_SERVER['HTTP_HOST'] ?? 'localhost' ?>:<?= $websocket_port ?></code></small><br>
                            <small>Current User: <strong><?= session()->get('user')['name'] ?? 'Guest' ?></strong> (ID: <?= $user_id ?>, Role: <?= $user_role ?>)</small>
                        </div>
                        <div>
                            <span id="serverStatusBadge" class="badge bg-secondary">Checking...</span>
                        </div>
                    </div>
                </div>

                <!-- Connection Controls -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Connection Settings</h6>
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text">Port</span>
                                    <input type="number" id="wsPort" class="form-control" value="<?= $websocket_port ?>" style="width: 80px;">
                                    <button class="btn btn-primary" onclick="updatePort()">Update</button>
                                </div>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-success" onclick="connectWebSocket()" id="connectBtn">
                                        🔌 Connect
                                    </button>
                                    <button class="btn btn-danger" onclick="disconnectWebSocket()" id="disconnectBtn" disabled>
                                        ⏹️ Disconnect
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Connection Status</h6>
                                <div id="connectionStatus" class="text-center">
                                    <div class="status-badge status-disconnected d-inline-block">
                                        ● Disconnected
                                    </div>
                                </div>
                                <div id="connectionInfo" class="small text-muted mt-2 text-center">
                                    Not connected
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Test Commands -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <strong>Test Commands</strong>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-primary w-100 mb-2" onclick="sendPing()" id="pingBtn" disabled>
                                            📡 Send PING
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-success w-100 mb-2" onclick="sendAuth()" id="authBtn" disabled>
                                            🔐 Send AUTH
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-info w-100 mb-2" onclick="sendGetProperties()" id="propsBtn" disabled>
                                            🏠 Get Properties
                                        </button>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-outline-warning w-100 mb-2" onclick="clearMessages()">
                                            🗑️ Clear Log
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Log -->
                <div class="mb-3">
                    <label class="fw-bold">Message Log:</label>
                    <div id="messageLog" class="message-log">
                        <div class="message-item message-info">
                            <span class="timestamp"><?= date('H:i:s') ?></span>
                            Ready to test WebSocket connection...
                        </div>
                        <div class="message-item message-info">
                            <span class="timestamp"><?= date('H:i:s') ?></span>
                            Click "Connect" to start testing
                        </div>
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="alert alert-secondary small">
                    <strong>💡 Quick Tips:</strong><br>
                    • Make sure your WebSocket server is running: <code>php server.php</code><br>
                    • The server should be running on port <strong><?= $websocket_port ?></strong><br>
                    • Check the terminal for connection logs<br>
                    • Use the buttons above to test different WebSocket commands
                </div>
            </div>
        </div>
    </div>

    <script>
        let ws = null;
        let reconnectAttempts = 0;
        const maxReconnectAttempts = 3;
        
        // DOM Elements
        const messageLog = document.getElementById('messageLog');
        const connectBtn = document.getElementById('connectBtn');
        const disconnectBtn = document.getElementById('disconnectBtn');
        const pingBtn = document.getElementById('pingBtn');
        const authBtn = document.getElementById('authBtn');
        const propsBtn = document.getElementById('propsBtn');
        const connectionStatus = document.getElementById('connectionStatus');
        const connectionInfo = document.getElementById('connectionInfo');
        
        // User info from PHP
        const userId = <?= $user_id ?>;
        const userRole = '<?= $user_role ?>';
        
        // Add message to log
        function addMessage(text, type = 'info') {
            const messageDiv = document.createElement('div');
            messageDiv.className = `message-item message-${type}`;
            const timestamp = new Date().toLocaleTimeString();
            messageDiv.innerHTML = `<span class="timestamp">[${timestamp}]</span> ${text}`;
            messageLog.appendChild(messageDiv);
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'end' });
            
            // Auto-scroll
            messageLog.scrollTop = messageLog.scrollHeight;
        }
        
        // Update connection status UI
        function updateConnectionStatus(status, info = '') {
            let statusHtml = '';
            let statusClass = '';
            
            switch(status) {
                case 'connected':
                    statusHtml = '<div class="status-badge status-connected d-inline-block">● Connected</div>';
                    statusClass = 'connected';
                    connectBtn.disabled = true;
                    disconnectBtn.disabled = false;
                    pingBtn.disabled = false;
                    authBtn.disabled = false;
                    propsBtn.disabled = false;
                    break;
                case 'connecting':
                    statusHtml = '<div class="status-badge status-connecting d-inline-block">⟳ Connecting...</div>';
                    statusClass = 'connecting';
                    connectBtn.disabled = true;
                    disconnectBtn.disabled = false;
                    pingBtn.disabled = true;
                    authBtn.disabled = true;
                    propsBtn.disabled = true;
                    break;
                default:
                    statusHtml = '<div class="status-badge status-disconnected d-inline-block">● Disconnected</div>';
                    statusClass = 'disconnected';
                    connectBtn.disabled = false;
                    disconnectBtn.disabled = true;
                    pingBtn.disabled = true;
                    authBtn.disabled = true;
                    propsBtn.disabled = true;
            }
            
            connectionStatus.innerHTML = statusHtml;
            connectionInfo.innerHTML = info;
        }
        
        // Connect to WebSocket
        function connectWebSocket() {
            if (ws && ws.readyState === WebSocket.OPEN) {
                addMessage('Already connected', 'warning');
                return;
            }
            
            const port = document.getElementById('wsPort').value;
            const wsUrl = `ws://${window.location.hostname}:${port}`;
            
            addMessage(`Attempting to connect to ${wsUrl}...`, 'info');
            updateConnectionStatus('connecting', `Connecting to ${wsUrl}...`);
            
            try {
                ws = new WebSocket(wsUrl);
                
                ws.onopen = function() {
                    addMessage(`✅ Successfully connected to WebSocket server!`, 'received');
                    updateConnectionStatus('connected', `Connected to ${wsUrl}`);
                    reconnectAttempts = 0;
                    
                    // Auto-authenticate
                    setTimeout(() => {
                        sendAuth();
                    }, 500);
                };
                
                ws.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        addMessage(`📩 Received: ${data.type}`, 'received');
                        console.log('Full message:', data);
                        
                        // Handle different message types
                        switch(data.type) {
                            case 'CONNECTION_ESTABLISHED':
                                addMessage(`  Server: ${data.payload.message}`, 'info');
                                addMessage(`  Connection ID: ${data.payload.connection_id}`, 'info');
                                break;
                                
                            case 'AUTH_SUCCESS':
                                addMessage(`  ✅ Authentication successful! User: ${data.payload.user_id} (${data.payload.role})`, 'success');
                                break;
                                
                            case 'AUTH_FAILED':
                                addMessage(`  ❌ Authentication failed: ${data.payload.message}`, 'error');
                                break;
                                
                            case 'PONG':
                                addMessage(`  🏓 Pong received at ${data.payload.server_time}`, 'success');
                                addMessage(`  Server Stats: Uptime: ${Math.floor(data.payload.stats.uptime/60)} minutes`, 'info');
                                break;
                                
                            case 'PROPERTIES_LIST':
                                addMessage(`  📋 Received ${data.payload.length} properties`, 'success');
                                if (data.payload.length > 0) {
                                    addMessage(`  First property: ${data.payload[0].title} - ₱${data.payload[0].price}`, 'info');
                                }
                                break;
                                
                            case 'SERVER_STATS':
                                addMessage(`  📊 Server Stats:`, 'info');
                                addMessage(`     Active Connections: ${data.payload.active_connections}`, 'info');
                                addMessage(`     Total Messages: ${data.payload.total_messages_processed}`, 'info');
                                addMessage(`     Uptime: ${Math.floor(data.payload.uptime/60)} minutes`, 'info');
                                break;
                                
                            case 'NEW_OFFER':
                                addMessage(`  💰 New offer: ${data.payload.buyer_name} offered ₱${data.payload.amount} for ${data.payload.property_title}`, 'success');
                                break;
                                
                            case 'OFFER_UPDATED':
                                addMessage(`  📝 Offer ${data.payload.offer_id} status: ${data.payload.status}`, 'info');
                                break;
                                
                            case 'ERROR':
                                addMessage(`  ❌ Server error: ${data.payload.message}`, 'error');
                                break;
                                
                            default:
                                addMessage(`  Unhandled type: ${data.type}`, 'info');
                        }
                    } catch (e) {
                        addMessage(`Error parsing message: ${e.message}`, 'error');
                        addMessage(`Raw: ${event.data.substring(0, 100)}...`, 'info');
                    }
                };
                
                ws.onclose = function(event) {
                    let reason = '';
                    if (event.code === 1000) reason = 'Normal closure';
                    else if (event.code === 1001) reason = 'Going away';
                    else if (event.code === 1006) reason = 'Abnormal closure (server not running?)';
                    else reason = `Code: ${event.code}`;
                    
                    addMessage(`❌ Disconnected: ${reason}`, 'error');
                    updateConnectionStatus('disconnected', reason);
                    ws = null;
                    
                    // Auto-reconnect
                    if (reconnectAttempts < maxReconnectAttempts) {
                        reconnectAttempts++;
                        addMessage(`Attempting to reconnect (${reconnectAttempts}/${maxReconnectAttempts})...`, 'info');
                        setTimeout(connectWebSocket, 3000);
                    }
                };
                
                ws.onerror = function(error) {
                    addMessage(`WebSocket error: ${error}`, 'error');
                    updateConnectionStatus('error', 'Connection error');
                };
                
            } catch (error) {
                addMessage(`Failed to create WebSocket: ${error.message}`, 'error');
                updateConnectionStatus('disconnected', error.message);
                ws = null;
            }
        }
        
        // Disconnect from WebSocket
        function disconnectWebSocket() {
            if (ws) {
                addMessage('Closing connection...', 'info');
                ws.close();
                ws = null;
                updateConnectionStatus('disconnected', 'Manually closed');
            }
        }
        
        // Send PING
        function sendPing() {
            if (!ws || ws.readyState !== WebSocket.OPEN) {
                addMessage('Cannot send PING: Not connected', 'error');
                return;
            }
            
            const pingMsg = JSON.stringify({
                type: 'PING',
                payload: { timestamp: Date.now() }
            });
            ws.send(pingMsg);
            addMessage('📡 Sent PING', 'sent');
        }
        
        // Send AUTH
        function sendAuth() {
            if (!ws || ws.readyState !== WebSocket.OPEN) {
                addMessage('Cannot send AUTH: Not connected', 'error');
                return;
            }
            
            const authMsg = JSON.stringify({
                type: 'AUTH',
                payload: {
                    user_id: userId,
                    role: userRole
                }
            });
            ws.send(authMsg);
            addMessage(`🔐 Sent AUTH for User ${userId} (${userRole})`, 'sent');
        }
        
        // Send GET_PROPERTIES
        function sendGetProperties() {
            if (!ws || ws.readyState !== WebSocket.OPEN) {
                addMessage('Cannot send GET_PROPERTIES: Not connected', 'error');
                return;
            }
            
            ws.send(JSON.stringify({
                type: 'GET_PROPERTIES',
                payload: {}
            }));
            addMessage('🏠 Sent GET_PROPERTIES', 'sent');
        }
        
        // Clear messages
        function clearMessages() {
            messageLog.innerHTML = '<div class="message-item message-info">Messages cleared</div>';
            addMessage('Message log cleared', 'info');
        }
        
        // Update port
        function updatePort() {
            const port = document.getElementById('wsPort').value;
            const wsUrl = `ws://${window.location.hostname}:${port}`;
            document.getElementById('wsEndpoint').innerHTML = wsUrl;
            addMessage(`WebSocket endpoint updated to ${wsUrl}`, 'info');
            
            // Check server status
            checkServerStatus();
        }
        
        // Check if WebSocket server is running
        async function checkServerStatus() {
            const port = document.getElementById('wsPort').value;
            const statusBadge = document.getElementById('serverStatusBadge');
            
            statusBadge.className = 'badge bg-warning';
            statusBadge.textContent = 'Checking...';
            
            try {
                const response = await fetch(`<?= base_url('websocket-test/status') ?>?port=${port}`);
                const data = await response.json();
                
                if (data.status === 'online') {
                    statusBadge.className = 'badge bg-success';
                    statusBadge.textContent = '● Online';
                    addMessage(`WebSocket server is running on port ${port}`, 'success');
                } else {
                    statusBadge.className = 'badge bg-danger';
                    statusBadge.textContent = '● Offline';
                    addMessage(`WebSocket server is NOT running on port ${port}. Start it with: php server.php`, 'error');
                }
            } catch (error) {
                statusBadge.className = 'badge bg-danger';
                statusBadge.textContent = '● Unknown';
                addMessage(`Cannot check server status: ${error.message}`, 'error');
            }
        }
        
        // Auto-check server status on page load
        window.addEventListener('load', function() {
            addMessage('WebSocket Test Page Loaded', 'info');
            addMessage(`User ID: ${userId}, Role: ${userRole}`, 'info');
            checkServerStatus();
            
            // Check status every 10 seconds
            setInterval(checkServerStatus, 10000);
        });
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'c') {
                e.preventDefault();
                connectWebSocket();
            } else if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                disconnectWebSocket();
            } else if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                sendPing();
            }
        });
        
        // Export functions to console
        window.testWs = {
            connect: connectWebSocket,
            disconnect: disconnectWebSocket,
            ping: sendPing,
            auth: sendAuth,
            getProps: sendGetProperties,
            clear: clearMessages
        };
        
        console.log('WebSocket Test Tool Ready!');
        console.log('Commands: testWs.connect(), testWs.ping(), testWs.auth()');
    </script>
</body>
</html>