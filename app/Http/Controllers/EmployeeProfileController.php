<?php

namespace App\Http\Controllers;

use App\Models\Employee;

class EmployeeProfileController extends Controller
{
    public function show(Employee $employee)
    {
        $employee->load(['department', 'documents']);

        return view('employees.profile', compact('employee'));
    }
}