<?php

namespace App\Mail;

use App\Models\SalesContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProjectMovedToDesignMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SalesContract $contract)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'مشروع جديد وصل إلى قسم التصاميم',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.project_to_design',
            with: [
                'contract' => $this->contract,
            ],
        );
    }
}
