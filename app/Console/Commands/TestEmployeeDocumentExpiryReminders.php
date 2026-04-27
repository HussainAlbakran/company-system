<?php

namespace App\Console\Commands;

use App\Services\EmployeeDocumentExpiryReminderService;
use Illuminate\Console\Command;

class TestEmployeeDocumentExpiryReminders extends Command
{
    protected $signature = 'notifications:test-employee-document-expiry';

    protected $description = '[Test] Run EmployeeDocumentExpiryReminderService::handle() (same entry point as the daily scheduler).';

    public function handle(EmployeeDocumentExpiryReminderService $service): int
    {
        $service->handle();
        $this->info('EmployeeDocumentExpiryReminderService::handle() completed.');

        return self::SUCCESS;
    }
}
