<?php

require __DIR__ . '/../vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

class HousingWebSocketServer implements MessageComponentInterface {
    
    protected $clients;
    protected $pdo;
    protected $startTime;
    protected $messageCount = 0;
    protected $connectionCount = 0;
    protected $errorCount = 0;
    protected $userConnections = []; // Map user_id -> connection
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->startTime = time();
        
        // Database connection
        try {
            $this->pdo = new \PDO(
                "mysql:host=localhost;port=3306;dbname=housing;charset=utf8mb4",
                "root",
                ""
            );
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET NAMES utf8mb4");
            $this->log("✓ Database connected successfully", "success");
        } catch (\PDOException $e) {
            $this->log("✗ Database connection failed: " . $e->getMessage(), "error");
            $this->pdo = null;
        }
    }
    
    private function log($message, $type = "info") {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] ";
        
        switch($type) {
            case "success":
                $logMessage .= "✅ ";
                break;
            case "error":
                $logMessage .= "❌ ";
                break;
            case "warning":
                $logMessage .= "⚠️  ";
                break;
            default:
                $logMessage .= "ℹ️  ";
        }
        
        $logMessage .= $message;
        echo $logMessage . "\n";
        
        // Also write to log file
        $this->writeToLogFile($message, $type);
    }
    
    private function writeToLogFile($message, $type) {
        $logDir = __DIR__ . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        
        $logFile = $logDir . '/websocket_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$type}] {$message}\n";
        @file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
    
    public function getServerStats() {
        return [
            'uptime' => time() - $this->startTime,
            'active_connections' => $this->connectionCount,
            'total_messages_processed' => $this->messageCount,
            'total_errors' => $this->errorCount,
            'database_connected' => $this->pdo !== null,
            'memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true),
            'authenticated_users' => count($this->userConnections)
        ];
    }
    
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        $this->connectionCount++;
        
        $clientInfo = $conn->remoteAddress;
        $this->log("New connection from {$clientInfo} (Total: {$this->connectionCount})", "success");
        
        // Send welcome message
        $conn->send(json_encode([
            'type' => 'CONNECTION_ESTABLISHED',
            'payload' => [
                'message' => 'Connected to Housing WebSocket Server',
                'connection_id' => $conn->resourceId,
                'server_time' => date('Y-m-d H:i:s'),
                'server_stats' => $this->getServerStats()
            ]
        ]));
        
        $this->broadcastStatus();
    }
    
    public function onMessage(ConnectionInterface $from, $msg) {
        $this->messageCount++;
        $data = json_decode($msg, true);
        
        if (!$data || !isset($data['type'])) {
            $this->log("Invalid message received from {$from->resourceId}", "warning");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Invalid message format. Type field is required.']
            ]));
            return;
        }
        
        $this->log("Received: {$data['type']} from connection {$from->resourceId}");
        
        // Handle AUTH message
        if ($data['type'] === 'AUTH') {
            $this->handleAuth($from, $data['payload']);
            return;
        }
        
        // Check if client is authenticated
        if (!isset($from->authenticated) || !$from->authenticated) {
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Please authenticate first']
            ]));
            return;
        }
        
        // Handle NOTIFICATION with target user (system messages)
        if ($data['type'] === 'NOTIFICATION' && isset($data['target_user_id'])) {
            $this->sendToUser($data['target_user_id'], $data['data']);
            return;
        }
        
        switch ($data['type']) {
            case 'PING':
                $this->handlePing($from);
                break;
                
            case 'SEND_MESSAGE':
                $this->handleSendMessage($from, $data['payload']);
                break;
                
            case 'GET_MESSAGES':
                $this->handleGetMessages($from, $data['payload']);
                break;
                
            case 'MAKE_OFFER':
                $this->handleMakeOffer($from, $data['payload']);
                break;
                
            case 'CANCEL_OFFER':
                $this->handleCancelOffer($from, $data['payload']);
                break;
                
            case 'GET_OFFERS':
                $this->handleGetOffers($from, $data['payload']);
                break;
                
            case 'UPDATE_OFFER':
                $this->handleUpdateOffer($from, $data['payload']);
                break;
                
            case 'GET_PROPERTIES':
                $this->handleGetProperties($from, $data['payload']);
                break;
                
            case 'GET_PROPERTY':
                $this->handleGetProperty($from, $data['payload']);
                break;
                
            case 'GET_STATS':
                $this->handleGetStats($from);
                break;
                
            default:
                $this->log("Unknown message type: {$data['type']}", "warning");
                $from->send(json_encode([
                    'type' => 'ERROR',
                    'payload' => ['message' => "Unknown message type: {$data['type']}"]
                ]));
                break;
        }
    }
    
    private function handleAuth($from, $payload) {
        if (!isset($payload['user_id']) || !isset($payload['role'])) {
            $from->send(json_encode([
                'type' => 'AUTH_FAILED',
                'payload' => ['message' => 'Authentication failed: Missing credentials']
            ]));
            return;
        }
        
        // Store user info in connection
        $from->user_id = $payload['user_id'];
        $from->role = $payload['role'];
        if (isset($payload['name'])) {
            $from->name = $payload['name'];
        }
        $from->authenticated = true;
        
        // Map user_id to this connection (if multiple connections, store in array)
        if (!isset($this->userConnections[$payload['user_id']])) {
            $this->userConnections[$payload['user_id']] = [];
        }
        $this->userConnections[$payload['user_id']][] = $from;
        
        $this->log("User {$payload['user_id']} ({$payload['role']}) authenticated on connection {$from->resourceId}", "success");
        
        $from->send(json_encode([
            'type' => 'AUTH_SUCCESS',
            'payload' => [
                'message' => 'Authentication successful',
                'user_id' => $payload['user_id'],
                'role' => $payload['role'],
                'server_stats' => $this->getServerStats()
            ]
        ]));
        
        // Send any pending notifications
        $this->sendPendingNotifications($payload['user_id'], $from);
        
        // Send properties list after authentication
        $this->handleGetProperties($from, []);
        
        $this->broadcastStatus();
    }
    
    private function sendToUser($userId, $message) {
        if (isset($this->userConnections[$userId])) {
            $messageJson = json_encode($message);
            foreach ($this->userConnections[$userId] as $connection) {
                if ($connection && $connection->authenticated) {
                    $connection->send($messageJson);
                    $this->log("Sent message to user {$userId}", "success");
                }
            }
            return true;
        }
        $this->log("User {$userId} not connected", "warning");
        return false;
    }
    
    private function sendPendingNotifications($userId, $connection) {
        if (!$this->pdo) return;
        
        try {
            // Check if notifications table exists (optional feature)
            $stmt = $this->pdo->query("SHOW TABLES LIKE 'notifications'");
            if ($stmt->rowCount() == 0) {
                return; // Notifications table doesn't exist
            }
            
            $stmt = $this->pdo->prepare(
                "SELECT * FROM notifications WHERE user_id = :user_id AND delivered = 0 
                 ORDER BY created_at ASC LIMIT 50"
            );
            $stmt->execute([':user_id' => $userId]);
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($notifications as $notification) {
                $message = json_decode($notification['data'], true);
                if ($message) {
                    $connection->send(json_encode($message));
                    $this->log("Sent pending notification to user {$userId}", "info");
                    
                    $updateStmt = $this->pdo->prepare(
                        "UPDATE notifications SET delivered = 1, delivered_at = NOW() WHERE id = :id"
                    );
                    $updateStmt->execute([':id' => $notification['id']]);
                }
            }
        } catch (\Exception $e) {
            $this->log("Error sending pending notifications: " . $e->getMessage(), "error");
        }
    }
    
    private function handlePing($from) {
        $from->send(json_encode([
            'type' => 'PONG',
            'payload' => [
                'timestamp' => time(),
                'server_time' => date('Y-m-d H:i:s'),
                'stats' => $this->getServerStats()
            ]
        ]));
    }
    
    private function handleGetStats($from) {
        $from->send(json_encode([
            'type' => 'SERVER_STATS',
            'payload' => $this->getServerStats()
        ]));
    }
    
    private function handleCancelOffer($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Database connection unavailable']
            ]));
            return;
        }
        
        try {
            if (!isset($payload['offer_id'])) {
                throw new \Exception('Missing offer_id');
            }
            
            // Get offer details
            $stmt = $this->pdo->prepare(
                "SELECT o.*, p.seller_id, p.title as property_title 
                 FROM offers o
                 JOIN properties p ON o.property_id = p.id
                 WHERE o.id = :offer_id"
            );
            $stmt->execute([':offer_id' => $payload['offer_id']]);
            $offer = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$offer) {
                throw new \Exception('Offer not found');
            }
            
            // Check if user is authorized (buyer who made the offer)
            if ($offer['buyer_id'] != $from->user_id) {
                throw new \Exception('Unauthorized to cancel this offer');
            }
            
            // Check if offer is still pending
            if ($offer['status'] != 'pending') {
                throw new \Exception('Cannot cancel offer that is already ' . $offer['status']);
            }
            
            // Update offer status to cancelled
            $updateStmt = $this->pdo->prepare(
                "UPDATE offers SET status = 'cancelled' WHERE id = :offer_id"
            );
            $updateStmt->execute([':offer_id' => $payload['offer_id']]);
            
            // Notify seller about cancellation
            $this->sendToUser($offer['seller_id'], [
                'type' => 'OFFER_CANCELLED',
                'payload' => [
                    'offer_id' => (int)$payload['offer_id'],
                    'property_id' => $offer['property_id'],
                    'property_title' => $offer['property_title'],
                    'buyer_id' => $from->user_id,
                    'message' => 'An offer has been cancelled by the buyer'
                ]
            ]);
            
            $from->send(json_encode([
                'type' => 'OFFER_CANCELLED',
                'payload' => [
                    'offer_id' => (int)$payload['offer_id'],
                    'status' => 'cancelled',
                    'message' => 'Offer cancelled successfully'
                ]
            ]));
            
            $this->log("Offer cancelled: {$payload['offer_id']} by user {$from->user_id}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error cancelling offer: " . $e->getMessage(), "error");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Failed to cancel offer: ' . $e->getMessage()]
            ]));
        }
    }
    
    private function handleSendMessage($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Database connection unavailable']
            ]));
            return;
        }
        
        try {
            if (!isset($payload['receiver_id']) || !isset($payload['property_id']) || !isset($payload['message'])) {
                throw new \Exception('Missing required fields');
            }
            
            // Insert message - created_at will use MySQL DEFAULT CURRENT_TIMESTAMP if set
            $stmt = $this->pdo->prepare(
                "INSERT INTO messages (sender_id, receiver_id, property_id, message) 
                 VALUES (:sender_id, :receiver_id, :property_id, :message)"
            );
            
            $stmt->execute([
                ':sender_id' => $from->user_id,
                ':receiver_id' => $payload['receiver_id'],
                ':property_id' => $payload['property_id'],
                ':message' => $payload['message']
            ]);
            
            $messageId = $this->pdo->lastInsertId();
            
            // Get sender info
            $senderStmt = $this->pdo->prepare("SELECT name FROM users WHERE id = :user_id");
            $senderStmt->execute([':user_id' => $from->user_id]);
            $sender = $senderStmt->fetch(\PDO::FETCH_ASSOC);
            
            $response = [
                'type' => 'NEW_MESSAGE',
                'payload' => [
                    'id' => (int)$messageId,
                    'sender_id' => $from->user_id,
                    'sender_name' => $sender['name'],
                    'receiver_id' => $payload['receiver_id'],
                    'property_id' => $payload['property_id'],
                    'message' => $payload['message'],
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            // Send to receiver if connected
            $this->sendToUser($payload['receiver_id'], $response);
            
            // Send confirmation to sender
            $from->send(json_encode([
                'type' => 'MESSAGE_SENT',
                'payload' => ['id' => $messageId, 'status' => 'delivered']
            ]));
            
            $this->log("Message sent: ID {$messageId} from user {$from->user_id} to user {$payload['receiver_id']}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error sending message: " . $e->getMessage(), "error");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Failed to send message: ' . $e->getMessage()]
            ]));
        }
    }
    
    private function handleMakeOffer($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Database connection unavailable']
            ]));
            return;
        }
        
        try {
            if (!isset($payload['property_id']) || !isset($payload['amount'])) {
                throw new \Exception('Missing required fields: property_id and amount required');
            }
            
            // Check if property exists and get seller info
            $propertyStmt = $this->pdo->prepare(
                "SELECT p.*, u.name as seller_name, u.id as seller_id 
                 FROM properties p 
                 LEFT JOIN users u ON p.seller_id = u.id 
                 WHERE p.id = :property_id AND (p.is_archived = 0 OR p.is_archived IS NULL)"
            );
            $propertyStmt->execute([':property_id' => $payload['property_id']]);
            $property = $propertyStmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$property) {
                throw new \Exception('Property not found or is archived');
            }
            
            // Check if pending offer already exists for this property by this buyer
            $checkStmt = $this->pdo->prepare(
                "SELECT id FROM offers WHERE property_id = :property_id AND buyer_id = :buyer_id AND status = 'pending'"
            );
            $checkStmt->execute([
                ':property_id' => $payload['property_id'],
                ':buyer_id' => $from->user_id
            ]);
            
            if ($checkStmt->rowCount() > 0) {
                $from->send(json_encode([
                    'type' => 'ERROR',
                    'payload' => ['message' => 'You already have a pending offer for this property']
                ]));
                return;
            }
            
            // Check if buyer already has an accepted offer for this property
            $acceptedStmt = $this->pdo->prepare(
                "SELECT id FROM offers WHERE property_id = :property_id AND buyer_id = :buyer_id AND status = 'accepted'"
            );
            $acceptedStmt->execute([
                ':property_id' => $payload['property_id'],
                ':buyer_id' => $from->user_id
            ]);
            
            if ($acceptedStmt->rowCount() > 0) {
                $from->send(json_encode([
                    'type' => 'ERROR',
                    'payload' => ['message' => 'You already have an accepted offer for this property']
                ]));
                return;
            }
            
            // Insert offer - don't include created_at if column doesn't exist
            // Let's first check if the offers table has a created_at column
            $columnsStmt = $this->pdo->query("SHOW COLUMNS FROM offers LIKE 'created_at'");
            $hasCreatedAt = $columnsStmt->rowCount() > 0;
            
            if ($hasCreatedAt) {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO offers (property_id, buyer_id, amount, status, created_at) 
                     VALUES (:property_id, :buyer_id, :amount, 'pending', NOW())"
                );
            } else {
                $stmt = $this->pdo->prepare(
                    "INSERT INTO offers (property_id, buyer_id, amount, status) 
                     VALUES (:property_id, :buyer_id, :amount, 'pending')"
                );
            }
            
            $stmt->execute([
                ':property_id' => $payload['property_id'],
                ':buyer_id' => $from->user_id,
                ':amount' => $payload['amount']
            ]);
            
            $offerId = $this->pdo->lastInsertId();
            
            // Get buyer info
            $buyerStmt = $this->pdo->prepare("SELECT name FROM users WHERE id = :user_id");
            $buyerStmt->execute([':user_id' => $from->user_id]);
            $buyer = $buyerStmt->fetch(\PDO::FETCH_ASSOC);
            
            $response = [
                'type' => 'NEW_OFFER',
                'payload' => [
                    'id' => (int)$offerId,
                    'property_id' => $payload['property_id'],
                    'property_title' => $property['title'],
                    'buyer_id' => $from->user_id,
                    'buyer_name' => $buyer['name'],
                    'seller_id' => $property['seller_id'],
                    'seller_name' => $property['seller_name'],
                    'amount' => (float)$payload['amount'],
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            // Send to seller if connected
            $this->sendToUser($property['seller_id'], $response);
            
            // Send confirmation to buyer
            $from->send(json_encode([
                'type' => 'OFFER_SUBMITTED',
                'payload' => [
                    'id' => $offerId, 
                    'status' => 'submitted',
                    'message' => 'Offer submitted successfully'
                ]
            ]));
            
            $this->log("Offer made: ID {$offerId} by user {$from->user_id} for property {$payload['property_id']} amount {$payload['amount']}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error making offer: " . $e->getMessage(), "error");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Failed to make offer: ' . $e->getMessage()]
            ]));
        }
    }
    
    private function handleGetMessages($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'MESSAGES_LIST',
                'payload' => []
            ]));
            return;
        }
        
        try {
            if (!isset($payload['other_user_id'])) {
                throw new \Exception('Missing other_user_id');
            }
            
            $stmt = $this->pdo->prepare(
                "SELECT m.*, 
                        u1.name as sender_name, 
                        u2.name as receiver_name,
                        p.title as property_title
                 FROM messages m
                 LEFT JOIN users u1 ON m.sender_id = u1.id
                 LEFT JOIN users u2 ON m.receiver_id = u2.id
                 LEFT JOIN properties p ON m.property_id = p.id
                 WHERE (m.sender_id = :user_id AND m.receiver_id = :other_user_id)
                    OR (m.sender_id = :other_user_id AND m.receiver_id = :user_id)
                 ORDER BY m.created_at ASC
                 LIMIT 100"
            );
            
            $stmt->execute([
                ':user_id' => $from->user_id,
                ':other_user_id' => $payload['other_user_id']
            ]);
            
            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $response = [
                'type' => 'MESSAGES_LIST',
                'payload' => $messages
            ];
            
            $from->send(json_encode($response));
            $this->log("Sent " . count($messages) . " messages to user {$from->user_id}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error getting messages: " . $e->getMessage(), "error");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Failed to get messages']
            ]));
        }
    }
    
    private function handleGetOffers($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'OFFERS_LIST',
                'payload' => []
            ]));
            return;
        }
        
        try {
            if (isset($payload['seller_id'])) {
                $stmt = $this->pdo->prepare(
                    "SELECT o.*, 
                            p.title as property_title, 
                            u.name as buyer_name,
                            u.email as buyer_email
                     FROM offers o
                     LEFT JOIN properties p ON o.property_id = p.id
                     LEFT JOIN users u ON o.buyer_id = u.id
                     WHERE p.seller_id = :seller_id
                     ORDER BY o.id DESC"
                );
                $stmt->execute([':seller_id' => $payload['seller_id']]);
            } else if (isset($payload['buyer_id'])) {
                $stmt = $this->pdo->prepare(
                    "SELECT o.*, 
                            p.title as property_title,
                            u.name as seller_name
                     FROM offers o
                     LEFT JOIN properties p ON o.property_id = p.id
                     LEFT JOIN users u ON p.seller_id = u.id
                     WHERE o.buyer_id = :buyer_id
                     ORDER BY o.id DESC"
                );
                $stmt->execute([':buyer_id' => $payload['buyer_id']]);
            } else {
                // Get offers for the authenticated user based on their role
                if ($from->role === 'seller') {
                    $stmt = $this->pdo->prepare(
                        "SELECT o.*, p.title as property_title, u.name as buyer_name
                         FROM offers o
                         LEFT JOIN properties p ON o.property_id = p.id
                         LEFT JOIN users u ON o.buyer_id = u.id
                         WHERE p.seller_id = :seller_id
                         ORDER BY o.id DESC"
                    );
                    $stmt->execute([':seller_id' => $from->user_id]);
                } else {
                    $stmt = $this->pdo->prepare(
                        "SELECT o.*, p.title as property_title, u.name as seller_name
                         FROM offers o
                         LEFT JOIN properties p ON o.property_id = p.id
                         LEFT JOIN users u ON p.seller_id = u.id
                         WHERE o.buyer_id = :buyer_id
                         ORDER BY o.id DESC"
                    );
                    $stmt->execute([':buyer_id' => $from->user_id]);
                }
            }
            
            $offers = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $response = [
                'type' => 'OFFERS_LIST',
                'payload' => $offers
            ];
            
            $from->send(json_encode($response));
            $this->log("Sent " . count($offers) . " offers to user {$from->user_id}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error getting offers: " . $e->getMessage(), "error");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Failed to get offers']
            ]));
        }
    }
    
    private function handleUpdateOffer($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Database connection unavailable']
            ]));
            return;
        }
        
        try {
            if (!isset($payload['offer_id']) || !isset($payload['status'])) {
                throw new \Exception('Missing required fields');
            }
            
            // Verify user has permission to update this offer
            $checkStmt = $this->pdo->prepare(
                "SELECT o.*, p.seller_id, p.title as property_title
                 FROM offers o
                 JOIN properties p ON o.property_id = p.id
                 WHERE o.id = :offer_id"
            );
            $checkStmt->execute([':offer_id' => $payload['offer_id']]);
            $offer = $checkStmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$offer) {
                throw new \Exception('Offer not found');
            }
            
            // Only seller can update offer status
            if ($offer['seller_id'] != $from->user_id) {
                $from->send(json_encode([
                    'type' => 'ERROR',
                    'payload' => ['message' => 'Unauthorized to update this offer']
                ]));
                return;
            }
            
            // Update offer status
            $stmt = $this->pdo->prepare(
                "UPDATE offers SET status = :status WHERE id = :offer_id"
            );
            
            $stmt->execute([
                ':offer_id' => $payload['offer_id'],
                ':status' => $payload['status']
            ]);
            
            $response = [
                'type' => 'OFFER_UPDATED',
                'payload' => [
                    'offer_id' => $payload['offer_id'],
                    'status' => $payload['status'],
                    'property_title' => $offer['property_title']
                ]
            ];
            
            // Notify the buyer
            $this->sendToUser($offer['buyer_id'], $response);
            
            $this->log("Offer updated: {$payload['offer_id']} -> {$payload['status']} by user {$from->user_id}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error updating offer: " . $e->getMessage(), "error");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Failed to update offer']
            ]));
        }
    }
    
    private function handleGetProperties($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'PROPERTIES_LIST',
                'payload' => []
            ]));
            return;
        }
        
        try {
            $sql = "SELECT p.*, u.name as seller_name, u.email as seller_email, u.contact as seller_contact
                    FROM properties p
                    LEFT JOIN users u ON p.seller_id = u.id
                    WHERE (p.is_archived = 0 OR p.is_archived IS NULL)";
            
            $params = [];
            
            if (isset($payload['seller_id'])) {
                $sql .= " AND p.seller_id = :seller_id";
                $params[':seller_id'] = $payload['seller_id'];
            }
            
            if (isset($payload['search']) && !empty($payload['search'])) {
                $sql .= " AND (p.title LIKE :search OR p.description LIKE :search)";
                $params[':search'] = '%' . $payload['search'] . '%';
            }
            
            if (isset($payload['location']) && !empty($payload['location'])) {
                $sql .= " AND p.location LIKE :location";
                $params[':location'] = '%' . $payload['location'] . '%';
            }
            
            if (isset($payload['price_range']) && !empty($payload['price_range'])) {
                switch ($payload['price_range']) {
                    case '1':
                        $sql .= " AND p.price < 1000000";
                        break;
                    case '2':
                        $sql .= " AND p.price BETWEEN 1000000 AND 10000000";
                        break;
                    case '3':
                        $sql .= " AND p.price BETWEEN 10000000 AND 20000000";
                        break;
                    case '4':
                        $sql .= " AND p.price BETWEEN 20000000 AND 30000000";
                        break;
                    case '5':
                        $sql .= " AND p.price BETWEEN 30000000 AND 40000000";
                        break;
                    case '6':
                        $sql .= " AND p.price BETWEEN 40000000 AND 50000000";
                        break;
                    case '7':
                        $sql .= " AND p.price > 50000000";
                        break;
                }
            }
            
            $sql .= " ORDER BY p.id DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $properties = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Add offer info for the current user if they are a buyer
            if ($from->role === 'buyer') {
                $offerStmt = $this->pdo->prepare(
                    "SELECT * FROM offers WHERE buyer_id = :buyer_id AND property_id = :property_id"
                );
                
                foreach ($properties as &$property) {
                    $offerStmt->execute([
                        ':buyer_id' => $from->user_id,
                        ':property_id' => $property['id']
                    ]);
                    $property['offer'] = $offerStmt->fetch(\PDO::FETCH_ASSOC);
                    $property['has_chat'] = false;
                }
            }
            
            $response = [
                'type' => 'PROPERTIES_LIST',
                'payload' => $properties
            ];
            
            $from->send(json_encode($response));
            $this->log("Sent " . count($properties) . " properties to user {$from->user_id}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error getting properties: " . $e->getMessage(), "error");
            $from->send(json_encode([
                'type' => 'ERROR',
                'payload' => ['message' => 'Failed to get properties: ' . $e->getMessage()]
            ]));
        }
    }
    
    private function handleGetProperty($from, $payload) {
        if (!$this->pdo) {
            $from->send(json_encode([
                'type' => 'PROPERTY_DETAILS',
                'payload' => null
            ]));
            return;
        }
        
        try {
            if (!isset($payload['property_id'])) {
                throw new \Exception('Missing property_id');
            }
            
            $stmt = $this->pdo->prepare(
                "SELECT p.*, u.name as seller_name, u.email as seller_email, u.contact as seller_contact
                 FROM properties p
                 LEFT JOIN users u ON p.seller_id = u.id
                 WHERE p.id = :property_id"
            );
            
            $stmt->execute([':property_id' => $payload['property_id']]);
            $property = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $response = [
                'type' => 'PROPERTY_DETAILS',
                'payload' => $property
            ];
            
            $from->send(json_encode($response));
            $this->log("Sent property details: ID {$payload['property_id']} to user {$from->user_id}", "success");
            
        } catch (\Exception $e) {
            $this->errorCount++;
            $this->log("Error getting property: " . $e->getMessage(), "error");
        }
    }
    
    private function broadcastStatus() {
        $stats = $this->getServerStats();
        $status = [
            'type' => 'SERVER_STATUS_UPDATE',
            'payload' => [
                'active_connections' => $this->connectionCount,
                'total_messages' => $this->messageCount,
                'uptime_seconds' => $stats['uptime'],
                'authenticated_users' => count($this->userConnections)
            ]
        ];
        
        foreach ($this->clients as $client) {
            $client->send(json_encode($status));
        }
    }
    
    public function onClose(ConnectionInterface $conn) {
        // Remove from user mapping
        if (isset($conn->user_id) && isset($this->userConnections[$conn->user_id])) {
            $index = array_search($conn, $this->userConnections[$conn->user_id]);
            if ($index !== false) {
                unset($this->userConnections[$conn->user_id][$index]);
            }
            if (empty($this->userConnections[$conn->user_id])) {
                unset($this->userConnections[$conn->user_id]);
            }
        }
        
        $this->clients->detach($conn);
        $this->connectionCount--;
        
        $userInfo = isset($conn->user_id) ? "User {$conn->user_id}" : "Guest";
        $this->log("{$userInfo} disconnected (Remaining: {$this->connectionCount})", "warning");
        
        $this->broadcastStatus();
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e) {
        $this->errorCount++;
        $this->log("Error on connection {$conn->resourceId}: {$e->getMessage()}", "error");
        $conn->close();
    }
}

