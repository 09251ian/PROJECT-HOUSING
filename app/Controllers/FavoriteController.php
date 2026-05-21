<?php

namespace App\Controllers;

use App\Models\FavoriteModel;
use App\Models\PropertyModel;

class FavoriteController extends BaseController
{
    protected function checkRoleOrRedirect(string $role)
    {
        $session = session();

        $user = $session->get('user');

        if (!$user || $user['role'] !== $role) {

            redirect()->to('/login')->send();

            exit;
        }

        return $user;
    }

    public function favorites()
    {
        $user = $this->checkRoleOrRedirect('buyer');

        $buyerId = (int) $user['id'];

        $favoriteModel = new FavoriteModel();

        $favorites = $favoriteModel->getFavoritesForBuyer($buyerId);

        return view('buyer/favorites', [
            'user' => $user,
            'favorites' => $favorites,
        ]);
    }

    public function toggleFavorite()
    {
        $user = $this->checkRoleOrRedirect('buyer');

        $buyerId = (int) $user['id'];

        $propertyId = (int) $this->request->getPost('property_id');

        if ($propertyId <= 0) {

            session()->setFlashdata('error', 'Invalid property.');

            return redirect()->to('/buyer/dashboard');
        }

        $propertyModel = new PropertyModel();

        $property = $propertyModel
            ->where('id', $propertyId)
            ->where('is_archived', 0)
            ->first();

        if (!$property) {

            session()->setFlashdata('error', 'Property not found.');

            return redirect()->to('/buyer/dashboard');
        }

        $favoriteModel = new FavoriteModel();

        $isDeletedOrInserted = $favoriteModel->toggleFavorite(
            $buyerId,
            $propertyId
        );

        // REALTIME WEBSOCKET EVENT
        $client = \Config\Services::curlrequest();

        try {

            $client->post('http://localhost:3000/new-property', [

                'json' => [

                    'type' => 'favorite-update',

                    'property_id' => $propertyId,

                    'buyer_id' => $buyerId,

                    'property_title' => $property['title']

                ]

            ]);

        } catch (\Exception $e) {

            log_message(
                'error',
                'Favorite WebSocket Error: ' . $e->getMessage()
            );
        }

        if ($isDeletedOrInserted) {

            session()->setFlashdata(
                'success',
                'Favorites updated successfully.'
            );

        } else {

            session()->setFlashdata(
                'error',
                'Unable to update favorites.'
            );
        }

        return redirect()->to('/buyer/favorites');
    }
}