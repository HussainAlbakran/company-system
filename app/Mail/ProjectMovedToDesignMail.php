<?php

namespace App\Mail;

use App\Models\SalesContract;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectMovedToDesignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contract;

    public function __construct(SalesContract $contract)
    {
        $this->contract = $contract;
    }

    public function build()
    {
        return $this->subject('مشروع جديد للتصاميم')
            ->view('emails.project_to_design');
    }
}