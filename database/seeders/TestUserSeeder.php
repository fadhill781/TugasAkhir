<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create test admin
        User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin Test',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]
        );

        // Create test user
        User::firstOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'User Test',
                'password' => Hash::make('password'),
                'role' => 'user'
            ]
        );

        echo "Test users created:\n";
        echo "Admin: admin@test.com / password\n";
        echo "User: user@test.com / password\n";
    }
}
