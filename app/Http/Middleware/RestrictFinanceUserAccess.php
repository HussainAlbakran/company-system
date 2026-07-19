<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Limits users with role "finance" to explicitly allowed routes only.
 */
class RestrictFinanceUserAccess
{
    /** @var list<string> */
    protected array $allowedRouteNames = [
        'dashboard',
        'dashboard.dismiss',
        'cash-flow.index',
        'cash-flow.maintenance',
        'cash-flow.store',
        'cash-flow.destroy',
        'financial-custodies.index',
        'financial-custodies.create',
        'financial-custodies.store',
        'financial-custodies.show',
        'financial-custodies.settle-full',
        'financial-custodies.settle-partial',
        'financial-custodies.return-remaining',
        'custody-settlements.index',
        'custody-settlements.records',
        'custody-settlements.open',
        'custody-settlements.show',
        'custody-settlements.update',
        'custody-settlements.approve',
        'custody-settlements.upload-attachment',
        'custody-settlements.attachment',
        'custody-invoices.index',
        'custody-invoices.store',
        'custody-invoices.update',
        'custody-invoices.attachment',
        'employee-advances.index',
        'employee-advances.create',
        'employee-advances.store',
        'ai.page',
        'ai.ask',
        'ai.clear',
        'technical-support.index',
        'support.index',
        'profile.show',
        'profile.update',
        'profile.destroy',
        'leaves.create',
        'leaves.store',
        'general-purchases.index',
        'general-purchases.create',
        'general-purchases.store',
        'general-purchases.edit',
        'general-purchases.update',
        'general-purchases.destroy',
        'purchases.index',
        'purchases.create',
        'purchases.store',
        'purchases.show',
        'purchases.edit',
        'purchases.update',
        'purchases.destroy',
        'purchases.material-requests.index',
        'purchases.material-requests.show',
        'purchases.material-requests.status',
        'purchases.material-requests.approve',
        'purchases.material-requests.reject',
        'purchases.material-requests.convert',
        'architect.material-requests.attachment',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isFinance()) {
            return $next($request);
        }

        $name = $request->route()?->getName();

        if ($name && in_array($name, $this->allowedRouteNames, true)) {
            return $next($request);
        }

        return redirect()
            ->route('dashboard')
            ->with('error', __('roles.finance_route_denied'));
    }
}
