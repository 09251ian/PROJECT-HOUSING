<?php

namespace App\Controllers;

use App\Models\FavoriteModel;
use App\Models\MessageModel;
use App\Models\OfferModel;
use App\Models\PaymentModel;
use App\Models\PropertyModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    protected function checkRoleOrRedirect(string $role): array
    {
        $session = \Config\Services::session();
        $user = $session->get('user');

        if (!$user || ($user['role'] ?? null) !== $role) {
            $session->setFlashdata(
                'error',
                'Admin guard blocked: ' .
                'expected=' . $role . ', ' .
                'session_user=' . ($user ? json_encode($user) : 'null')
            );

            redirect()->to('/login')->send();
            exit;
        }

        return $user;
    }

    public function dashboard()
    {
        $this->checkRoleOrRedirect('admin');

        $userModel = new UserModel();
        $propertyModel = new PropertyModel();
        $offerModel = new OfferModel();
        $paymentModel = new PaymentModel();

        $buyersCount = (int) $userModel->where('role', 'buyer')->countAllResults();
        $sellersCount = (int) $userModel->where('role', 'seller')->countAllResults();
        $propertiesCount = (int) $propertyModel->countAllResults();
        $offersCount = (int) $offerModel->countAllResults();
        $paymentsCount = (int) $paymentModel->countAllResults();
        
        $recentUsers = $userModel->orderBy('id', 'DESC')->limit(5)->findAll();

        return view('admin/dashboard', [
            'buyersCount' => $buyersCount,
            'sellersCount' => $sellersCount,
            'propertiesCount' => $propertiesCount,
            'offersCount' => $offersCount,
            'paymentsCount' => $paymentsCount,
            'recentUsers' => $recentUsers,
        ]);
    }

    public function getCounts()
    {
        $this->checkRoleOrRedirect('admin');
        
        $userModel = new UserModel();
        $propertyModel = new PropertyModel();
        $offerModel = new OfferModel();
        $paymentModel = new PaymentModel();
        
        return $this->response->setJSON([
            'success' => true,
            'buyersCount' => (int) $userModel->where('role', 'buyer')->countAllResults(),
            'sellersCount' => (int) $userModel->where('role', 'seller')->countAllResults(),
            'propertiesCount' => (int) $propertyModel->countAllResults(),
            'offersCount' => (int) $offerModel->countAllResults(),
            'paymentsCount' => (int) $paymentModel->countAllResults(),
        ]);
    }

    public function users()
    {
        $this->checkRoleOrRedirect('admin');
        $userModel = new UserModel();

        $buyers = $userModel->where('role', 'buyer')->orderBy('id', 'DESC')->findAll();
        $sellers = $userModel->where('role', 'seller')->orderBy('id', 'DESC')->findAll();

        return view('admin/users', [
            'buyers' => $buyers,
            'sellers' => $sellers,
        ]);
    }

    public function properties()
    {
        $this->checkRoleOrRedirect('admin');
        $propertyModel = new PropertyModel();

        $properties = $propertyModel->select('properties.*, users.name as seller_name')
            ->join('users', 'users.id = properties.seller_id')
            ->orderBy('properties.id', 'DESC')
            ->findAll();

        return view('admin/properties', [
            'properties' => $properties,
        ]);
    }

    public function offers()
    {
        $this->checkRoleOrRedirect('admin');
        $offerModel = new OfferModel();
        $offers = $offerModel
            ->select('offers.*, buyers.name as buyer_name, properties.title as property_title')
            ->join('users as buyers', 'buyers.id = offers.buyer_id')
            ->join('properties', 'properties.id = offers.property_id')
            ->orderBy('offers.id', 'DESC')
            ->findAll();

        return view('admin/offers', [
            'offers' => $offers,
        ]);
    }

    public function payments()
    {
        $this->checkRoleOrRedirect('admin');
        $paymentModel = new PaymentModel();

        $payments = $paymentModel
            ->select('payments.*, buyers.name as buyer_name, sellers.name as seller_name, properties.title as property_title')
            ->join('offers', 'offers.id = payments.offer_id', 'left')
            ->join('users as buyers', 'buyers.id = payments.buyer_id')
            ->join('users as sellers', 'sellers.id = payments.seller_id')
            ->join('properties', 'properties.id = payments.property_id')
            ->orderBy('payments.id', 'DESC')
            ->findAll();

        return view('admin/payments', [
            'payments' => $payments,
        ]);
    }

    public function addProperty()
    {
        $this->checkRoleOrRedirect('admin');

        $userModel = new UserModel();
        $sellers = $userModel->where('role', 'seller')->orderBy('id', 'DESC')->findAll();

        if ($this->request->getMethod() === 'post') {
            $data = [
                'title' => $this->request->getPost('title', FILTER_SANITIZE_STRING),
                'description' => $this->request->getPost('description', FILTER_SANITIZE_STRING),
                'price' => $this->request->getPost('price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'location' => $this->request->getPost('location', FILTER_SANITIZE_STRING),
                'seller_id' => $this->request->getPost('seller_id', FILTER_SANITIZE_NUMBER_INT),
            ];

            $rules = [
                'seller_id' => 'required|integer',
                'title' => 'required|max_length[255]',
                'description' => 'required',
                'price' => 'required|decimal',
                'location' => 'required|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $imagePath = null;
            $img = $this->request->getFile('image');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $img->move(FCPATH . 'uploads', $newName);
                $imagePath = 'uploads/' . $newName;
            }

            $propertyModel = new PropertyModel();
            
            $seller = $userModel->find($data['seller_id']);
            
            $propertyData = [
                'seller_id' => (int) $data['seller_id'],
                'title' => $data['title'],
                'description' => $data['description'],
                'price' => $data['price'],
                'location' => $data['location'],
                'image_path' => $imagePath,
                'is_archived' => 0,
            ];
            
            $propertyModel->insert($propertyData);
            $propertyId = $propertyModel->getInsertID();
            
            $propertyData['id'] = $propertyId;
            $propertyData['seller_name'] = $seller['name'] ?? 'Seller';
            
            $this->sendSocketNotification('new-property', $propertyData);

            session()->setFlashdata('success', 'Property added successfully and broadcasted in real-time!');
            return redirect()->to('/admin/properties');
        }

        return view('admin/add_property', [
            'sellers' => $sellers,
        ]);
    }

    public function editProperty($id = null)
    {
        $this->checkRoleOrRedirect('admin');

        $userModel = new UserModel();
        $sellers = $userModel->where('role', 'seller')->orderBy('id', 'DESC')->findAll();

        $propertyModel = new PropertyModel();
        $property = $propertyModel->where('id', $id)->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/admin/properties');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'title' => $this->request->getPost('title', FILTER_SANITIZE_STRING),
                'description' => $this->request->getPost('description', FILTER_SANITIZE_STRING),
                'price' => $this->request->getPost('price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'location' => $this->request->getPost('location', FILTER_SANITIZE_STRING),
                'seller_id' => $this->request->getPost('seller_id', FILTER_SANITIZE_NUMBER_INT),
            ];

            $rules = [
                'seller_id' => 'required|integer',
                'title' => 'required|max_length[255]',
                'description' => 'required',
                'price' => 'required|decimal',
                'location' => 'required|max_length[255]',
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $img = $this->request->getFile('image');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $img->move(FCPATH . 'uploads', $newName);
                $data['image_path'] = 'uploads/' . $newName;
                
                // Delete old image if exists
                if (!empty($property['image_path']) && file_exists(FCPATH . $property['image_path'])) {
                    unlink(FCPATH . $property['image_path']);
                }
            } else {
                $data['image_path'] = $property['image_path'] ?? null;
            }

            $data['is_archived'] = (int) ($property['is_archived'] ?? 0);

            $propertyModel->update((int) $id, $data);
            
            // Get updated property with seller info for broadcast
            $updatedProperty = $propertyModel->select('properties.*, users.name as seller_name')
                ->join('users', 'users.id = properties.seller_id')
                ->where('properties.id', $id)
                ->first();
            
            // Send socket notification for real-time update
            $this->sendSocketNotification('update-property', $updatedProperty);

            session()->setFlashdata('success', 'Property updated successfully and broadcasted in real-time!');
            return redirect()->to('/admin/properties');
        }

        return view('admin/edit_property', [
            'property' => $property,
            'sellers' => $sellers,
        ]);
    }

    public function deleteProperty()
    {
        $this->checkRoleOrRedirect('admin');
        
        $propertyId = $this->request->getPost('property_id');
        
        if (!$propertyId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Property ID required']);
        }
        
        $propertyModel = new PropertyModel();
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON(['success' => false, 'message' => 'Property not found']);
        }
        
        if (!empty($property['image_path']) && file_exists(FCPATH . $property['image_path'])) {
            unlink(FCPATH . $property['image_path']);
        }
        
        $offerModel = new \App\Models\OfferModel();
        $favoriteModel = new \App\Models\FavoriteModel();
        $messageModel = new \App\Models\MessageModel();
        
        $offerModel->where('property_id', $propertyId)->delete();
        $favoriteModel->where('property_id', $propertyId)->delete();
        $messageModel->where('property_id', $propertyId)->delete();
        
        $deleted = $propertyModel->delete($propertyId);
        
        if ($deleted) {
            $this->sendSocketNotification('delete-property', ['id' => $propertyId]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Property deleted successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete property'
            ]);
        }
    }

    public function updateOfferStatus()
    {
        $this->checkRoleOrRedirect('admin');
        
        $offerId = $this->request->getPost('offer_id');
        $status = $this->request->getPost('status');
        
        if (!$offerId || !$status) {
            return $this->response->setJSON(['success' => false, 'message' => 'Offer ID and status required']);
        }
        
        $offerModel = new OfferModel();
        $offer = $offerModel->find($offerId);
        
        if (!$offer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Offer not found']);
        }
        
        $offerModel->update($offerId, ['status' => $status]);
        
        $propertyModel = new PropertyModel();
        $userModel = new UserModel();
        
        $property = $propertyModel->find($offer['property_id']);
        $buyer = $userModel->find($offer['buyer_id']);
        
        $offerUpdate = [
            'id' => $offerId,
            'property_id' => $offer['property_id'],
            'property_title' => $property['title'] ?? 'Property',
            'buyer_id' => $offer['buyer_id'],
            'buyer_name' => $buyer['name'] ?? 'Buyer',
            'status' => $status,
            'amount' => $offer['amount'],
            'message' => $status === 'accepted' ? 'Offer Accepted!' : 'Offer Rejected'
        ];
        
        $this->sendSocketNotification('offer-status-updated', $offerUpdate);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Offer status updated successfully'
        ]);
    }

    private function sendSocketNotification($endpoint, $data)
    {
        try {
            $client = \Config\Services::curlrequest();
            $client->post('http://localhost:3000/' . $endpoint, [
                'json' => $data,
                'timeout' => 2
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Socket notification failed: ' . $e->getMessage());
        }
    }
}