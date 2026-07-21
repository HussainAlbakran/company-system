<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\AuditHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        if (! Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ])) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        if (! $user->isApprovedAndActive()) {
            Auth::logout();

            return back()->withErrors([
                'email' => __('auth.pending_or_inactive'),
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        AuditHelper::log(
            'login',
            'user',
            $user->id,
            sprintf(
                'route=%s | ip=%s | user_agent=%s | timestamp=%s',
                $request->route()?->getName() ?? 'login',
                $request->ip() ?? '-',
                (string) $request->userAgent(),
                now()->toDateTimeString()
            ),
            (int) $user->id
        );
        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user) {
            AuditHelper::log(
                'logout',
                'user',
                $user->id,
                sprintf(
                    'route=%s | ip=%s | user_agent=%s | timestamp=%s',
                    $request->route()?->getName() ?? 'logout',
                    $request->ip() ?? '-',
                    (string) $request->userAgent(),
                    now()->toDateTimeString()
                ),
                (int) $user->id
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
