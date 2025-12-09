<?php

/**
 * Create a test user for development
 * Run: php create-test-user.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'test@w3university.com';
$password = 'password123';

// Check if user already exists
$existingUser = User::where('email', $email)->first();

if ($existingUser) {
    echo "✓ Test user already exists!\n";
    echo "  Email: {$email}\n";
    echo "  Password: {$password}\n";
    echo "\nYou can use these credentials to login.\n";
    exit(0);
}

// Create test user
$user = User::create([
    'name' => 'Test User',
    'email' => $email,
    'password' => Hash::make($password),
    'email_verified_at' => now(),
]);

echo "✓ Test user created successfully!\n";
echo "  Name: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Password: {$password}\n";
echo "\nYou can now login with these credentials.\n";
