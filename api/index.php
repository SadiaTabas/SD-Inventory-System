<?php

// Vercel serverless entry point for Laravel

// 1. Force HTTPS detection when coming from Vercel's Edge
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
    $_SERVER['SERVER_PORT'] = 443;
}

// 2. Initialize /tmp storage directories (AWS Lambda filesystem is read-only except for /tmp)
$storagePath = '/tmp/storage';
$dirs = [
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/logs',
    $storagePath . '/app/public',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

putenv("LARAVEL_STORAGE_PATH={$storagePath}");
$_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
$_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;

putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");
$_ENV['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";
$_SERVER['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";

putenv("SESSION_DRIVER=cookie");
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';

putenv("CACHE_STORE=array");
$_ENV['CACHE_STORE'] = 'array';
$_SERVER['CACHE_STORE'] = 'array';

// 3. Database configuration for Vercel Serverless
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? ''));
$isRemoteDb = !empty($dbHost) && $dbHost !== '127.0.0.1' && $dbHost !== 'localhost';

if (!$isRemoteDb) {
    $tmpDb = '/tmp/database.sqlite';
    $srcDb = __DIR__ . '/../database/database.sqlite';
    if (!file_exists($tmpDb) && file_exists($srcDb)) {
        @copy($srcDb, $tmpDb);
    } elseif (!file_exists($tmpDb)) {
        @touch($tmpDb);
    }
    @chmod($tmpDb, 0666);

    putenv("DB_CONNECTION=sqlite");
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

// 4. Forward request to Laravel public index
require __DIR__ . '/../public/index.php';
