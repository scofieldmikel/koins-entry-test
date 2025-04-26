<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\VerifyPaystack;
use App\Http\Middleware\IsProfileUpdated;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'IsProfileUpdated' => IsProfileUpdated::class,
            'paystack' => VerifyPaystack::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
