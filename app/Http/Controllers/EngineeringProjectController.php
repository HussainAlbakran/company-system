<?php

namespace App\Http\Controllers;

use App\Helpers\AuditHelper;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EngineeringProjectController extends Controller
{
    protected function authorizeProjects(): void
    {
        if (! auth()->check() || ! auth()->user()->canManageProjects()) {
            abort(403, __('projects.abort_unauthorized'));
        }
    }

    protected function getEngineeringDepartment()
    {
        return Department::where('name', 'الهندسة')
            ->orWhere('name', 'Engineering')
            ->first();
    }

    protected function getEngineeringEmployees($engineeringDepartment)
    {
        return Employee::with('department')
            ->when($engineeringDepartment, function ($query) use ($engineeringDepartment) {
                $query->where('department_id', $engineeringDepartment->id);
            })
            ->select(['id', 'name', 'department_id'])
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    protected function baseProjectValidationRules(bool $statusRequired): array
    {
        return [
            'responsible_employee_id' => ['nullable', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'progress_percentage' => ['required', 'integer', 'min:0', 'max:100'],
            'status' => $statusRequired
                ? ['required', 'string', 'max:255']
                : ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'project_pdf' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    /**
     * @return array{project_value: float, expenses: float}
     */
    protected function resolveProjectFinancialsFromRequest(Request $request, ?Project $existing = null): array
    {
        $user = auth()->user();

        if ($user->canViewProjectFinancials()) {
            return [
                'project_value' => (float) $request->input('project_value'),
                'expenses' => (float) $request->input('expenses'),
            ];
        }

        if ($user->canViewProjectValueOnly()) {
            $expenses = $existing !== null
                ? (float) $request->input('expenses', $existing->expenses)
                : (float) $request->input('expenses', 0);

            return [
                'project_value' => (float) $request->input('project_value'),
                'expenses' => $expenses,
            ];
        }

        return [
            'project_value' => $existing !== null ? (float) $existing->project_value : 0.0,
            'expenses' => $existing !== null ? (float) $existing->expenses : 0.0,
        ];
    }

    public function index()
    {
        $this->authorizeProjects();

        $engineeringDepartment = $this->getEngineeringDepartment();

        $projects = Project::with(['department', 'responsibleEmployee', 'updates'])
            ->when($engineeringDepartment, function ($query) use ($engineeringDepartment) {
                $query->where('department_id', $engineeringDepartment->id);
            })
            ->latest()
            ->paginate(15);

        $employees = $this->getEngineeringEmployees($engineeringDepartment);

        return view('projects.index', compact('projects', 'employees', 'engineeringDepartment'));
    }

    public function create()
    {
        $this->authorizeProjects();

        $engineeringDepartment = $this->getEngineeringDepartment();
        $employees = $this->getEngineeringEmployees($engineeringDepartment);

        return view('projects.create', compact('employees', 'engineeringDepartment'));
    }

    public function store(Request $request)
    {
        $this->authorizeProjects();

        $user = auth()->user();
        $rules = $this->baseProjectValidationRules(false);
        if ($user->canViewProjectFinancials()) {
            $rules['project_value'] = ['required', 'numeric', 'min:0'];
            $rules['expenses'] = ['required', 'numeric', 'min:0'];
        } elseif ($user->canViewProjectValueOnly()) {
            $rules['project_value'] = ['required', 'numeric', 'min:0'];
            $rules['expenses'] = ['nullable', 'numeric', 'min:0'];
        }

        $request->validate($rules);

        $financials = $this->resolveProjectFinancialsFromRequest($request, null);

        $employee = null;
        if ($request->filled('responsible_employee_id')) {
            $employee = Employee::find($request->responsible_employee_id);
        }

        $departmentId = null;

        if ($employee) {
            $departmentId = $employee->department_id;
        } else {
            $engineeringDepartment = $this->getEngineeringDepartment();
            if ($engineeringDepartment) {
                $departmentId = $engineeringDepartment->id;
            }
        }

        if (! $departmentId) {
            return back()
                ->withInput()
                ->with('error', __('projects.flash_no_department'));
        }

        $pdfPath = null;

        if ($request->hasFile('project_pdf')) {
            $pdfPath = $request->file('project_pdf')->store('project_pdfs', 'public');
            AuditHelper::log(
                'file_uploaded',
                'Project',
                null,
                'module=projects | file_name=' . $request->file('project_pdf')->getClientOriginalName()
            );
        }

        $project = DB::transaction(function () use ($request, $departmentId, $financials, $pdfPath) {
            return Project::create([
                'project_code' => Project::generateNextCode(),
                'department_id' => $departmentId,
                'responsible_employee_id' => $request->responsible_employee_id,
                'name' => $request->name,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'progress_percentage' => $request->progress_percentage,
                'project_value' => $financials['project_value'],
                'expenses' => $financials['expenses'],
                'status' => $request->status ?: 'ongoing',
                'project_pdf' => $pdfPath,
                'notes' => $request->notes,
                'client_user_id' => null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        });

        AuditHelper::log(
            'create',
            'Project',
            $project->id,
            'تم إنشاء مشروع: '.$project->name
        );

        return redirect()
            ->route('engineering-projects.index')
            ->with('success', __('projects.flash_created'));
    }

    public function show(Project $project)
    {
        $this->authorizeProjects();

        $project->load(['department', 'responsibleEmployee', 'updates']);

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorizeProjects();

        $engineeringDepartment = $this->getEngineeringDepartment();
        $employees = $this->getEngineeringEmployees($engineeringDepartment);

        return view('projects.edit', compact('project', 'employees', 'engineeringDepartment'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeProjects();

        $user = auth()->user();
        $rules = $this->baseProjectValidationRules(true);
        if ($user->canViewProjectFinancials()) {
            $rules['project_value'] = ['required', 'numeric', 'min:0'];
            $rules['expenses'] = ['required', 'numeric', 'min:0'];
        } elseif ($user->canViewProjectValueOnly()) {
            $rules['project_value'] = ['required', 'numeric', 'min:0'];
            $rules['expenses'] = ['nullable', 'numeric', 'min:0'];
        }

        $request->validate($rules);

        $financials = $this->resolveProjectFinancialsFromRequest($request, $project);

        if ($request->filled('responsible_employee_id')) {
            $employee = Employee::find($request->responsible_employee_id);

            if ($employee) {
                $project->responsible_employee_id = $employee->id;
                $project->department_id = $employee->department_id;
            }
        } else {
            $project->responsible_employee_id = null;

            $engineeringDepartment = $this->getEngineeringDepartment();
            if ($engineeringDepartment) {
                $project->department_id = $engineeringDepartment->id;
            }
        }

        if ($request->hasFile('project_pdf')) {
            if ($project->project_pdf && Storage::disk('public')->exists($project->project_pdf)) {
                Storage::disk('public')->delete($project->project_pdf);
            }

            $project->project_pdf = $request->file('project_pdf')->store('project_pdfs', 'public');
            AuditHelper::log(
                'file_uploaded',
                'Project',
                $project->id,
                'module=projects | file_name=' . $request->file('project_pdf')->getClientOriginalName()
            );
        }

        $project->name = $request->name;
        $project->description = $request->description;
        $project->start_date = $request->start_date;
        $project->end_date = $request->end_date;
        $project->progress_percentage = $request->progress_percentage;
        $project->project_value = $financials['project_value'];
        $project->expenses = $financials['expenses'];
        $project->status = $request->status;
        $project->notes = $request->notes;
        $project->client_user_id = null;
        $project->updated_by = Auth::id();
        $project->save();

        AuditHelper::log(
            'update',
            'Project',
            $project->id,
            'تم تعديل مشروع: '.$project->name
        );

        return redirect()
            ->route('engineering-projects.index')
            ->with('success', __('projects.flash_updated'));
    }

    public function destroy(Project $project)
    {
        $this->authorizeProjects();

        $project->load('updates');

        if ($project->project_pdf && Storage::disk('public')->exists($project->project_pdf)) {
            Storage::disk('public')->delete($project->project_pdf);
        }

        foreach ($project->updates as $update) {
            if ($update->attachment && Storage::disk('public')->exists($update->attachment)) {
                Storage::disk('public')->delete($update->attachment);
            }
        }

        AuditHelper::log(
            'delete',
            'Project',
            $project->id,
            'تم حذف مشروع: '.$project->name
        );

        $project->delete();

        return redirect()
            ->route('engineering-projects.index')
            ->with('success', __('projects.flash_deleted'));
    }
}
