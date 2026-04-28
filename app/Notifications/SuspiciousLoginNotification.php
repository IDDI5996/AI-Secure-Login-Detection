<?php

namespace App\Notifications;

use App\Models\LoginAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuspiciousLoginNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $loginAttempt;

    /**
     * Create a new notification instance.
     */
    public function __construct(LoginAttempt $loginAttempt)
    {
        $this->loginAttempt = $loginAttempt;
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
        $location = trim(implode(', ', array_filter([
            $this->loginAttempt->city,
            $this->loginAttempt->country
        ])));

        return (new MailMessage)
            ->subject('⚠️ Suspicious Login Attempt Detected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We detected a potentially suspicious login to your account.')
            ->line('**Details:**')
            ->line('• IP Address: ' . $this->loginAttempt->ip_address)
            ->line('• Location: ' . ($location ?: 'Unknown'))
            ->line('• Device: ' . $this->loginAttempt->device_type)
            ->line('• Browser: ' . $this->loginAttempt->browser)
            ->line('• Time: ' . $this->loginAttempt->attempted_at->format('M d, Y H:i:s'))
            ->line('If this was you, no further action is needed. However, we strongly recommend verifying your identity.')
            ->action('Verify My Account', route('login')) // You can change this to a direct verification link later
            ->line('If this was NOT you, please change your password immediately and contact support.');
    }

    /**
     * Get the array representation of the notification (for database storage, if needed).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'login_attempt_id' => $this->loginAttempt->id,
            'ip' => $this->loginAttempt->ip_address,
            'time' => $this->loginAttempt->attempted_at->toDateTimeString(),
        ];
    }
}