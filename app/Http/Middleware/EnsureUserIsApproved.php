<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (($user->approval_status ?? null) !== 'approved' || ! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'حسابك غير مفعل أو بانتظار الموافقة من الإدارة.',
            ]);
        }

        return $next($request);
    }
}