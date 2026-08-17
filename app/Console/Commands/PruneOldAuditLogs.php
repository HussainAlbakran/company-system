<?php

namespace App\Console\Commands;

use App\Services\AuditLogPruneService;
use Illuminate\Console\Command;

class PruneOldAuditLogs extends Command
{
    protected $signature = 'audit:prune-old-logs';

    protected $description = 'Delete audit log rows older than 30 days.';

    public function handle(AuditLogPruneService $service): int
    {
        $deleted = $service->prune();

        $this->info("Deleted {$deleted} audit log row(s) older than ".AuditLogPruneService::RETENTION_DAYS.' days.');

        return self::SUCCESS;
    }
}
