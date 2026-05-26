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
        
        // Get search parameters for each table
        $buyersSearch = $this->request->getGet('buyers_search');
        $buyersSearch = trim($buyersSearch);
        $sellersSearch = $this->request->getGet('sellers_search');
        $sellersSearch = trim($sellersSearch);
        
        // Buyers pagination
        $buyersCurrentPage = (int) ($this->request->getGet('buyers_page') ?? 1);
        $buyersPerPage = 10;
        
        $buyersQuery = $userModel->where('role', 'buyer');
        
        // Apply search filter to buyers
        if (!empty($buyersSearch)) {
            $buyersQuery->groupStart()
                ->like('name', $buyersSearch)
                ->orLike('email', $buyersSearch)
                ->orLike('contact', $buyersSearch)
                ->groupEnd();
        }
        
        $buyersTotal = $buyersQuery->countAllResults(false);
        $buyers = $buyersQuery->orderBy('id', 'DESC')
                            ->limit($buyersPerPage, ($buyersCurrentPage - 1) * $buyersPerPage)
                            ->get()
                            ->getResultArray();
        
        $buyersLastPage = ceil($buyersTotal / $buyersPerPage);
        $buyersPager = (object) [
            'currentPage' => $buyersCurrentPage,
            'lastPage' => $buyersLastPage,
            'total' => $buyersTotal,
            'perPage' => $buyersPerPage,
            'firstItem' => $buyersTotal > 0 ? (($buyersCurrentPage - 1) * $buyersPerPage) + 1 : 0,
            'lastItem' => min($buyersCurrentPage * $buyersPerPage, $buyersTotal)
        ];
        
        // Sellers pagination
        $sellersCurrentPage = (int) ($this->request->getGet('sellers_page') ?? 1);
        $sellersPerPage = 10;
        
        $sellersQuery = $userModel->where('role', 'seller');
        
        // Apply search filter to sellers
        if (!empty($sellersSearch)) {
            $sellersQuery->groupStart()
                ->like('name', $sellersSearch)
                ->orLike('email', $sellersSearch)
                ->orLike('contact', $sellersSearch)
                ->groupEnd();
        }
        
        $sellersTotal = $sellersQuery->countAllResults(false);
        $sellers = $sellersQuery->orderBy('id', 'DESC')
                                ->limit($sellersPerPage, ($sellersCurrentPage - 1) * $sellersPerPage)
                                ->get()
                                ->getResultArray();
        
        $sellersLastPage = ceil($sellersTotal / $sellersPerPage);
        $sellersPager = (object) [
            'currentPage' => $sellersCurrentPage,
            'lastPage' => $sellersLastPage,
            'total' => $sellersTotal,
            'perPage' => $sellersPerPage,
            'firstItem' => $sellersTotal > 0 ? (($sellersCurrentPage - 1) * $sellersPerPage) + 1 : 0,
            'lastItem' => min($sellersCurrentPage * $sellersPerPage, $sellersTotal)
        ];

        return view('admin/users', [
            'buyers' => $buyers,
            'sellers' => $sellers,
            'buyersPager' => $buyersPager,
            'sellersPager' => $sellersPager,
            'buyersSearch' => $buyersSearch,
            'sellersSearch' => $sellersSearch
        ]);
    }

    public function properties()
    {
        $this->checkRoleOrRedirect('admin');
        $propertyModel = new PropertyModel();
        
        // Get current page from URL, default to 1
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        
        // Get search parameter
        $search = $this->request->getGet('search');
        $search = trim($search);
        
        // Build query
        $query = $propertyModel->select('properties.*, users.name as seller_name')
            ->join('users', 'users.id = properties.seller_id');
        
        // Apply search filter if provided
        if (!empty($search)) {
            $query->groupStart()
                ->like('properties.title', $search)
                ->orLike('properties.location', $search)
                ->orLike('properties.description', $search)
                ->orLike('users.name', $search)
                ->groupEnd();
        }
        
        // Get total count
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
            'firstItem' => $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0,
            'lastItem' => min($currentPage * $perPage, $total)
        ];

        return view('admin/properties', [
            'properties' => $properties,
            'pager' => $pager,
            'search' => $search
        ]);
    }

    public function offers()
    {
        $this->checkRoleOrRedirect('admin');
        $offerModel = new OfferModel();
        
        // Get current page from URL, default to 1
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        
        // Get search parameter
        $search = $this->request->getGet('search');
        $search = trim($search);
        
        // Build query
        $query = $offerModel
            ->select('offers.*, buyers.name as buyer_name, properties.title as property_title')
            ->join('users as buyers', 'buyers.id = offers.buyer_id')
            ->join('properties', 'properties.id = offers.property_id');
        
        // Apply search filter if provided
        if (!empty($search)) {
            $query->groupStart()
                ->like('properties.title', $search)
                ->orLike('buyers.name', $search)
                ->orLike('offers.amount', $search)
                ->groupEnd();
        }
        
        // Get total count
        $total = $query->countAllResults(false);
        
        // Get paginated results
        $offers = $query->orderBy('offers.id', 'DESC')
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
            'firstItem' => $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0,
            'lastItem' => min($currentPage * $perPage, $total)
        ];

        return view('admin/offers', [
            'offers' => $offers,
            'pager' => $pager,
            'search' => $search
        ]);
    }

    public function payments()
    {
        $this->checkRoleOrRedirect('admin');
        $paymentModel = new PaymentModel();
        
        // Get current page from URL, default to 1
        $currentPage = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        
        // Get search parameter
        $search = $this->request->getGet('search');
        $search = trim($search);
        
        // Build query - specify table name for amount to avoid ambiguity
        $query = $paymentModel
            ->select('payments.*, buyers.name as buyer_name, sellers.name as seller_name, properties.title as property_title, payments.amount as payment_amount')
            ->join('offers', 'offers.id = payments.offer_id', 'left')
            ->join('users as buyers', 'buyers.id = payments.buyer_id')
            ->join('users as sellers', 'sellers.id = payments.seller_id')
            ->join('properties', 'properties.id = payments.property_id');
        
        // Apply search filter if provided
        if (!empty($search)) {
            $query->groupStart()
                ->like('properties.title', $search)
                ->orLike('buyers.name', $search)
                ->orLike('sellers.name', $search)
                ->orLike('payments.amount', $search)  // Specify payments.amount
                ->orLike('payments.status', $search)
                ->groupEnd();
        }
        
        // Get total count
        $total = $query->countAllResults(false);
        
        // Get paginated results
        $payments = $query->orderBy('payments.id', 'DESC')
                        ->limit($perPage, ($currentPage - 1) * $perPage)
                        ->get()
                        ->getResultArray();
        
        // Calculate total amount for all payments (without pagination)
        $totalAmountQuery = $paymentModel
            ->select('SUM(payments.amount) as total')  // Specify payments.amount
            ->join('offers', 'offers.id = payments.offer_id', 'left')
            ->join('users as buyers', 'buyers.id = payments.buyer_id')
            ->join('users as sellers', 'sellers.id = payments.seller_id')
            ->join('properties', 'properties.id = payments.property_id');
        
        if (!empty($search)) {
            $totalAmountQuery->groupStart()
                ->like('properties.title', $search)
                ->orLike('buyers.name', $search)
                ->orLike('sellers.name', $search)
                ->groupEnd();
        }
        
        $totalAmountResult = $totalAmountQuery->get()->getRow();
        $totalAmount = $totalAmountResult->total ?? 0;
        
        // Calculate pagination data
        $lastPage = ceil($total / $perPage);
        $pager = (object) [
            'currentPage' => $currentPage,
            'lastPage' => $lastPage,
            'total' => $total,
            'totalAmount' => $totalAmount,
            'perPage' => $perPage,
            'firstItem' => $total > 0 ? (($currentPage - 1) * $perPage) + 1 : 0,
            'lastItem' => min($currentPage * $perPage, $total)
        ];

        return view('admin/payments', [
            'payments' => $payments,
            'pager' => $pager,
            'search' => $search
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