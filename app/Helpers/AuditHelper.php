<?php

namespace App\Helpers;

use App\Models\AuditLog;
use App\Models\User;

class AuditHelper
{
    /**
     * Noisy actions skipped only for super_admin (own actions).
     * All other roles keep full logging. CRUD + view always stored.
     *
     * @var list<string>
     */
    private const SUPER_ADMIN_SKIPPED_ACTIONS = [
        'ai_request',
        'ai_response',
        'ai_opened',
        'system_action',
    ];

    public static function log($action, $model, $model_id = null, $description = null, ?int $userId = null): void
    {
        try {
            $uid = $userId ?? auth()->id();

            if (! self::shouldStoreForActor((string) $action, $uid)) {
                return;
            }

            AuditLog::create([
                'user_id' => $uid,
                'action' => $action,
                'model' => $model,
                'model_id' => $model_id,
                'description' => self::redactSensitiveDescription($description),
            ]);
        } catch (\Throwable $e) {
            // Never break business flow when audit logging fails.
        }
    }

    /**
     * super_admin: skip noisy AI/system noise only; other roles keep full logging.
     */
    private static function shouldStoreForActor(string $action, ?int $userId): bool
    {
        if (! $userId) {
            return true;
        }

        $user = User::query()->find($userId);

        if (! $user || ! $user->isSuperAdmin()) {
            return true;
        }

        return ! in_array($action, self::SUPER_ADMIN_SKIPPED_ACTIONS, true);
    }

    private static function redactSensitiveDescription(?string $description): ?string
    {
        if ($description === null || $description === '') {
            return $description;
        }

        $patterns = [
            '/\b(password|current_password|password_confirmation)\s*=\s*[^\s|]+/iu' => '$1=[redacted]',
            '/\b(token|api_key|apikey|secret|client_secret|authorization)\s*=\s*[^\s|]+/iu' => '$1=[redacted]',
        ];

        $out = $description;
        foreach ($patterns as $pattern => $replacement) {
            $out = preg_replace($pattern, $replacement, $out) ?? $out;
        }

        return $out;
    }
}
