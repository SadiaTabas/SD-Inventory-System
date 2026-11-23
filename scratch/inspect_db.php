<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::select('SHOW TABLES');
$dbName = DB::getDatabaseName();
$prop = 'Tables_in_' . $dbName;

$result = [];
foreach ($tables as $t) {
    $tableName = $t->$prop;
    $columns = Schema::getColumnListing($tableName);
    $result[$tableName] = $columns;
}

echo json_encode($result, JSON_PRETTY_PRINT);
