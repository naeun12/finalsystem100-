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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
    $app = Illuminate\Foundation\Application::configure(basePath: dirname(__DIR__))
    ->withKernels(
        http: App\Http\Kernel::class,
        console: App\Console\Kernel::class
    )
    // ... rest of configuration
    ->create();
