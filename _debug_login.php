<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Checking admin user ===\n";
$user = App\Models\User::where('email', 'admin@manna.pos')->first();
if (!$user) {
    echo "ERROR: admin@manna.pos not found in database!\n";
    exit(1);
}
echo "ID: " . $user->id . "\n";
echo "Name: " . $user->name . "\n";
echo "Email: " . $user->email . "\n";
echo "Role: " . var_export($user->role, true) . "\n";
echo "Password hash: " . $user->password . "\n";
echo "Hash check (password): " . (password_verify('password', $user->password) ? 'OK' : 'FAIL') . "\n";

echo "\n=== Attempting auth::attempt ===\n";
if (auth()->attempt(['email' => 'admin@manna.pos', 'password' => 'password'])) {
    echo "Login SUCCESS\n";
    echo "User role: " . auth()->user()->role . "\n";
} else {
    echo "Login FAILED\n";
    echo "Last attempt user: "; var_dump(auth()->user());
}

echo "\n=== Checking LoginController ===\n";
echo "The attemptLogin method adds role filter: role IN ['user', 'admin']\n";
echo "Admin user role is: " . var_export($user->role, true) . "\n";

echo "\n=== All users in DB ===\n";
foreach (App\Models\User::all() as $u) {
    echo "  - {$u->email} (role: {$u->role}) hash_check(password): " . (password_verify('password', $u->password) ? 'OK' : 'FAIL') . "\n";
}
