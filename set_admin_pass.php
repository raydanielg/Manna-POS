<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$u = App\Models\User::where('email', 'admin@manna.pos')->first();
if ($u) {
    $u->password = bcrypt('password');
    $u->save();
    echo "Password set for: " . $u->email . "\n";
} else {
    echo "Admin user not found\n";
}
