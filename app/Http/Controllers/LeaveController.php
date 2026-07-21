<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    protected function authorizeHrApprovals(): void
    {
        if (! auth()->check() || ! auth()->user()->canManageLeaveApprovals()) {
            abort(403, __('common.unauthorized'));
        }
    }

    public function index()
    {
        $this->authorizeHrApprovals();

        $leaves = Leave::with('employee')->latest()->get();

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $user = auth()->user();
        $isPrivileged = $user && $user->canAccessHRModule();

        if ($isPrivileged) {
            $employees = Employee::query()->orderBy('name')->get();
        } else {
            $employee = $user?->employee;
            if (! $employee) {
                return back()->withErrors(['error' => __('leaves.error_employee_required')]);
            }
            $employees = collect([$employee]);
        }

        return view('leaves.create', [
            'employees' => $employees,
            'canChooseEmployee' => $isPrivileged,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $isPrivileged = $user && $user->canAccessHRModule();

        $rules = [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ];

        if ($isPrivileged) {
            $rules['employee_id'] = 'required|exists:employees,id';
        }

        $validated = $request->validate($rules);

        if ($isPrivileged) {
            $employee = Employee::findOrFail($validated['employee_id']);
        } else {
            if (! $user?->employee) {
                return back()->withErrors(['error' => __('leaves.error_employee_required')]);
            }
            $employee = $user->employee;
        }

        $days = Carbon::parse($validated['start_date'])
            ->diffInDays(Carbon::parse($validated['end_date'])) + 1;

        if ($employee->leave_balance < $days) {
            return back()->with('error', __('leaves.error_insufficient_balance', ['balance' => $employee->leave_balance]));
        }

        $payload = [
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'days' => $days,
            'reason' => $validated['reason'] ?? null,
            'status' => 'pending',
            'approved_at' => null,
            'is_deducted' => false,
            'deducted_at' => null,
        ];

        $payload['employee_id'] = $isPrivileged
            ? $employee->id
            : $user->employee->id;

        Leave::create($payload);

        return back()->with('success', __('leaves.success_submitted'));
    }

    public function approve($id)
    {
        $this->authorizeHrApprovals();

        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', __('leaves.error_already_processed'));
        }

        $deducted = DB::transaction(function () use ($leave) {
            $leave = Leave::query()->whereKey($leave->id)->lockForUpdate()->firstOrFail();
            if ($leave->status !== 'pending') {
                return false;
            }

            $employee = Employee::query()->whereKey($leave->employee_id)->lockForUpdate()->first();
            if (! $employee || $employee->leave_balance < $leave->days) {
                return null;
            }

            $employee->decrement('leave_balance', (int) $leave->days);

            $leave->update([
                'status' => 'approved',
                'approved_at' => now(),
                'is_deducted' => true,
                'deducted_at' => now(),
            ]);

            return true;
        });

        if ($deducted === false) {
            return back()->with('error', __('leaves.error_already_processed'));
        }

        if ($deducted === null) {
            return back()->with('error', __('leaves.error_insufficient_simple'));
        }

        return back()->with('success', __('leaves.success_approved'));
    }

    public function reject($id)
    {
        $this->authorizeHrApprovals();

        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', __('leaves.error_already_processed'));
        }

        $leave->status = 'rejected';
        $leave->approved_at = null;
        $leave->is_deducted = false;
        $leave->deducted_at = null;
        $leave->save();

        return back()->with('success', __('leaves.success_rejected'));
    }
}