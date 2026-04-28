<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerificationRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        // No specific data needed for this generic notification
    }

    /**
     * Get the notification's delivery channels.
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
            ->subject('🔐 Action Required: Verify Your Identity')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Due to a suspicious login attempt, we require you to verify your identity before you can access your account.')
            ->line('Please follow the link below to complete the verification process.')
            ->action('Verify Now', route('login')) // You can change this to a dedicated verification route later
            ->line('This verification is required for your security. If you did not attempt to log in, please secure your account immediately.');
    }

    /**
     * Get the array representation of the notification (for database storage, if needed).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Identity verification required due to suspicious activity.'
        ];
    }
}