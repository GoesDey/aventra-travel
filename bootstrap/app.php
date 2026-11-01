<?php

use Illuminate\Foundation\Application;
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
        $middleware->validateCsrfTokens(except: [
            'api/midtrans/webhook',
            'midtrans/webhook',
        ]);

        $middleware->alias([
            'admin'          => \App\Http\Middleware\EnsureUserIsAdmin::class,
            'is_user'        => \App\Http\Middleware\IsUser::class,
            'is_agent'       => \App\Http\Middleware\IsAgent::class,
            'is_super_admin' => \App\Http\Middleware\IsSuperAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
