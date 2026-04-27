<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    public function index()
    {
        $departments = Department::with('managerUser')->latest()->get();

        return view('departments.index', compact('departments'));
    }


    public function create()
    {
        $managerUsers = $this->departmentManagerCandidates();
        return view('departments.create', compact('managerUsers'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'manager_user_id' => ['nullable', 'exists:users,id'],
        ]);

        Department::create($validated);

        return redirect()->route('departments.index');
    }


    public function show(Department $department)
    {
        return redirect()->route('departments.edit', $department->id);
    }


    public function edit(Department $department)
    {
        $managerUsers = $this->departmentManagerCandidates();
        return view('departments.edit', compact('department', 'managerUsers'));
    }


    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'manager_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $department->update($validated);

        return redirect()->route('departments.index');
    }


    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index');
    }

    private function departmentManagerCandidates()
    {
        return User::query()
            ->whereNotNull('email')
            ->where('is_active', true)
            ->where('approval_status', 'approved')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);
    }

}