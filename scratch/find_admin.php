<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$admin = App\Models\User::where('email', 'tabassum.aktar24@gmail.com')->first();
if ($admin) {
    echo "Found user: " . $admin->name . " | Role: " . $admin->role . " | Status: " . $admin->status . PHP_EOL;
} else {
    echo "User tabassum.aktar24@gmail.com NOT found in local database." . PHP_EOL;
    $all = App\Models\User::all(['id', 'name', 'email', 'role']);
    echo "Existing users: " . json_encode($all->toArray()) . PHP_EOL;
}
