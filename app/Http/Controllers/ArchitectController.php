<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ArchitectController extends Controller
{
    public function index()
    {
        $projects = Project::where('current_stage', 'architect')
            ->latest()
            ->get();

        return view('architect.index', compact('projects'));
    }

    public function complete($id)
    {
        $project = Project::findOrFail($id);

        // تحويل المشروع مباشرة إلى المشتريات
        $project->update([
            'current_stage' => 'purchasing',
            'status' => 'ongoing',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'architect_completed',
            'model' => 'Project',
            'model_id' => $project->id,
            'description' => 'تم إنهاء المرحلة المعمارية وتحويل المشروع تلقائياً إلى المشتريات.',
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم إنهاء المرحلة المعمارية وتحويل المشروع إلى المشتريات.');
    }
}