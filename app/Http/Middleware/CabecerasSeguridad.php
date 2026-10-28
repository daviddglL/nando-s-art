<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class CabecerasSeguridad
{
    // Todo el JS es propio y lo compila Vite, así que la CSP solo permite scripts del mismo dominio.
    // Los estilos en línea se permiten porque la página de error 500 no depende de Vite y lleva su <style>.
    public const CSP = "default-src 'self'; script-src 'self'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; "
        ."font-src 'self'; connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'";

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $cabeceras = [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
        ];

        // Con `npm run dev` los scripts vienen del servidor de Vite (otro puerto), y la página de errores
        // de Laravel en modo debug usa JS en línea: en esos dos casos no se aplica la CSP
        $paginaDeDebug = config('app.debug') && $response->getStatusCode() >= 500;
        if (! Vite::isRunningHot() && ! $paginaDeDebug) {
            $cabeceras['Content-Security-Policy'] = self::CSP;
        }

        // HSTS solo tiene sentido (y solo lo respetan los navegadores) cuando ya se está en HTTPS
        if ($request->isSecure()) {
            $cabeceras['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($cabeceras as $nombre => $valor) {
            if (! $response->headers->has($nombre)) {
                $response->headers->set($nombre, $valor);
            }
        }

        return $response;
    }
}
