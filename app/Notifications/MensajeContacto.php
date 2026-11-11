<?php

namespace App\Notifications;

use App\Support\Markdown;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Aviso al artista: alguien le ha escrito desde el formulario de /contacto
class MensajeContacto extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $nombre, public string $email, public string $mensaje) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Mensaje de '.$this->nombre.' desde la web')
            // "Responder" en el correo contesta directamente a quien escribió
            ->replyTo($this->email, $this->nombre)
            ->greeting('¡Hola!')
            ->line(Markdown::escapar($this->nombre).' ('.Markdown::escapar($this->email).') te ha escrito desde la página de contacto:')
            ->line('«'.Markdown::escapar($this->mensaje).'»')
            ->line('Para contestar, responde a este correo.')
            ->salutation('Nando\'s Art');
    }
}
