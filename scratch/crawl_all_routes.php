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

$routesToTest = [
    '/admin/dashboard',
    '/order',
    '/customer',
    '/product',
    '/category',
    '/coupons',
    '/suppliers',
    '/purchases',
    '/inventory',
    '/inventory/movements',
    '/inventory/low-stock',
    '/payments',
    '/deliveries',
    '/deliveries/agents',
    '/reports',
    '/activity-logs',
    '/settings',
    '/admin/admins',
    '/admin/admins/create',
    '/',
    '/shop',
    '/cart',
    '/checkout',
    '/account',
    '/account/orders',
    '/account/profile',
    '/account/addresses',
    '/account/wishlist',
    '/account/reviews',
    '/health-check',
];

$errors = [];
echo "Testing all " . count($routesToTest) . " routes locally..." . PHP_EOL;

foreach ($routesToTest as $uri) {
    try {
        $request = Illuminate\Http\Request::create($uri, 'GET');
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();
        $isOk = ($status >= 200 && $status < 400);
        echo sprintf("%-28s : HTTP %d %s\n", $uri, $status, $isOk ? 'OK' : 'ERROR');
        if (!$isOk) {
            $errors[$uri] = "HTTP {$status}";
        }
    } catch (\Throwable $e) {
        echo sprintf("%-28s : EXCEPTION %s\n", $uri, $e->getMessage());
        $errors[$uri] = $e->getMessage();
    }
}

if (empty($errors)) {
    echo PHP_EOL . "ALL " . count($routesToTest) . " ROUTES PASSED WITH 200/302!" . PHP_EOL;
} else {
    echo PHP_EOL . "FAILED ROUTES: " . json_encode($errors, JSON_PRETTY_PRINT) . PHP_EOL;
}
