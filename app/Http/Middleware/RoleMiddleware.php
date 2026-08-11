<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, __('common.unauthorized'));
        }

        // مدير النظام فقط يتجاوز قيود الأدوار؛ الإدارة العليا تبقى على المسارات المصرّح لها صراحةً.
        if (method_exists($user, 'isSystemAdmin') && $user->isSystemAdmin()) {
            return $next($request);
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, __('common.forbidden'));
        }

        return $next($request);
    }
}