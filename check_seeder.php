<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
$c = App\Models\SystemConfig::count();
$b = App\Models\BusinessCategory::count();
$g = App\Models\PaymentGateway::count();
echo "SystemConfigs: $c, BusinessCategories: $b, PaymentGateways: $g\n";
