<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Helpers\AuditHelper;
use App\Models\Factory;

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

        $departments = \App\Models\Department::latest()->get();
        $factories = \App\Models\Factory::orderBy('id', 'desc')->get();

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
            'residency_expiry_date' => 'nullable|date',
        ]);

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

        $employee->load(['documents', 'department']);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $this->authorizeHR();

        $departments = Department::latest()->get();

        return view('employees.edit', compact('employee', 'departments'));
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
            'residency_expiry_date' => 'nullable|date',
        ]);

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
}