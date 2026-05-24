<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;
use App\Models\UserModel;

class DevAdminSeeder extends Seeder
{
    public function run()
    {
        $model = new UserModel();

        // Avoid inserting duplicates if seeder runs multiple times.
        $email = 'admin@example.com';
        $existing = $model->where('email', $email)->first();
        if ($existing) {
            return;
        }

        $model->insert([
            'name' => 'Dev Admin',
            'email' => $email,
            'password' => password_hash('Admin12345', PASSWORD_DEFAULT),
            'role' => 'admin',
            'contact' => null,
            'bio' => null,
            'profile_pic' => null,
        ]);
    }
}

