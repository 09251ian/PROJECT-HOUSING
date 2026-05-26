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
        $user = session()->get('user');
        
        if (!$user || $user['role'] !== 'buyer') {
            return redirect()->to('/login');
        }
        
        $propertyModel = new PropertyModel();
        
        // Get current page from URL, default to 1
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        
        // Get search/filter parameters
        $search = $this->request->getGet('search');
        $location = $this->request->getGet('location');
        $priceRange = $this->request->getGet('price_range');
        
        // Build query
        $query = $propertyModel
            ->select('properties.*, users.name as seller_name')
            ->join('users', 'users.id = properties.seller_id')
            ->where('properties.is_archived', 0);
        
        // Apply search filters
        if (!empty($search)) {
            $query->groupStart()
                ->like('properties.title', $search)
                ->orLike('properties.description', $search)
                ->groupEnd();
        }
        
        if (!empty($location)) {
            $query->like('properties.location', $location);
        }
        
        if (!empty($priceRange)) {
            switch ($priceRange) {
                case '1': $query->where('properties.price <', 1000000); break;
                case '2': $query->where('properties.price >=', 1000000)->where('properties.price <=', 10000000); break;
                case '3': $query->where('properties.price >=', 10000000)->where('properties.price <=', 20000000); break;
                case '4': $query->where('properties.price >=', 20000000)->where('properties.price <=', 30000000); break;
                case '5': $query->where('properties.price >=', 30000000)->where('properties.price <=', 40000000); break;
                case '6': $query->where('properties.price >=', 40000000)->where('properties.price <=', 50000000); break;
                case '7': $query->where('properties.price >', 50000000); break;
            }
        }
        
        // Get total count for pagination
        $total = $query->countAllResults(false);
        
        // Get paginated results
        $properties = $query->orderBy('properties.id', 'DESC')
                            ->limit($perPage, ($currentPage - 1) * $perPage)
                            ->get()
                            ->getResultArray();
        
        // Calculate pagination data
        $lastPage = ceil($total / $perPage);
        $pager = (object) [
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'total' => $total,
            'perPage' => $perPage,
            'firstItem' => (($currentPage - 1) * $perPage) + 1,
            'lastItem' => min($currentPage * $perPage, $total)
        ];
        
        // Get existing offers for properties
        $offerModel = new OfferModel();
        $existingOffers = [];
        foreach ($properties as $property) {
            $offer = $offerModel->where('property_id', $property['id'])
                                ->where('buyer_id', $user['id'])
                                ->first();
            if ($offer) {
                $existingOffers[$property['id']] = $offer;
            }
        }
        
        // Get chats exist for properties
        $messageModel = new MessageModel();
        $chatsExist = [];
        foreach ($properties as $property) {
            $sentCount = $messageModel->where('property_id', $property['id'])->where('sender_id', $user['id'])->countAllResults();
            $receivedCount = $messageModel->where('property_id', $property['id'])->where('receiver_id', $user['id'])->countAllResults();
            $chatsExist[$property['id']] = ($sentCount > 0 || $receivedCount > 0);
        }
        
        // Get favorites
        $favoriteModel = new FavoriteModel();
        $favorites = [];
        $userFavorites = $favoriteModel->where('buyer_id', $user['id'])->findAll();
        foreach ($userFavorites as $fav) {
            $favorites[$fav['property_id']] = true;
        }
        
        return view('buyer/dashboard', [
            'properties' => $properties,
            'pager' => $pager,
            'search' => $search,
            'location' => $location,
            'price_range' => $priceRange,
            'existingOffers' => $existingOffers,
            'chatsExist' => $chatsExist,
            'favorites' => $favorites,
            'user' => $user
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