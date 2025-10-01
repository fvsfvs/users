<?php

use App\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.api' => \App\Http\Middleware\ApiAuthenticate::class,
        ]);

    })
    ->withExceptions(new ExceptionHandler())
    ->create();
