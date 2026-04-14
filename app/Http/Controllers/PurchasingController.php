<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class PurchasingController extends Controller
{
    public function index()
    {
        $projects = Project::where('current_stage', 'purchasing')
            ->latest()
            ->get();

        return view('purchasing.index', compact('projects'));
    }

    public function complete($id)
    {
        $project = Project::findOrFail($id);

        // تحويل المشروع مباشرة إلى المصنع والتركيبات معًا
        $project->update([
            'current_stage' => 'production_installation',
            'status' => 'ongoing',
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'purchasing_completed',
            'model' => 'Project',
            'model_id' => $project->id,
            'description' => 'تم إنهاء المشتريات وتحويل المشروع تلقائياً إلى المصنع والتركيبات.',
        ]);

        return redirect()
            ->back()
            ->with('success', 'تم إنهاء المشتريات وتحويل المشروع إلى المصنع والتركيبات.');
    }
}