<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogPruneService;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected function authorizeAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->canViewAuditLogs()) {
            abort(403, __('audit.abort_unauthorized'));
        }
    }

    public function index(Request $request, AuditLogPruneService $pruneService)
    {
        $this->authorizeAdmin();

        try {
            $pruneService->prune(AuditLogPruneService::CHUNK_SIZE, 40);
        } catch (\Throwable $e) {
            // Never block the audit page if cleanup fails.
        }

        $logsQuery = AuditLog::with('user')->latest();

        if ($request->filled('user_id')) {
            $logsQuery->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $logsQuery->where('action', $request->action);
        }

        if ($request->filled('model')) {
            $logsQuery->where('model', $request->model);
        }

        if ($request->filled('date_from')) {
            $from = \Carbon\Carbon::parse($request->date_from, config('app.timezone'))->startOfDay();
            $logsQuery->where('created_at', '>=', $from);
        }

        if ($request->filled('date_to')) {
            $to = \Carbon\Carbon::parse($request->date_to, config('app.timezone'))->endOfDay();
            $logsQuery->where('created_at', '<=', $to);
        }

        $logs = $logsQuery->paginate(20)->withQueryString();

        $users = User::orderBy('name')->get();

        $actions = AuditLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $models = AuditLog::query()
            ->select('model')
            ->whereNotNull('model')
            ->distinct()
            ->orderBy('model')
            ->pluck('model');

        return view('audit.index', compact(
            'logs',
            'users',
            'actions',
            'models'
        ));
    }
}