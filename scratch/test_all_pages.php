<?php
$cookieFile = __DIR__ . '/cookies.txt';

$pages = [
    'Admin Dashboard'  => 'https://sd-inventory.vercel.app/admin/dashboard',
    'Order Index'      => 'https://sd-inventory.vercel.app/order',
    'Customer Index'   => 'https://sd-inventory.vercel.app/customer',
    'Product Index'    => 'https://sd-inventory.vercel.app/product',
    'Category Index'   => 'https://sd-inventory.vercel.app/category',
    'Coupons Index'    => 'https://sd-inventory.vercel.app/coupons',
    'Suppliers Index'  => 'https://sd-inventory.vercel.app/suppliers',
    'Purchases Index'  => 'https://sd-inventory.vercel.app/purchases',
    'Inventory Index'  => 'https://sd-inventory.vercel.app/inventory',
    'Payments Index'   => 'https://sd-inventory.vercel.app/payments',
    'Deliveries Index' => 'https://sd-inventory.vercel.app/deliveries',
    'Reports Index'    => 'https://sd-inventory.vercel.app/reports',
    'Activity Logs'    => 'https://sd-inventory.vercel.app/activity-logs',
    'Settings Index'   => 'https://sd-inventory.vercel.app/settings',
    'Customer Home'    => 'https://sd-inventory.vercel.app/',
    'Shop Page'        => 'https://sd-inventory.vercel.app/shop',
    'Cart Page'        => 'https://sd-inventory.vercel.app/cart',
];

echo "Testing all key pages on live Vercel..." . PHP_EOL;

$allPassed = true;
foreach ($pages as $name => $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $is500 = ($httpCode >= 500);
    $statusText = $is500 ? "FAILED (HTTP {$httpCode})" : "PASSED (HTTP {$httpCode})";
    echo sprintf("%-20s : %s\n", $name, $statusText);

    if ($is500) {
        $allPassed = false;
    }
}

if ($allPassed) {
    echo PHP_EOL . "ALL PAGES RETURNED NON-500 STATUS CODES SUCCESSFULLY!" . PHP_EOL;
} else {
    echo PHP_EOL . "SOME PAGES FAILED WITH 500!" . PHP_EOL;
}
