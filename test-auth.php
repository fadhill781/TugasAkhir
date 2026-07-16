<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "=== Testing Authentication System ===\n\n";

// Test credentials
$testUsers = [
    ['email' => 'admin@test.com', 'password' => 'password', 'expected_role' => 'admin'],
    ['email' => 'user@test.com', 'password' => 'password', 'expected_role' => 'user'],
];

foreach ($testUsers as $testUser) {
    echo "Testing: {$testUser['email']}\n";
    
    $user = User::where('email', $testUser['email'])->first();
    
    if (!$user) {
        echo "  ❌ User not found\n\n";
        continue;
    }
    
    echo "  ✓ User found (ID: {$user->id})\n";
    echo "  ✓ Role: {$user->role}\n";
    
    $passwordValid = Hash::check($testUser['password'], $user->password);
    echo "  " . ($passwordValid ? "✓" : "❌") . " Password: " . ($passwordValid ? "VALID" : "INVALID") . "\n";
    
    $roleMatch = $user->role === $testUser['expected_role'];
    echo "  " . ($roleMatch ? "✓" : "❌") . " Role match: " . ($roleMatch ? "YES" : "NO") . "\n";
    
    // Test Auth::attempt
    $canLogin = Auth::attempt(['email' => $testUser['email'], 'password' => $testUser['password']]);
    echo "  " . ($canLogin ? "✓" : "❌") . " Auth::attempt: " . ($canLogin ? "SUCCESS" : "FAILED") . "\n";
    
    if ($canLogin) {
        Auth::logout();
    }
    
    echo "\n";
}

echo "=== Summary ===\n";
echo "Total users in database: " . User::count() . "\n";
echo "Admin users: " . User::where('role', 'admin')->count() . "\n";
echo "Regular users: " . User::where('role', 'user')->count() . "\n";
