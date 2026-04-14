<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\AuditLog;
use App\Models\ProductionOrder;
use App\Models\ArchitectTask;
use App\Models\ArchitectMeasurement;
use Illuminate\Support\Facades\Auth;

class InstallationController extends Controller
{
    public function index()
    {
        $projects = Project::with('productionOrders')
            ->where('current_stage', 'production_installation')
            ->latest()
            ->get();

        foreach ($projects as $project) {
            $this->hydrateProjectProductionStats($project);

            $project->architectTask = ArchitectTask::where('project_id', $project->id)->first();
            $project->measurements_count = ArchitectMeasurement::where('project_id', $project->id)->count();
        }

        return view('installations.index', compact('projects'));
    }

    public function show($projectId)
    {
        $project = Project::with('productionOrders')->findOrFail($projectId);
        $this->hydrateProjectProductionStats($project);

        $architectTask = ArchitectTask::where('project_id', $project->id)->first();
        $measurements = ArchitectMeasurement::where('project_id', $project->id)
            ->latest()
            ->get();

        $productionOrders = $project->productionOrders()->latest()->get();

        return view('installations.show', compact('project', 'architectTask', 'measurements', 'productionOrders'));
    }

    public function complete($id)
    {
        $project = Project::findOrFail($id);

        $project->update([
            'current_stage' => 'completed',
            'status' => 'completed',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'installation_completed',
            'model' => 'Project',
            'model_id' => $project->id,
            'description' => 'تم إنهاء التركيبات وإغلاق المشروع',
        ]);

        return redirect()->route('installations.index')->with('success', 'تم إنهاء التركيبات وإغلاق المشروع');
    }

    private function hydrateProjectProductionStats(Project $project): void
    {
        $required = (float) $project->productionOrders->sum('planned_quantity');
        $produced = (float) $project->productionOrders->sum('produced_quantity');
        $remaining = max($required - $produced, 0);

        $project->planned_quantity = $required;
        $project->produced_quantity = $produced;
        $project->remaining_quantity = $remaining;
        $project->progress_percentage = $required > 0
            ? round(min(($produced / $required) * 100, 100), 2)
            : 0;
    }
}