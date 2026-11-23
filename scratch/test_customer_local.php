<?php
putenv("DB_CONNECTION=sqlite");
putenv("DB_DATABASE=" . dirname(__DIR__) . "/database/database.sqlite");
$_ENV['DB_CONNECTION'] = 'sqlite';
$_ENV['DB_DATABASE'] = dirname(__DIR__) . "/database/database.sqlite";

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$admin = App\Models\User::where('email', 'tabassum.aktar24@gmail.com')->first();
Illuminate\Support\Facades\Auth::login($admin);

try {
    $request = Illuminate\Http\Request::create('/customer', 'GET');
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . PHP_EOL;
    if ($response->getStatusCode() !== 200) {
        echo substr($response->getContent(), 0, 1000) . PHP_EOL;
    } else {
        echo "Customer index rendered successfully: " . strlen($response->getContent()) . " bytes." . PHP_EOL;
    }
} catch (\Throwable $e) {
    echo "EXCEPTION: " . get_class($e) . ": " . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
}
