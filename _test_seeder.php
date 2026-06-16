<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
try {
    $seeder = new Database\Seeders\AdminDemoSeeder();
    $seeder->run();
    echo "OK\n";
} catch (Exception $e) {
    echo get_class($e).': '.$e->getMessage()."\n";
}
