<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('email', 'admin@manna.pos')->first();
if (!$user) {
    echo "admin@manna.pos NOT FOUND\n";
    exit(1);
}
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . $user->role . "\n";
echo "Password hash: " . $user->password . "\n";
echo "Hash check (password): " . (password_verify('password', $user->password) ? 'OK' : 'FAIL') . "\n";

// Try login
$credentials = ['email' => 'admin@manna.pos', 'password' => 'password'];
if (auth()->attempt($credentials)) {
    echo "Login SUCCESS\n";
} else {
    echo "Login FAILED\n";
}
