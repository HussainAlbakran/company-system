<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $employees = Employee::with('department')->get();
        $projects = Project::with('department')->get();

        return view('reports.index', compact('employees', 'projects'));
    }
}