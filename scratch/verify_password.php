<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$admin = App\Models\User::where('email', 'tabassum.aktar24@gmail.com')->first();
$valid = Illuminate\Support\Facades\Hash::check('#ayaan24', $admin->password);
echo "Password valid: " . ($valid ? 'YES' : 'NO') . PHP_EOL;
if (!$valid) {
    echo "Updating password to #ayaan24..." . PHP_EOL;
    $admin->password = Illuminate\Support\Facades\Hash::make('#ayaan24');
    $admin->save();
    echo "Updated successfully." . PHP_EOL;
}
