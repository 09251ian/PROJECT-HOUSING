<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\OfferModel;
use App\Models\MessageModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class SellerController extends BaseController
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

    // Helper method to send notifications to Socket.IO server
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
            log_message('error', 'Socket notification failed for ' . $endpoint . ': HTTP ' . $httpCode);
        }
        
        return $httpCode === 200;
    }

    // API endpoint to get a single property (for real-time updates)
    public function getProperty($id = null)
    {
        $propertyModel = new PropertyModel();
        
        $property = $propertyModel
            ->select('properties.*, users.name as seller_name')
            ->join('users', 'users.id = properties.seller_id')
            ->where('properties.id', $id)
            ->where('properties.is_archived', 0)
            ->first();
        
        if (!$property) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Property not found']);
        }
        
        return $this->response->setJSON($property);
    }

    public function dashboard()
    {
        $user = $this->checkRoleOrRedirect('seller');
        $sellerId = $user['id'];
        
        $propertyModel = new PropertyModel();
        
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        
        $search = $this->request->getGet('search');
        $search = trim($search);
        
        $query = $propertyModel
            ->where('seller_id', $sellerId)
            ->where('is_archived', 0);
        
        if (!empty($search)) {
            $query->groupStart()
                ->like('title', $search)
                ->orLike('location', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }
        
        $total = $query->countAllResults(false);
        
        $properties = $query->orderBy('id', 'DESC')
                            ->limit($perPage, ($currentPage - 1) * $perPage)
                            ->get()
                            ->getResultArray();
        
        $lastPage = ceil($total / $perPage);
        $pager = (object) [
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'total' => $total,
            'perPage' => $perPage,
            'firstItem' => $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0,
            'lastItem' => min($currentPage * $perPage, $total)
        ];
        
        $offerModel = new OfferModel();
        $offersData = [];
        foreach ($properties as $property) {
            $offersData[$property['id']] = $offerModel
                ->select('offers.*, users.name as buyer_name')
                ->join('users', 'users.id = offers.buyer_id')
                ->where('property_id', $property['id'])
                ->orderBy('id', 'DESC')
                ->findAll();
        }
        
        $messageModel = new MessageModel();
        $chatsData = [];
        foreach ($properties as $property) {
            $chatsData[$property['id']] = $messageModel
                ->select('users.id as buyer_id, users.name as buyer_name')
                ->distinct()
                ->join('users', 'users.id = messages.sender_id OR users.id = messages.receiver_id')
                ->where('messages.property_id', $property['id'])
                ->where('users.id !=', $sellerId)
                ->where('users.role', 'buyer')
                ->groupBy('users.id')
                ->findAll();
        }
        
        return view('seller/dashboard', [
            'user' => $user,
            'properties' => $properties,
            'offersData' => $offersData,
            'chatsData' => $chatsData,
            'pager' => $pager,
            'search' => $search
        ]);
    }

    public function offerAction()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $offerId = $this->request->getPost('offer_id');
        $action = $this->request->getPost('action');

        if (!$offerId || !$action) {
            session()->setFlashdata('error', 'Invalid request.');
            return redirect()->to('/seller/dashboard');
        }

        $offerModel = new OfferModel();
        $propertyModel = new PropertyModel();
        $userModel = new UserModel();
        
        $offer = $offerModel->find($offerId);
        if (!$offer) {
            session()->setFlashdata('error', 'Offer not found.');
            return redirect()->to('/seller/dashboard');
        }
        
        $property = $propertyModel->find($offer['property_id']);
        $buyer = $userModel->find($offer['buyer_id']);

        if ($action === 'accept') {
            $offerModel->update($offerId, ['status' => 'accepted']);

            $offerModel->where('property_id', $offer['property_id'])
                ->where('id !=', $offerId)
                ->set(['status' => 'rejected'])
                ->update();

            session()->setFlashdata('success', 'Offer accepted successfully!');
            
            $notificationData = [
                'offer_id' => $offerId,
                'property_id' => $offer['property_id'],
                'property_title' => $property['title'],
                'amount' => $offer['amount'],
                'status' => 'accepted',
                'buyer_id' => $offer['buyer_id'],
                'buyer_name' => $buyer['name'] ?? 'Buyer',
                'seller_name' => $user['name'],
                'message' => 'Your offer has been accepted! Congratulations!'
            ];
            
            $this->sendSocketNotification('offer-status-updated', $notificationData);
            
            $rejectedOffers = $offerModel
                ->where('property_id', $offer['property_id'])
                ->where('id !=', $offerId)
                ->where('status', 'rejected')
                ->findAll();
                
            foreach ($rejectedOffers as $rejectedOffer) {
                if ($rejectedOffer['buyer_id'] != $offer['buyer_id']) {
                    $rejectedData = [
                        'offer_id' => $rejectedOffer['id'],
                        'property_id' => $offer['property_id'],
                        'property_title' => $property['title'],
                        'amount' => $rejectedOffer['amount'],
                        'status' => 'rejected',
                        'buyer_id' => $rejectedOffer['buyer_id'],
                        'seller_name' => $user['name'],
                        'message' => 'Your offer was rejected because another offer was accepted on this property.'
                    ];
                    $this->sendSocketNotification('offer-status-updated', $rejectedData);
                }
            }

        } elseif ($action === 'reject') {
            $offerModel->update($offerId, ['status' => 'rejected']);
            session()->setFlashdata('success', 'Offer rejected successfully!');
            
            $notificationData = [
                'offer_id' => $offerId,
                'property_id' => $offer['property_id'],
                'property_title' => $property['title'],
                'amount' => $offer['amount'],
                'status' => 'rejected',
                'buyer_id' => $offer['buyer_id'],
                'buyer_name' => $buyer['name'] ?? 'Buyer',
                'seller_name' => $user['name'],
                'message' => 'Your offer has been rejected.'
            ];
            
            $this->sendSocketNotification('offer-status-updated', $notificationData);

        } else {
            session()->setFlashdata('error', 'Invalid action.');
        }

        return redirect()->to('/seller/dashboard');
    }

    // ========== ADD PROPERTY WITH AUDIT LOG ==========
    public function addProperty()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'location' => $this->request->getPost('location'),
        ];

        $rules = [
            'title' => 'required|max_length[255]',
            'description' => 'required',
            'price' => 'required|decimal',
            'location' => 'required|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $imagePath = null;
        $img = $this->request->getFile('image');

        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads', $newName);
            $imagePath = 'uploads/' . $newName;
        }

        $propertyModel = new PropertyModel();

        $propertyData = [
            'seller_id' => $user['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'location' => $data['location'],
            'image_path' => $imagePath,
            'is_archived' => 0
        ];

        $propertyModel->insert($propertyData);
        $propertyId = $propertyModel->getInsertID();

        // ========== AUDIT LOG: SELLER CREATE PROPERTY ==========
        $auditLog = new AuditLogModel();
        $auditLog->logActivity(
            'create',
            'property',
            $propertyId,
            [
                'title' => $data['title'],
                'price' => $data['price'],
                'location' => $data['location'],
                'source' => 'seller'
            ]
        );
        // ========== END AUDIT LOG ==========

        $propertyData['id'] = $propertyId;
        $propertyData['seller_name'] = $user['name'];
        $propertyData['image_path'] = $imagePath;

        $this->sendSocketNotification('new-property', $propertyData);

        session()->setFlashdata('success', 'Property added successfully!');
        return redirect()->to('/seller/dashboard');
    }

    public function archived()
    {
        $user = $this->checkRoleOrRedirect('seller');
        $sellerId = $user['id'];

        $propertyModel = new PropertyModel();
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;

        $properties = $propertyModel
            ->where('seller_id', $sellerId)
            ->where('is_archived', 1)
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'default', $page);

        $pager = $propertyModel->pager;

        return view('seller/archived', [
            'user' => $user,
            'properties' => $properties,
            'pager' => $pager
        ]);

        // ========== AUDIT LOG: SELLER ARCHIVE PROPERTY ==========
        $auditLog = new AuditLogModel();
        $auditLog->logActivity(
            'archive',
            'property',
            $propertyId,
            [
                'title' => $property['title'],
                'price' => $property['price'],
                'location' => $property['location'],
                'is_archived' => 1,
                'source' => 'seller'
            ]
        );
        // ========== END AUDIT LOG ==========
    }

    // ========== EDIT PROPERTY WITH AUDIT LOG ==========
    public function editProperty($id = null)
    {
        $user = $this->checkRoleOrRedirect('seller');
        $propertyModel = new PropertyModel();

        $property = $propertyModel
            ->where('id', $id)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/dashboard');
        }

        if ($this->request->getMethod() === 'post') {
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'price' => $this->request->getPost('price'),
                'location' => $this->request->getPost('location'),
            ];

            $rules = [
                'title' => 'required|max_length[255]',
                'description' => 'required',
                'price' => 'required|decimal',
                'location' => 'required|max_length[255]'
            ];

            if (!$this->validate($rules)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', $this->validator->getErrors());
            }

            $img = $this->request->getFile('image');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $img->move(FCPATH . 'uploads', $newName);
                $data['image_path'] = 'uploads/' . $newName;

                if (!empty($property['image_path']) && file_exists(FCPATH . $property['image_path'])) {
                    unlink(FCPATH . $property['image_path']);
                }
            } else {
                $data['image_path'] = $property['image_path'];
            }

            $propertyModel->update($id, $data);
            
            // ========== AUDIT LOG: SELLER UPDATE PROPERTY ==========
            $auditLog = new AuditLogModel();
            $auditLog->logActivity(
                'update',
                'property',
                $id,
                [
                    'old' => [
                        'title' => $property['title'],
                        'price' => $property['price'],
                        'location' => $property['location']
                    ],
                    'new' => [
                        'title' => $data['title'],
                        'price' => $data['price'],
                        'location' => $data['location']
                    ],
                    'source' => 'seller'
                ]
            );
            // ========== END AUDIT LOG ==========
            
            $updatedProperty = $propertyModel->find($id);
            
            $notificationData = [
                'id' => $updatedProperty['id'],
                'title' => $updatedProperty['title'],
                'description' => $updatedProperty['description'],
                'price' => $updatedProperty['price'],
                'location' => $updatedProperty['location'],
                'seller_id' => $updatedProperty['seller_id'],
                'seller_name' => $user['name'],
                'image_path' => $updatedProperty['image_path']
            ];
            
            $this->sendSocketNotification('update-property', $notificationData);
            session()->setFlashdata('success', 'Property updated successfully!');
            return redirect()->to('/seller/dashboard');
        }

        return view('seller/edit_property', [
            'user' => $user,
            'property' => $property
        ]);
    }

    // ========== ARCHIVE PROPERTY WITH AUDIT LOG ==========
    public function archive()
    {
        $user = $this->checkRoleOrRedirect('seller');
        $propertyId = $this->request->getPost('property_id');
        $propertyModel = new PropertyModel();

        $property = $propertyModel
            ->where('id', $propertyId)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/dashboard');
        }

        // ========== AUDIT LOG: SELLER ARCHIVE PROPERTY ==========
        $auditLog = new AuditLogModel();
        $auditLog->logActivity(
            'archive',
            'property',
            $propertyId,
            [
                'title' => $property['title'],
                'price' => $property['price'],
                'location' => $property['location'],
                'source' => 'seller'
            ]
        );
        // ========== END AUDIT LOG ==========

        $propertyModel->update($propertyId, ['is_archived' => 1]);
        
        $this->sendSocketNotification('archive-property', ['id' => $propertyId]);
        
        session()->setFlashdata('success', 'Property archived successfully!');
        return redirect()->to('/seller/dashboard');
    }

    public function unarchive()
    {
        $user = $this->checkRoleOrRedirect('seller');
        $propertyId = $this->request->getPost('property_id');
        $propertyModel = new PropertyModel();

        $property = $propertyModel
            ->where('id', $propertyId)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/archived');
        }

        // ========== AUDIT LOG: SELLER UNARCHIVE PROPERTY ==========
        $auditLog = new \App\Models\AuditLogModel();
        $auditLog->logActivity(
            'unarchive',
            'property',
            $propertyId,
            [
                'title' => $property['title'],
                'price' => $property['price'],
                'location' => $property['location'],
                'is_archived' => 0,
                'source' => 'seller'
            ]
        );
        // ========== END AUDIT LOG ==========

        $propertyModel->update($propertyId, ['is_archived' => 0]);
        $restoredProperty = $propertyModel->find($propertyId);
        
        $notificationData = [
            'id' => $restoredProperty['id'],
            'title' => $restoredProperty['title'],
            'description' => $restoredProperty['description'],
            'price' => $restoredProperty['price'],
            'location' => $restoredProperty['location'],
            'seller_id' => $restoredProperty['seller_id'],
            'seller_name' => $user['name'],
            'image_path' => $restoredProperty['image_path']
        ];
        
        $this->sendSocketNotification('unarchive-property', ['id' => $propertyId]);
        $this->sendSocketNotification('new-property', $notificationData);
        
        session()->setFlashdata('success', 'Property restored successfully!');
        return redirect()->to('/seller/archived');
    }

    public function delete()
    {
        $user = $this->checkRoleOrRedirect('seller');
        $propertyId = $this->request->getPost('property_id');

        if (!$propertyId) {
            session()->setFlashdata('error', 'Invalid property id.');
            return redirect()->to('/seller/archived');
        }

        $propertyModel = new PropertyModel();
        $property = $propertyModel
            ->where('id', $propertyId)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/archived');
        }

        // ========== AUDIT LOG: SELLER DELETE PROPERTY ==========
        $auditLog = new AuditLogModel();
        $auditLog->logActivity(
            'delete',
            'property',
            $propertyId,
            [
                'title' => $property['title'],
                'price' => $property['price'],
                'location' => $property['location'],
                'source' => 'seller'
            ]
        );
        // ========== END AUDIT LOG ==========

        if (!empty($property['image_path']) && file_exists(FCPATH . $property['image_path'])) {
            unlink(FCPATH . $property['image_path']);
        }

        $propertyModel->delete($propertyId);
        $this->sendSocketNotification('delete-property', ['id' => $propertyId]);
        session()->setFlashdata('success', 'Property permanently deleted.');
        return redirect()->to('/seller/archived');
    }
}