<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\OfferModel;
use App\Models\MessageModel;

class BuyerController extends BaseController
{
    public function dashboard()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) return redirect()->to('/login');

        $buyerId = $user['id'];

        // Get search & filter inputs
        $search = trim($this->request->getGet('search') ?? '');
        $location = trim($this->request->getGet('location') ?? '');
        $price_range = trim($this->request->getGet('price_range') ?? '');

        // Fetch properties
        $propertyModel = new PropertyModel();
        $properties = $propertyModel->getFilteredProperties($search, $location, $price_range);

        // Prepare offers for each property
        $offerModel = new OfferModel();
        $existingOffers = [];
        foreach ($properties as $property) {
            $existingOffers[$property['id']] = $offerModel
                ->where('property_id', $property['id'])
                ->where('buyer_id', $buyerId)
                ->first();
        }

        // Prepare chat info for each property
        $messageModel = new MessageModel();
        $chatsExist = [];
        foreach ($properties as $property) {
            $chatsExist[$property['id']] = $messageModel
                ->where('property_id', $property['id'])
                ->groupStart()
                    ->where('sender_id', $buyerId)
                    ->orWhere('receiver_id', $buyerId)
                ->groupEnd()
                ->countAllResults() > 0;
        }

        // Check WebSocket server status
        $websocketStatus = $this->checkWebSocketServer();

        // Pass all data to view including WebSocket config
        return view('buyer/dashboard', [
            'user' => $user,
            'properties' => $properties,
            'existingOffers' => $existingOffers,
            'chatsExist' => $chatsExist,
            'search' => $search,
            'location' => $location,
            'price_range' => $price_range,
            'websocket_port' => '8080',
            'websocket_host' => 'localhost',
            'websocket_status' => $websocketStatus
        ]);
    }

    /**
     * WebSocket Test Page
     */
    public function websocketTest()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) return redirect()->to('/login');
        
        $data = [
            'title' => 'WebSocket Connection Test',
            'websocket_port' => 8080,
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role']
        ];
        
        return view('buyer/websocket_test', $data);
    }

    /**
     * API endpoint for AJAX requests to get properties with offers and chat info
     */
    public function apiGetProperties()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) {
            return $this->response->setJSON([
                'success' => false, 
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }

        try {
            $search = trim($this->request->getGet('search') ?? '');
            $location = trim($this->request->getGet('location') ?? '');
            $price_range = trim($this->request->getGet('price_range') ?? '');

            $propertyModel = new PropertyModel();
            $properties = $propertyModel->getFilteredProperties($search, $location, $price_range);
            
            // Add offer and chat information to each property
            $offerModel = new OfferModel();
            $messageModel = new MessageModel();
            
            $enhancedProperties = [];
            foreach ($properties as $property) {
                // Get existing offer for this buyer on this property
                $offer = $offerModel
                    ->where('property_id', $property['id'])
                    ->where('buyer_id', $user['id'])
                    ->first();
                
                // Check if chat exists for this property
                $hasChat = $messageModel
                    ->where('property_id', $property['id'])
                    ->groupStart()
                        ->where('sender_id', $user['id'])
                        ->orWhere('receiver_id', $user['id'])
                    ->groupEnd()
                    ->countAllResults() > 0;
                
                // Add offer and chat info to property
                $property['offer'] = $offer;
                $property['has_chat'] = $hasChat;
                
                $enhancedProperties[] = $property;
            }

            return $this->response->setJSON([
                'success' => true,
                'properties' => $enhancedProperties,
                'count' => count($enhancedProperties),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API Get Properties Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to fetch properties: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * API endpoint for making offers via AJAX with WebSocket notification
     */
    public function apiMakeOffer()
{
    $user = $this->checkRoleOrRedirect('buyer');
    if (!$user) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Unauthorized'
        ])->setStatusCode(401);
    }

    // Get POST data
    $propertyId = $this->request->getPost('property_id');
    $amount = $this->request->getPost('amount');
    
    // Validate input
    if (!$propertyId || !$amount) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Missing required fields: property_id and amount are required'
        ])->setStatusCode(400);
    }
    
    // Validate amount
    if (!is_numeric($amount) || $amount <= 0) {
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Invalid amount. Please enter a positive number.'
        ]);
    }
    
    try {
        $offerModel = new OfferModel();
        $propertyModel = new PropertyModel();
        
        // Check if property exists with seller info
        $property = $propertyModel
            ->select('properties.*, users.id as seller_id, users.name as seller_name')
            ->join('users', 'users.id = properties.seller_id')
            ->where('properties.id', $propertyId)
            ->first();
            
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Property not found'
            ])->setStatusCode(404);
        }
        
        // Check if pending offer already exists
        $existingOffer = $offerModel
            ->where('property_id', $propertyId)
            ->where('buyer_id', $user['id'])
            ->where('status', 'pending')
            ->first();

        if ($existingOffer) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'You already have a pending offer for this property'
            ]);
        }
        
        // Check if offer already accepted or rejected
        $existingFinalOffer = $offerModel
            ->where('property_id', $propertyId)
            ->where('buyer_id', $user['id'])
            ->whereIn('status', ['accepted', 'rejected'])
            ->first();
            
        if ($existingFinalOffer) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'You have already made an offer that was ' . $existingFinalOffer['status'] . ' for this property'
            ]);
        }

        // Create the offer
        $offerData = [
            'property_id' => $propertyId,
            'buyer_id' => $user['id'],
            'amount' => $amount,
            'status' => 'pending',
        ];
        
        $offerId = $offerModel->insert($offerData);
        
        if (!$offerId) {
            throw new \Exception('Failed to insert offer');
        }
        
        // Prepare response with offer details (no created_at)
        $responseData = [
            'success' => true,
            'offer_id' => $offerId,
            'message' => 'Offer submitted successfully',
            'offer' => [
                'id' => $offerId,
                'property_id' => $propertyId,
                'property_title' => $property['title'],
                'amount' => $amount,
                'status' => 'pending',
                'buyer_id' => $user['id'],
                'buyer_name' => $user['name'],
                'seller_id' => $property['seller_id'],
                'seller_name' => $property['seller_name']
            ]
        ];
        
        // Send WebSocket notification to seller (no created_at)
        $this->sendWebSocketNotification($property['seller_id'], [
            'type' => 'NEW_OFFER',
            'payload' => [
                'id' => (int)$offerId,
                'property_id' => $propertyId,
                'property_title' => $property['title'],
                'buyer_id' => $user['id'],
                'buyer_name' => $user['name'],
                'seller_id' => $property['seller_id'],
                'seller_name' => $property['seller_name'],
                'amount' => $amount,
                'status' => 'pending'
            ]
        ]);
        
        return $this->response->setJSON($responseData);
        
    } catch (\Exception $e) {
        log_message('error', 'API Make Offer Error: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'error' => 'Failed to submit offer: ' . $e->getMessage()
        ])->setStatusCode(500);
    }
}
    
    /**
     * API endpoint to get single property details
     */
    public function apiGetProperty($propertyId)
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }
        
        try {
            $propertyModel = new PropertyModel();
            $property = $propertyModel
                ->select('properties.*, users.name as seller_name, users.id as seller_id')
                ->join('users', 'users.id = properties.seller_id')
                ->where('properties.id', $propertyId)
                ->first();
            
            if (!$property) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Property not found'
                ])->setStatusCode(404);
            }
            
            // Get offer for this property if exists
            $offerModel = new OfferModel();
            $offer = $offerModel
                ->where('property_id', $propertyId)
                ->where('buyer_id', $user['id'])
                ->first();
            
            $property['offer'] = $offer;
            
            return $this->response->setJSON([
                'success' => true,
                'property' => $property
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API Get Property Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to fetch property details'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * API endpoint to check WebSocket connection status
     */
    public function apiWebSocketStatus()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }
        
        // Check if WebSocket server is running
        $port = $this->request->getGet('port') ?? 8080;
        $connection = @fsockopen('localhost', $port, $errno, $errstr, 2);
        
        if ($connection) {
            fclose($connection);
            return $this->response->setJSON([
                'status' => 'online',
                'port' => $port,
                'message' => 'WebSocket server is running on port ' . $port,
                'success' => true
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'offline',
                'port' => $port,
                'message' => 'WebSocket server is not running on port ' . $port,
                'error' => $errstr,
                'success' => false
            ]);
        }
    }
    
    /**
     * API endpoint to get user's offers
     */
    public function apiGetMyOffers()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }
        
        try {
            $offerModel = new OfferModel();
            $offers = $offerModel
                ->select('offers.*, properties.title as property_title, properties.location, users.name as seller_name')
                ->join('properties', 'properties.id = offers.property_id')
                ->join('users', 'users.id = properties.seller_id')
                ->where('offers.buyer_id', $user['id'])
                ->orderBy('offers.created_at', 'DESC')
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'offers' => $offers
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API Get My Offers Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to fetch offers'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * API endpoint to cancel a pending offer
     */
    public function apiCancelOffer()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }
        
        $offerId = $this->request->getPost('offer_id');
        
        if (!$offerId) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Offer ID is required'
            ])->setStatusCode(400);
        }
        
        try {
            $offerModel = new OfferModel();
            $offer = $offerModel->find($offerId);
            
            if (!$offer) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Offer not found'
                ])->setStatusCode(404);
            }
            
            // Check if the offer belongs to this buyer
            if ($offer['buyer_id'] != $user['id']) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Unauthorized to cancel this offer'
                ])->setStatusCode(403);
            }
            
            // Check if offer is still pending
            if ($offer['status'] != 'pending') {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Cannot cancel an offer that is already ' . $offer['status']
                ]);
            }
            
            // Cancel the offer (update status to cancelled)
            $offerModel->update($offerId, ['status' => 'cancelled']);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Offer cancelled successfully'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'API Cancel Offer Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to cancel offer'
            ])->setStatusCode(500);
        }
    }
    
    /**
     * API endpoint to get WebSocket configuration
     */
    public function apiWebSocketConfig()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Unauthorized'
            ])->setStatusCode(401);
        }
        
        // Check WebSocket server status
        $connection = @fsockopen('localhost', 8080, $errno, $errstr, 1);
        $serverRunning = ($connection !== false);
        if ($serverRunning) fclose($connection);
        
        return $this->response->setJSON([
            'success' => true,
            'host' => 'localhost',
            'port' => 8080,
            'endpoint' => 'ws://localhost:8080',
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'role' => $user['role'],
            'server_running' => $serverRunning
        ]);
    }

    /**
     * Send WebSocket notification to a specific user
     * 
     * @param int $userId The user ID to send notification to
     * @param array $message The message data to send
     * @return bool Success status
     */
    protected function sendWebSocketNotification($userId, $message)
    {
        try {
            // Check if WebSocket server is running
            $connection = @fsockopen('localhost', 8080, $errno, $errstr, 1);
            if (!$connection) {
                log_message('debug', 'WebSocket server not running, skipping notification');
                return false;
            }
            fclose($connection);
            
            // Create a WebSocket client connection
            $wsClient = new \WebSocket\Client("ws://localhost:8080");
            
            // Authenticate as system
            $wsClient->send(json_encode([
                'type' => 'AUTH',
                'payload' => [
                    'user_id' => 0, // System user
                    'role' => 'system'
                ]
            ]));
            
            // Wait a bit for auth
            usleep(100000);
            
            // Send the notification with target user
            $wsClient->send(json_encode([
                'type' => 'NOTIFICATION',
                'target_user_id' => $userId,
                'data' => $message
            ]));
            
            $wsClient->close();
            return true;
            
        } catch (\Exception $e) {
            log_message('error', 'WebSocket notification error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Helper to check if WebSocket server is running
     */
    protected function checkWebSocketServer()
    {
        $connection = @fsockopen('localhost', 8080, $errno, $errstr, 1);
        if ($connection) {
            fclose($connection);
            return ['status' => 'online', 'message' => 'WebSocket server is running'];
        }
        return ['status' => 'offline', 'message' => 'WebSocket server is not running'];
    }

    /**
     * Helper to check user role or redirect
     */
    protected function checkRoleOrRedirect(string $role)
    {
        $session = session();
        $user = $session->get('user');
        if (!$user || $user['role'] !== $role) {
            return null;
        }
        return $user;
    }
}