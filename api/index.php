<?php

// Vercel serverless entry point for Laravel

// Initialize /tmp storage directories (AWS Lambda filesystem is read-only except for /tmp)
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

// Forward request to Laravel public index
require __DIR__ . '/../public/index.php';
