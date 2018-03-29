<?php

namespace Intranet\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MyResetPassword extends ResetPassword
{
    use Queueable;

    
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Estas rebent aquest email perquè hem rebut una notificació de canvi de contrasenya per al teu compte.')
            ->action('Reset Password', route('password.reset', $this->token))
            ->line('Si no ho has fet, no cal que faces res més.');
    }
}
