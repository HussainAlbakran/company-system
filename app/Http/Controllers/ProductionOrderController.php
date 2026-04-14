<?php

namespace App\Http\Controllers;

use App\Models\ProductionOrder;
use App\Models\Project;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function index()
    {
        return redirect()->route('factory.index');
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
            'project_id' => 'required|exists:projects,id',
            'order_number' => 'required|string|max:255|unique:production_orders,order_number',
            'product_name' => 'required|string|max:255',
            'planned_quantity' => 'required|numeric|min:0',
            'production_start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'daily_target' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
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
            ->with('success', 'تم إنشاء أمر الإنتاج بنجاح');
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
        $architectTask = $project?->architectTask;
        $measurements = $project
            ? $project->architectMeasurements()->latest()->get()
            : collect();

        return view('factory.show', compact('order', 'project', 'architectTask', 'measurements'));
    }

    public function edit($id)
    {
        $order = ProductionOrder::findOrFail($id);

        $projects = Project::where('current_stage', 'production_installation')
            ->latest()
            ->get();

        return view('factory.edit', compact('order', 'projects'));
    }

    public function update(Request $request, $id)
    {
        $order = ProductionOrder::findOrFail($id);

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'order_number' => 'required|string|max:255|unique:production_orders,order_number,' . $order->id,
            'product_name' => 'required|string|max:255',
            'planned_quantity' => 'required|numeric|min:0',
            'production_start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date',
            'daily_target' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,in_progress,paused,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $order->update([
            'project_id' => $validated['project_id'],
            'order_number' => $validated['order_number'],
            'product_name' => $validated['product_name'],
            'planned_quantity' => $validated['planned_quantity'],
            'production_start_date' => $validated['production_start_date'] ?? null,
            'expected_end_date' => $validated['expected_end_date'] ?? null,
            'daily_target' => $validated['daily_target'] ?? null,
            'status' => $validated['status'] ?? $order->status,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('factory.show', $order->id)
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