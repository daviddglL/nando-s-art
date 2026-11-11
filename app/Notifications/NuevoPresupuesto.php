<?php

namespace App\Notifications;

use App\Models\Encargo;
use App\Support\Markdown;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Aviso al artista: un cliente ha pedido un presupuesto
class NuevoPresupuesto extends Notification implements ShouldQueue
{
    use Queueable;

    // Si el presupuesto se borra antes de enviarse el aviso, el trabajo se descarta en vez de fallar
    public bool $deleteWhenMissingModels = true;

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

        return (new MailMessage)
            ->subject('Nuevo presupuesto de '.$encargo->user->name)
            ->greeting('¡Hola!')
            ->line($encargo->user->name.' ('.$encargo->user->email.') ha pedido un presupuesto.')
            ->line('Técnica: '.($encargo->styleEnum()?->label() ?? $encargo->style).' · Tema: '.($encargo->categoryEnum()?->label() ?? $encargo->category))
            ->line('Medidas: '.$encargo->height.' x '.$encargo->width.' cm')
            ->line('«'.Markdown::escapar($encargo->description).'»')
            ->action('Ver y responder', route('encargos.show', $encargo))
            ->salutation('Nando\'s Art');
    }
}
