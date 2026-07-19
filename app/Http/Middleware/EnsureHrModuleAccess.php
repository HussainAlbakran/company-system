<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHrModuleAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->canAccessHRModule()) {
            abort(403, 'هذه الصفحة متاحة لمدير النظام والإدارة وموارد البشرية فقط.');
        }

        return $next($request);
    }
}
