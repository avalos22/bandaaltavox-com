<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $code,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Código de verificación - Banda Alta Vox')
            ->greeting("Hola {$notifiable->name},")
            ->line('Tu código de verificación de dos factores es:')
            ->line("**{$this->code}**")
            ->line('Este código expira en 10 minutos.')
            ->line('Si no solicitaste este código, ignora este correo.')
            ->salutation('Banda Alta Vox');
    }
}
