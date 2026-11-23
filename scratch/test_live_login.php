<?php
// Test login on live Vercel
$cookieFile = __DIR__ . '/cookies.txt';
if (file_exists($cookieFile)) unlink($cookieFile);

// 1. GET /login
$ch = curl_init('https://sd-inventory.vercel.app/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
curl_close($ch);

preg_match('/name="_token" value="([^"]+)"/', $response, $matches);
$csrfToken = $matches[1] ?? null;
echo "1. Obtained CSRF Token: " . ($csrfToken ? 'OK' : 'FAILED') . PHP_EOL;

if (!$csrfToken) {
    exit(1);
}

// 2. POST /login
$postData = http_build_query([
    '_token' => $csrfToken,
    'email' => 'tabassum.aktar24@gmail.com',
    'password' => '#ayaan24',
]);

$ch = curl_init('https://sd-inventory.vercel.app/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
$loginResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "2. Login HTTP Status: " . $httpCode . PHP_EOL;
preg_match('/Location: ([^\r\n]+)/i', $loginResponse, $locMatches);
$redirectUrl = $locMatches[1] ?? 'None';
echo "   Redirect Location: " . $redirectUrl . PHP_EOL;

// 3. Follow redirect to verify authenticated dashboard
if ($httpCode === 302 && str_contains($redirectUrl, 'dashboard')) {
    $ch = curl_init($redirectUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    $dashResponse = curl_exec($ch);
    $dashCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "3. Dashboard HTTP Status: " . $dashCode . PHP_EOL;
    echo "   Dashboard contains 'Admin Dashboard': " . (str_contains($dashResponse, 'Dashboard') ? 'YES' : 'NO') . PHP_EOL;
}
