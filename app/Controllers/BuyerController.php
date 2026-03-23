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

        // 2️⃣ Get search & filter inputs and sanitize
        $search = trim($this->request->getGet('search', FILTER_SANITIZE_STRING));
        $location = trim($this->request->getGet('location', FILTER_SANITIZE_STRING));
        $price_range = trim($this->request->getGet('price_range', FILTER_SANITIZE_STRING));

        // 3️⃣ Fetch properties
        $propertyModel = new PropertyModel();
        $properties = $propertyModel->getFilteredProperties($search, $location, $price_range);

        // 4️⃣ Prepare offers for each property
        $offerModel = new OfferModel();
        $existingOffers = [];
        foreach ($properties as $property) {
            $existingOffers[$property['id']] = $offerModel
                ->where('property_id', $property['id'])
                ->where('buyer_id', $buyerId)
                ->first();
        }

        // 5️⃣ Prepare chat info for each property
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

        // 6️⃣ Pass all data to view
        return view('buyer/dashboard', [
            'user' => $user,
            'properties' => $properties,
            'existingOffers' => $existingOffers,
            'chatsExist' => $chatsExist,
            'search' => $search,
            'location' => $location,
            'price_range' => $price_range
        ]);
    }

    /**
     * Helper to check user role or redirect
     * @param string $role expected user role
     * @return array|null user data if role matches, or null
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
