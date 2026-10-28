<?php

namespace App\Notifications;

use App\Models\Encargo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Aviso al cliente: su presupuesto ha cambiado de estado o tiene respuesta
class PresupuestoActualizado extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Encargo $encargo) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $encargo = $this->encargo;

        $mensaje = (new MailMessage)
            ->subject('Tu presupuesto en Nando\'s Art: '.mb_strtolower($encargo->status->label()))
            ->greeting('¡Hola, '.$encargo->user->name.'!')
            ->line('Tu presupuesto del '.$encargo->created_at?->format('d/m/Y').' está ahora: **'.$encargo->status->label().'**.');

        if ($encargo->respuesta) {
            $mensaje->line('Respuesta del artista:')->line('«'.$encargo->respuesta.'»');
        }

        if ($encargo->precio !== null) {
            $mensaje->line('Precio orientativo: '.$encargo->precioTexto());
        }

        return $mensaje
            ->action('Ver mi presupuesto', route('encargos.show', $encargo))
            ->salutation('Nando\'s Art');
    }
}
