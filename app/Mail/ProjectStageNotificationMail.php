<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectStageNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public string $viewName,
        public string $emailSubject,
    ) {
    }

    public function envelope(): Envelope
    {
        $subject = trim(str_replace(["\r", "\n"], ' ', $this->emailSubject));

        return new Envelope(
            subject: $subject !== '' ? $subject : config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->viewName,
            with: [
                'project' => $this->project,
            ],
        );
    }
}
