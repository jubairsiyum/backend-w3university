<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Use /tmp/bootstrap for cache on Vercel
$bootstrapPath = defined('LARAVEL_BOOTSTRAP_CACHE') ? dirname(LARAVEL_BOOTSTRAP_CACHE) : __DIR__;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);
        
        // Register custom middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })->create();

// Override storage and bootstrap paths for Vercel
if (isset($_ENV['VERCEL']) || isset($_ENV['APP_STORAGE'])) {
    $app->useStoragePath($_ENV['APP_STORAGE'] ?? '/tmp/storage');
    
    // Override bootstrap path
    if (defined('LARAVEL_BOOTSTRAP_CACHE')) {
        $app->useBootstrapPath(dirname(LARAVEL_BOOTSTRAP_CACHE));
    }
}

return $app;
