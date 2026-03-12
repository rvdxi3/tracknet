<?php

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
        // Trust Railway's reverse proxy so HTTPS URLs are generated correctly
        $middleware->trustProxies(at: '*');

        // Global middleware (runs on every request)
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);
        $middleware->append(\App\Http\Middleware\EnsureAccountIsActive::class);

        // Exclude webhook routes from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhooks/paymongo',
        ]);

        // Route middleware aliases
        $middleware->alias([
            'admin'     => \App\Http\Middleware\AdminMiddleware::class,
            'inventory' => \App\Http\Middleware\InventoryMiddleware::class,
            'sales'     => \App\Http\Middleware\SalesMiddleware::class,
            'customer'  => \App\Http\Middleware\CustomerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
