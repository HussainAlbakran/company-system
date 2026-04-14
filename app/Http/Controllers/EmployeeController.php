<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;
use App\Models\Factory;
use App\Models\EmployeeAsset;

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
            'salary' => 'nullable|numeric',
            'status' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'factory_id' => 'nullable|exists:factories,id',
            'manager_id' => 'nullable|exists:employees,id',
            'user_id' => 'nullable|exists:users,id',
            'residency_expiry_date' => 'nullable|date',
            'leave_balance' => 'nullable|integer|min:0',
        ]);

        if (!isset($validated['leave_balance']) || $validated['leave_balance'] === null) {
            $validated['leave_balance'] = 26;
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

        $employee->load(['documents', 'department', 'assets']);

        return view('employees.show', compact('employee'));
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
            'status' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'factory_id' => 'nullable|exists:factories,id',
            'manager_id' => 'nullable|exists:employees,id',
            'user_id' => 'nullable|exists:users,id',
            'residency_expiry_date' => 'nullable|date',
            'leave_balance' => 'nullable|integer|min:0',
        ]);

        if (!isset($validated['leave_balance']) || $validated['leave_balance'] === null) {
            $validated['leave_balance'] = $employee->leave_balance ?? 26;
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