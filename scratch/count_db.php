<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['users', 'categories', 'products', 'customers', 'orders', 'order_items', 'coupons', 'delivery_agents', 'addresses'];
$counts = [];
foreach ($tables as $t) {
    try {
        $counts[$t] = DB::table($t)->count();
    } catch (\Throwable $e) {
        $counts[$t] = 'Error: ' . $e->getMessage();
    }
}
echo json_encode($counts, JSON_PRETTY_PRINT);
