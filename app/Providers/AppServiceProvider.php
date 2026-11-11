<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registro: como mucho 5 cuentas por hora desde la misma IP
        RateLimiter::for('registro', fn (Request $request) => Limit::perHour(5)->by($request->ip()));

        // Contacto sin cuenta: 3 mensajes por minuto y 10 por hora desde la misma IP
        RateLimiter::for('contacto', fn (Request $request) => [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perHour(10)->by($request->ip()),
        ]);

        // Detrás de un proxy: sin esto, HTTPS no se detecta (ni HSTS ni URLs con https) y la IP es la del proxy
        if ($proxies = config('app.trusted_proxies')) {
            TrustProxies::at($proxies === '*' ? '*' : array_map('trim', explode(',', (string) $proxies)));
        }

        // API: 60 peticiones por minuto por usuario o, sin sesión, por IP
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)->by($request->user()?->id ?: $request->ip()));
    }
}
