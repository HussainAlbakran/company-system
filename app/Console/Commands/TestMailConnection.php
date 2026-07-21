<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Throwable;

class TestMailConnection extends Command
{
    protected $signature = 'mail:test {email? : Optional recipient (defaults to MAIL_FROM_ADDRESS)}';

    protected $description = 'Send a test email via the configured Laravel mailer (Gmail SMTP).';

    public function handle(): int
    {
        $to = $this->argument('email') ?: config('mail.from.address');

        if (! filter_var((string) $to, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid recipient email.');

            return self::FAILURE;
        }

        $this->info('Mailer: '.config('mail.default'));
        $this->info('Host: '.config('mail.mailers.smtp.host'));
        $this->info('Port: '.config('mail.mailers.smtp.port'));
        $this->info('From: '.config('mail.from.address'));
        $this->info('To: '.$to);

        try {
            Mail::raw(
                'اختبار إرسال البريد من نظام التقدم — Laravel Mail يعمل بنجاح.',
                function ($message) use ($to): void {
                    $message->to($to)->subject('اختبار بريد النظام');
                }
            );
        } catch (Throwable $exception) {
            $this->error('Send failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Test email sent successfully.');

        return self::SUCCESS;
    }
}
