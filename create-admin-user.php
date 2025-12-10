<?php

/**
 * Create an admin user for development
 * Run: php create-admin-user.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$email = 'admin@w3university.com';
$password = 'admin123';

// Check if user already exists
$existingUser = User::where('email', $email)->first();

if ($existingUser) {
    // Update role to admin if not already
    if ($existingUser->role !== 'admin') {
        $existingUser->update(['role' => 'admin']);
        echo "✓ Updated existing user to admin!\n";
    } else {
        echo "✓ Admin user already exists!\n";
    }
    echo "  Email: {$email}\n";
    echo "  Password: {$password}\n";
    echo "\nYou can use these credentials to login to admin panel.\n";
    exit(0);
}

// Create admin user
$user = User::create([
    'name' => 'Admin User',
    'email' => $email,
    'password' => Hash::make($password),
    'role' => 'admin',
    'email_verified_at' => now(),
]);

echo "✓ Admin user created successfully!\n";
echo "  Name: {$user->name}\n";
echo "  Email: {$user->email}\n";
echo "  Password: {$password}\n";
echo "  Role: {$user->role}\n";
echo "\nYou can now login with these credentials to access admin panel.\n";
