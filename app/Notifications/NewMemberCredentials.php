<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewMemberCredentials extends Notification
{
    use Queueable;

    public $user;
    public $password;
    public $community;

    /**
     * Create a new notification instance.
     */
    public function __construct($user, $password, $community = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->community = $community;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Vos identifiants de connexion')
            ->greeting('Bienvenue sur la plateforme !')
            ->line('Voici vos identifiants de connexion :')
            ->line('Email : ' . $this->user->email)
            ->line('Mot de passe : ' . $this->password)
            ->action('Se connecter', url('/login'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
