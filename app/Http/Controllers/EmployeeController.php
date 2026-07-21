<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;
use App\Services\CashFlowLedgerService;
use App\Models\Factory;
use App\Models\EmployeeAsset;
use App\Models\EmployeePayrollAdjustment;
use App\Models\EmployeeAdvancePayment;
use App\Models\PayrollRegister;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    protected function authorizeHR()
    {
        if (! auth()->check() || ! auth()->user()->canManageEmployees()) {
            abort(403, __('roles.hr_module_only'));
        }
    }

    public function index(Request $request)
    {
        $this->authorizeHR();

        $employees = Employee::with('department')
            ->withCount([
                'activeAssets as active_assets_count',
                'activeAssetAssignments as active_asset_assignments_count',
            ])
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('employee_number', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $this->authorizeHR();

        $departments = Department::latest()->get();
        $factories = Factory::orderBy('id', 'desc')->get();
        $accountRoleKeys = User::EMPLOYEE_ACCOUNT_ROLES;

        return view('employees.create', compact('departments', 'factories', 'accountRoleKeys'));
    }

    public function payrollRegister()
    {
        $this->authorizeHR();

        $pending = PayrollRegister::query()
            ->where('status', 'pending')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        if ($pending) {
            return redirect()->route('employees.payroll-register.show', $pending);
        }

        $register = PayrollRegister::firstOrCreate(
            [
                'month' => now()->month,
                'year' => now()->year,
            ],
            [
                'status' => 'pending',
            ]
        );

        if ($register->isApproved()) {
            return redirect()
                ->route('employees.payroll-registers.index')
                ->with('success', __('employees.payroll_no_pending_hint'));
        }

        return redirect()->route('employees.payroll-register.show', $register);
    }

    public function payrollRegistersIndex()
    {
        $this->authorizeHR();

        $registers = PayrollRegister::query()
            ->with('approver')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        return view('employees.payroll-registers.index', compact('registers'));
    }

    public function showPayrollRegister(PayrollRegister $payrollRegister)
    {
        $this->authorizeHR();

        return view('employees.payroll-register', $this->payrollRegisterViewData($payrollRegister));
    }

    public function createPayrollRegister()
    {
        $this->authorizeHR();

        $existingPending = PayrollRegister::query()
            ->where('status', 'pending')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        if ($existingPending) {
            return redirect()
                ->route('employees.payroll-register.show', $existingPending)
                ->with('error', __('employees.payroll_pending_exists', [
                    'period' => $existingPending->periodLabel(),
                ]));
        }

        $latest = PayrollRegister::query()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        if ($latest) {
            $period = PayrollRegister::nextPeriodAfter((int) $latest->month, (int) $latest->year);
        } else {
            $period = ['month' => now()->month, 'year' => now()->year];
        }

        $register = PayrollRegister::firstOrCreate(
            [
                'month' => $period['month'],
                'year' => $period['year'],
            ],
            [
                'status' => 'pending',
            ]
        );

        if ($register->isApproved()) {
            return redirect()
                ->route('employees.payroll-registers.index')
                ->with('error', __('employees.payroll_period_already_approved', [
                    'period' => $register->periodLabel(),
                ]));
        }

        return redirect()
            ->route('employees.payroll-register.show', $register)
            ->with('success', __('employees.payroll_new_opened', ['period' => $register->periodLabel()]));
    }

    /**
     * @return array{month: int, year: int}
     */
    private function payrollEditPeriod(): array
    {
        $pending = PayrollRegister::query()
            ->where('status', 'pending')
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->first();

        if ($pending) {
            return [
                'month' => (int) $pending->month,
                'year' => (int) $pending->year,
            ];
        }

        return [
            'month' => (int) now()->month,
            'year' => (int) now()->year,
        ];
    }

    public function updatePayrollRegisterAdjustments(Request $request, PayrollRegister $payrollRegister)
    {
        $this->authorizeHR();

        if ($payrollRegister->isApproved()) {
            return back()->with('error', __('employees.payroll_cannot_edit_approved'));
        }

        $validated = $request->validate([
            'adjustments' => ['required', 'array'],
            'adjustments.*.overtime_hours' => ['nullable', 'numeric', 'min:0'],
            'adjustments.*.leave_deduction_days' => ['nullable', 'numeric', 'min:0'],
            'adjustments.*.other_deduction' => ['nullable', 'numeric', 'min:0'],
            'adjustments.*.notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $month = (int) $payrollRegister->month;
        $year = (int) $payrollRegister->year;
        $validEmployeeIds = Employee::query()->pluck('id')->all();

        foreach ($validated['adjustments'] as $employeeId => $row) {
            $employeeId = (int) $employeeId;

            if (! in_array($employeeId, $validEmployeeIds, true)) {
                continue;
            }

            EmployeePayrollAdjustment::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => $month,
                    'year' => $year,
                ],
                [
                    'overtime_hours' => $row['overtime_hours'] ?? 0,
                    'leave_deduction_days' => $row['leave_deduction_days'] ?? 0,
                    'other_deduction' => $row['other_deduction'] ?? 0,
                    'notes' => isset($row['notes']) && trim((string) $row['notes']) !== ''
                        ? trim((string) $row['notes'])
                        : null,
                    'created_by' => auth()->id(),
                ]
            );
        }

        AuditHelper::log(
            'update',
            'PayrollRegister',
            $payrollRegister->id,
            'تحديث حسابات مسير الرواتب '.$month.'/'.$year
        );

        return redirect()
            ->route('employees.payroll-register.show', $payrollRegister)
            ->with('success', __('employees.payroll_saved_success'));
    }

    /**
     * @return array<string, mixed>
     */
    private function payrollRegisterViewData(PayrollRegister $payrollRegister): array
    {
        $employees = Employee::latest()->get();
        $currentMonth = (int) $payrollRegister->month;
        $currentYear = (int) $payrollRegister->year;

        $adjustments = EmployeePayrollAdjustment::query()
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get()
            ->keyBy('employee_id');

        $hasPendingRegister = PayrollRegister::query()->where('status', 'pending')->exists();

        $advancePayments = EmployeeAdvancePayment::query()
            ->with(['advance.employee', 'recorder'])
            ->where(function ($query) use ($payrollRegister) {
                $query->where('payroll_register_id', $payrollRegister->id)
                    ->orWhere(function ($inner) use ($payrollRegister) {
                        $inner->whereNull('payroll_register_id')
                            ->where('month', $payrollRegister->month)
                            ->where('year', $payrollRegister->year);
                    });
            })
            ->orderBy('id')
            ->get();

        return array_merge(compact(
            'employees',
            'payrollRegister',
            'currentMonth',
            'currentYear',
            'adjustments',
            'hasPendingRegister',
            'advancePayments'
        ), [
            'canEditPayroll' => $payrollRegister->isPending(),
        ]);
    }

    public function leaveRegister(Request $request)
    {
        $this->authorizeHR();

        $employees = Employee::query()
            ->withCount('leaves')
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%' . $request->search . '%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('employee_number', 'like', $term);
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('employees.leave-register', compact('employees'));
    }

    public function approvePayrollRegister(Request $request)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'payroll_register_id' => ['required', 'exists:payroll_registers,id'],
        ]);

        $payrollRegister = PayrollRegister::query()->findOrFail($validated['payroll_register_id']);
        $currentMonth = (int) $payrollRegister->month;
        $currentYear = (int) $payrollRegister->year;

        if ($payrollRegister->status !== 'approved') {
            $payrollRegister->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $payrollRegister->refresh();
            app(CashFlowLedgerService::class)->syncPayrollRegister($payrollRegister);
            $advancePaymentsRecorded = app(\App\Services\AdvancePayrollService::class)->recordInstallmentsOnPayrollApproval(
                $payrollRegister,
                (int) auth()->id()
            );

            AuditHelper::log(
                'update',
                'PayrollRegister',
                $payrollRegister->id,
                'تم اعتماد مسير الرواتب لشهر '.$currentMonth.'/'.$currentYear
            );

            $successMessage = __('employees.payroll_approved_success');
            if ($advancePaymentsRecorded > 0) {
                $successMessage .= ' '.__('employees.payroll_advance_payments_recorded', [
                    'count' => $advancePaymentsRecorded,
                ]);
            }

            return redirect()
                ->route('employees.payroll-register.show', $payrollRegister)
                ->with('success', $successMessage);
        }

        return redirect()
            ->route('employees.payroll-register.show', $payrollRegister)
            ->with('success', __('employees.payroll_already_approved'));
    }

    public function store(Request $request)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_number' => 'nullable|string|max:255|unique:employees,employee_number',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'salary' => 'nullable|numeric',
            'housing_allowance' => 'nullable|numeric',
            'transportation_allowance' => 'nullable|numeric',
            'travel_allowance' => 'nullable|numeric',
            'risk_allowance' => 'nullable|numeric',
            'transfer_allowance' => 'nullable|numeric',
            'overtime_allowance' => 'nullable|numeric',
            'status' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'factory_id' => 'nullable|exists:factories,id',
            'manager_id' => 'nullable|exists:employees,id',
            'user_id' => 'nullable|exists:users,id',
            'passport_number' => 'nullable|string|max:255',
            'passport_expiry_date' => 'nullable|date',
            'residency_expiry_date' => 'nullable|date',
            'leave_balance' => 'nullable|integer|min:0',
            'create_system_account' => 'nullable|boolean',
            'account_name' => 'nullable|string|max:255|required_if:create_system_account,1',
            'account_email' => 'nullable|email|max:255|required_if:create_system_account,1|unique:users,email',
            'account_password' => 'nullable|string|min:8|required_if:create_system_account,1',
            'account_role' => [
                'nullable',
                'required_if:create_system_account,1',
                Rule::in(User::EMPLOYEE_ACCOUNT_ROLES),
            ],
        ]);

        if (($validated['create_system_account'] ?? false) && in_array(($validated['account_role'] ?? null), ['super_admin', 'admin'], true)) {
            return back()
                ->withErrors(['account_role' => __('employees.account_role_forbidden')])
                ->withInput();
        }

        if ($validated['create_system_account'] ?? false) {
            $user = User::create([
                'name' => $validated['account_name'],
                'email' => $validated['account_email'],
                'password' => Hash::make($validated['account_password']),
                'role' => $validated['account_role'],
                'approval_status' => 'approved',
                'is_active' => true,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            $validated['user_id'] = $user->id;
        }

        unset(
            $validated['create_system_account'],
            $validated['account_name'],
            $validated['account_email'],
            $validated['account_password'],
            $validated['account_role']
        );

        if (!isset($validated['leave_balance']) || $validated['leave_balance'] === null) {
            $validated['leave_balance'] = 26;
        }

        $allowanceFields = [
            'housing_allowance',
            'transportation_allowance',
            'travel_allowance',
            'risk_allowance',
            'transfer_allowance',
            'overtime_allowance',
        ];

        foreach ($allowanceFields as $field) {
            if (! isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                $validated[$field] = 0;
            }
        }

        $employee = Employee::create($validated);

        AuditHelper::log(
            'create',
            'Employee',
            $employee->id,
            'تمت إضافة موظف: ' . $employee->name
        );

        return redirect()->route('employees.index')->with('success', __('employees.flash_created'));
    }

    public function show(Employee $employee)
    {
        $this->authorizeHR();

        $employee->load(['documents', 'department', 'assets', 'assetAssignments.asset']);

        $period = $this->payrollEditPeriod();
        $currentMonth = $period['month'];
        $currentYear = $period['year'];

        $payrollAdjustment = EmployeePayrollAdjustment::firstOrNew([
            'employee_id' => $employee->id,
            'month' => $currentMonth,
            'year' => $currentYear,
        ]);

        $payrollRegister = PayrollRegister::query()
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        $canEditPayrollAdjustment = ! $payrollRegister || $payrollRegister->isPending();

        return view('employees.show', compact(
            'employee',
            'payrollAdjustment',
            'currentMonth',
            'currentYear',
            'canEditPayrollAdjustment'
        ));
    }

    public function savePayrollAdjustment(Request $request, Employee $employee)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'overtime_hours' => 'nullable|numeric|min:0',
            'leave_deduction_days' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        $period = $this->payrollEditPeriod();
        $currentMonth = (int) ($validated['month'] ?? $period['month']);
        $currentYear = (int) ($validated['year'] ?? $period['year']);

        $register = PayrollRegister::query()
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();

        if ($register?->isApproved()) {
            return back()->with('error', __('employees.payroll_cannot_edit_approved'));
        }

        EmployeePayrollAdjustment::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'month' => $currentMonth,
                'year' => $currentYear,
            ],
            [
                'overtime_hours' => $validated['overtime_hours'] ?? 0,
                'leave_deduction_days' => $validated['leave_deduction_days'] ?? 0,
                'other_deduction' => $validated['other_deduction'] ?? 0,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]
        );

        return redirect()
            ->route('employees.show', $employee)
            ->with('success', __('employees.payroll_adjustment_saved'));
    }

    public function edit(Employee $employee)
    {
        $this->authorizeHR();

        $departments = Department::latest()->get();
        $factories = Factory::orderBy('id', 'desc')->get();

        return view('employees.edit', compact('employee', 'departments', 'factories'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_number' => 'nullable|string|max:255|unique:employees,employee_number,' . $employee->id,
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'salary' => 'nullable|numeric',
            'housing_allowance' => 'nullable|numeric',
            'transportation_allowance' => 'nullable|numeric',
            'travel_allowance' => 'nullable|numeric',
            'risk_allowance' => 'nullable|numeric',
            'transfer_allowance' => 'nullable|numeric',
            'overtime_allowance' => 'nullable|numeric',
            'status' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'factory_id' => 'nullable|exists:factories,id',
            'manager_id' => 'nullable|exists:employees,id',
            'user_id' => 'nullable|exists:users,id',
            'passport_number' => 'nullable|string|max:255',
            'passport_expiry_date' => 'nullable|date',
            'residency_expiry_date' => 'nullable|date',
            'leave_balance' => 'nullable|integer|min:0',
        ]);

        if (!isset($validated['leave_balance']) || $validated['leave_balance'] === null) {
            $validated['leave_balance'] = $employee->leave_balance ?? 26;
        }

        $allowanceFields = [
            'housing_allowance',
            'transportation_allowance',
            'travel_allowance',
            'risk_allowance',
            'transfer_allowance',
            'overtime_allowance',
        ];

        foreach ($allowanceFields as $field) {
            if (! isset($validated[$field]) || $validated[$field] === null || $validated[$field] === '') {
                $validated[$field] = 0;
            }
        }

        $employee->update($validated);

        AuditHelper::log(
            'update',
            'Employee',
            $employee->id,
            'تم تحديث بيانات الموظف: ' . $employee->name
        );

        return redirect()->route('employees.show', $employee)->with('success', __('employees.flash_updated'));
    }

    public function destroy(Employee $employee)
    {
        $this->authorizeHR();

        AuditHelper::log(
            'delete',
            'Employee',
            $employee->id,
            'تم حذف موظف: ' . $employee->name
        );

        $employee->delete();

        return redirect()->route('employees.index')->with('success', __('employees.flash_deleted'));
    }

    public function storeAsset(Request $request, $employeeId)
    {
        $this->authorizeHR();

        $employee = Employee::findOrFail($employeeId);

        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,ended,lost,damaged',
            'notes' => 'nullable|string',
        ]);

        $serialNumber = $this->generateEmployeeAssetSerialNumber();

        $asset = EmployeeAsset::create([
            'employee_id' => $employee->id,
            'asset_name' => $validated['asset_name'],
            'serial_number' => $serialNumber,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditHelper::log(
            'create',
            'EmployeeAsset',
            $asset->id,
            'تم إضافة عهدة للموظف: ' . $employee->name . ' - ' . $validated['asset_name'] . ' - ' . $serialNumber
        );

        return back()->with('success', __('employees.flash_custody_added'));
    }

    public function destroyAsset($id)
    {
        $this->authorizeHR();

        $asset = EmployeeAsset::findOrFail($id);

        AuditHelper::log(
            'delete',
            'EmployeeAsset',
            $asset->id,
            'تم حذف عهدة لموظف رقم: ' . $asset->employee_id
        );

        $asset->delete();

        return back()->with('success', __('employees.flash_custody_deleted'));
    }

    private function generateEmployeeAssetSerialNumber(): string
    {
        do {
            $serial = 'AST-' . str_pad((string) (EmployeeAsset::max('id') + 1), 4, '0', STR_PAD_LEFT);
        } while (EmployeeAsset::where('serial_number', $serial)->exists());

        return $serial;
    }
}