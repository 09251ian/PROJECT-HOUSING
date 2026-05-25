<?php

namespace App\Controllers;

use App\Models\MessageModel;

class MessageController extends BaseController
{
    public function chat($receiverId = null, $propertyId = null)
    {
        $session = session();
        $user = $session->get('user');

        if (!$user) {
            return redirect()->to('/login');
        }

        $senderId = $user['id'];

        if (!$receiverId || !$propertyId) {
            $session->setFlashdata('error', 'Invalid chat request.');
            return redirect()->back();
        }

        $messageModel = new MessageModel();

        // Handle POST (sending message)
        if ($this->request->getMethod() === 'post') {
            $content = $this->request->getPost('message');
            if ($content) {
                $messageModel->insert([
                    'sender_id' => $senderId,
                    'receiver_id' => $receiverId,
                    'property_id' => $propertyId,
                    'message' => $content,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                return redirect()->to("/message/$receiverId/$propertyId");
            }
        }

        // Fetch messages for this chat
        $messages = $messageModel
            ->where('property_id', $propertyId)
            ->groupStart()
                ->where('sender_id', $senderId)
                ->orWhere('sender_id', $receiverId)
            ->groupEnd()
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('buyer/chat', [
            'user' => $user,
            'messages' => $messages,
            'receiverId' => $receiverId,
            'propertyId' => $propertyId,
            'userRole' => $user['role'] ?? null,
        ]);
    }

    /**
     * API endpoint for WebSocket to save messages
     * Called by socket-server when a message is sent via WebSocket
     */
    public function saveMessage()
{
    // Log that the endpoint was hit
    log_message('info', 'saveMessage endpoint called');
    
    // Get JSON input
    $json = $this->request->getJSON();
    
    // Log the received data
    log_message('info', 'Received data: ' . json_encode($json));
    
    // Validate
    if (!$json || !$json->sender_id || !$json->receiver_id || !$json->message) {
        $error = 'Missing required fields';
        log_message('error', $error);
        return $this->response
            ->setStatusCode(400)
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setJSON(['error' => $error]);
    }
    
    // Save to database
    $messageModel = new \App\Models\MessageModel();
    $data = [
        'sender_id' => $json->sender_id,
        'receiver_id' => $json->receiver_id,
        'message' => $json->message,
        'property_id' => $json->property_id ?? null,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $messageId = $messageModel->insert($data);
    
    if ($messageId) {
        return $this->response
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setJSON([
                'success' => true,
                'message_id' => $messageId
            ]);
    } else {
        return $this->response
            ->setStatusCode(500)
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setJSON(['error' => 'Failed to save message']);
    }
}
}