<?php

namespace App\Helpers;

use App\Models\AuditLog;
use Carbon\CarbonInterface;

class AuditLabelHelper
{
    public static function actionLabel(?string $action): string
    {
        if (! $action) {
            return '-';
        }

        $key = 'audit.actions.'.$action;
        $trans = __($key);

        return $trans === $key ? $action : $trans;
    }

    public static function modelLabel(?string $model): string
    {
        if (! $model) {
            return '-';
        }

        $basename = class_basename($model);
        $key = 'audit.models.'.$basename;
        $trans = __($key);

        return $trans === $key ? $basename : $trans;
    }

    public static function sectionLabel(?string $route, ?string $model = null): string
    {
        if ($route) {
            $key = 'audit.routes.'.$route;
            $trans = __($key);
            if ($trans !== $key) {
                return $trans;
            }

            $smart = self::smartRouteLabel($route);
            if ($smart !== null) {
                return $smart;
            }
        }

        if ($model) {
            return self::modelLabel($model);
        }

        return __('audit.unknown');
    }

    public static function description(AuditLog $log): string
    {
        $meta = self::parseMetadata($log->description);
        $route = $meta['route'] ?? null;
        $section = self::sectionLabel($route, $log->model);

        return match ($log->action) {
            'login' => __('audit.descriptions.login'),
            'logout' => __('audit.descriptions.logout'),
            'role_changed' => isset($meta['old_role'], $meta['new_role'])
                ? __('audit.descriptions.role_changed', [
                    'old' => self::roleLabel($meta['old_role']),
                    'new' => self::roleLabel($meta['new_role']),
                ])
                : __('audit.descriptions.role_changed_fallback'),
            'unauthorized_access' => __('audit.descriptions.unauthorized_access'),
            'ai_opened' => __('audit.descriptions.ai_opened_fallback'),
            'ai_request' => __('audit.descriptions.ai_request_fallback'),
            'ai_response' => __('audit.descriptions.ai_response_fallback'),
            'password_changed' => __('audit.descriptions.password_changed_fallback'),
            'file_uploaded' => __('audit.descriptions.file_uploaded'),
            'user_employee_link_updated' => __('audit.descriptions.user_employee_link_updated'),
            'profile_updated' => __('audit.descriptions.profile_updated'),
            'read' => __('audit.descriptions.http_read', ['page' => $section]),
            'create' => $route
                ? __('audit.descriptions.http_create', ['page' => $section])
                : self::plainOrFallback($log, __('audit.descriptions.http_create', ['page' => $section])),
            'update' => $route
                ? __('audit.descriptions.http_update', ['page' => $section])
                : self::plainOrFallback($log, __('audit.descriptions.http_update', ['page' => $section])),
            'delete' => $route
                ? __('audit.descriptions.http_delete', ['page' => $section])
                : self::plainOrFallback($log, __('audit.descriptions.http_delete', ['page' => $section])),
            'approve' => __('audit.descriptions.approve', ['page' => $section]),
            'reject' => __('audit.descriptions.reject', ['page' => $section]),
            'suspend' => __('audit.descriptions.suspend', ['page' => $section]),
            'reactivate' => __('audit.descriptions.reactivate', ['page' => $section]),
            default => self::defaultDescription($log, $meta, $section),
        };
    }

    public static function formatDate(?CarbonInterface $date): string
    {
        return $date ? $date->timezone(config('app.timezone'))->format('Y-m-d') : '-';
    }

    public static function formatTime(?CarbonInterface $date): string
    {
        return $date ? $date->timezone(config('app.timezone'))->format('h:i A') : '-';
    }

    /**
     * @return array<string, string>
     */
    public static function parseMetadata(?string $description): array
    {
        $result = [];
        if (! $description) {
            return $result;
        }

        foreach (explode('|', $description) as $part) {
            $part = trim($part);
            if (! str_contains($part, '=')) {
                continue;
            }
            [$k, $v] = array_map('trim', explode('=', $part, 2));
            if ($k !== '') {
                $result[$k] = $v;
            }
        }

        return $result;
    }

    private static function roleLabel(string $role): string
    {
        $key = 'roles.'.$role;
        $trans = __($key);

        return $trans === $key ? $role : $trans;
    }

    private static function smartRouteLabel(string $route): ?string
    {
        $parts = explode('.', $route);
        if (count($parts) < 2) {
            return null;
        }

        $suffix = array_pop($parts);
        $resourceKey = implode('.', $parts);

        $resourceLabel = __('audit.resources.'.$resourceKey);
        if ($resourceLabel === 'audit.resources.'.$resourceKey) {
            $resourceLabel = __('audit.resources.'.str_replace('-', '_', $resourceKey));
            if ($resourceLabel === 'audit.resources.'.str_replace('-', '_', $resourceKey)) {
                $resourceLabel = null;
            }
        }

        $actionLabel = __('audit.route_actions.'.$suffix);
        if ($actionLabel === 'audit.route_actions.'.$suffix) {
            $actionLabel = $suffix;
        }

        if ($resourceLabel) {
            return $resourceLabel.' — '.$actionLabel;
        }

        return null;
    }

    private static function plainOrFallback(AuditLog $log, string $fallback): string
    {
        $description = trim((string) $log->description);
        if ($description === '' || str_contains($description, 'route=')) {
            return $fallback;
        }

        return $description;
    }

    /**
     * @param  array<string, string>  $meta
     */
    private static function defaultDescription(AuditLog $log, array $meta, string $section): string
    {
        $actionKey = 'audit.descriptions.action_generic';
        $generic = __($actionKey, [
            'action' => self::actionLabel($log->action),
            'page' => $section,
        ]);

        if ($generic !== $actionKey) {
            return $generic;
        }

        return self::plainOrFallback($log, self::actionLabel($log->action).' — '.$section);
    }
}
