<?php

namespace App\Http\Controllers;

use App\Models\ArchitectMeasurement;
use App\Models\ArchitectTask;
use App\Models\ProductionOrder;
use App\Models\Project;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    public function index()
    {
        // المشاريع الجاهزة للمصنع بعد المعماري
        $projects = Project::with(['architectTask', 'architectMeasurements'])
            ->where('current_stage', 'production_installation')
            ->latest()
            ->get();

        foreach ($projects as $project) {
            $project->measurements_count = $project->architectMeasurements->count();
        }

        $orders = ProductionOrder::with(['project'])
            ->latest()
            ->paginate(15);

        return view('factory.index', compact('orders', 'projects'));
    }

    public function create()
    {
        $projects = Project::with(['architectMeasurements'])
            ->where('current_stage', 'production_installation')
            ->latest()
            ->get();

        return view('factory.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'order_number' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'planned_quantity' => ['required', 'numeric', 'min:0.01'],
            'production_start_date' => ['nullable', 'date'],
            'expected_end_date' => ['nullable', 'date'],
            'daily_target' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        ProductionOrder::create([
            'project_id' => $validated['project_id'],
            'order_number' => $validated['order_number'],
            'product_name' => $validated['product_name'],
            'planned_quantity' => $validated['planned_quantity'],
            'produced_quantity' => 0,
            'supplied_quantity' => 0,
            'production_start_date' => $validated['production_start_date'] ?? null,
            'expected_end_date' => $validated['expected_end_date'] ?? null,
            'daily_target' => $validated['daily_target'] ?? null,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('factory.index')
            ->with('success', 'تم إنشاء أمر الإنتاج وربطه بالمشروع بنجاح');
    }

    public function show($id)
    {
        $order = ProductionOrder::with([
            'entries',
            'supplies',
            'project',
            'project.architectTask',
            'project.architectMeasurements',
        ])->findOrFail($id);

        $project = $order->project;

        $architectTask = null;
        $measurements = collect();

        if ($project) {
            $architectTask = $project->architectTask;
            $measurements = $project->architectMeasurements()->latest()->get();
        }

        return view('factory.show', compact('order', 'project', 'architectTask', 'measurements'));
    }

    public function edit($id)
    {
        $order = ProductionOrder::with('project')->findOrFail($id);

        $projects = Project::where('current_stage', 'production_installation')
            ->latest()
            ->get();

        return view('factory.edit', compact('order', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $order = ProductionOrder::findOrFail($id);

        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'order_number' => ['required', 'string', 'max:255'],
            'product_name' => ['required', 'string', 'max:255'],
            'planned_quantity' => ['required', 'numeric', 'min:0.01'],
            'production_start_date' => ['nullable', 'date'],
            'expected_end_date' => ['nullable', 'date'],
            'daily_target' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);

        $order->update([
            'project_id' => $validated['project_id'],
            'order_number' => $validated['order_number'],
            'product_name' => $validated['product_name'],
            'planned_quantity' => $validated['planned_quantity'],
            'production_start_date' => $validated['production_start_date'] ?? null,
            'expected_end_date' => $validated['expected_end_date'] ?? null,
            'daily_target' => $validated['daily_target'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('factory.index')
            ->with('success', 'تم تحديث أمر الإنتاج بنجاح');
    }

    public function destroy($id)
    {
        $order = ProductionOrder::findOrFail($id);
        $order->delete();

        return redirect()
            ->route('factory.index')
            ->with('success', 'تم حذف أمر الإنتاج بنجاح');
    }
}