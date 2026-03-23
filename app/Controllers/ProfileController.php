<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    public function view($userId)
    {
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/')->with('error', 'User not found.');
        }

        // Only keep registration input fields
        $allowedFields = ['name', 'email', 'role', 'contact', 'bio', 'profile_pic'];
        $filteredUser = array_intersect_key($user, array_flip($allowedFields));

        // Get logged-in user's role for back navigation in profile view
        $session = session();
        $loggedInUser = $session->get('user');
        $userRole = $loggedInUser['role'] ?? null;

        return view('profile/view', [
            'user' => $filteredUser,
            'userRole' => $userRole,
        ]);
    }
}
