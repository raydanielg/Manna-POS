<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
foreach (['john@example.com', 'jane@example.com'] as $email) {
    $u = App\Models\User::where('email', $email)->first();
    if ($u) {
        $u->password = password_hash('password', PASSWORD_BCRYPT);
        $u->save();
        echo "Password set for: " . $u->email . "\n";
    }
}
