<?php

use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $e) {
            error_log('LARAVEL_ERROR: ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        });
    })
    ->create();

if (!empty($_ENV['LARAVEL_STORAGE_PATH'])) {
    $app->useStoragePath($_ENV['LARAVEL_STORAGE_PATH']);
} elseif (!empty($_SERVER['LARAVEL_STORAGE_PATH'])) {
    $app->useStoragePath($_SERVER['LARAVEL_STORAGE_PATH']);
}

return $app;