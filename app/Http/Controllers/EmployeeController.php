<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;
use App\Models\Factory;
use App\Models\EmployeeAsset;
use App\Models\EmployeePayrollAdjustment;
use App\Models\PayrollRegister;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    protected function authorizeHR()
    {
        if (!auth()->check() || !auth()->user()->canManageEmployees()) {
            abort(403, 'غير مصرح لك');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeHR();

        $employees = Employee::with('department')
            ->withCount(['activeAssets as active_assets_count'])
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

        return view('employees.create', compact('departments', 'factories'));
    }

    public function payrollRegister()
    {
        $this->authorizeHR();

        $employees = Employee::latest()->get();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $payrollRegister = PayrollRegister::firstOrCreate(
            [
                'month' => $currentMonth,
                'year' => $currentYear,
            ],
            [
                'status' => 'pending',
            ]
        );

        $adjustments = EmployeePayrollAdjustment::query()
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->get()
            ->keyBy('employee_id');

        return view('employees.payroll-register', compact('employees', 'payrollRegister', 'currentMonth', 'currentYear', 'adjustments'));
    }

    public function approvePayrollRegister()
    {
        $this->authorizeHR();

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $payrollRegister = PayrollRegister::firstOrCreate(
            [
                'month' => $currentMonth,
                'year' => $currentYear,
            ],
            [
                'status' => 'pending',
            ]
        );

        if ($payrollRegister->status !== 'approved') {
            $payrollRegister->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            AuditHelper::log(
                'update',
                'PayrollRegister',
                $payrollRegister->id,
                'تم اعتماد مسير الرواتب لشهر ' . $currentMonth . '/' . $currentYear
            );
        }

        return redirect()
            ->route('employees.payroll-register')
            ->with('success', 'تم اعتماد كشف الرواتب');
    }

    public function store(Request $request)
    {
        $this->authorizeHR();

        $employeeAllowedAccountRoles = [
            'sales_manager',
            'sales',
            'engineering_manager',
            'engineer',
            'procurement_manager',
            'procurement',
            'hr_manager',
            'hr',
            'operations_manager',
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'employee_number' => 'nullable|string|max:255|unique:employees,employee_number',
            'job_title' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'hire_date' => 'nullable|date',
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
                Rule::in($employeeAllowedAccountRoles),
            ],
        ]);

        if (($validated['create_system_account'] ?? false) && in_array(($validated['account_role'] ?? null), ['super_admin', 'admin'], true)) {
            return back()
                ->withErrors(['account_role' => 'هذه الصلاحية لا يمكن إنشاؤها من الموظفين'])
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

        return redirect()->route('employees.index')->with('success', 'تم إضافة الموظف بنجاح');
    }

    public function show(Employee $employee)
    {
        $this->authorizeHR();

        $employee->load(['documents', 'department', 'assets', 'assetAssignments.asset']);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $payrollAdjustment = EmployeePayrollAdjustment::firstOrNew([
            'employee_id' => $employee->id,
            'month' => $currentMonth,
            'year' => $currentYear,
        ]);

        return view('employees.show', compact('employee', 'payrollAdjustment', 'currentMonth', 'currentYear'));
    }

    public function savePayrollAdjustment(Request $request, Employee $employee)
    {
        $this->authorizeHR();

        $validated = $request->validate([
            'overtime_hours' => 'nullable|numeric|min:0',
            'leave_deduction_days' => 'nullable|numeric|min:0',
            'other_deduction' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $currentMonth = now()->month;
        $currentYear = now()->year;

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
            ->with('success', 'تم حفظ حسابات مسير الرواتب للشهر الحالي');
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

        return redirect()->route('employees.show', $employee)->with('success', 'تم تحديث بيانات الموظف');
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

        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف');
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

        return back()->with('success', 'تمت إضافة العهدة بنجاح');
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

        return back()->with('success', 'تم حذف العهدة');
    }

    private function generateEmployeeAssetSerialNumber(): string
    {
        do {
            $serial = 'AST-' . str_pad((string) (EmployeeAsset::max('id') + 1), 4, '0', STR_PAD_LEFT);
        } while (EmployeeAsset::where('serial_number', $serial)->exists());

        return $serial;
    }
}