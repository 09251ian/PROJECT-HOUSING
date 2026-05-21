<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function loginForm()
    {
        return view('auth/login_form');
    }

    public function login()
    {
        $session = session();
        $model = new UserModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // Fetch user by email
        $user = $model->where('email', $email)->first();

        if (!$user) {
            $session->setFlashdata('error', 'Invalid credentials! (user not found for email)');
            return redirect()->to('/login');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            $session->setFlashdata('error', 'Invalid credentials! (password mismatch)');
            return redirect()->to('/login');
        }

        // Save user to session
        $session->set('user', [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ]);

        $role = $user['role'] ?? null;

        // Redirect based on role
        if ($role === 'admin') {
            $session->setFlashdata('success', 'Login successful!');
            return redirect()->to('/admin/dashboard');
        } 
        elseif ($role === 'seller') {
            $session->setFlashdata('success', 'Login successful!');
            return redirect()->to('/seller/dashboard');
        }

        $session->setFlashdata('success', 'Login successful!');
        return redirect()->to('/buyer/dashboard');
    }

    public function registerForm()
    {
        return view('auth/register_form');
    }

    public function register()
    {
        $session = session();
        $model = new \App\Models\UserModel();

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $contact = $this->request->getPost('contact');
        $bio = $this->request->getPost('bio');
        $role = $this->request->getPost('role');

        // Hash password
        $password = password_hash(
            $this->request->getPost('password'),
            PASSWORD_DEFAULT
        );

        // Check email duplicate
        if ($model->where('email', $email)->first()) {
            $session->setFlashdata('error', 'Email already exists!');
            return redirect()->to('/register');
        }

        // Handle profile picture upload
        $file = $this->request->getFile('profile_pic');
        $profileName = null;

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $profileName = $file->getRandomName();
            $file->move('uploads/profile_pics/', $profileName);
        }

        // Save user
        $model->save([
            'name'        => $name,
            'email'       => $email,
            'contact'     => $contact,
            'bio'         => $bio,
            'role'        => $role,
            'password'    => $password,
            'profile_pic' => $profileName
        ]);

        $session->setFlashdata(
            'success',
            'Account created successfully! You can now login.'
        );

        return redirect()->to('/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}