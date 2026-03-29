<?php

namespace App\Controllers;

use App\Models\MessageModel;

class MessageController extends BaseController
{
    protected $messageModel;

    public function __construct()
    {
        $this->messageModel = new MessageModel();
    }

    /**
     * Display chat view (GET only)
     */
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

        // Fetch all messages for this conversation
        $messages = $this->messageModel
            ->where('property_id', $propertyId)
            ->groupStart()
                ->where('sender_id', $senderId)
                ->orWhere('receiver_id', $senderId)
            ->groupEnd()
            ->orderBy('created_at', 'ASC')
            ->findAll();

        return view('buyer/chat', [
            'user'       => $user,
            'messages'   => $messages,
            'receiverId' => $receiverId,
            'propertyId' => $propertyId,
            'userRole'   => $user['role'] ?? null,
        ]);
    }

    /**
     * AJAX endpoint for sending and fetching messages
     */
    public function ajax($receiverId, $propertyId)
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $session = session();
        $user = $session->get('user');
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'error' => 'Not logged in']);
        }

        $currentUserId = $user['id'];

        // Verify that the user is part of this conversation (optional but recommended)
        $isParticipant = $this->messageModel
            ->where('property_id', $propertyId)
            ->groupStart()
                ->where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId)
            ->groupEnd()
            ->countAllResults() > 0;

        if (!$isParticipant) {
            // If no messages exist yet, we still allow (first message)
            // But you could also check if the user is the seller or buyer of the property.
            // For simplicity, we allow if the user is either the sender or receiver.
        }

        // Handle POST (send message)
        if ($this->request->getMethod() === 'post') {
            $message = $this->request->getPost('message');
            $csrf = $this->request->getPost('csrf_test_name');

            // Validate CSRF
            if (!$this->validate(['csrf_test_name' => 'required'])) {
                return $this->response->setJSON(['success' => false, 'error' => 'Invalid CSRF token']);
            }

            if (empty($message)) {
                return $this->response->setJSON(['success' => false, 'error' => 'Message cannot be empty']);
            }

            // Save message
            $data = [
                'sender_id'   => $currentUserId,
                'receiver_id' => $receiverId,
                'property_id' => $propertyId,
                'message'     => trim($message),
                'created_at'  => date('Y-m-d H:i:s')
            ];

            $inserted = $this->messageModel->insert($data);
            if ($inserted) {
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false, 'error' => 'Failed to save message']);
            }
        }

        // Handle GET (fetch new messages)
        $since = (int) $this->request->getGet('since');
        
        $messages = $this->messageModel
            ->where('property_id', $propertyId)
            ->groupStart()
                ->where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId)
            ->groupEnd()
            ->where('id >', $since)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        // Convert timestamps to milliseconds for JavaScript
        foreach ($messages as &$msg) {
            $msg['created_at'] = strtotime($msg['created_at']) * 1000;
        }

        return $this->response->setJSON([
            'success'  => true,
            'messages' => $messages
        ]);
    }
}