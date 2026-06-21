<?php

namespace App\Notifications;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TenderStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $tender;
    public $oldStatus;
    public $newStatus;
    public $descriptionMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Tender $tender, string $oldStatus, string $newStatus, string $descriptionMessage)
    {
        $this->tender = $tender;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->descriptionMessage = $descriptionMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tender_id' => $this->tender->id,
            'tender_title' => $this->tender->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => $this->descriptionMessage,
        ];
    }
}
