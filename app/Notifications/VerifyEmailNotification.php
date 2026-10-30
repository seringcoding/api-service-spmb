<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
    // app/Notifications/VerifyEmailNotification.php

    public function toMail(object $notifiable): MailMessage
    {
        // 1. Generate Signed Route asli Laravel (menggunakan http://127.0.0.1:8000)
        $verify_url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification())
            ]
        );

        // 2. Ambil Query Parameters (?expires=xxx&signature=yyy)
        $parsedUrl = parse_url($verify_url);
        $queryParams = $parsedUrl['query'] ?? '';

        $id = $notifiable->getKey();
        $hash = sha1($notifiable->getEmailForVerification());

        // 3. Buat URL mengarah ke React (5173)
        $frontendURL = "http://localhost:5173/email/verify/{$id}/{$hash}?{$queryParams}";

        return (new MailMessage)
            ->subject('Verifikasi Akun SPMB')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Silakan klik tombol di bawah ini untuk memverifikasi akun anda.')
            ->action('Verifikasi', $frontendURL);
    }
}
