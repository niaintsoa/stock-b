<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeCustomerNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Bienvenue ! Créez votre mot de passe')
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Votre compte client a été créé avec succès.')
            ->line('Pour pouvoir vous connecter à votre espace, veuillez définir votre mot de passe en cliquant sur le bouton ci-dessous :')
            ->action('Définir mon mot de passe', $url)
            ->line('Ce lien expirera dans 60 minutes.')
            ->line('Si vous n\'avez pas demandé ce compte, aucune action n\'est requise.');
    }
}
