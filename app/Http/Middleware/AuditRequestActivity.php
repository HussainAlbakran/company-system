<?php

namespace App\Http\Middleware;

use App\Helpers\AuditHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditRequestActivity
{
    /**
     * Routes where generic http_request audit is skipped for super_admin (AI/navigation noise).
     *
     * @var list<string>
     */
    private const SUPER_ADMIN_SKIP_HTTP_AUDIT_ROUTES = [
        'ai.page',
        'ai.ask',
        'ai.clear',
    ];

    private const SENSITIVE_QUERY_KEYS = [
        'password',
        'password_confirmation',
        'token',
        'api_key',
        'apikey',
        'secret',
        'client_secret',
        'authorization',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $user = $request->user();
        $method = strtoupper($request->method());

        if ($method === 'GET' && ! $this->shouldLogReadRoute($request)) {
            if ((int) $response->getStatusCode() === 403) {
                $this->logUnauthorizedAttempt($request, $user);
            }

            return $response;
        }

        $action = match ($method) {
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'read',
        };

        $routeName = $request->route()?->getName() ?? 'unnamed';
        $safeUrl = $this->safeUrl($request);
        $description = sprintf(
            'route=%s | url=%s | method=%s | ip=%s | user_agent=%s | timestamp=%s',
            $routeName,
            $safeUrl,
            $method,
            $request->ip() ?? '-',
            (string) $request->userAgent(),
            now()->timezone(config('app.timezone'))->toDateTimeString()
        );

        if ($user && ! $this->shouldSkipGenericHttpAudit($user, $routeName)) {
            AuditHelper::log($action, 'http_request', null, $description, (int) $user->id);
        }

        if ((int) $response->getStatusCode() === 403) {
            $this->logUnauthorizedAttempt($request, $user);
        }

        return $response;
    }

    private function shouldLogReadRoute(Request $request): bool
    {
        $routeName = $request->route()?->getName();
        if (! $routeName) {
            return false;
        }

        $skipReads = [
            'home',
            'locale.switch',
            'up',
        ];

        return ! in_array($routeName, $skipReads, true);
    }

    private function safeUrl(Request $request): string
    {
        $query = $request->query();
        foreach ($query as $key => $value) {
            if (in_array(strtolower((string) $key), self::SENSITIVE_QUERY_KEYS, true)) {
                $query[$key] = '[redacted]';
            }
        }

        $base = $request->url();
        if (empty($query)) {
            return $base;
        }

        return $base.'?'.http_build_query($query);
    }

    private function shouldSkipGenericHttpAudit($user, string $routeName): bool
    {
        if (! $user || ! method_exists($user, 'isSuperAdmin') || ! $user->isSuperAdmin()) {
            return false;
        }

        return in_array($routeName, self::SUPER_ADMIN_SKIP_HTTP_AUDIT_ROUTES, true);
    }

    private function logUnauthorizedAttempt(Request $request, $user): void
    {
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return;
        }

        $routeName = $request->route()?->getName() ?? 'unnamed';
        AuditHelper::log(
            'unauthorized_access',
            'http_request',
            null,
            sprintf(
                'route=%s | url=%s | method=%s | ip=%s | user_agent=%s | timestamp=%s',
                $routeName,
                $this->safeUrl($request),
                strtoupper($request->method()),
                $request->ip() ?? '-',
                (string) $request->userAgent(),
                now()->timezone(config('app.timezone'))->toDateTimeString()
            ),
            $user ? (int) $user->id : null
        );
    }
}
