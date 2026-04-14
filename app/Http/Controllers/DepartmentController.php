<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{

    public function index()
    {
        $departments = Department::latest()->get();

        return view('departments.index', compact('departments'));
    }


    public function create()
    {
        return view('departments.create');
    }


    public function store(Request $request)
    {
        Department::create([
            'name' => $request->name
        ]);

        return redirect()->route('departments.index');
    }


    public function show(Department $department)
    {
        return redirect()->route('departments.edit', $department->id);
    }


    public function edit(Department $department)
    {
        return view('departments.edit', compact('department'));
    }


    public function update(Request $request, Department $department)
    {
        $department->update([
            'name' => $request->name
        ]);

        return redirect()->route('departments.index');
    }


    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('departments.index');
    }

}