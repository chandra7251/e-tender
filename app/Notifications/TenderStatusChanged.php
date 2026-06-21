<?php
namespace App\Notifications;
use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
class TenderStatusChanged extends Notification
{
    use Queueable;
    public $tender;
    public $oldStatus;
    public $newStatus;
    public $descriptionMessage;
    public function __construct(Tender $tender, string $oldStatus, string $newStatus, string $descriptionMessage)
    {
        $this->tender = $tender;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->descriptionMessage = $descriptionMessage;
    }
    public function via(object $notifiable): array
    {
        return ['database'];
    }
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
