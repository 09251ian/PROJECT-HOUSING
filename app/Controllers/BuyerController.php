<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\OfferModel;
use App\Models\MessageModel;
use App\Models\FavoriteModel;

class BuyerController extends BaseController
{
    public function dashboard()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) return redirect()->to('/login');

        $buyerId = (int) ($user['id'] ?? 0);
        if ($buyerId <= 0) {
            return redirect()->to('/login');
        }

        // Get search & filter inputs and sanitize
        $search = trim($this->request->getGet('search') ?? '');
        $location = trim($this->request->getGet('location') ?? '');
        $price_range = trim($this->request->getGet('price_range') ?? '');

        // Fetch properties
        $propertyModel = new PropertyModel();
        $properties = $propertyModel->getFilteredProperties($search, $location, $price_range);

        // Prepare favorites for each property
        $favoriteModel = new FavoriteModel();
        $favorites = [];
        foreach ($properties as $property) {
            $propertyId = (int) ($property['id'] ?? 0);
            if ($propertyId <= 0) continue;
            $favorites[$propertyId] = $favoriteModel->isFavorited($buyerId, $propertyId);
        }

        // Favorites preview for the embedded widget
        $favoritesPreview = $favoriteModel->getFavoritesForBuyer($buyerId);
        if (is_array($favoritesPreview) && count($favoritesPreview) > 4) {
            $favoritesPreview = array_slice($favoritesPreview, 0, 4);
        }

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

        // Pass all data to view
        return view('buyer/dashboard', [
            'user' => $user,
            'properties' => $properties,
            'favorites' => $favorites,
            'favoritesPreview' => $favoritesPreview ?? [],
            'existingOffers' => $existingOffers,
            'chatsExist' => $chatsExist,
            'search' => $search,
            'location' => $location,
            'price_range' => $price_range
        ]);
    }

    public function favorites()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) return redirect()->to('/login');

        $favoriteModel = new FavoriteModel();
        $favorites = $favoriteModel->getFavoritesForBuyer($user['id']);

        return view('buyer/favorites', [
            'user' => $user,
            'favorites' => $favorites
        ]);
    }

    public function toggleFavorite()
    {
        $user = $this->checkRoleOrRedirect('buyer');
        if (!$user) {
            return redirect()->to('/login');
        }

        $propertyId = $this->request->getPost('property_id');
        if (!$propertyId) {
            return redirect()->back()->with('error', 'Invalid property');
        }

        $favoriteModel = new FavoriteModel();
        $isFavorited = $favoriteModel->isFavorited($user['id'], $propertyId);

        if ($isFavorited) {
            $favoriteModel->where('buyer_id', $user['id'])
                         ->where('property_id', $propertyId)
                         ->delete();
            $message = 'Property removed from favorites';
        } else {
            $favoriteModel->insert([
                'buyer_id' => $user['id'],
                'property_id' => $propertyId
            ]);
            $message = 'Property added to favorites';
        }

        return redirect()->back()->with('success', $message);
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