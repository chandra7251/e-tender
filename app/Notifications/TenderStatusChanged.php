<?php
namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenderStatusChanged extends Notification
{
    use Queueable;

    public function __construct(
        public Tender $tender,
        public string $oldStatus,
        public string $newStatus,
        public string $descriptionMessage,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Kirim email jika user punya email & status penting
        if ($notifiable->email) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'open'       => 'Dibuka ',
            'aanwijzing' => 'Aanwijzing',
            'bidding'    => 'Bidding Dimulai',
            'closed'     => 'Ditutup',
            'finished'   => 'Selesai',
        ];
        $newLabel = $statusLabels[$this->newStatus] ?? ucfirst($this->newStatus);

        return (new MailMessage)
            ->subject("[ZETA] Tender Status Update: {$this->tender->title}")
            ->greeting("Halo, {$notifiable->name}!")
            ->line("Status tender **{$this->tender->title}** telah berubah.")
            ->line($this->descriptionMessage)
            ->line("Status baru: **{$newLabel}**")
            ->action('Lihat Tender', url('/api/tenders/' . $this->tender->id))
            ->line('Terima kasih telah menggunakan ZETA E-Procurement.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tender_id'    => $this->tender->id,
            'tender_title' => $this->tender->title,
            'old_status'   => $this->oldStatus,
            'new_status'   => $this->newStatus,
            'message'      => $this->descriptionMessage,
        ];
    }

    /**
     * FCM push payload — dipakai oleh channel kustom FcmChannel jika terpasang,
     * atau bisa di-dispatch manual via FcmService.
     */
    public function toFcm(object $notifiable): array
    {
        return [
            'title'  => "Status Tender Berubah: {$this->tender->title}",
            'body'   => $this->descriptionMessage,
            'data'   => [
                'type'      => 'tender_status_changed',
                'tender_id' => (string) $this->tender->id,
                'new_status' => $this->newStatus,
            ],
        ];
    }
}
