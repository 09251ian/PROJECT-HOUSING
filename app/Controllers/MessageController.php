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
}
