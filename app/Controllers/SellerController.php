<?php

namespace App\Controllers;

use App\Models\PropertyModel;
use App\Models\OfferModel;
use App\Models\MessageModel;

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

    public function dashboard()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $sellerId = $user['id'];

        $propertyModel = new PropertyModel();
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $properties = $propertyModel->where('seller_id', $sellerId)
            ->where('is_archived', 0)
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'default', $page);

        $pager = $propertyModel->pager;

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

        // Return view with data including pager
        return view('seller/dashboard', [
            'user' => $user,
            'properties' => $properties,
            'offersData' => $offersData,
            'pager' => $pager
        ]);
    }
      

    public function addProperty()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $validation = \Config\Services::validation();

        $data = [
            'title' => $this->request->getPost('title', FILTER_SANITIZE_STRING),
            'description' => $this->request->getPost('description', FILTER_SANITIZE_STRING),
            'price' => $this->request->getPost('price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'location' => $this->request->getPost('location', FILTER_SANITIZE_STRING),
        ];

        $rules = [
            'title' => 'required|max_length[255]',
            'description' => 'required',
            'price' => 'required|decimal',
            'location' => 'required|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle image upload
        $imagePath = null;
        $img = $this->request->getFile('image');
        if ($img && $img->isValid() && !$img->hasMoved()) {
            $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads', $newName);
            $imagePath = 'uploads/' . $newName;
        }

        $propertyModel = new PropertyModel();
        $propertyModel->insert([
            'seller_id' => $user['id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'location' => $data['location'],
            'image_path' => $imagePath,
            'is_archived' => 0
        ]);

        session()->setFlashdata('success', 'Property added successfully!');
        return redirect()->to('/seller/dashboard');
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

        if ($action === 'accept') {
            $offerModel->update($offerId, ['status' => 'accepted']);

            $offer = $offerModel->find($offerId);
            if ($offer) {
                $offerModel->where('property_id', $offer['property_id'])
                           ->where('id !=', $offerId)
                           ->set(['status' => 'rejected'])
                           ->update();
            }

            session()->setFlashdata('success', 'Offer accepted successfully!');
        } elseif ($action === 'reject') {
            $offerModel->update($offerId, ['status' => 'rejected']);
            session()->setFlashdata('success', 'Offer rejected successfully!');
        } else {
            session()->setFlashdata('error', 'Invalid action.');
        }

        return redirect()->to('/seller/dashboard');
    }

    public function archived()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $sellerId = $user['id'];

        $propertyModel = new PropertyModel();
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10;
        $properties = $propertyModel->where('seller_id', $sellerId)
            ->where('is_archived', 1)
            ->orderBy('id', 'DESC')
            ->paginate($perPage, 'default', $page);

        $pager = $propertyModel->pager;

        return view('seller/archived', [
            'user' => $user,
            'properties' => $properties,
            'pager' => $pager
        ]);
    }

    public function editProperty($id = null)
    {
        $user = $this->checkRoleOrRedirect('seller');

        $propertyModel = new PropertyModel();
        $property = $propertyModel->where('id', $id)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/dashboard');
        }

        if ($this->request->getMethod() === 'post') {
            $validation = \Config\Services::validation();

            $data = [
                'title' => $this->request->getPost('title', FILTER_SANITIZE_STRING),
                'description' => $this->request->getPost('description', FILTER_SANITIZE_STRING),
                'price' => $this->request->getPost('price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
                'location' => $this->request->getPost('location', FILTER_SANITIZE_STRING),
            ];

            $rules = [
                'title' => 'required|max_length[255]',
                'description' => 'required',
                'price' => 'required|decimal',
                'location' => 'required|max_length[255]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $img = $this->request->getFile('image');
            if ($img && $img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
            $img->move(FCPATH . 'uploads', $newName);
            $data['image_path'] = 'uploads/' . $newName;
            } else {
                $data['image_path'] = $property['image_path'];
            }

            $propertyModel->update($id, $data);

            session()->setFlashdata('success', 'Property updated successfully!');
            return redirect()->to('/seller/dashboard');
        }

        return view('seller/edit_property', [
            'user' => $user,
            'property' => $property
        ]);
    }

    public function archive()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $propertyId = $this->request->getPost('property_id');

        $propertyModel = new PropertyModel();
        $property = $propertyModel->where('id', $propertyId)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/dashboard');
        }

        $propertyModel->update($propertyId, ['is_archived' => 1]);
        session()->setFlashdata('success', 'Property archived successfully!');
        return redirect()->to('/seller/dashboard');
    }

    public function unarchive()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $propertyId = $this->request->getPost('property_id');

        $propertyModel = new PropertyModel();
        $property = $propertyModel->where('id', $propertyId)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/archived');
        }

        $propertyModel->update($propertyId, ['is_archived' => 0]);
        session()->setFlashdata('success', 'Property restored successfully!');
        return redirect()->to('/seller/archived');
    }

    public function message($buyerId, $propertyId)
    {
        $user = $this->checkRoleOrRedirect('seller');

        // Redirect to MessageController with buyer and property info
        return redirect()->to("/message/conversation/{$buyerId}/{$propertyId}");
    }

    public function delete()
    {
        $user = $this->checkRoleOrRedirect('seller');

        $propertyId = $this->request->getPost('property_id');
        if (!$propertyId) {
            session()->setFlashdata('error', 'Invalid property id.');
            return redirect()->to('/seller/archived');
        }

        $propertyModel = new \App\Models\PropertyModel();
        $property = $propertyModel->where('id', $propertyId)
            ->where('seller_id', $user['id'])
            ->first();

        if (!$property) {
            session()->setFlashdata('error', 'Property not found.');
            return redirect()->to('/seller/archived');
        }

        // Delete image file if exists
        if (!empty($property['image_path']) && file_exists(FCPATH . $property['image_path'])) {
            unlink(FCPATH . $property['image_path']);
        }

        // Delete property from database
        $propertyModel->delete($propertyId);

        session()->setFlashdata('success', 'Property permanently deleted.');
        return redirect()->to('/seller/archived');
    }
}
