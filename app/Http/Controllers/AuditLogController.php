<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    protected function authorizeAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'غير مصرح لك بالوصول إلى سجل العمليات.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

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
            $logsQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $logsQuery->whereDate('created_at', '<=', $request->date_to);
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