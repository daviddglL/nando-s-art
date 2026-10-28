<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CabecerasSeguridad;
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
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        // CSP, X-Frame-Options, Referrer-Policy, HSTS… en todas las respuestas
        $middleware->append(CabecerasSeguridad::class);

        // Límite de peticiones en toda la API (el limitador 'api' está en AppServiceProvider)
        $middleware->throttleApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
