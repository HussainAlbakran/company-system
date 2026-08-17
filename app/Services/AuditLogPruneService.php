<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AuditLogPruneService
{
    public const RETENTION_DAYS = 30;

    public const CHUNK_SIZE = 500;

    /**
     * Delete audit rows whose created_at is older than 30 days.
     * Chunked deletes avoid long table locks.
     */
    public function prune(int $chunkSize = self::CHUNK_SIZE, ?int $maxChunks = null): int
    {
        $chunkSize = max(1, min(1000, $chunkSize));
        $cutoff = now()->subDays(self::RETENTION_DAYS);
        $deleted = 0;
        $chunks = 0;

        do {
            $count = AuditLog::query()
                ->where('created_at', '<=', $cutoff)
                ->limit($chunkSize)
                ->delete();

            $deleted += $count;
            $chunks++;
        } while ($count > 0 && ($maxChunks === null || $chunks < $maxChunks));

        return $deleted;
    }

    /**
     * At most once every 6 hours: remove stale rows without blocking the UI.
     */
    public function pruneIfDue(int $maxChunks = 20): int
    {
        if (! Cache::add('audit_logs:prune:due', 1, now()->addHours(6))) {
            return 0;
        }

        try {
            return $this->prune(self::CHUNK_SIZE, $maxChunks);
        } catch (\Throwable $e) {
            Log::warning('Audit log prune failed: '.$e->getMessage());

            return 0;
        }
    }
}
