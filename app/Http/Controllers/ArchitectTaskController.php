<?php

namespace App\Http\Controllers;

use App\Models\ArchitectMeasurement;
use App\Models\ArchitectTask;
use App\Models\AuditLog;
use App\Models\ProductionOrder;
use App\Models\Project;
use App\Services\StageNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ArchitectTaskController extends Controller
{
    private function authorizeArchitect()
    {
        if (!in_array(auth()->user()->role, ['admin', 'engineer'])) {
            abort(403, 'غير مصرح لك بالدخول');
        }
    }

    public function index()
    {
        $this->authorizeArchitect();

        $projects = Project::where('current_stage', 'architect')
            ->latest()
            ->get();

        return view('architect.tasks.index', compact('projects'));
    }

    public function show($projectId)
    {
        $this->authorizeArchitect();

        $project = Project::findOrFail($projectId);

        $architectTask = ArchitectTask::firstOrCreate(
            ['project_id' => $project->id],
            [
                'drawing_status' => 'pending',
                'planning_status' => 'pending',
            ]
        );

        $measurements = ArchitectMeasurement::where('project_id', $project->id)
            ->latest()
            ->get();

        return view('architect.tasks.show', compact('project', 'architectTask', 'measurements'));
    }

    public function updateTask(Request $request, $projectId)
    {
        $this->authorizeArchitect();

        $project = Project::findOrFail($projectId);

        $request->validate([
            'drawing_type' => 'nullable|string|max:255',
            'drawing_status' => 'required|in:pending,in_progress,completed',
            'planning_status' => 'required|in:pending,in_progress,completed',
            'drawing_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg,dxf,xlsx,xls,csv|max:10240',
            'planning_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,dwg,dxf,xlsx,xls,csv|max:10240',
            'notes' => 'nullable|string',
        ]);

        $architectTask = ArchitectTask::firstOrCreate(
            ['project_id' => $project->id],
            [
                'drawing_status' => 'pending',
                'planning_status' => 'pending',
            ]
        );

        $drawingFilePath = $architectTask->drawing_file;
        $planningFilePath = $architectTask->planning_file;

        if ($request->hasFile('drawing_file')) {
            if ($architectTask->drawing_file && Storage::disk('public')->exists($architectTask->drawing_file)) {
                Storage::disk('public')->delete($architectTask->drawing_file);
            }

            $drawingFilePath = $request->file('drawing_file')->store('architect/drawings', 'public');
        }

        if ($request->hasFile('planning_file')) {
            if ($architectTask->planning_file && Storage::disk('public')->exists($architectTask->planning_file)) {
                Storage::disk('public')->delete($architectTask->planning_file);
            }

            $planningFilePath = $request->file('planning_file')->store('architect/planning', 'public');
        }

        $architectTask->update([
            'drawing_type' => $request->drawing_type,
            'drawing_status' => $request->drawing_status,
            'planning_status' => $request->planning_status,
            'drawing_file' => $drawingFilePath,
            'planning_file' => $planningFilePath,
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'تم تحديث بيانات المعماري بنجاح');
    }

    public function storeMeasurement(Request $request, $projectId)
    {
        $this->authorizeArchitect();

        $project = Project::findOrFail($projectId);

        $request->validate([
            'rows' => 'required|array',
        ]);

        foreach ($request->rows as $row) {

            if (empty($row['name']) || empty($row['quantity'])) {
                continue;
            }

            $measurement = ArchitectMeasurement::create([
                'project_id' => $project->id,
                'type' => $row['type'] ?? null,
                'name' => $row['name'],
                'length' => $row['length'] ?? 0,
                'width' => $row['width'] ?? 0,
                'height' => $row['height'] ?? 0,
                'quantity' => $row['quantity'],
                'unit' => $row['unit'] ?? 'm',
                'price' => $row['price'] ?? 0,
                'notes' => $row['notes'] ?? null,
            ]);

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'model' => 'ArchitectMeasurement',
                'model_id' => $measurement->id,
                'description' => 'تمت إضافة مقاس جديد للمشروع ' . $project->name,
            ]);
        }

        return back()->with('success', 'تم الحفظ');
    }

    public function updateMeasurement(Request $request, $id)
    {
        $this->authorizeArchitect();

        $measurement = ArchitectMeasurement::findOrFail($id);

        $validated = $request->validate([
            'type' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'unit' => 'nullable|string|max:20',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $measurement->update($validated);

        return back()->with('success', 'تم تعديل المقاس بنجاح');
    }

    public function destroyMeasurement($id)
    {
        $this->authorizeArchitect();

        $measurement = ArchitectMeasurement::findOrFail($id);
        $measurement->delete();

        return back()->with('success', 'تم حذف المقاس بنجاح');
    }

    public function sendToFactory($projectId, StageNotificationService $stageNotificationService)
    {
        $this->authorizeArchitect();

        $project = Project::findOrFail($projectId);

        $measurements = ArchitectMeasurement::where('project_id', $project->id)
            ->where('quantity', '>', 0)
            ->get();

        if ($measurements->isEmpty()) {
            return back()->with('error', 'لا توجد مقاسات');
        }

        foreach ($measurements as $m) {
            ProductionOrder::updateOrCreate([
                'project_id' => $project->id,
                'order_number' => 'PRJ-' . $project->id . '-' . $m->id,
            ], [
                'project_id' => $project->id,
                'product_name' => $m->name,
                'planned_quantity' => $m->quantity,
                'status' => 'pending',
            ]);
        }

        $project->update([
            'current_stage' => 'production_installation',
            'status' => 'ongoing',
        ]);

        $stageNotificationService->sendFactoryStageNotification($project);
        $stageNotificationService->sendInstallationStageNotification($project);
        $stageNotificationService->sendPurchasesStageNotification($project);

        return back()->with('success', 'تم إرسال المشروع + الإيميلات');
    }

    public function approve($projectId)
    {
        $this->authorizeArchitect();

        $project = Project::findOrFail($projectId);

        $project->update([
            'current_stage' => 'production_installation',
            'status' => 'ongoing',
        ]);

        return redirect()->route('architect-tasks.index')->with('success', 'تم الاعتماد');
    }
}