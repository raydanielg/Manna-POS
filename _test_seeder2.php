<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
    // Just log successful queries silently
});

// Override to catch raw PDO error
$pdo = DB::connection()->getPdo();
try {
    $seeder = new Database\Seeders\AdminDemoSeeder();
    $seeder->run();
    echo "ALL OK\n";
} catch (\ErrorException $e) {
    // This is the Str::replaceArray error
    $prev = $e->getPrevious();
    if ($prev) {
        echo "Previous: " . get_class($prev) . ": " . $prev->getMessage() . "\n";
    }
    // Try to get the actual PDO error from the connection
    $pdo = DB::connection()->getPdo();
    $info = $pdo->errorInfo();
    echo "PDO ErrorInfo: " . json_encode($info) . "\n";
} catch (\Exception $e) {
    echo "Exception: " . get_class($e) . ": " . $e->getMessage() . "\n";
    $prev = $e->getPrevious();
    if ($prev) {
        echo "Previous: " . get_class($prev) . ": " . $prev->getMessage() . "\n";
    }
}
