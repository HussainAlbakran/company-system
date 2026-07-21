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

        if (! $user->isApprovedAndActive()) {
            return redirect()->route('login')->withErrors([
                'email' => __('auth.pending_or_inactive'),
            ]);
        }

        return $next($request);
    }
}
