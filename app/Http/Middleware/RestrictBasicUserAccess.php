<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Limits users with role "user" to dashboard, leave request, support, and profile routes only.
 */
class RestrictBasicUserAccess
{
    /** @var list<string> */
    protected array $allowedRouteNames = [
        'dashboard',
        'leaves.create',
        'leaves.store',
        'support.index',
        'technical-support.index',
        'profile.show',
        'profile.update',
        'profile.destroy',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isBasicUser()) {
            return $next($request);
        }

        $name = $request->route()?->getName();

        if ($name && in_array($name, $this->allowedRouteNames, true)) {
            return $next($request);
        }

        return redirect()
            ->route('dashboard')
            ->with('error', __('roles.basic_user_denied'));
    }
}
