<?php
$cookieFile = __DIR__ . '/cookies.txt';
$ch = curl_init('https://sd-inventory.vercel.app/customer');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
$response = curl_exec($ch);
curl_close($ch);
echo substr($response, 0, 1000) . PHP_EOL;
file_put_contents(__DIR__ . '/customer_err.html', $response);
