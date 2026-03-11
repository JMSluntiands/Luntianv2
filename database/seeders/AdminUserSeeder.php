<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'fullname' => 'Admin',
                'role' => 'admin',
                'password' => 'admin123', // Model cast hashes it
            ]
        );
    }
}
