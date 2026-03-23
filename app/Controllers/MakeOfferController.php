<?php

namespace App\Controllers;

use App\Models\OfferModel;

class MakeOfferController extends BaseController
{
    public function create()
    {
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
        $offerModel->insert([
            'property_id' => $propertyId,
            'buyer_id' => $buyerId,
            'amount' => $amount,
            'status' => 'pending'
        ]);

        $session->setFlashdata('success', 'Your offer has been sent. Please wait for seller response.');
        return redirect()->back();
    }
}
