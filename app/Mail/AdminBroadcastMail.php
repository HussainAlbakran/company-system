<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBroadcastMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<string, string>|null $details Label => value pairs rendered as a details table.
     */
    public function __construct(
        public string $emailTitle,
        public string $emailBody,
        public ?array $details = null,
        public string $accentColor = '#2563eb',
        public ?string $attachmentPath = null,
        public ?string $attachmentName = null,
    ) {
    }

    public function envelope(): Envelope
    {
        // Strip CR/LF to prevent any header injection through the subject.
        $subject = trim(str_replace(["\r", "\n"], ' ', $this->emailTitle));

        return new Envelope(subject: $subject !== '' ? $subject : config('app.name'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_broadcast',
            with: [
                'emailTitle' => $this->emailTitle,
                'emailBody' => $this->emailBody,
                'details' => $this->details,
                'accentColor' => $this->accentColor,
            ],
        );
    }

    public function attachments(): array
    {
        if (! $this->attachmentPath) {
            return [];
        }

        $attachment = Attachment::fromStorageDisk('local', $this->attachmentPath);

        if ($this->attachmentName) {
            $attachment->as($this->attachmentName);
        }

        return [$attachment];
    }
}
