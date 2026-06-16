<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

set_error_handler(function($severity, $msg, $file, $line) {
    throw new ErrorException($msg, 0, $severity, $file, $line);
});

try {
    $seeder = new Database\Seeders\AdminDemoSeeder();
    $seeder->run();
    echo "OK\n";
} catch (Exception $e) {
    echo 'Error: '.get_class($e).': '.substr($e->getMessage(), 0, 500)."\n";
    $prev = $e->getPrevious();
    if ($prev) {
        echo 'Prev: '.get_class($prev).': '.substr($prev->getMessage(), 0, 500)."\n";
        echo 'Prev SQL State: '.($prev->errorInfo[0] ?? 'N/A')."\n";
        echo 'Prev SQL Code: '.($prev->errorInfo[1] ?? 'N/A')."\n";
        echo 'Prev SQL Error: '.($prev->errorInfo[2] ?? 'N/A')."\n";
    }
    echo $e->getTraceAsString()."\n";
}
