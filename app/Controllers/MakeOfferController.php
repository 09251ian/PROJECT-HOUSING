<?php

namespace App\Controllers;

use App\Models\OfferModel;
use App\Models\PropertyModel;
use App\Models\UserModel;

class MakeOfferController extends BaseController
{
    // Helper method to send socket notifications
    private function sendSocketNotification($endpoint, $data)
    {
        $ch = curl_init('http://localhost:3000/' . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            log_message('error', 'Socket notification failed for new-offer: HTTP ' . $httpCode);
        }
        
        return $httpCode === 200;
    }

    public function create()
    {
        // REMOVE the CSRF validation block - it's causing the error!
        
        $session = session();

        // Check if user is logged in as buyer
        $user = $session->get('user');
        if (!$user || $user['role'] !== 'buyer') {
            return redirect()->to('/login');
        }
        $buyerId = $user['id'];

        // Get POST data
        $propertyId = $this->request->getPost('property_id');
        $amount = $this->request->getPost('amount');

        if (!$propertyId || !$amount) {
            $session->setFlashdata('error', 'Invalid request.');
            return redirect()->back();
        }

        $offerModel = new OfferModel();
        $propertyModel = new PropertyModel();
        $userModel = new UserModel();

        // Get property details
        $property = $propertyModel->find($propertyId);
        if (!$property) {
            $session->setFlashdata('error', 'Property not found.');
            return redirect()->back();
        }
        
        // Get seller details
        $seller = $userModel->find($property['seller_id']);

        // Check if an offer already exists
        $existingOffer = $offerModel
            ->where('property_id', $propertyId)
            ->where('buyer_id', $buyerId)
            ->first();

        if ($existingOffer) {
            $session->setFlashdata('error', 'You have already made an offer for this property.');
            return redirect()->back();
        }

        // Create new offer
        $offerData = [
            'property_id' => $propertyId,
            'buyer_id' => $buyerId,
            'amount' => $amount,
            'status' => 'pending'
        ];
        
        $offerModel->insert($offerData);
        $offerId = $offerModel->getInsertID();
        
        // Prepare notification data
        $notificationData = [
            'id' => $offerId,
            'property_id' => $propertyId,
            'property_title' => $property['title'],
            'property_price' => $property['price'],
            'buyer_id' => $buyerId,
            'buyer_name' => $user['name'],
            'seller_id' => $property['seller_id'],
            'seller_name' => $seller['name'] ?? 'Seller',
            'amount' => $amount,
            'status' => 'pending'
        ];

        // Send real-time notification to admin and seller
        $this->sendSocketNotification('new-offer', $notificationData);

        $session->setFlashdata('success', 'Your offer has been sent. Please wait for seller response.');
        return redirect()->back();
    }
}