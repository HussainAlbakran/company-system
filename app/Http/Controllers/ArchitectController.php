<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Contracts\View\View;

class ArchitectController extends Controller
{
    private function authorizeDesigns(): void
    {
        abort_unless(auth()->check() && auth()->user()->canAccessDesignsModule(), 403, __('architect.abort_module'));
    }

    public function index(): View
    {
        $this->authorizeDesigns();

        $projects = Project::where('current_stage', 'architect')
            ->latest()
            ->get();

        return view('architect.index', compact('projects'));
    }
}
