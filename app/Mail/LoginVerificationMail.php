<?php

namespace App\Mail;

use App\Models\User;
use App\Models\LoginAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    public $loginAttempt;
    public $analysis;

    public function __construct(User $user, string $code, LoginAttempt $loginAttempt, array $analysis)
    {
        $this->user = $user;
        $this->code = $code;
        $this->loginAttempt = $loginAttempt;
        $this->analysis = $analysis;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Login Verification Required - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-verification',
        );
    }
}