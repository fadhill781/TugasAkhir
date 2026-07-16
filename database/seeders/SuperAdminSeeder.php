<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create or update superadmin
        User::updateOrCreate(
            ['email' => 'superadmin@test.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password'),
                'role'     => 'superadmin',
            ]
        );

        // Make sure existing admin@test.com is admin
        User::where('email', 'admin@test.com')
            ->update(['role' => 'admin']);

        $this->command->info('SuperAdmin seeded: superadmin@test.com / password');
    }
}
