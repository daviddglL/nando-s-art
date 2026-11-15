<?php

namespace App\Http\Controllers;

use App\Galeria\Pagina;
use App\Http\Requests\EnviarMensajeContactoRequest;
use App\Notifications\MensajeContacto;
use Illuminate\Support\Facades\Notification;
use Throwable;

// Formulario de /contacto: el mensaje llega por email al artista. No se guarda nada.
class MensajeContactoController extends Controller
{
    public function __invoke(EnviarMensajeContactoRequest $request)
    {
        $datos = $request->safe()->only(['nombre', 'email', 'mensaje']);
        $volver = redirect()->to(route('paginas.contacto').'#formulario-contacto');

        // CONTACTO_EMAIL en .env y, si no, el email público de resources/contenido/paginas.php
        $emailPublico = Pagina::leer(Pagina::CONTACTO)->email;
        $destino = config('contacto.email') ?: $emailPublico;

        try {
            if (! $destino) {
                throw new \RuntimeException('No hay adónde enviar el formulario de contacto: falta CONTACTO_EMAIL');
            }

            // Se envía en el momento (sin colas): si el correo falla, quien escribe se entera
            Notification::route('mail', $destino)
                ->notifyNow(new MensajeContacto($datos['nombre'], $datos['email'], $datos['mensaje']));
        } catch (Throwable $e) {
            report($e);

            return $volver->withInput()->with('error', 'No se ha podido enviar el mensaje. Inténtalo dentro de un rato'
                .($emailPublico ? " o escríbeme directamente a {$emailPublico}" : '').'.');
        }

        return $volver->with('success', '¡Gracias! Tu mensaje ha llegado. Te responderé lo antes posible.');
    }
}
