<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminInvitationNotification extends Notification
{
    use Queueable;

    public $invitation;

    public function __construct($invitation)
    {
        $this->invitation = $invitation;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = route('admin.verify.invitation', ['token' => $this->invitation->token]);

        return (new MailMessage)
            ->subject('Verify & Activate Your Admin Account')
            ->greeting('Hello ' . $this->invitation->name . ',')
            ->line('You have been invited to join the SD Inventory System as an administrator.')
            ->line('To complete your registration and activate your account, click the button below:')
            ->action('Verify Email & Join as Admin', $verificationUrl)
            ->line('If you did not expect this invitation, no action is required.');
    }
}