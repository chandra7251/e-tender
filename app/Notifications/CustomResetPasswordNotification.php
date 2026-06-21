<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    // Konstruktor buat nerima token dari controller pas dipanggil
    public function __construct($token)
    {
        $this->token = $token;
    }

    // Kita cuma pake via email (bisa diubah kalo mau notif via DB atau SMS kedepannya)
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    // Isi emailnya dirakit di sini pake MailMessage bawaan Laravel
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Reset Password Aplikasi ZETA')
                    ->greeting('Halo!')
                    ->line('Anda menerima email ini karena kami menerima permintaan reset password untuk akun Anda.')
                    ->line('Silakan copy token unik di bawah ini dan paste di form Reset Password di aplikasi ZETA:')
                    ->line('**' . $this->token . '**')
                    ->line('Token ini akan kedaluwarsa dalam 60 menit.')
                    ->line('Jika Anda tidak merasa melakukan request ini, Anda bisa mengabaikan pesan ini.')
                    ->salutation('Terima kasih, ZETA (Zona E-Procurement Tender Akses)');
    }

    // Kalo disimpen ke database bentuknya array (tapi kita ga pake ini buat sekarang)
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
