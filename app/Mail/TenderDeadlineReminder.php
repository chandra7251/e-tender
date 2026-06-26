<?php
namespace App\Mail;

use App\Models\Tender;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenderDeadlineReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tender $tender,
        public string $reminderType, // "24h" | "1h"
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->reminderType === '1h' ? '1 Jam Lagi' : '24 Jam Lagi';
        return new Envelope(
            subject: "[ZETA] Deadline Bidding {$label}: {$this->tender->title}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.tender-deadline-reminder');
    }
}
