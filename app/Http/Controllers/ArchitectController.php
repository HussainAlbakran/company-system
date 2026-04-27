<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\AuditLog;
use App\Services\StageNotificationService;
use Illuminate\Support\Facades\Auth;

class ArchitectController extends Controller
{
    private function authorizeDesigns(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessDesignsModule(), 403, __('architect.abort_module'));
    }

    public function index()
    {
        $this->authorizeDesigns();

        $projects = Project::where('current_stage', 'architect')
            ->latest()
            ->get();

        return view('architect.index', compact('projects'));
    }

    public function complete($id, StageNotificationService $stageNotificationService)
    {
        $this->authorizeDesigns();

        $project = Project::findOrFail($id);

        // تحويل المشروع مباشرة إلى المشتريات
        $project->update([
            'current_stage' => 'purchasing',
            'status' => 'ongoing',
        ]);

        $stageNotificationService->sendPurchasesStageNotification($project);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'architect_completed',
            'model' => 'Project',
            'model_id' => $project->id,
            'description' => 'تم إنهاء المرحلة المعمارية وتحويل المشروع تلقائياً إلى المشتريات.',
        ]);

        return redirect()
            ->back()
            ->with('success', __('architect.flash_complete'));
    }
}