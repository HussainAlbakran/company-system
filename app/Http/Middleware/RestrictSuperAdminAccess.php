<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Limits "super_admin" (الإدارة العليا) to an explicit allowlist of routes.
 */
class RestrictSuperAdminAccess
{
    /** @var list<string> */
    protected array $allowedRouteNames = [
        // Dashboard
        'dashboard',
        'dashboard.dismiss',

        // Project reports
        'project-reports.board',
        'project-reports.archive',
        'project-reports.show',
        'project-reports.create',
        'project-reports.store',
        'project-reports.download',
        'project-reports.destroy',
        'project-reports.complete',
        'project-reports.material-attachment',
        'project-reports.completion-letter',
        'project-reports.contract-file',

        // Leave management (approval only — no personal leave request)
        'leaves.index',
        'leaves.approve',
        'leaves.reject',

        // Administration center
        'administration.index',
        'administration.assignments',
        'administration.updates',

        // Profile
        'profile.show',
        'profile.update',
        'profile.destroy',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSuperAdmin()) {
            return $next($request);
        }

        $name = $request->route()?->getName();

        if ($name && in_array($name, $this->allowedRouteNames, true)) {
            return $next($request);
        }

        return redirect()
            ->route('dashboard')
            ->with('error', __('roles.super_admin_route_denied'));
    }
}
