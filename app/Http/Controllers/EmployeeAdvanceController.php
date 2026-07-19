<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeAdvance;
use App\Services\EmployeeAdvanceService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmployeeAdvanceController extends Controller
{
    public function __construct(
        protected EmployeeAdvanceService $advanceService
    ) {}

    protected function authorizeFinanceModule(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessCashFlowModule(), 403);
    }

    public function index()
    {
        $this->authorizeFinanceModule();

        $advances = EmployeeAdvance::query()
            ->with([
                'employee',
                'issuer',
                'payments' => fn ($q) => $q->with(['payrollRegister', 'recorder'])->orderByDesc('recorded_at'),
            ])
            ->latest('issued_at')
            ->paginate(20);

        return view('employee-advances.index', compact('advances'));
    }

    public function create()
    {
        $this->authorizeFinanceModule();

        $employees = Employee::query()
            ->orderBy('name')
            ->get(['id', 'name', 'salary']);

        return view('employee-advances.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $this->authorizeFinanceModule();

        $validated = $request->validate([
            'employee_id' => ['required', 'exists:employees,id'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'installment_count' => ['required', 'integer', 'in:2,3,4,5,6'],
            'issued_at' => ['nullable', 'date'],
            'repayment_delay_months' => ['nullable', 'integer', 'min:0', 'max:12'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->advanceService->issue($validated, (int) auth()->id());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()
            ->route('employee-advances.index')
            ->with('success', __('employee_advance.issued_success'));
    }
}
