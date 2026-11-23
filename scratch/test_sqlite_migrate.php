<?php
// Test running migrations on SQLite
$sqlitePath = __DIR__ . '/test.sqlite';
if (file_exists($sqlitePath)) {
    unlink($sqlitePath);
}
touch($sqlitePath);

putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE={$sqlitePath}");
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = $sqlitePath;
$_SERVER['DB_CONNECTION'] = 'sqlite';
$_SERVER['DB_DATABASE'] = $sqlitePath;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Running migrate on SQLite..." . PHP_EOL;
$status = $kernel->call('migrate', ['--force' => true]);
echo "Migrate exit code: " . $status . PHP_EOL;
echo $kernel->output() . PHP_EOL;
