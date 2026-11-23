<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$cols = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'");
print_r($cols);
$orderCols = DB::select("SHOW COLUMNS FROM orders");
print_r($orderCols);
