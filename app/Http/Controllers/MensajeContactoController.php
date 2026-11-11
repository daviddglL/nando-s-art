<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\EnviarMensajeContactoRequest;
use App\Models\Pagina;
use App\Models\User;
use App\Notifications\MensajeContacto;
use Illuminate\Support\Facades\Notification;

// Formulario de /contacto: cualquiera puede escribir al artista sin crear una cuenta
class MensajeContactoController extends Controller
{
    public function __invoke(EnviarMensajeContactoRequest $request)
    {
        $datos = $request->safe()->only(['nombre', 'email', 'mensaje']);
        $aviso = new MensajeContacto($datos['nombre'], $datos['email'], $datos['mensaje']);

        // CONTACTO_EMAIL en .env, si no el email público de la página Contacto y, si tampoco, los admins
        $destino = config('admin.email_contacto') ?: Pagina::leer(Pagina::CONTACTO)->email;
        if ($destino) {
            Notification::route('mail', $destino)->notify($aviso);
        } else {
            Notification::send(User::where('role', Role::Admin)->get(), $aviso);
        }

        return redirect()->to(route('paginas.contacto').'#formulario-contacto')
            ->with('success', '¡Gracias! Tu mensaje ha llegado. Te responderé lo antes posible.');
    }
}
