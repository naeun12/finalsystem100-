<?php

use App\Console\Kernel as ConsoleKernel;
use App\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // register global middleware here if needed
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // register exception handlers here if needed
    }) ->withProviders([
        \App\Providers\BroadcastServiceProvider::class,
    ]) 
    ->withKernels(
        HttpKernel::class,
        ConsoleKernel::class
    )
    ->create();
