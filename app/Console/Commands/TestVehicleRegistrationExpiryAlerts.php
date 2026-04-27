<?php

namespace App\Console\Commands;

use App\Services\ExpiryAlertService;
use Illuminate\Console\Command;

class TestVehicleRegistrationExpiryAlerts extends Command
{
    protected $signature = 'notifications:test-vehicle-registration';

    protected $description = '[Test] Run ExpiryAlertService::handle() (same entry point as the daily scheduler).';

    public function handle(ExpiryAlertService $service): int
    {
        $service->handle();
        $this->info('ExpiryAlertService::handle() completed.');

        return self::SUCCESS;
    }
}
