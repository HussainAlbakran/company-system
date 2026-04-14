<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('employee')->latest()->get();

        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $employees = Employee::all();

        return view('leaves.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        $employee = Employee::findOrFail($request->employee_id);

        $days = Carbon::parse($request->start_date)
            ->diffInDays(Carbon::parse($request->end_date)) + 1;

        if ($employee->leave_balance < $days) {
            return back()->with('error', 'رصيد الإجازات غير كافي (الرصيد الحالي: ' . $employee->leave_balance . ')');
        }

        Leave::create([
            'employee_id' => $request->employee_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'reason' => $request->reason,
            'status' => 'pending',
            'approved_at' => null,
            'is_deducted' => false,
            'deducted_at' => null,
        ]);

        return back()->with('success', 'تم تقديم طلب الإجازة بنجاح');
    }

    public function approve($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذا الطلب مسبقًا');
        }

        $employee = $leave->employee;

        if ($employee->leave_balance < $leave->days) {
            return back()->with('error', 'رصيد الإجازات غير كافي');
        }

        $leave->status = 'approved';
        $leave->approved_at = now();
        $leave->is_deducted = false;
        $leave->deducted_at = null;
        $leave->save();

        return back()->with('success', 'تم اعتماد الإجازة، وسيتم خصم الرصيد تلقائيًا عند بداية الإجازة');
    }

    public function reject($id)
    {
        $leave = Leave::findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذا الطلب مسبقًا');
        }

        $leave->status = 'rejected';
        $leave->approved_at = null;
        $leave->is_deducted = false;
        $leave->deducted_at = null;
        $leave->save();

        return back()->with('success', 'تم رفض الإجازة');
    }
}