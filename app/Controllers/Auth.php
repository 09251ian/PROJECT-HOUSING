<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditLogModel;

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

        $user = $model->where('email', $email)->first();

        if (!$user) {
            $session->setFlashdata('error', 'Invalid credentials!');
            return redirect()->to('/login');
        }

        if (!password_verify($password, $user['password'])) {
            $session->setFlashdata('error', 'Invalid credentials!');
            return redirect()->to('/login');
        }

        // FIRST: Save user to session
        $session->set('user', [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role']
        ]);

        // ========== PUT THE DIRECT TEST HERE ==========
        $db = \Config\Database::connect();
        $db->table('audit_logs')->insert([
            'actor_user_id' => $user['id'],
            'actor_role' => $user['role'],
            'activity_type' => 'direct_test',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        // ========== END DIRECT TEST ==========

        // SECOND: Add audit log
        $auditLog = new \App\Models\AuditLogModel();
        $auditLog->logActivity(
            'login',
            'user',
            $user['id'],
            ['email' => $user['email'], 'name' => $user['name'], 'role' => $user['role']]
        );

        $role = $user['role'] ?? null;

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
            'profile_pic' => $profileName,
            'created_at'  => date('Y-m-d H:i:s')
        ]);
        
        $userId = $model->getInsertID();
        
        // Get the newly created user
        $newUser = $model->find($userId);

        // ========== ADD AUDIT LOG FOR REGISTRATION ==========
        $auditLog = new AuditLogModel();
        $auditLog->logActivity(
            'register',
            'user',
            $userId,
            ['email' => $newUser['email'], 'name' => $newUser['name'], 'role' => $newUser['role']]
        );
        // ========== END AUDIT LOG ==========

        // Send socket notification for real-time user registration
        $this->sendSocketNotification('new-user', [
            'id' => $newUser['id'],
            'name' => $newUser['name'],
            'email' => $newUser['email'],
            'role' => $newUser['role'],
            'contact' => $newUser['contact'],
            'bio' => $newUser['bio'],
            'profile_pic' => $newUser['profile_pic'],
            'created_at' => $newUser['created_at'] ?? date('Y-m-d H:i:s')
        ]);

        $session->setFlashdata(
            'success',
            'Account created successfully! You can now login.'
        );

        return redirect()->to('/login');
    }

    public function logout()
    {
        $session = session();
        $user = $session->get('user');
        
        // ========== ADD AUDIT LOG FOR LOGOUT ==========
        if ($user) {
            $auditLog = new \App\Models\AuditLogModel();
            $auditLog->logActivity(
                'logout',
                'user',
                $user['id'],
                ['email' => $user['email'], 'name' => $user['name'], 'role' => $user['role']]
            );
        }
        // ========== END AUDIT LOG ==========
        
        session()->destroy();
        return redirect()->to('/');
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