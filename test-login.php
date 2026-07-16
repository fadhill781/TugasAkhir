<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Login Credentials ===\n\n";

// Test user
$user = User::where('email', 'user@test.com')->first();
if ($user) {
    echo "User found: {$user->email}\n";
    echo "Role: {$user->role}\n";
    echo "Password check: " . (Hash::check('password', $user->password) ? 'VALID' : 'INVALID') . "\n\n";
} else {
    echo "User not found\n\n";
}

// Test admin
$admin = User::where('email', 'admin@test.com')->first();
if ($admin) {
    echo "Admin found: {$admin->email}\n";
    echo "Role: {$admin->role}\n";
    echo "Password check: " . (Hash::check('password', $admin->password) ? 'VALID' : 'INVALID') . "\n\n";
} else {
    echo "Admin not found\n\n";
}

// List all users
echo "=== All Users ===\n";
$users = User::all();
foreach ($users as $u) {
    echo "ID: {$u->id} | Email: {$u->email} | Role: {$u->role}\n";
}