// Start the server
$websocketPort = 8080;

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     HOUSING WEBSOCKET SERVER v1.0                          ║\n";
echo "║     Starting up...                                         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

try {
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new HousingWebSocketServer()
            )
        ),
        $websocketPort
    );
    
    echo "✓ WebSocket server listening on port {$websocketPort}\n";
    echo "✓ WebSocket endpoint: ws://localhost:{$websocketPort}\n";
    echo "✓ Database: housing\n";
    echo "\n";
    
    echo "📊 Server Information:\n";
    echo "   - PHP Version: " . phpversion() . "\n";
    echo "   - Memory Limit: " . ini_get('memory_limit') . "\n";
    echo "   - Max Execution Time: " . ini_get('max_execution_time') . " seconds\n";
    echo "\n";
    
    echo "💡 Commands:\n";
    echo "   - Press Ctrl+C to stop the server\n";
    echo "   - Test connection: Use a WebSocket client to connect\n";
    echo "   - Monitor logs: Check logs/websocket_*.log\n";
    echo "\n";
    
    echo "════════════════════════════════════════════════════════════\n";
    echo "🚀 Server is running and waiting for connections...\n";
    echo "════════════════════════════════════════════════════════════\n\n";
    
    $server->run();
    
} catch (\Exception $e) {
    echo "❌ Failed to start server: " . $e->getMessage() . "\n";
    echo "💡 The port might be in use. Try changing the port to 8081\n";
}